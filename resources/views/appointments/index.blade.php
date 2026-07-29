<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Appointments</h1>
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
        <div class="row g-3">
            @foreach ($appointments as $appointment)
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $appointment->appointment_type }}</h5>
                            <p class="card-text mb-1"><strong>Date:</strong> {{ $appointment->appointment_date->format('Y-m-d') }}</p>
                            <p class="card-text mb-1"><strong>Time:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                            <p class="card-text mb-0"><strong>Status:</strong> {{ str($appointment->status)->replace('_', ' ')->title() }}</p>
                            <div class="mt-3 d-flex gap-2">
                                @if ($appointment->status === 'scheduled')
                                    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endif
                                @if (in_array($appointment->status, ['scheduled', 'confirmed'], true))
                                    <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Cancel this appointment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
