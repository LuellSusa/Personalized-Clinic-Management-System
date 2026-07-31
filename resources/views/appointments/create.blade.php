<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - TitaClinic</title>
    @include('partials.styles')
    <link rel="stylesheet" href="{{ asset('css/pages/appointment-form.css') }}">
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container appointment-form-shell py-5">
    <div class="appointment-form-heading">
        <a class="appointment-back-button" href="{{ route('appointments.index') }}">&#8592; Go back</a>
        <h1>Book Appointment</h1>
    </div>

    <div class="card"><div class="card-body p-4">
        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Child</label>
                    <select name="child_id" class="form-select" required>
                        <option value="">Select child</option>
                        @foreach ($children as $child)
                            <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>{{ $child->first_name }} {{ $child->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Doctor</label>
                    <select name="doctor_user_id" class="form-select">
                        <option value="">Any available doctor</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_user_id') == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Appointment type</label>
                    <select name="appointment_type" class="form-select" required>
                        @foreach (['consultation' => 'Consultation', 'follow_up' => 'Follow-up', 'vaccination' => 'Vaccination', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('appointment_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @include('appointments.partials.branch-calendar', ['selectedBranch' => null, 'selectedDate' => null])

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
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Go back</a>
                <button class="btn btn-primary">Book appointment</button>
            </div>
        </form>
    </div></div>
</div>
</body>
</html>
