<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $appointments = Appointment::with(['child', 'doctor'])
            ->latest()
            ->take(6)
            ->get();
        $pendingUsers = User::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'pageProps' => [
                'admin' => [
                    'name' => trim(auth()->user()->first_name.' '.auth()->user()->last_name),
                    'initials' => strtoupper(substr(auth()->user()->first_name, 0, 1).substr(auth()->user()->last_name, 0, 1)),
                ],
                'kpis' => [
                    'users' => User::count(),
                    'pending' => User::where('status', 'pending')->count(),
                    'doctors' => User::where('role', 'doctor')->where('status', 'active')->count(),
                    'patients' => Child::where('status', 'active')->count(),
                    'upcoming' => Appointment::whereDate('appointment_date', '>=', today())
                        ->whereIn('status', ['scheduled', 'confirmed'])
                        ->count(),
                ],
                'pendingUsers' => $pendingUsers->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => trim("{$user->first_name} {$user->last_name}"),
                    'email' => $user->email,
                    'createdAt' => $user->created_at->format('Y-m-d'),
                ])->values(),
                'appointments' => $appointments->map(fn ($appointment) => [
                    'id' => $appointment->id,
                    'patient' => trim("{$appointment->child->first_name} {$appointment->child->last_name}"),
                    'doctor' => $appointment->doctor
                        ? trim("{$appointment->doctor->first_name} {$appointment->doctor->last_name}")
                        : 'Unassigned',
                    'date' => $appointment->appointment_date->format('Y-m-d'),
                    'status' => $appointment->status,
                    'type' => $appointment->appointment_type,
                ])->values(),
                'routes' => [
                    'home' => url('/'),
                    'dashboard' => route('admin.dashboard'),
                    'users' => route('admin.users'),
                    'logout' => route('logout'),
                ],
                'csrfToken' => csrf_token(),
            ],
        ]);
    }

    public function users(): View
    {
        return view('admin.users', [
            'users' => User::latest()->paginate(15),
        ]);
    }

    public function updateUserAccess(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,doctor,parent'],
            'status' => ['required', 'in:pending,active,suspended,inactive'],
        ]);

        $wouldRemoveActiveAdmin = $user->role === 'admin'
            && $user->status === 'active'
            && ($validated['role'] !== 'admin' || $validated['status'] !== 'active');

        if (
            $wouldRemoveActiveAdmin
            && User::where('role', 'admin')->where('status', 'active')->where('id', '!=', $user->id)->doesntExist()
        ) {
            throw ValidationException::withMessages([
                'role' => 'At least one active administrator must remain.',
            ]);
        }

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'User access updated.');
    }
}
