@extends('layout')

@section('title', 'Add Team Player')

@section('content')
<div class="container mt-3">
    <h1>Add Team Player for Session ID: {{ $session->id }}</h1>
    <h4 class="my-4">Event ID: {{ $session->event->id }} - {{ $session->event->name }}</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Add New Player --}}
    <div class="card mb-4">
        <div class="card-body">
            <h4 id="form-title">Add New Team Player</h4>
            <form action="{{ route('team-players.store', $session->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="wikipedia_link" class="form-label">Wikipedia Link</label>
                    <input type="url" class="form-control" id="wikipedia_link" name="wikipedia_link">
                </div>

                <div class="mb-3">
                    <label for="picture" class="form-label">Picture</label>
                    <input type="file" class="form-control" id="picture" name="picture">
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" class="btn btn-primary">Add Player</button>
                </div>
            </form>
        </div>
    </div>


    {{-- Current Team Players List --}}
    <div class="card mb-4">

        <div class="card-header d-flex justify-content-between">
            <h4>Current Team Players</h4>
            <div>

                <select id="sort-order" onchange="applyAddPlayerSorting()">
                    <option value="asc">Sort Ascending</option>
                    <option value="desc">Sort Descending</option>
                </select>

            </div>
        </div>

        <div class="card-body">
            <h4 id="team-players-title">
                Session ID: {{ $session->id }} -
                @if($session->start_time->format('Y-m-d') === $session->end_time->format('Y-m-d'))
                    {{ $session->start_time->format('j F Y, g:ia') }} to {{ $session->end_time->format('g:ia') }}
                @else
                    {{ $session->start_time->format('j F Y, g:ia') }} to {{ $session->end_time->format('j F Y, g:ia') }}
                @endif
            </h4>
            <ul class="list-group" id="team-players-list">
                @forelse($teamPlayers as $player)
                    <li class="list-group-item" data-session-id="{{ $player->session->id }}">
                        <span>{{ $player->name }}</span>
                        <div>
                            <a href="{{ route('team-players.edit', ['sessionId' => $session->id, 'playerId' => $player->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('team-players.destroy', $player->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove the player?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-teamplayer-btn">Delete</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No players added yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

