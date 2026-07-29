<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">Book Appointment</h2>
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Child</label>
                                <select name="child_id" class="form-select" required>
                                    <option value="">Select child</option>
                                    @foreach ($children as $child)
                                        <option value="{{ $child->id }}">{{ $child->first_name }} {{ $child->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_user_id" class="form-select">
                                    <option value="">Any available doctor</option>
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_user_id') == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->first_name }} {{ $doctor->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Appointment type</label>
                                <select name="appointment_type" class="form-select" required>
                                    <option value="consultation">Consultation</option>
                                    <option value="follow_up">Follow-up</option>
                                    <option value="vaccination">Vaccination</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Appointment date</label>
                                <input type="date" name="appointment_date" class="form-control" required value="{{ old('appointment_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start time</label>
                                <input type="time" name="start_time" class="form-control" required value="{{ old('start_time') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End time</label>
                                <input type="time" name="end_time" class="form-control" required value="{{ old('end_time') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Reason</label>
                                <input type="text" name="reason" class="form-control" value="{{ old('reason') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
                        @endif
                        <button class="btn btn-primary mt-4">Book appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
