@extends('layout')

@section('title', 'Edit Team Player')

@section('content')
<div class="container mt-3">
    <h1>Edit Team Player for Session ID: {{ $session->id }}</h1>
    <h4 class="my-4">Event ID: {{ $session->event->id }} - {{ $session->event->name }}</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h4 id="form-title">Edit Team Player #{{ $player->id }}: {{ $player->name }} </h4>
            <form action="{{ route('team-players.update', $player->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $player->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="wikipedia_link" class="form-label">Wikipedia Link</label>
                    <input type="url" class="form-control" id="wikipedia_link" name="wikipedia_link" value="{{ $player->wikipedia_link }}">
                </div>

                <div class="mb-3">
                    <div class="teamplayer-img-container">
                        <label for="picture" class="form-label d-block">Picture</label>
                        @if($player->picture_filename)
                            <img src="{{ asset('storage/images/team_players/' . $player->picture_filename) }}" alt="Player Image" class="img-thumbnail teamplayer-img">
                        @endif
                    </div>
                    <input type="file" class="form-control mt-2" id="picture" name="picture">
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" class="btn btn-primary">Update Player</button>
                    <button type="button" class="btn btn-danger" data-player-id="{{ $player->id }}" onclick="deletePlayer()">Delete</button>
                </div>
            </form>

            <form id="delete-form" action="{{ route('team-players.destroy', $player->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <div class="mt-5">
        <button type="button" class="btn btn-secondary mb-4" onclick="location.href='{{ route('team-players.create', $session->id) }}'">
            <i class="fas fa-arrow-left"></i> Back to Add New Player
        </button>
    </div>
</div>
@endsection



