<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - TitaClinic</title>
    @include('partials.styles')
    <link rel="stylesheet" href="{{ asset('css/pages/appointments-index.css') }}">
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Booking history</h1>
            <p class="text-muted mb-0">Upcoming and previous clinic schedules.</p>
        </div>
        <a href="{{ route('appointments.create') }}" class="btn btn-primary">Book appointment</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if ($appointments->isEmpty())
        <div class="alert alert-info">No appointments yet.</div>
    @else
        <div class="appointments-history-grid">
            @foreach ($appointments as $appointment)
                @php
                    $isPast = $appointment->appointment_date->lt(today());
                    $branch = $branches[$appointment->branch] ?? null;
                    $canReschedule = $isPast && in_array($appointment->status, ['scheduled', 'confirmed', 'no_show'], true);
                @endphp
                <article class="appointment-history-card {{ $isPast ? 'is-past' : '' }}">
                    <div class="appointment-history-top">
                        <div>
                            <h2>{{ str($appointment->appointment_type)->replace('_', ' ')->title() }}</h2>
                            <span>{{ $appointment->child->first_name }} {{ $appointment->child->last_name }}</span>
                        </div>
                        <span class="appointment-history-status">{{ $isPast ? 'Past' : str($appointment->status)->replace('_', ' ')->title() }}</span>
                    </div>

                    <div class="appointment-history-details">
                        <p><strong>Date:</strong> {{ $appointment->appointment_date->format('F j, Y') }}</p>
                        <p><strong>Branch:</strong> {{ $branch['name'] ?? 'Clinic branch' }}</p>
                        <p><strong>Clinic hours:</strong> {{ $branch['hours'] ?? 'Not available' }}</p>
                        <p><strong>Doctor:</strong> {{ $appointment->doctor ? 'Dr. '.$appointment->doctor->first_name.' '.$appointment->doctor->last_name : 'Any available doctor' }}</p>
                        <p><strong>Recorded status:</strong> {{ str($appointment->status)->replace('_', ' ')->title() }}</p>
                    </div>

                    @if ($isPast)
                        <p class="appointment-history-past-note">This booking date has passed. If the visit did not happen, choose a new schedule below.</p>
                    @endif

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if (! $isPast && $appointment->status === 'scheduled')
                            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @endif
                        @if (! $isPast && in_array($appointment->status, ['scheduled', 'confirmed'], true))
                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Cancel this appointment?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Cancel</button>
                            </form>
                        @endif
                        @if ($canReschedule)
                            <a href="{{ route('appointments.reschedule', $appointment) }}" class="btn btn-sm btn-primary">Reschedule</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
