@extends('layout')

@section('title', 'Organizer Requests')

@section('content')
<div class="container mt-3 request-organizer-access-container">
    <h1 class="mb-4">Organizer Requests</h1>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Organization Name</th>
                    <th class="message">Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td>{{ $request->user->id }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->user->email }}</td>
                        <td>{{ $request->organization_name }}</td>
                        <td class="message">{{ $request->message }}</td>
                        <td>
                            <div style="display: inline-block; margin-right: 10px;">
                                <form action="{{ route('organizer.approve', $request->user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success approve-organizer-btn">Approve</button>
                                </form>
                            </div>
                            <div style="display: inline-block;">
                                <form action="{{ route('organizer.deny', $request->user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger deny-organizer-btn">Deny</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">There are no organizer requests at the moment</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(request()->routeIs('profile.organizer.manage'))
        <div class="mt-5">
            <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    @endif
</div>
@endsection
