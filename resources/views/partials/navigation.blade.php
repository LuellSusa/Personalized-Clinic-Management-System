@auth
    <nav class="clinic-nav">
        <div class="container d-flex flex-wrap align-items-center gap-3 py-3">
            <a class="clinic-brand me-auto" href="{{ route('dashboard') }}">TitaClinic</a>

            @if (auth()->user()->role === 'parent')
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('parent-profile.create') }}">Profile</a>
                <a href="{{ route('children.index') }}">Children</a>
                <a href="{{ route('appointments.index') }}">Appointments</a>
            @elseif (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.users') }}">Users</a>
            @elseif (auth()->user()->role === 'doctor')
                <a href="{{ route('doctor.dashboard') }}">Appointments</a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Logout</button>
            </form>
        </div>
    </nav>
@endauth
