@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">AGS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users List</li>
                </ol>
            </nav>
            <h2 class="h4">Users List</h2>
            <p class="mb-0">Manage your users and their roles.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if (auth()->user()->roles->contains('name', 'Supervisor') || auth()->user()->roles->contains('name', 'Leader'))
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
                    <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                        </path>
                    </svg>
                    New User
                </a>
            @endif
            <div class="btn-group ms-2 ms-lg-3">
                <button type="button" class="btn btn-sm btn-outline-gray-600">Share</button>
                <button type="button" class="btn btn-sm btn-outline-gray-600">Export</button>
            </div>
    </div>
</div>

{{-- Table filter and search (You can add backend support for these) --}}
<div class="table-settings mb-4">
    <div class="row justify-content-between align-items-center">
        <div class="col-9 col-lg-8 d-md-flex">
            <form method="GET" action="{{ route('users.index') }}" class="d-flex">
                <div class="input-group me-2 me-lg-3 fmxw-300">
                    <span class="input-group-text">
                        <svg class="icon icon-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search users">
                </div>
                <select name="status" class="form-select fmxw-200 d-none d-md-inline" aria-label="Filter by status">
                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <button type="submit" class="btn btn-primary ms-2">Filter</button>
            </form>
        </div>
    </div>
</div>

@if (session('tempPassword'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Password sementara:</strong> {{ session('tempPassword') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<div class="card card-body shadow border-0 table-wrapper table-responsive">
    <div class="d-flex mb-3">
        <select class="form-select fmxw-200" aria-label="Bulk action select">
            <option selected>Bulk Action</option>
            <option value="send_email">Send Email</option>
            <option value="change_group">Change Group</option>
            <option value="delete_user">Delete User</option>
        </select>
        <button class="btn btn-sm px-3 btn-secondary ms-3">Apply</button>
    </div>
    <table class="table user-table table-hover align-items-center">
        <thead>
            <tr>
                <th class="border-bottom">
                    <div class="form-check dashboard-check">
                        <input class="form-check-input" type="checkbox" id="checkAllUsers">
                        <label class="form-check-label" for="checkAllUsers"></label>
                    </div>
                </th>
                <th class="border-bottom">Name</th>
                <th class="border-bottom">Phone</th>
                <th class="border-bottom">Roles</th>
                <th class="border-bottom">Date Created</th>
                <th class="border-bottom">Status</th>
                <th class="border-bottom">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        <div class="form-check dashboard-check">
                            <input class="form-check-input" type="checkbox" value="{{ $user->id }}"
                                id="userCheck{{ $user->id }}">
                            <label class="form-check-label" for="userCheck{{ $user->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user->id) }}"
                            class="d-flex align-items-center text-decoration-none text-dark">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('storage/avatars/default-avatar.png') }}"
                                class="avatar rounded-circle me-3" alt="Avatar">
                            <div class="d-block">
                                <span class="fw-bold">{{ $user->name }}</span>
                                <div class="small text-muted">{{ $user->email }}</div>
                            </div>
                        </a>
                    </td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>
                        @if ($user->roles->count())
                            @foreach ($user->roles as $role)
                                <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No Role</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        @php
                            $statusClass = match ($user->status ?? 'inactive') {
                                'active' => 'success',
                                'pending' => 'warning',
                                'inactive' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($user->status ?? 'Inactive') }}</span>
                    </td>
                    <td>
                        <div>
                            <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 p-0"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                                    </path>
                                </svg>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                {{-- Tombol New User --}}
                                @php
                                    $currentUserRoles = auth()->user()->roles->pluck('name')->toArray();

                                    // Tentukan apakah bisa create user
                                    $canCreateUser = false;
                                    if (in_array('Supervisor', $currentUserRoles)) {
                                        $canCreateUser = true;
                                    } elseif (in_array('Leader', $currentUserRoles)) {
                                        $canCreateUser = true;
                                    }
                                @endphp
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                        <svg class="icon icon-xxs me-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                </li>
                                @if ($canCreateUser)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                            <svg class="icon icon-xxs me-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536M9 11l6-6m1.5 8.5L16 15l-3-3-3 3v1.5h1.5l3-3z">
                                                </path>
                                            </svg>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <svg class="icon icon-xxs me-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if (canResetPassword(auth()->user(), $user))
                                    <li>
                                        <form action="{{ route('users.reset-password', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to reset the password for {{ $user->name }}?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <svg class="icon icon-xxs me-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4v1m0 14v1m7-7h1M4 12H3m15.364-6.364l.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707.707M6.343 6.343l-.707-.707" />
                                                </svg>
                                                Reset Password
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
