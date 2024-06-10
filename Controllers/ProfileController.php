<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Seat;
use App\Models\Cart;
use App\Models\Review;
use App\Models\User;
use App\Models\Organizer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Event;
use App\Models\TeamPlayer;
use App\Models\EventSession;
use App\Models\OrganizerRequest;
use App\Notifications\OrganizerUpdated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;



class ProfileController extends Controller
{

    public function getMostPopularTeamPlayer()
    {
        return TeamPlayer::with('session.event')
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->first();
    }


    private function getTopPurchasedUsers()
    {
        $topPurchasedUsers = User::with(['orders'])
            ->whereHas('orders', function ($query) {
                $query->where('total_amount', '>', 0);
            })
            ->get()
            ->map(function ($user) {
                $totalAmount = $user->orders->sum('total_amount');
                return [
                    'name' => $user->name,
                    'totalAmount' => $totalAmount,
                ];
            })
            ->sortByDesc('totalAmount')
            ->take(1)
            ->first();

        return $topPurchasedUsers;
    }

    private function getMostPopularEventCategory()
    {
        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        $eventsGroupedByCategory = Event::with(['sessions.orders.carts'])
            ->get()
            ->groupBy('category')
            ->map(function ($events) {
                $totalAttendees = $events->sum(function ($event) {
                    return $event->sessions->sum(function ($session) {
                        return $session->orders->sum(function ($order) {
                            return count(json_decode($order->seat_ids, true));
                        });
                    });
                });
                return ['totalAttendees' => $totalAttendees];
            });

        $mostPopularCategory = $eventsGroupedByCategory->sortDesc()->keys()->first();

        return $categoryLabels[$mostPopularCategory] ?? 'N/A';
    }


    public function getMostPopularEvent()
    {
        $events = Event::with(['sessions.orders.carts'])->get();

        $eventAttendees = $events->map(function ($event) {
            $totalAttendees = $event->sessions->sum(function ($session) {
                return $session->orders->sum(function ($order) {
                    return $order->carts->where('is_purchased', true)->sum(function ($cart) {
                        return count($cart->seat_ids);
                    });
                });
            });

            return (object)['event' => $event, 'attendees' => $totalAttendees];
        });

        $mostPopularEvent = $eventAttendees->sortByDesc('attendees')->first();

        return $mostPopularEvent ? $mostPopularEvent->event->name : 'N/A';
    }


    private function calculateEventsAttended($userId)
    {
        $orders = Order::where('user_id', $userId)->get();

        $sessionIds = $orders->flatMap(function($order) {
            return collect(json_decode($order->seat_ids))->map(function($seatId) {
                return explode('_', $seatId)[0];
            });
        })->unique()->all();

        $eventIds = EventSession::whereIn('id', $sessionIds)->get()->pluck('event_id')->unique();

        return $eventIds->count();
    }

    private function calculateAverageRatingForOrganizerEvents($organizerId)
    {

        $events = Event::where('organizer_id', $organizerId)->get();
        $eventIds = $events->pluck('id');
        $reviews = Review::whereIn('event_id', $eventIds)->get();
        if ($reviews->isNotEmpty()) {
            $averageRating = $reviews->avg('rating');
        } else {
            $averageRating = null;
        }
        return $averageRating;
    }


    private function getTotalBookedSeats($organizerId)
    {
        $totalBookedSeats = Cart::whereHas('eventSession.event', function ($query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        })->where('is_purchased', 1)
          ->get()
          ->sum(function($cart) {
              return count($cart->seat_ids);
          });

        return $totalBookedSeats;
    }


    private function calculateTotalRevenuesForOrganizer($organizerId)
    {
        $totalRevenues = Payment::whereHas('order.carts.eventSession.event', function ($query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        })->sum('amount');

        return $totalRevenues;
    }

    private function getSessionsManagedByOrganizer($organizerId)
    {
        $sessionsManaged = EventSession::whereHas('event', function ($query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        })->count();

        return $sessionsManaged;
    }


    private function getMostPopularEventForOrganizer($organizerId)
    {
        $mostPopularEvent = Event::where('organizer_id', $organizerId)
            ->withCount(['sessions' => function ($query) {
                $query->whereHas('orders', function ($q) {
                    $q->whereHas('carts', function($subQuery) {
                        $subQuery->where('is_purchased', 1);
                    });
                });
            }])
            ->orderByDesc('sessions_count')
            ->first();

        return $mostPopularEvent ? $mostPopularEvent->name : 'N/A';
    }

