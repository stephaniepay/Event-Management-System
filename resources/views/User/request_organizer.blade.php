@extends('layout')

@section('title', 'Request Organizer Access')

@section('content')
<div class="container mt-3 request-organizer-access-container">
    <h1 class="mb-4">Request Organizer Access</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('organizer.request') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="organizationName">Organization Name</label>
            <input type="text" class="form-control" id="organizationName" name="organization_name" value="{{ $organizer->name ?? '' }}">
        </div>
        <div class="form-group mb-3">
            <label for="message">Message</label>
            <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Send Request</button>
        </div>
    </form>

    <div class="mt-3">
        <a href="{{ route('profile') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>
@endsection
