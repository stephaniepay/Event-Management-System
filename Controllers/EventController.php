<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventSession;
use App\Models\Organizer;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Log;


class EventController extends Controller
{

    public function index(Request $request)
    {
        $query = Event::query();
        $noEvents = false;

        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('starting_time') && $request->starting_time != '') {
            $days = (int) $request->starting_time;
            $query->whereHas('sessions', function ($q) use ($days) {
                $q->where('start_time', '>=', Carbon::now())
                ->where('start_time', '<=', Carbon::now()->addDays($days));
            });
        }

        if ($request->has('organizer_id')) {
            $query->where('organizer_id', $request->organizer_id);
            $organizerExists = User::where('id', $request->organizer_id)->exists();

            if ($organizerExists && $query->doesntExist()) {
                $noEvents = true;
            } else {
                $noEvents = false;
            }
        }

        if ($request->has('state') && $request->state != 'all') {
            $query->where('state', $request->state);
        }

        $events = $query->get();

        $states = Event::distinct()->pluck('state');

        $categories = Event::distinct()->pluck('category');

        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        if (auth()->user()->isAdmin() || auth()->user()->is_organizer) {
            return view('Admin.events_list', compact('events', 'states', 'noEvents', 'categories', 'categoryLabels'));
        } else {
            return view('User.events_list', compact('events', 'states', 'noEvents', 'categories', 'categoryLabels'));
        }

    }

    public function showWelcomePage()
    {
        $categories = Event::distinct()->pluck('category');

        $categoryImageUrls = [
            'teamSports' => asset('storage/images/home_categories/team_sports.jpeg'),
            'individualSports' => asset('storage/images/home_categories/individual_sports.jpg'),
            'racquetSports' => asset('storage/images/home_categories/racquet_sports.jpg'),
            'waterSports' => asset('storage/images/home_categories/water_sports.jpeg'),
            'outdoorAdventure' => asset('storage/images/home_categories/outdoor_adventure.jpeg'),
            'fitnessHealth' => asset('storage/images/home_categories/fitness_health.jpg'),
        ];

        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        $categoryDescriptions = [
            'teamSports' => 'Engage in exciting team-based sports events.',
            'individualSports' => 'Explore sports focusing on individual performance.',
            'racquetSports' => 'Experience the thrill of racquet sports.',
            'waterSports' => 'Dive into adventurous water sports activities.',
            'outdoorAdventure' => 'Join outdoor and adventure sports events.',
            'fitnessHealth' => 'Participate in fitness and health-related activities.'
        ];

        $events = Event::all();

        return view('welcome', compact('categories', 'events', 'categoryImageUrls', 'categoryLabels', 'categoryDescriptions'));
    }

    public function create()
    {
        return view('Admin.add_event');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address_line' => 'required|string|max:255',
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|numeric',
            'event_images' => 'required|array|min:1',
            'event_images.*' => 'required|file|image|max:2048',
            'sessions' => 'required|array|min:1',
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i|after:sessions.*.start_time',
            'max_capacity_per_session' => 'required|integer|min:1|max:1000',
            'price_per_seat' => 'required|numeric|min:10|max:500',
            'category' => 'required|string|in:teamSports,individualSports,racquetSports,waterSports,outdoorAdventure,fitnessHealth',
        ]);

        if (auth()->user()->isAdmin()) {
            $validatedData['organizer_id'] = null;
        } elseif (auth()->user()->is_organizer) {
            $validatedData['organizer_id'] = auth()->id();
        } else {
            return back()->withErrors('Unauthorized: Only administrators and organizers can create events.');
        }

        $event = Event::create($validatedData);

        // --------- HANDLE EVENT SESSIONS ---------

        if ($request->has('sessions')) {
            foreach ($request->sessions as $session) {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $session['date'] . ' ' . $session['start_time']);
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $session['date'] . ' ' . $session['end_time']);
                $event->sessions()->create([
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'max_capacity_per_session' => $validatedData['max_capacity_per_session'],
                ]);
            }
        }

        // --------- HANDLE EVENT IMAGES ---------

        if ($request->hasFile('event_images')) {
            foreach ($request->file('event_images') as $file) {
                $path = $file->store('public/images/event');
                $event->images()->create(['filename' => basename($path)]);
            }
        }


        return redirect()->route('events.edit', ['event' => $event->id])
                        ->with('success', 'Event created successfully.');
    }


    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('Admin.edit_event', compact('event'));
    }


    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address_line' => 'required|string|max:255',
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|numeric',
            'max_capacity_per_session' => 'required|integer|min:1|max:1000',
            'price_per_seat' => 'required|numeric|min:10|max:500',
            'category' => 'required|string|in:teamSports,individualSports,racquetSports,waterSports,outdoorAdventure,fitnessHealth',
        ]);

        $event->update($validatedData);

        // --------- HANDLE EVENT SESSIONS ---------

        if ($request->has('deleted_sessions')) {
            EventSession::destroy($request->deleted_sessions);
        }

        if ($request->has('sessions')) {
            foreach ($request->sessions as $type => $sessions) {
                foreach ($sessions as $session) {
                    if ($type === 'existing') {
                        if (!isset($session['id']) || !isset($session['date'])) continue;
                        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $session['date'] . ' ' . $session['start_time']);
                        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $session['date'] . ' ' . $session['end_time']);
                        EventSession::find($session['id'])->update([
                            'start_time' => $startDateTime,
                            'end_time' => $endDateTime,
                            'max_capacity_per_session' => $validatedData['max_capacity_per_session'],
                        ]);
                    } elseif ($type === 'new') {
                        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $session['date'] . ' ' . $session['start_time']);
                        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $session['date'] . ' ' . $session['end_time']);
                        $event->sessions()->create([
                            'start_time' => $startDateTime,
                            'end_time' => $endDateTime,
                            'max_capacity_per_session' => $validatedData['max_capacity_per_session'],
                        ]);
                    }
                }
            }

        }

        // --------- HANDLE EVENT IMAGES ---------

        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $delete_id) {
                $image = EventImage::findOrFail($delete_id);
                Storage::delete('public/images/event/' . $image->filename);
                $image->delete();
            }
        }

        if ($request->hasFile('event_images')) {
            $request->validate([
                'event_images' => 'required|array|min:1',
                'event_images.*' => 'required|file|image|max:2048',
            ]);

            foreach ($request->file('event_images') as $file) {
                $path = $file->store('public/images/event');
                $event->images()->create(['filename' => basename($path)]);
            }
        }


        return redirect()->route('events.edit', ['event' => $event->id])
                        ->with('success', 'Event updated successfully.');
    }



    public function destroy($id)
    {
        Event::findOrFail($id)->delete();
        return redirect()->route('events.index');
    }

    public function show($id)
    {
        $event = Event::with('reviews.user')->findOrFail($id);

        $publishedByAdmin = $event->isPublishedByAdmin();
        $organizerName = $publishedByAdmin ? 'Admin' : $event->organizer->name;

        $categories = Event::distinct()->pluck('category');
        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        if (auth()->user()->isAdmin() || auth()->user()->is_organizer) {
            return view('Admin.event_details', compact('event','publishedByAdmin','categoryLabels','organizerName'));
        } else {
            return view('User.event_details', compact('event', 'publishedByAdmin', 'categoryLabels', 'organizerName'));
        }
    }


    public function favorite(Event $event)
    {
        auth()->user()->favoriteEvents()->attach($event->id);

        return back()->with('success', 'Event added to your favorites.');
    }

    public function unfavorite(Event $event)
    {
        auth()->user()->favoriteEvents()->detach($event->id);
        return back()->with('success', 'Event removed from your favorites.');
    }

    public function eventsByOrganizer($organizerId)
    {
        return redirect()->route('events.index', ['organizer_id' => $organizerId]);
    }




}