    public function index()
    {
        $user = auth()->user();
        $organizer = $user->is_organizer ? $user->organizer : null;

        if ($user->isAdmin()) {

            $eventsManagedByAdmin = Event::whereNull('organizer_id')->count();
            $totalTransactions = Payment::sum('amount');
            $newUserSignUps = User::whereDate('created_at', '>=', now()->subMonth())->count();
            $organizerRequests = OrganizerRequest::where('status', 'pending')->count();
            $sessionsManaged = EventSession::whereHas('event', function ($q) {
                                    $q->whereNull('organizer_id');
                                })->count();
            $teamPlayerEngagement = Vote::count();

            $mostPopularEventCategoryName = $this->getMostPopularEventCategory();
            $mostPopularEventName = $this->getMostPopularEvent();
            $mostPopularTeamPlayer = $this->getMostPopularTeamPlayer();
            $topPurchasedUsers = $this->getTopPurchasedUsers();

            return view('Admin.profile', compact(
                'user', 'eventsManagedByAdmin',
                'totalTransactions', 'newUserSignUps', 'organizerRequests',
                'sessionsManaged', 'mostPopularTeamPlayer', 'mostPopularEventCategoryName',
                'mostPopularEventName', 'topPurchasedUsers',
            ));

        }
        elseif ($user->is_organizer) {

            $organizerId = $user->id;

            $events = Event::where('organizer_id', $organizerId)->get();
            $sessionsManaged = $this->getSessionsManagedByOrganizer($organizerId);
            $totalRegistrations = $this->getTotalBookedSeats($organizerId);
            $averageRating = $this->calculateAverageRatingForOrganizerEvents($organizerId);
            $mostPopularEvent = $this->getMostPopularEventForOrganizer($organizerId);
            $totalRevenues = $this->calculateTotalRevenuesForOrganizer($organizerId);

            return view('Organizer.profile', compact(
                'user', 'organizer', 'events', 'sessionsManaged',
                'totalRegistrations', 'averageRating', 'mostPopularEvent', 'totalRevenues'
            ));

        }
        else {

            $user->load(['favoriteEvents', 'orders.carts.eventSession', 'reviews', 'votes']);
            $votingHistory = Vote::where('user_id', $user->id)->get();
            $eventsAttended = $this->calculateEventsAttended($user->id);
            $totalVotesMade = $votingHistory->count();

            $upcomingSessionsCount = $upcomingSessionsCount = Cart::whereHas('eventSession', function($query) {
                $query->where('start_time', '>', now());
            })
            ->where('user_id', $user->id)
            ->where('is_purchased', true)
            ->get()
            ->pluck('eventSession.id')
            ->unique()
            ->count();

            $favoritedEventsCount = $user->favoriteEvents->count();
            $orderHistoryCount = $user->orders->count();
            $reviewHistoryCount = $user->reviews->count();

            return view('User.profile', compact(
                'user', 'votingHistory', 'eventsAttended', 'totalVotesMade',
                'upcomingSessionsCount', 'favoritedEventsCount', 'orderHistoryCount',
                'reviewHistoryCount'
            ));

        }
    }

    public function edit()
    {
        if (auth()->user()->isAdmin()) {
            return view('Admin.profile_edit');
        } elseif (auth()->user()->is_organizer) {
            return view('Organizer.profile_edit');
        }
        else {
            return view('User.profile_edit');
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();


        $validatedData = $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'old_password' => 'required',
        ]);

        if (!Hash::check($validatedData['old_password'], $user->password)) {
            return back()->withErrors(['old_password' => 'The provided password does not match your current password.']);
        }

        $updatedata['name'] = $validatedData['name'];
        $updatedata['email'] = $validatedData['email'];
        $updatedata['password'] = Hash::make($validatedData['password']);

