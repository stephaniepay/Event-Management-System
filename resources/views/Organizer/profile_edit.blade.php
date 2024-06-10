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

        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('old_password'))
            <div class="alert alert-danger">
                {{ $errors->first('old_password') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3">Organization Details</h2>
                <form method="POST" action="{{ route('organizer.updateDetails') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="d-flex flex-column align-items-center">
                        <div class="profile-picture-container mb-5 position-relative">
                            @php
                                $hasProfilePicture = auth()->user()->organizer && auth()->user()->organizer->picture_filename;
                                $profilePicturePath = $hasProfilePicture ? Storage::url('images/organizer_profiles/' . auth()->user()->organizer->picture_filename) : asset('storage/images/organizer_profiles/default.png');
                            @endphp

                            <img id="profilePicturePreview" src="{{ $profilePicturePath }}" alt="Profile Picture"
                            style="width: 250px; height: 250px; object-fit: cover;"
                            class="avatar rounded-circle organizer-img-avatar img-thumbnail shadow-sm {{ $hasProfilePicture ? '' : 'hidden' }}">

                            <input type="file" id="profilePicture" name="profile_picture" onchange="previewImage();" hidden>
                        </div>

                        <div class="mt-4">

                            @if($hasProfilePicture)
                                <button type="button" class="remove-profile-picture btn btn-danger position-absolute"
                                        style="width: 2.8rem; top: 66%; right: 35em; transform: translateY(-50%);" onclick="removeProfilePicture();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif

                            <input type="hidden" id="removeProfilePictureFlag" name="remove_profile_picture" value="0">

                            <label for="profilePicture" class="upload-profile-picture btn btn-primary position-absolute"
                                style="width: 2.8rem; top: 66%; left: 35rem; transform: translateY(-50%);">
                                <i class="fas fa-camera"></i>
                            </label>

                        </div>

                        <div class="w-100 mb-3">
                            <label for="organizationName" class="form-label">Organization Name</label>
                            <input type="text" class="form-control" id="organizationName" name="organization_name"
                                   value="{{ auth()->user()->organizer->name ?? '' }}" placeholder="Enter organization name">
                        </div>

                        <div class="text-end w-100">
                            <button type="submit" class="btn btn-primary">Save Organization Details</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3">User Details</h2>

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
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="old_password" class="form-label">Old Password</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5">
            <a href="{{ route('profile.organizer') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var defaultProfilePictureUrl = "{{ asset('storage/images/organizer_profiles/default.png') }}";
</script>
@endsection

