<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Profile - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">Parent Profile</h2>
                    @if (session('warning'))
                        <div class="alert alert-warning">{{ session('warning') }}</div>
                    @endif
                    <form method="POST" action="{{ route('parent-profile.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $profile?->address) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Emergency contact</label>
                            <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $profile?->emergency_contact) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $profile?->occupation) }}">
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <button class="btn btn-primary">Save profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
