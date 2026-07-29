<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">Edit Appointment</h2>
                    <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Child</label>
                                <select name="child_id" class="form-select" required>
                                    @foreach ($children as $child)
                                        <option value="{{ $child->id }}" {{ $appointment->child_id == $child->id ? 'selected' : '' }}>{{ $child->first_name }} {{ $child->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_user_id" class="form-select">
                                    <option value="">Any available doctor</option>
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_user_id', $appointment->doctor_user_id) == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->first_name }} {{ $doctor->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Appointment type</label>
                                <select name="appointment_type" class="form-select" required>
                                    <option value="consultation" {{ $appointment->appointment_type === 'consultation' ? 'selected' : '' }}>Consultation</option>
                                    <option value="follow_up" {{ $appointment->appointment_type === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                    <option value="vaccination" {{ $appointment->appointment_type === 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                                    <option value="other" {{ $appointment->appointment_type === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Appointment date</label>
                                <input type="date" name="appointment_date" class="form-control" required value="{{ old('appointment_date', $appointment->appointment_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start time</label>
                                <input type="time" name="start_time" class="form-control" required value="{{ old('start_time', $appointment->start_time) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End time</label>
                                <input type="time" name="end_time" class="form-control" required value="{{ old('end_time', $appointment->end_time) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Reason</label>
                                <input type="text" name="reason" class="form-control" value="{{ old('reason', $appointment->reason) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-4">Update appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
