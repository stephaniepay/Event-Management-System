<?php

namespace App\Http\Controllers;

use App\Models\TeamPlayer;
use App\Models\EventSession;
use App\Models\Event;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;


class TeamPlayerController extends Controller
{
    public function list()
    {
        $profileController = new ProfileController();
        $teamPlayers = TeamPlayer::with(['session.event', 'votes'])->get();

        //Players who have same name and same wikipedialink consider as ONE player
        $uniqueTeamPlayers = $teamPlayers->unique(function ($item) {
            return $item['name'].$item['wikipedia_link'];
        });

        $teamPlayers->loadCount('votes');

        //Aggregate unique team players
        $aggregatedData = $uniqueTeamPlayers->map(function ($teamPlayer) use ($teamPlayers) {
            $filteredPlayers = $teamPlayers->filter(function ($item) use ($teamPlayer) {
                return $item->name == $teamPlayer->name && $item->wikipedia_link == $teamPlayer->wikipedia_link;
            });

            $wonTimes = $filteredPlayers->where('is_winner', true)->count();
            $sessionsParticipated = $filteredPlayers->pluck('event_session_id')->unique()->count();
            $eventsParticipated = $filteredPlayers->pluck('session.event.id')->unique()->count();
            $votesCount = $teamPlayer->votes_count;

            return [
                'name' => $teamPlayer->name,
                'wikipedia_link' => $teamPlayer->wikipedia_link,
                'wonTimes' => $wonTimes,
                'sessionsParticipated' => $sessionsParticipated,
                'eventsParticipated' => $eventsParticipated,
                'votesCount' => $votesCount,
            ];
        });

        $uniqueSessionsCount = $teamPlayers->pluck('event_session_id')->unique()->count();
        $mostPopularPlayer = $profileController->getMostPopularTeamPlayer();
        $mostWinsPlayer = $this->getMostWinsTeamPlayer();
        $mostParticipatedPlayer = $this->getMostParticipatedPlayer();

        return view('Admin.teamplayer_list', ['teamPlayers' => $aggregatedData,
            'mostPopularPlayer' => $mostPopularPlayer,
            'mostWinsPlayer' => $mostWinsPlayer,
            'mostParticipatedPlayer' => $mostParticipatedPlayer,
            'uniqueSessionsCount' => $uniqueSessionsCount,
        ]);
    }

    private function getMostWinsTeamPlayer()
    {
        $players = TeamPlayer::with('session.event')
                    ->get()
                    ->groupBy(function ($item) {
                        return $item['name'].$item['wikipedia_link'];
                    });

        $maxWins = 0;
        $mostWinsPlayer = null;
        foreach ($players as $playerGroup) {
            $wins = $playerGroup->sum(function ($player) {
                return $player->is_winner ? 1 : 0;
            });

            if ($wins > $maxWins) {
                $maxWins = $wins;
                $mostWinsPlayer = $playerGroup->first();
            }
        }

        return $mostWinsPlayer;
    }

    private function getMostParticipatedPlayer()
    {
        $players = TeamPlayer::with('session')->get();

        $groupedPlayers = $players->groupBy(function ($item) {
            return $item['name'].$item['wikipedia_link'];
        });

        $mostParticipatedPlayer = null;
        $maxParticipations = 0;

        foreach ($groupedPlayers as $group) {
            $participations = $group->pluck('session.id')->unique()->count();

            if ($participations > $maxParticipations) {
                $maxParticipations = $participations;
                $mostParticipatedPlayer = $group->first();
            }
        }

        return $mostParticipatedPlayer;
    }





    private function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'wikipedia_link' => 'nullable|url',
            'picture' => 'nullable|image|max:2048',
        ]);
    }

    public function store(Request $request, $sessionId)
    {
        $validatedData = $this->validateRequest($request);

        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('public/images/team_players');
            $validatedData['picture_filename'] = basename($path);
        }

        TeamPlayer::create(array_merge($validatedData, ['event_session_id' => $sessionId]));

        return back()->with('success', 'Team player added successfully.');
    }

    public function destroy($id)
    {
        $teamPlayer = TeamPlayer::findOrFail($id);
        $sessionId = $teamPlayer->event_session_id;

        if ($teamPlayer->picture_filename) {
            Storage::delete('public/images/team_players/' . $teamPlayer->picture_filename);
        }
        $teamPlayer->delete();

        return redirect()->route('team-players.create', ['sessionId' => $sessionId])
                        ->with('success', 'Team player deleted successfully.');
    }

    public function update(Request $request, $id)
    {
        $teamPlayer = TeamPlayer::findOrFail($id);

        $validatedData = $this->validateRequest($request);

        if ($request->hasFile('picture')) {
            if ($teamPlayer->picture_filename) {
                Storage::delete('public/images/team_players/' . $teamPlayer->picture_filename);
            }

            $path = $request->file('picture')->store('public/images/team_players');
            $validatedData['picture_filename'] = basename($path);
        }

        $teamPlayer->update($validatedData);

        return redirect()->route('team-players.edit', ['sessionId' => $teamPlayer->event_session_id, 'playerId' => $teamPlayer->id])
                         ->with('success', 'Team player updated successfully.');
    }


    public function create($sessionId)
    {
        $session = EventSession::with('event')->findOrFail($sessionId);
        $sessions = $session->event->sessions;
        $teamPlayers = $session->teamPlayers;

        return view('Admin.add_teamplayer', compact('session', 'sessions', 'teamPlayers'));
    }



    public function edit($sessionId, $playerId)
    {
        $session = EventSession::findOrFail($sessionId);
        $player = TeamPlayer::findOrFail($playerId);
        return view('Admin.edit_teamplayer', compact('session', 'player'));
    }



    public function vote($sessionId, $playerId)
    {
        $existingVote = Vote::where('user_id', auth()->user()->id)
            ->where('event_session_id', $sessionId)
            ->where('team_player_id', $playerId)
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already voted for this player.');
        }

        Vote::create([
            'user_id' => auth()->user()->id,
            'event_session_id' => $sessionId,
            'team_player_id' => $playerId,
        ]);

        TeamPlayer::where('id', $playerId)->increment('votes');

        return back()->with('success', 'You have successfully voted for this player.');
    }


    public function unvote($sessionId, $playerId)
    {
        $existingVote = Vote::where('user_id', auth()->user()->id)
            ->where('event_session_id', $sessionId)
            ->where('team_player_id', $playerId)
            ->first();

        if (!$existingVote) {
            return back()->with('error', 'You have not voted for this player.');
        }

        $existingVote->delete();

        TeamPlayer::where('id', $playerId)->decrement('votes');

        return back()->with('success', 'You have successfully unvoted for this player.');
    }


}

