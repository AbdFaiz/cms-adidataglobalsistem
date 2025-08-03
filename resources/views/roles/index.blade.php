@extends('layouts.app')

@section('content')
<div>
    <!-- Header dan Breadcrumb tetap -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#">Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role Management</li>
                </ol>
            </nav>
            <h2 class="h4">Role Management</h2>
            <p class="mb-0">Your role management dashboard template.</p>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card card-body shadow-sm table-wrapper table-responsive">
        <table class="table user-table table-hover align-items-center">
            <thead>
                <tr>
                    <th class="border-bottom">Name</th>
                    <th class="border-bottom">Guard Name</th>
                    <th class="border-bottom">Date Created</th>
                    <th class="border-bottom">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                <tr>
                    <td><span>{{ $role->name }}</span></td>
                    <td><span>{{ ucfirst($role->guard_name) }}</span></td>
                    <td><span>{{ $role->created_at->format('d M Y') }}</span></td>
                    <td>
                        <div>
                            <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 p-0"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                                    </path>
                                </svg>
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
                                <a class="dropdown-item rounded-top" href="{{ route('roles.edit', $role->id) }}">
                                    <span class="fas fa-user-shield me-2"></span> Edit role
                                </a>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger rounded-bottom">
                                        <span class="fas fa-user-times me-2"></span> Delete role
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if ($roles->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">No roles found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
