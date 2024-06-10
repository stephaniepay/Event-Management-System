
@extends('layout')

@section('title', 'Teamplayer Statistics')

@section('content')

<div class="container mt-3">
    <h1>Team Players Statistics</h1>


    <div class="stats-section mt-3 p-3 rounded">
        <div class="row my-3 text-center">

            {{-- Total Players  --}}
            <div class="col-md-3">
                <div class="stats-card total-players-card p-3 border rounded">
                    <h5>Total Players</h5>
                    <p class="stats-number stats-int">{{ count($teamPlayers) }}</p>
                </div>
            </div>

            {{-- Unique Sessions with Team Players  --}}
            <div class="col-md-3">
                <div class="stats-card active-sessions-card p-3 border rounded">
                    <h5>Active Sessions</h5>
                    <p class="stats-number stats-int">{{ $uniqueSessionsCount }}</p>
                </div>
            </div>

            {{-- Most Popular Player --}}
            <div class="col-md-3">
                <div class="stats-card most-popular-player-card p-3 border rounded">
                    <h5>Most Popular Player</h5>
                    <p class="stats-number stats-name">{{ $mostPopularPlayer->name ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Most Participated Player --}}
            <div class="col-md-3">
                <div class="stats-card most-active-player-card p-3 border rounded">
                    <h5>Most Active Player</h5>
                    <p class="stats-number stats-name">{{ $mostParticipatedPlayer['name'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>



    <div class="table-responsive">
        <div class="teamplayers-table-section mt-1 p-3 border rounded">
            <div class="input-group teamplayer-search-group mt-3">
                <input type="text" class="form-control" placeholder="Search for team players..." onkeyup="filterPlayers()">
                <div class="input-group-append">
                    <span class="input-group-text teamplayer-search-icon"><i class="fas fa-search"></i></span>
                </div>
            </div>

            <div class="mt-4 text-end">
                Sort by:
                <select id="sort-order" onchange="applySorting()">
                    <option value="default">Default</option>
                    <option value="name-asc">Name Ascending</option>
                    <option value="name-desc">Name Descending</option>
                    <option value="votes-asc">Votes Ascending</option>
                    <option value="votes-desc">Votes Descending</option>
                </select>
            </div>

            <div class="legend mb-3">
                <span class="legend-item"><i class="fas fa-trophy" style="color: #ffbf00;"></i> Most Wins</span>
                <span class="legend-item"><i class="fas fa-star" style="color: #00b9da;"></i> Most Popular</span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped team-players-table mt-4" id="team-players-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Wikipedia Link</th>
                            <th>Events Participated</th>
                            <th>Sessions Participated</th>
                            <th>Won Times</th>
                            <th>Votes Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamPlayers as $teamPlayer)
                            <tr>
                                <td data-label="Name">
                                    {{ $teamPlayer['name'] }}
                                    @if($teamPlayer['name'] == $mostWinsPlayer->name && $teamPlayer['wikipedia_link'] == $mostWinsPlayer->wikipedia_link)
                                        <i class="fas fa-trophy" style="color: #ffbf00;"></i>
                                    @endif
                                    @if($teamPlayer['name'] == $mostPopularPlayer->name && $teamPlayer['wikipedia_link'] == $mostPopularPlayer->wikipedia_link)
                                        <i class="fas fa-star" style="color: #00b9da;"></i>
                                    @endif
                                </td>
                                <td data-label="Wikipedia Link">
                                    @if($teamPlayer['wikipedia_link'])
                                        <a href="{{ $teamPlayer['wikipedia_link'] }}" target="_blank">{{ basename($teamPlayer['wikipedia_link']) }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td data-label="Events Participated">{{ $teamPlayer['eventsParticipated'] }}</td>
                                <td data-label="Sessions Participated">{{ $teamPlayer['sessionsParticipated'] }}</td>
                                <td data-label="Won Times">{{ $teamPlayer['wonTimes'] }}</td>
                                <td data-label="Votes Count" data-votes="{{ $teamPlayer['votesCount'] }}">{{ $teamPlayer['votesCount'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(request()->routeIs('profile.team-players.list'))
        <div class="mt-5">
            <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    @endif
</div>
@endsection