        $user->update($updatedata);

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');

    }

    public function showFavorites()
    {
        $user = auth()->user();
        $user->load('favoriteEvents');

        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        return view('User.favorites', compact('user', 'categoryLabels'));
    }

    public function showVotingDetails()
    {
        $user = auth()->user();
        $votingHistory = Vote::where('user_id', $user->id)->get();

        foreach ($votingHistory as $vote) {
            $session = $vote->eventSession;
            $event = $session->event->name;
            $player = $vote->teamPlayer;

            $vote->event = $event;
            $vote->player = $player;
        }

        return view('User.votes_details', compact('votingHistory'));
    }

    public function storeOrganizerRequest(Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required|string|max:1000',
            'organization_name' => 'required|string|max:255'
        ]);

        $existingRequest = OrganizerRequest::where('user_id', auth()->id())
                                           ->where('status', 'pending')
                                           ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending request.');
        }

        OrganizerRequest::create([
            'user_id' => auth()->id(),
            'message' => $validatedData['message'],
            'status' => 'pending',
            'organization_name' => $validatedData['organization_name'],
        ]);

        $admins = User::all()->filter(function ($user) {
            return $user->isAdmin();
        });

        foreach ($admins as $admin) {
            $admin->notify(new OrganizerUpdated("New organizer request from user: " . auth()->user()->name));
        }


        return redirect()->back()->with('success', 'Request sent successfully!');
    }

    public function showOrganizerRequests()
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $requests = OrganizerRequest::where('status', 'pending')->with('user')->get();

        return view('admin.manage_organizer_requests', ['requests' => $requests]);
    }

    public function approveOrganizerRequest($userId)
    {
        $latestOrganizerRequest = OrganizerRequest::where('user_id', $userId)
                                              ->where('status', 'pending')
                                              ->latest('created_at')
                                              ->firstOrFail();

        User::find($userId)->update(['is_organizer' => true]);

        Organizer::firstOrCreate(
            ['user_id' => $userId],
            ['name' => $latestOrganizerRequest->organization_name]
        );

        $latestOrganizerRequest->update(['status' => 'approved']);

        $user = User::find($userId);
        $user->notify(new OrganizerUpdated("Your request to become an organizer has been approved."));

        return redirect()->back()->with('success', 'Organizer request approved successfully.');
    }

    public function denyOrganizerRequest($userId)
    {
        OrganizerRequest::where('user_id', $userId)->update(['status' => 'denied']);

        $user = User::find($userId);
        $user->notify(new OrganizerUpdated("Your request to become an organizer has been denied."));

        return redirect()->back()->with('success', 'Organizer request denied.');
    }

    public function requestOrganizerForm()
    {
        return view('user.request_organizer');
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return back()->with('success', 'Notification marked as read.');
    }

    public function showOrganizers()
    {
        $organizers = User::with('organizer')->where('is_organizer', true)->get();

        if (auth()->user()->isAdmin()) {
            return view('Admin.organizers_list', compact('organizers'));
        } else {
            return view('User.organizers_list', compact('organizers'));
        }
    }

    public function deleteOrganizer($userId)
    {
        $user = User::findOrFail($userId);

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->is_organizer) {
            $user->update(['is_organizer' => false]);
            Organizer::where('user_id', $userId)->delete();
            $user->notify(new OrganizerUpdated('Your organizer role has been removed.'));
        }

        return back()->with('success', 'Organizer deleted successfully.');
    }


    public function updateOrganizerDetails(Request $request)
    {
        $validatedData = $request->validate([
            'organization_name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_profile_picture' => 'nullable|boolean',
        ]);

        $organizer = Organizer::where('user_id', auth()->id())->firstOrCreate(
            ['user_id' => auth()->id()],
            ['name' => $validatedData['organization_name']]
        );

        if ($request->hasFile('profile_picture')) {

            if ($organizer->picture_filename && $organizer->picture_filename != 'default.png') {
                Storage::delete('public/images/organizer_profiles/' . $organizer->picture_filename);
            }

            $path = $request->file('profile_picture')->store('public/images/organizer_profiles');
            $organizer->picture_filename = basename($path);
        } elseif ($request->input('remove_profile_picture') == '1') {

            if ($organizer->picture_filename != 'default.png') {
                Storage::delete('public/images/organizer_profiles/' . $organizer->picture_filename);
            }
            $organizer->picture_filename = 'default.png';

        }

        $organizer->name = $validatedData['organization_name'];
        $organizer->save();

        return back()->with('success', 'Organization details updated successfully.');
    }


    public function recentOrders()
    {
        $orders = Order::with(['user', 'payment'])
                    ->latest()
                    ->get();

        $groupedOrders = $orders->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('F Y');
        });

        return view('Admin.order_list', compact('groupedOrders'));
    }

    public function showEventStatistics()
    {
        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        $events = Event::all()->map(function ($event) use ($categoryLabels) {
            $totalRegistrations = 0;
            $totalRevenue = 0;

            $orders = Order::whereHas('carts.eventSession', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->with(['carts'])->get();

            foreach ($orders as $order) {
                foreach ($order->carts as $cart) {
                    if ($cart->is_purchased) {
                        $seatIds = $cart->seat_ids;
                        $seatsCount = count($seatIds);
                        $totalRegistrations += $seatsCount;
                        $totalRevenue += $cart->total_amount;
                    }
                }
            }

            $categoryName = $categoryLabels[$event->category] ?? 'Other';

            return [
                'name' => $event->name,
                'category' => $categoryName,
                'attendeesCount' => $totalRegistrations,
                'sessionsCount' => $event->sessions->count(),
                'totalRevenue' => $totalRevenue,
                'pricePerSeat' => $event->price_per_seat,
            ];
        });

        return view('Admin.event_statistics', compact('events'));
    }

    public function showEventCategoryStatistics()
    {
        $categoryLabels = [
            'teamSports' => 'Team Sports',
            'individualSports' => 'Individual Sports',
            'racquetSports' => 'Racquet Sports',
            'waterSports' => 'Water Sports',
            'outdoorAdventure' => 'Outdoor & Adventure',
            'fitnessHealth' => 'Fitness & Health',
        ];

        $categories = [];

        $events = Event::all();
        foreach ($events as $event) {
            $orders = Order::whereHas('carts.eventSession', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->with(['carts'])->get();

            $totalRegistrations = 0;
            $totalRevenue = 0;
            foreach ($orders as $order) {
                foreach ($order->carts as $cart) {
                    if ($cart->is_purchased) {
                        $seatsCount = count($cart->seat_ids);
                        $totalRegistrations += $seatsCount;
                        $totalRevenue += $cart->total_amount;
                    }
                }
            }

            $categoryName = $categoryLabels[$event->category] ?? 'Other';
            if (!isset($categories[$categoryName])) {
                $categories[$categoryName] = [
                    'categoryName' => $categoryName,
                    'totalAttendees' => 0,
                    'totalRevenue' => 0,
                    'eventCount' => 0,
                    'sessionsCount' => 0,
                ];
            }

            $categories[$categoryName]['totalAttendees'] += $totalRegistrations;
            $categories[$categoryName]['totalRevenue'] += $totalRevenue;
            $categories[$categoryName]['eventCount'] += 1;
            $categories[$categoryName]['sessionsCount'] += $event->sessions->count();
        }

        return view('Admin.event_category_statistics', compact('categories'));
    }

    public function showUpcomingEvents()
    {
        $user = auth()->user();

        $upcomingEvents = EventSession::whereHas('orders', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('start_time', '>', now())->get()
        ->map(function ($session) {
            $startTime = $session->start_time;
            $endTime = $session->end_time;
            $durationInHours = $endTime->diffInHours($startTime);
            $session->duration = $durationInHours >= 24 ? ($durationInHours / 24) . ' days' : $durationInHours . ' hours';
            return $session;
        });
        $upcomingEvents = $upcomingEvents->sortBy('start_time');

        return view('user.upcoming_sessions', compact('upcomingEvents'));
    }


    public function showRegisteredEvents()
    {
        $user = auth()->user();

        $eventData = Order::where('user_id', $user->id)
                        ->with('carts.eventSession.event')
                        ->get()
                        ->pluck('carts')
                        ->flatten()
                        ->mapToGroups(function ($cart) {
                            if ($cart->eventSession && $cart->eventSession->event) {
                                return [$cart->eventSession->event->id => $cart->created_at];
                            }
                            return [];
                        })
                        ->map(function ($dates) {
                            return $dates->sort()->first();
                        });

        $events = Event::whereIn('id', $eventData->keys())
                    ->get()
                    ->map(function ($event) use ($eventData) {
                        $event->joined_date = $eventData[$event->id];
                        $event->status = $event->sessions->where('start_time', '>', now())->isNotEmpty() ? 'Upcoming' : 'Past';
                        return $event;
                    });

        return view('User.registered_events', compact('events'));
    }


    public function showOrganizerSessions()
    {
        $organizerId = auth()->user()->id;
        $sessions = EventSession::whereHas('event', function ($query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        })->get();

        return view('Organizer.session_list', compact('sessions'));
    }

    public function showOrganizerEvents()
    {
        $organizerId = auth()->user()->id;
        $events = Event::where('organizer_id', $organizerId)->get();
        $states = Event::distinct()->pluck('state');

        return view('Organizer.events_list', compact('events', 'states'));
    }

    public function showTotalRegistrations()
    {
        $organizerId = auth()->user()->id;

        $orders = Order::with(['user', 'carts' => function ($query) use ($organizerId) {
            $query->whereHas('eventSession.event', function ($q) use ($organizerId) {
                $q->where('organizer_id', $organizerId);
            });
        }])
        ->get()
        ->groupBy('user_id');

        $registrations = [];

        foreach ($orders as $userId => $userOrders) {
            foreach ($userOrders as $order) {
                foreach ($order->carts as $cart) {
                    $session = $cart->eventSession;
                    $event = $session->event;
                    $user = $order->user;

                    $groupKey = $userId . '_' . $session->id;

                    if (!isset($registrations[$groupKey])) {
                        $registrations[$groupKey] = [
                            'user_name' => $user->name,
                            'user_email' => $user->email,
                            'event_name' => $event->name,
                            'session_date' => $session->start_time->toDateString(),
                            'session_time' => $session->start_time->format('H:i') . ' - ' . $session->end_time->format('H:i'),
                            'seats_booked' => [],
                            'seats_booked_count' => 0,
                        ];
                    }

                    $registrations[$groupKey]['seats_booked'] = array_merge(
                        $registrations[$groupKey]['seats_booked'],
                        $cart->seat_ids
                    );

                    $registrations[$groupKey]['seats_booked_count'] += count($cart->seat_ids);
                }
            }
        }

        return view('Organizer.total_registrations', compact('registrations'));
    }

    public function showEventPopularity()
    {
        $organizerId = auth()->user()->id;

        $events = Event::with('sessions.orders')
            ->where('organizer_id', $organizerId)
            ->get()
            ->map(function ($event) {
                $seatIds = $event->sessions->flatMap(function ($session) {
                    return $session->orders->flatMap(function ($order) {
                        return json_decode($order->seat_ids, true);
                    });
                });

                $totalRegistrations = count($seatIds->unique());

                return [
                    'event_name' => $event->name,
                    'total_registrations' => $totalRegistrations,
                ];
            })
            ->sortByDesc('total_registrations');

        $mostPopularEvent = $events->first();

        return view('Organizer.event_popularity', compact('events', 'mostPopularEvent'));
    }

    public function showTotalRevenuesByEvent()
    {
        $organizerId = auth()->user()->id;

        $events = Event::where('organizer_id', $organizerId)->get();

        $revenuesByEvent = $events->map(function ($event) use ($organizerId) {
            $totalRegistrations = 0;
            $totalRevenue = 0;

            $orders = Order::whereHas('carts.eventSession', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->with(['carts'])->get();

            foreach ($orders as $order) {
                foreach ($order->carts as $cart) {
                    if ($cart->is_purchased) {
                        $seatIds = $cart->seat_ids;
                        $seatsCount = count($seatIds);
                        $totalRegistrations += $seatsCount;
                        $totalRevenue += $cart->total_amount;
                    }
                }
            }
            $sessionsCount = $event->sessions->count();

            return [
                'event_name' => $event->name,
                'sessions_count' => $sessionsCount,
                'total_registrations' => $totalRegistrations,
                'total_revenue' => $totalRevenue,
            ];
        });

        $sortedRevenuesByEvent = $revenuesByEvent->sortByDesc('total_revenue');

        return view('Organizer.total_revenues', compact('sortedRevenuesByEvent'));
    }

    public function showReviewsRatingsReceived()
    {
        $organizerId = auth()->user()->id;

        $events = Event::where('organizer_id', $organizerId)->get();
        $eventIds = $events->pluck('id');

        $reviews = Review::whereIn('event_id', $eventIds)
                        ->with('event', 'user')
                        ->get();

        return view('Organizer.reviews_ratings_received', compact('reviews'));
    }

}
