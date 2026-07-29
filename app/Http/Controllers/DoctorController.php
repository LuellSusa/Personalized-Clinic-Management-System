<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function dashboard(): View
    {
        return view('doctor.dashboard', [
            'appointments' => Appointment::with(['child', 'parentProfile.user'])
                ->where('doctor_user_id', auth()->id())
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->get(),
        ]);
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->doctor_user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,completed,cancelled,no_show'],
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

        return redirect()
            ->route('doctor.dashboard')
            ->with('success', 'Appointment status updated.');
    }
}
