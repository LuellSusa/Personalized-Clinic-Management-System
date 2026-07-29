<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        }

        $profile = $user->parentProfile;
        $children = $profile
            ? $profile->children()->orderBy('first_name')->get()
            : collect();
        $appointments = $profile
            ? $profile->appointments()
                ->with(['child', 'doctor'])
                ->whereDate('appointment_date', '>=', today())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->take(5)
                ->get()
            : collect();

        return view('dashboard', [
            'pageProps' => [
                'user' => [
                    'firstName' => $user->first_name,
                    'fullName' => trim("{$user->first_name} {$user->last_name}"),
                    'initials' => strtoupper(substr($user->first_name, 0, 1).substr($user->last_name, 0, 1)),
                ],
                'profileComplete' => $profile !== null,
                'kpis' => [
                    'children' => $children->where('status', 'active')->count(),
                    'upcoming' => $profile
                        ? $profile->appointments()
                            ->whereDate('appointment_date', '>=', today())
                            ->whereIn('status', ['scheduled', 'confirmed'])
                            ->count()
                        : 0,
                    'confirmed' => $profile
                        ? $profile->appointments()
                            ->whereDate('appointment_date', '>=', today())
                            ->where('status', 'confirmed')
                            ->count()
                        : 0,
                    'completed' => $profile
                        ? $profile->appointments()->where('status', 'completed')->count()
                        : 0,
                ],
                'appointments' => $appointments->map(fn ($appointment) => [
                    'id' => $appointment->id,
                    'type' => $appointment->appointment_type,
                    'date' => $appointment->appointment_date->format('Y-m-d'),
                    'startTime' => $appointment->start_time,
                    'endTime' => $appointment->end_time,
                    'status' => $appointment->status,
                    'childName' => trim("{$appointment->child->first_name} {$appointment->child->last_name}"),
                    'doctorName' => $appointment->doctor
                        ? 'Dr. '.trim("{$appointment->doctor->first_name} {$appointment->doctor->last_name}")
                        : null,
                ])->values(),
                'children' => $children->take(4)->map(fn ($child) => [
                    'id' => $child->id,
                    'name' => trim("{$child->first_name} {$child->last_name}"),
                    'initials' => strtoupper(substr($child->first_name, 0, 1).substr($child->last_name, 0, 1)),
                    'ageLabel' => $child->birth_date->age.' years old',
                    'status' => $child->status,
                ])->values(),
                'routes' => [
                    'home' => url('/'),
                    'dashboard' => route('dashboard'),
                    'profile' => route('parent-profile.create'),
                    'children' => route('children.index'),
                    'addChild' => route('children.create'),
                    'appointments' => route('appointments.index'),
                    'bookAppointment' => route('appointments.create'),
                    'logout' => route('logout'),
                ],
                'csrfToken' => csrf_token(),
            ],
        ]);
    }
}
