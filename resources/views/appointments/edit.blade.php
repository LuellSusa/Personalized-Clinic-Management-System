<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isReschedule ? 'Reschedule' : 'Edit' }} Appointment - TitaClinic</title>
    @include('partials.styles')
    <link rel="stylesheet" href="{{ asset('css/pages/appointment-form.css') }}">
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container appointment-form-shell py-5">
    <div class="appointment-form-heading">
        <a class="appointment-back-button" href="{{ route('appointments.index') }}">&#8592; Go back</a>
        <h1>{{ $isReschedule ? 'Reschedule Appointment' : 'Edit Appointment' }}</h1>
    </div>

    <div class="card"><div class="card-body p-4">
        @if ($isReschedule)
            <div class="alert alert-info">Choose a new branch date. The original missed booking will remain in your history.</div>
        @endif
        <form method="POST" action="{{ $isReschedule ? route('appointments.reschedule.store', $appointment) : route('appointments.update', $appointment) }}">
            @csrf
            @method($isReschedule ? 'PATCH' : 'PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Child</label>
                    <select name="child_id" class="form-select" required>
                        @foreach ($children as $child)
                            <option value="{{ $child->id }}" {{ old('child_id', $appointment->child_id) == $child->id ? 'selected' : '' }}>{{ $child->first_name }} {{ $child->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Doctor</label>
                    <select name="doctor_user_id" class="form-select">
                        <option value="">Any available doctor</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_user_id', $appointment->doctor_user_id) == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Appointment type</label>
                    <select name="appointment_type" class="form-select" required>
                        @foreach (['consultation' => 'Consultation', 'follow_up' => 'Follow-up', 'vaccination' => 'Vaccination', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('appointment_type', $appointment->appointment_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @include('appointments.partials.branch-calendar', [
                    'selectedBranch' => $appointment->branch,
                    'selectedDate' => $isReschedule ? null : $appointment->appointment_date->format('Y-m-d'),
                ])

                <div class="col-12">
                    <label class="form-label">Reason</label>
                    <input type="text" name="reason" class="form-control" value="{{ old('reason', $appointment->reason) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
            @endif
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Go back</a>
                <button class="btn btn-primary">{{ $isReschedule ? 'Confirm new schedule' : 'Update appointment' }}</button>
            </div>
        </form>
    </div></div>
</div>
</body>
</html>
