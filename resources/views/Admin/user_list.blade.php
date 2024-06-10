@extends('layout')

@section('title', 'User List')

@section('content')

<div class="container mt-3">
    <h1>User List</h1>

    <div class="table-responsive">
        <table class="table table-striped mt-4" id="user-list-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined</th>
                    <th>Is Organizer?</th>
                    <th>Total Order Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>{{ $user->is_organizer ? 'Yes' : 'No' }}</td>
                        <td>
                            @if(!$user->is_organizer)
                                RM {{ number_format($user->total_amount, 2) }}
                            @else
                                No orders
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(request()->routeIs('profile.user.list'))
        <div class="mt-5">
            <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    @endif
</div>
@endsection
