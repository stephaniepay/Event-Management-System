@extends('layout')

@section('title', 'Select Winner')

@section('content')
<div class="container mt-3">
    <h1>Set Winner for Session ID: {{ $session->id }}</h1>
    <h4 class="my-4">Event ID: {{ $session->event->id }} - {{ $session->event->name }}</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('sessions.updateWinner', $session->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="mb-3">
                    <label for="winner_player_id" class="form-label">Select Winner</label>
                    <select class="form-control" id="winner_player_id" name="winner_player_id">
                        @foreach($session->teamPlayers as $teamPlayer)
                            <option value="{{ $teamPlayer->id }}" {{ $session->winner_player_id == $teamPlayer->id ? 'selected' : '' }}>
                                {{ $teamPlayer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary w-100">Update Winner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
