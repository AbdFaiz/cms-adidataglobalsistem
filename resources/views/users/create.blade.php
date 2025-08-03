@extends('layouts.app')

@section('title', 'Add User')

@section('content')
    <div>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
            <div class="d-block mb-4 mb-md-0">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">
                                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                            </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add user</li>
                    </ol>
                </nav>
                <h2 class="h4">Add user</h2>
                <p class="mb-0">Your user creation template.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-body shadow-sm mb-4">
                    <h2 class="h5 mb-4">General information</h2>
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Name</label>
                                <input name="name" class="form-control" id="name" type="text"
                                    placeholder="Enter your name" required value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username">Username</label>
                                <input name="username" class="form-control" id="username" type="text"
                                    placeholder="Enter your username" required value="{{ old('username') }}">
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email">Email</label>
                                <input name="email" class="form-control" id="email" type="email"
                                    placeholder="name@company.com" value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone">Phone</label>
                                <input name="phone" class="form-control" id="phone" type="text"
                                    placeholder="+12-345 678 910" value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status">Status</label>
                                <select name="status" class="form-select" id="status" required>
                                    <option value="" disabled selected>Choose</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role">Role</label>
                                <select name="role" class="form-select" id="role" required>
                                    <option value="" disabled selected>Choose...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><span class="fas fa-unlock-alt"></span></span>
                                    <input name="password" type="password" placeholder="Password" class="form-control"
                                        id="password" required>
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><span class="fas fa-unlock-alt"></span></span>
                                    <input name="password_confirmation" type="password" placeholder="Confirm Password"
                                        class="form-control" id="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="avatar">Select profile photo</label>
                            <input name="avatar" type="file" accept="image/*" class="form-control" id="avatar">
                            @error('avatar')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <img id="avatarPreview" src="#" alt="Avatar Preview"
                                style="display:none; max-width:150px; margin-top:10px;">
                        </div>
                        <button type="submit" class="btn btn-gray-800 mt-2 animate-up-2">Save All</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewAvatar(event) {
            const [file] = event.target.files;
            if (file) {
                const preview = document.getElementById('avatarPreview');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        }
    </script>
@endsection
