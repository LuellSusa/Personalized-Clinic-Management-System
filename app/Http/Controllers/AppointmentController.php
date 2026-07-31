<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ParentProfile;
use App\Models\User;
use App\Support\BranchSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

        return view('appointments.index', [
            'appointments' => $parentProfile->appointments()
                ->with(['child', 'doctor'])
                ->orderByDesc('appointment_date')
                ->latest('id')
                ->get(),
            'branches' => BranchSchedule::all(),
        ]);
    }

    public function create(): View
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

        return view('appointments.create', $this->formData($parentProfile));
    }

    public function edit(Appointment $appointment): View
    {
        $this->authorizeParent($appointment);
        abort_unless($appointment->status === 'scheduled' && $appointment->appointment_date->gte(today()), 403);

        return view('appointments.edit', [
            ...$this->formData($appointment->parentProfile),
            'appointment' => $appointment,
            'isReschedule' => false,
        ]);
    }

    public function reschedule(Appointment $appointment): View
    {
        $this->authorizeParent($appointment);
        abort_unless(
            $appointment->appointment_date->lt(today())
            && in_array($appointment->status, ['scheduled', 'confirmed', 'no_show'], true),
            403
        );

        return view('appointments.edit', [
            ...$this->formData($appointment->parentProfile),
            'appointment' => $appointment,
            'isReschedule' => true,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();
        $validated = $this->validateAppointment($request, $parentProfile);

        $parentProfile->appointments()->create([
            ...$validated,
            'parent_profile_id' => $parentProfile->id,
            'created_by_user_id' => auth()->id(),
            'start_time' => null,
            'end_time' => null,
            'status' => 'scheduled',
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked.');
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeParent($appointment);
        abort_unless($appointment->status === 'scheduled' && $appointment->appointment_date->gte(today()), 403);

        $validated = $this->validateAppointment($request, $appointment->parentProfile);
        $appointment->update([...$validated, 'start_time' => null, 'end_time' => null]);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function storeReschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeParent($appointment);
        abort_unless(
            $appointment->appointment_date->lt(today())
            && in_array($appointment->status, ['scheduled', 'confirmed', 'no_show'], true),
            403
        );

        $validated = $this->validateAppointment($request, $appointment->parentProfile);

        DB::transaction(function () use ($appointment, $validated): void {
            $appointment->update(['status' => 'no_show']);
            $appointment->parentProfile->appointments()->create([
                ...$validated,
                'parent_profile_id' => $appointment->parent_profile_id,
                'created_by_user_id' => auth()->id(),
                'start_time' => null,
                'end_time' => null,
                'status' => 'scheduled',
            ]);
        });

        return redirect()->route('appointments.index')->with('success', 'Appointment rescheduled. The original booking remains in your history.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->authorizeParent($appointment);

        if (! in_array($appointment->status, ['scheduled', 'confirmed'], true)) {
            throw ValidationException::withMessages([
                'appointment' => 'This appointment can no longer be cancelled.',
            ]);
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled.');
    }

    private function validateAppointment(Request $request, ParentProfile $parentProfile): array
    {
        $validated = $request->validate([
            'child_id' => [
                'required',
                Rule::exists('children', 'id')->where(
                    fn ($query) => $query
                        ->where('parent_profile_id', $parentProfile->id)
                        ->whereNull('deleted_at')
                ),
            ],
            'doctor_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', 'doctor')->where('status', 'active')
                ),
            ],
            'appointment_type' => ['required', 'in:consultation,follow_up,vaccination,other'],
            'branch' => ['required', Rule::in(BranchSchedule::keys())],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (! BranchSchedule::isAvailable($validated['branch'], $validated['appointment_date'])) {
            $branch = BranchSchedule::all()[$validated['branch']];

            throw ValidationException::withMessages([
                'appointment_date' => "{$branch['name']} is available only on {$branch['days_label']}.",
            ]);
        }

        return $validated;
    }

    private function formData(ParentProfile $parentProfile): array
    {
        return [
            'children' => $parentProfile->children()->where('status', 'active')->orderBy('first_name')->get(),
            'doctors' => User::where('role', 'doctor')->where('status', 'active')->orderBy('first_name')->get(),
            'branches' => BranchSchedule::all(),
        ];
    }

    private function authorizeParent(Appointment $appointment): void
    {
        abort_unless($appointment->parentProfile->user_id === auth()->id(), 403);
    }
}
