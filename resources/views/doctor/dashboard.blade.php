<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <h1 class="h3 mb-4">Doctor Dashboard</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if ($appointments->isEmpty())
        <div class="alert alert-info">No assigned appointments yet.</div>
    @else
        <div class="row g-3">
            @foreach ($appointments as $appointment)
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $appointment->appointment_type }}</h5>
                            <p class="card-text mb-1"><strong>Patient:</strong> {{ $appointment->child->first_name }} {{ $appointment->child->last_name }}</p>
                            <p class="card-text mb-1"><strong>Parent:</strong> {{ $appointment->parentProfile->user->first_name }} {{ $appointment->parentProfile->user->last_name }}</p>
                            <p class="card-text mb-1"><strong>Date:</strong> {{ $appointment->appointment_date->format('Y-m-d') }}</p>
                            <p class="card-text mb-1"><strong>Time:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                            <p class="card-text mb-1"><strong>Reason:</strong> {{ $appointment->reason ?: 'Not provided' }}</p>
                            <p class="card-text mb-0"><strong>Status:</strong> {{ str($appointment->status)->replace('_', ' ')->title() }}</p>

                            @php
                                $nextStatuses = match ($appointment->status) {
                                    'scheduled' => ['confirmed' => 'Confirm', 'cancelled' => 'Cancel'],
                                    'confirmed' => ['completed' => 'Complete', 'no_show' => 'No-show', 'cancelled' => 'Cancel'],
                                    default => [],
                                };
                            @endphp

                            @if ($nextStatuses)
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    @foreach ($nextStatuses as $status => $label)
                                        <form method="POST" action="{{ route('doctor.appointments.status', $appointment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button class="btn btn-sm {{ $status === 'cancelled' ? 'btn-outline-danger' : 'btn-primary' }}">
                                                {{ $label }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
