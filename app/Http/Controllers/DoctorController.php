<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Support\BranchSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function dashboard(Request $request): View
    {
        $validated = $request->validate(['date' => ['nullable', 'date_format:Y-m-d']]);
        $selectedDate = isset($validated['date']) ? Carbon::parse($validated['date'])->startOfDay() : today();
        $month = $selectedDate->copy()->startOfMonth();
        $baseQuery = Appointment::where('doctor_user_id', $request->user()->id);

        return view('doctor.dashboard', [
            'pageProps' => [
                'doctor' => [
                    'name' => trim($request->user()->first_name.' '.$request->user()->last_name),
                    'initials' => strtoupper(substr($request->user()->first_name, 0, 1).substr($request->user()->last_name, 0, 1)),
                ],
                'kpis' => [
                    'today' => (clone $baseQuery)->whereDate('appointment_date', today())->whereNotIn('status', ['cancelled'])->count(),
                    'upcoming' => (clone $baseQuery)->whereDate('appointment_date', '>=', today())->whereIn('status', ['scheduled', 'confirmed'])->count(),
                    'confirmed' => (clone $baseQuery)->whereDate('appointment_date', '>=', today())->where('status', 'confirmed')->count(),
                    'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
                ],
                'initialMonth' => $month->format('Y-m'),
                'selectedDate' => $selectedDate->format('Y-m-d'),
                'calendarBookings' => $this->calendarData($request->user()->id, $month),
                'appointments' => $this->appointmentsForDate($request->user()->id, $selectedDate),
                'branches' => BranchSchedule::all(),
                'routes' => [
                    'home' => url('/'),
                    'dashboard' => route('doctor.dashboard'),
                    'calendar' => route('doctor.calendar'),
                    'appointmentStatusBase' => url('/doctor/appointments'),
                    'logout' => route('logout'),
                ],
                'flash' => [
                    'success' => session('success'),
                    'error' => session('errors')?->first(),
                ],
                'csrfToken' => csrf_token(),
            ],
        ]);
    }

    public function calendar(Request $request): JsonResponse
    {
        $validated = $request->validate(['month' => ['required', 'date_format:Y-m']]);
        $month = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

        return response()->json([
            'month' => $month->format('Y-m'),
            'bookings' => $this->calendarData($request->user()->id, $month),
        ]);
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->doctor_user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,completed,cancelled,no_show'],
            'redirect_date' => ['nullable', 'date'],
        ]);

        $allowedTransitions = [
            'scheduled' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled', 'no_show'],
            'completed' => [],
            'cancelled' => [],
            'no_show' => [],
        ];

        if (! in_array($validated['status'], $allowedTransitions[$appointment->status] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "A {$appointment->status} appointment cannot be changed to {$validated['status']}.",
            ]);
        }

        $appointment->update(['status' => $validated['status']]);

        $url = route('doctor.dashboard', array_filter(['date' => $validated['redirect_date'] ?? null]));

        return redirect()->to($url.'#doctor-bookings')->with('success', 'Appointment status updated.');
    }

    private function calendarData(int $doctorId, Carbon $month): array
    {
        return Appointment::where('doctor_user_id', $doctorId)
            ->whereBetween('appointment_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->whereNotIn('status', ['cancelled'])
            ->get(['appointment_date', 'status'])
            ->groupBy(fn ($appointment) => $appointment->appointment_date->format('Y-m-d'))
            ->map(fn ($appointments) => [
                'count' => $appointments->count(),
                'confirmed' => $appointments->where('status', 'confirmed')->count(),
            ])
            ->all();
    }

    private function appointmentsForDate(int $doctorId, Carbon $date): array
    {
        return Appointment::with(['child', 'parentProfile.user'])
            ->where('doctor_user_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->latest('id')
            ->get()
            ->map(fn ($appointment) => [
                'id' => $appointment->id,
                'type' => $appointment->appointment_type,
                'branch' => $appointment->branch,
                'patient' => trim("{$appointment->child->first_name} {$appointment->child->last_name}"),
                'parent' => trim("{$appointment->parentProfile->user->first_name} {$appointment->parentProfile->user->last_name}"),
                'reason' => $appointment->reason,
                'status' => $appointment->status,
            ])
            ->values()
            ->all();
    }
}
