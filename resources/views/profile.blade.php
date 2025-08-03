@extends('layouts.app')

@section('title', "{$user->name} Profile")

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div>
            <a href="{{ url()->previous() ?? route('dashboard') }}" class="btn btn-gray-800 me-2">
                <span class="fas fa-arrow-left me-2"></span>Back
            </a>
        </div>
        <div>
            <button type="button" id="editBtn" class="btn btn-primary">
                <i class="fas fa-pencil-alt me-2"></i>Update Information
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5">Your information</h2>
                <hr class="my-4 mb-2 text-gray-900">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="avatar">Avatar</label>
                            <input disabled name="avatar" class="form-control" id="avatar" type="file"
                                accept="image/*">
                            <small class="text-muted
                                d-block mt-2">Upload a new avatar
                                image. Supported formats: JPG, PNG, GIF.</small>
                            @if ($errors->has('avatar'))
                                <small class="text-danger">{{ $errors->first('avatar') }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Name</label>
                            <input disabled name="name" class="form-control" id="name" type="text"
                                placeholder="Enter your name" required value="{{ old('name', $user->name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username">Username</label>
                            <input disabled name="username" class="form-control" id="username" type="text"
                                placeholder="Enter your username" required value="{{ old('username', $user->username) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="email">Email</label>
                            <input disabled name="email" class="form-control" id="email" type="email"
                                placeholder="name@company.com" value="{{ old('email', $user->email) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="phone">Phone</label>
                            <input disabled name="phone" class="form-control" id="phone" type="text"
                                placeholder="+12-345 678 910" value="{{ old('phone', $user->phone) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status">Status</label>
                            <select disabled name="status" class="form-select" id="status" required>
                                <option value="" disabled
                                    {{ old('status', $user->status ?? '') == '' ? 'selected' : '' }}>Choose</option>
                                <option value="active"
                                    {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="pending"
                                    {{ old('status', $user->status ?? '') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="suspended"
                                    {{ old('status', $user->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended
                                </option>
                                <option value="inactive"
                                    {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role">Role</label>
                            <select disabled name="role_id" class="form-select" id="role" required>
                                <option>
                                    {{ $user->getRoleNames()->first() ?? '' }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-secondary w-100" id="submitBtn" disabled>Save Changes</button>
                    </div>                    
                </form>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow border-0 text-center p-0">
                        <div class="profile-cover rounded-top"
                            style="background-image:url('{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('storage/avatars/default-avatar.png') }}')">
                        </div>
                        <div class="card-body pb-5">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('storage/avatars/default-avatar.png') }}"
                                class="avatar-xl border border-gray-900 rounded-circle mx-auto mt-n7" alt="User Avatar">
                            <h4 class="h3">
                                {{ $user->username }}
                            </h4>
                            <h5 class="fw-normal">{{ $user->getRoleNames()->first() }}</h5>
                            <p class="text-gray mb-4">{{ $user->email }}</p>
                            <a class="btn btn-sm btn-gray-800 d-inline-flex align-items-center me-2" href="#">
                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z">
                                    </path>
                                </svg>
                                Connect
                            </a>
                            <a class="btn btn-sm btn-secondary" href="#">Send Message</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editBtn = document.getElementById('editBtn');
            const submitBtn = document.getElementById('submitBtn');
    
            editBtn.addEventListener('click', function () {
                // Pilih semua input & select, lalu filter yang bukan id 'status' atau 'role'
                const formElements = document.querySelectorAll('form input, form select');
    
                formElements.forEach(el => {
                    if (el.id !== 'status' && el.id !== 'role') {
                        el.removeAttribute('disabled');
                    }
                });
    
                submitBtn.removeAttribute('disabled');
            });
        });
    </script>    
@endsection
