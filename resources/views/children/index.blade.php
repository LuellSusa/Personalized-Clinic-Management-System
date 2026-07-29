<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Children - TitaClinic</title>
    @include('partials.styles')
</head>
<body class="bg-light">
@include('partials.navigation')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Children</h1>
        <a href="{{ route('children.create') }}" class="btn btn-primary">Add child</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($children->isEmpty())
        <div class="alert alert-info">No children yet.</div>
    @else
        <div class="row g-3">
            @foreach ($children as $child)
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $child->first_name }} {{ $child->last_name }}</h5>
                            <p class="card-text mb-1"><strong>Patient number:</strong> {{ $child->patient_number }}</p>
                            <p class="card-text mb-1"><strong>Birth date:</strong> {{ $child->birth_date->format('Y-m-d') }}</p>
                            <p class="card-text mb-0"><strong>Status:</strong> {{ $child->status }}</p>
                            <div class="mt-3 d-flex gap-2">
                                <a href="{{ route('children.edit', $child) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('children.destroy', $child) }}" onsubmit="return confirm('Delete this child record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
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
