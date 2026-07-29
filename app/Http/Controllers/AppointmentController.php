<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

        return view('appointments.index', [
            'appointments' => $parentProfile->appointments()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

        return view('appointments.create', [
            'children' => $parentProfile->children()->where('status', 'active')->orderBy('first_name')->get(),
            'doctors' => User::where('role', 'doctor')->where('status', 'active')->orderBy('first_name')->get(),
        ]);
    }

    public function edit(Appointment $appointment): View
    {
        abort_unless($appointment->parentProfile->user_id === auth()->id(), 403);

        return view('appointments.edit', [
            'appointment' => $appointment,
            'children' => $appointment->parentProfile->children()->where('status', 'active')->orderBy('first_name')->get(),
            'doctors' => User::where('role', 'doctor')->where('status', 'active')->orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

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
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['parent_profile_id'] = $parentProfile->id;
        $validated['created_by_user_id'] = auth()->id();
        $validated['status'] = 'scheduled';

        $this->ensureDoctorIsAvailable($validated);

        $parentProfile->appointments()->create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked.');
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->parentProfile->user_id === auth()->id(), 403);
        abort_unless($appointment->status === 'scheduled', 403);

        $validated = $request->validate([
            'child_id' => [
                'required',
                Rule::exists('children', 'id')->where(
                    fn ($query) => $query
                        ->where('parent_profile_id', $appointment->parent_profile_id)
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
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->ensureDoctorIsAvailable($validated, $appointment);

        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->parentProfile->user_id === auth()->id(), 403);

        if (! in_array($appointment->status, ['scheduled', 'confirmed'], true)) {
            throw ValidationException::withMessages([
                'appointment' => 'This appointment can no longer be cancelled.',
            ]);
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled.');
    }

    private function ensureDoctorIsAvailable(array $appointmentData, ?Appointment $currentAppointment = null): void
    {
        if (empty($appointmentData['doctor_user_id'])) {
            return;
        }

        $conflict = Appointment::query()
            ->where('doctor_user_id', $appointmentData['doctor_user_id'])
            ->whereDate('appointment_date', $appointmentData['appointment_date'])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('start_time', '<', $appointmentData['end_time'])
            ->where('end_time', '>', $appointmentData['start_time'])
            ->when($currentAppointment, fn ($query) => $query->where('id', '!=', $currentAppointment->id))
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'doctor_user_id' => 'The selected doctor already has an appointment during this time.',
            ]);
        }
    }
}
