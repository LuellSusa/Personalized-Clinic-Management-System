<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Child - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">Edit Child</h2>
                    <form method="POST" action="{{ route('children.update', $child) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Patient number</label>
                                <input type="text" name="patient_number" class="form-control" required value="{{ old('patient_number', $child->patient_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $child->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $child->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">First name</label>
                                <input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $child->first_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $child->middle_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last name</label>
                                <input type="text" name="last_name" class="form-control" required value="{{ old('last_name', $child->last_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Birth date</label>
                                <input type="date" name="birth_date" class="form-control" required value="{{ old('birth_date', $child->birth_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sex</label>
                                <select name="sex" class="form-select" required>
                                    <option value="male" {{ $child->sex === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $child->sex === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Blood type</label>
                                <input type="text" name="blood_type" class="form-control" value="{{ old('blood_type', $child->blood_type) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Allergies</label>
                                <textarea name="allergies" class="form-control" rows="2">{{ old('allergies', $child->allergies) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Medical conditions</label>
                                <textarea name="medical_conditions" class="form-control" rows="2">{{ old('medical_conditions', $child->medical_conditions) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Current medications</label>
                                <textarea name="current_medications" class="form-control" rows="2">{{ old('current_medications', $child->current_medications) }}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-4">Update child</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
