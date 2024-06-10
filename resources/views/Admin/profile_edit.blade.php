@extends('layout')

@section('title', 'Edit Profile')

@section('content')

    <div class="container mt-3">

        <h1 class="mb-3">Edit Profile</h1>

        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif

        <form action="{{ route('profile.edit') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Username</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3 text-end">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>

        <div class="mt-5">
            <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>
@endsection
