<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\TeamPlayer;
use App\Models\Seat;
use App\Models\Order;
use App\Models\EventSession;
use App\Models\Payment;




class SessionController extends Controller
{

    private function getStadiumClass($category)
    {
        switch ($category) {
            case 'waterSports':
                return 'pool-container';
            case 'teamSports':
            case 'individualSports':
                return 'court-container';
            case 'racquetSports':
                return 'court-container';
            case 'outdoorAdventure':
                return 'adventure-container';
            case 'fitnessHealth':
                return 'gym-container';
            default:
                return 'default-stadium-container';
        }
    }

    private function getStadiumLabel($category)
    {
        switch ($category) {
            case 'waterSports':
                return 'Pool';
            case 'teamSports':
            case 'individualSports':
                return 'Court';
            case 'racquetSports':
                return 'Court';
            case 'outdoorAdventure':
                return 'Adventure Area';
            case 'fitnessHealth':
                return 'Gym / Studio';
            default:
                return 'Stadium';
        }
    }

    public function generateSeatMap($sessionId)
    {

        $existingSeats = Seat::where('event_session_id', $sessionId)->count();
        if ($existingSeats > 0) {
            return;
        }

        $session = EventSession::findOrFail($sessionId);
        $pricePerSeat = $session->event->price_per_seat;
        $maxCapacity = $session->max_capacity_per_session;

        $seatsInUpper = ceil($maxCapacity / 2);
        $seatsInLower = floor($maxCapacity / 2);

        $seatMap = ['upper' => [], 'lower' => []];
        $alphabet = range('A', 'Z');

        $fillSection = function ($sectionPrefix, $sectionName, $numberOfSeats) use (&$seatMap, $alphabet, $sessionId, $pricePerSeat) {
            $seatsRemaining = $numberOfSeats;
            foreach ($alphabet as $row) {
                if ($seatsRemaining <= 0) {
                    break;
                }

                $seatsInRow = min($seatsRemaining, 20);
                for ($j = 1; $j <= $seatsInRow; $j++) {
                    $seatId = "{$sessionId}_{$sectionPrefix}{$row}{$j}";

                    $seatModel = Seat::create(
                        [
                            'id' => $seatId,
                            'event_session_id' => $sessionId,
                            'row' => $row,
                            'number' => $j,
                            'section' => $sectionName,
                            'price' => $pricePerSeat,
                        ],
                        [
                            'is_booked' => false,
                        ]
                    );

                    $seatMap[$sectionName][$row][$j] = (object)[
                        'id' => $seatId,
                        'is_booked' => $seatModel->is_booked,
                        'section' => $sectionName,
                    ];
                }

                $seatsRemaining -= $seatsInRow;
            }
        };

        $fillSection('U', 'upper', $seatsInUpper);
        $fillSection('L', 'lower', $seatsInLower);

        return $seatMap;
    }

    public function getSeatMap($sessionId)
    {
        $seatsCollection = Seat::where('event_session_id', $sessionId)->get();

        $seatMap = ['upper' => [], 'lower' => []];

        foreach ($seatsCollection as $seat) {
            $sectionPrefix = strtoupper($seat->section[0]);
            $seatLabel = "{$sectionPrefix}{$seat->row}{$seat->number}";
            $seat->label = $seatLabel;

            $seatMap[$seat->section][$seat->row][$seat->number] = $seat;
        }

        foreach ($seatMap as $section => $rows) {
            ksort($seatMap[$section]);
            foreach ($rows as $row => $seats) {
                ksort($seatMap[$section][$row]);
            }
        }

        return $seatMap;
    }


    public function selectSeat($sessionId)
    {
        $session = EventSession::findOrFail($sessionId);
        $seatMap = $this->getSeatMap($sessionId);

        $category = $session->event->category;
        $stadiumClass = $this->getStadiumClass($category);
        $stadiumLabel = $this->getStadiumLabel($category);

        $seatMap = array_merge(['upper' => [], 'lower' => []], $seatMap);

        foreach ($seatMap as $section => $rows) {
            foreach ($rows as $row => $seats) {
                foreach ($seats as $number => $seat) {
                    $seatMap[$section][$row][$number]->is_in_cart = $session->isSeatInUserCart($seat->id);
                }
            }
        }

        if (auth()->user()->isAdmin() || auth()->user()->is_organizer) {
            return view('Admin.select_seat', compact('session', 'seatMap', 'stadiumClass', 'stadiumLabel'));
        } else {
            return view('User.select_seat', compact('session', 'seatMap', 'stadiumClass', 'stadiumLabel'));
        }
    }

    public function confirmSeat(Request $request, $sessionId)
    {

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to confirm seats.');
        }

        $session = EventSession::findOrFail($sessionId);
        $selectedSeats = json_decode($request->input('selected_seat_id'));

        $pricePerSeat = $session->event->price_per_seat;

        $totalAmount = count($selectedSeats) * $pricePerSeat;

        $cart = new Cart();
        $cart->user_id = auth()->id();
        $cart->event_session_id = $sessionId;
        $cart->total_amount = $totalAmount;
        $cart->seat_ids = json_encode($selectedSeats);
        $cart->save();

        foreach ($selectedSeats as $seatId) {
            $seat = Seat::find($seatId);
            if ($seat) {
                $seat->cart_id = $cart->id;
                $seat->save();
            }
        }

        return redirect()->route('session.select-seat', ['session' => $sessionId])->with('success', 'Cart is updated successfully.');
    }

    public function listAllSessions(Request $request)
    {
        $query = EventSession::with('event', 'teamPlayers');

        if ($request->has('eventName') && !empty($request->eventName)) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->eventName . '%');
            });
        }

        if ($request->has('adminOnly') && $request->adminOnly == 'true') {
            $query->whereHas('event', function ($q) {
                $q->whereNull('organizer_id');
            });
        }

        $sessions = $query->get();

        return view('Admin.session_list', compact('sessions'));
    }

    public function showSeatDetails($seatId)
    {
        $seat = Seat::with(['order.user', 'order.payment', 'eventSession.event'])
                    ->where('id', $seatId)
                    ->firstOrFail();

        if (!$seat->order) {
            abort(404, 'Order not found for this seat');
        }

        return view('Admin.seat_details', compact('seat'));
    }

    public function updateWinner(Request $request, $sessionId)
    {
        $session = EventSession::findOrFail($sessionId);

        if (now() > $session->end_time) {
            $winnerPlayerId = $request->input('winner_player_id');

            $session->winner_player_id = $winnerPlayerId;
            $session->save();

            TeamPlayer::where('event_session_id', $sessionId)->update(['is_winner' => false]);

            TeamPlayer::where('id', $winnerPlayerId)->update(['is_winner' => true]);

            return back()->with('success', 'Winner updated successfully.');
        } else {
            return back()->with('error', 'Cannot update winner for a future event.');
        }
    }

    public function showSelectWinner($sessionId)
    {
        $session = EventSession::with('teamPlayers', 'event')->findOrFail($sessionId);

        return view('Admin.select_winner', compact('session'));
    }


}
