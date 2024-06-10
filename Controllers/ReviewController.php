<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\Review;


class ReviewController extends Controller
{

    public function storeReview(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        Review::create([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
            'review' => $request->review,
            'rating' => $request->rating
        ]);

        return back()->with('success', 'Review added successfully.');
    }


    public function userReviews()
    {
        $user = auth()->user();
        $reviews = $user->reviews()->with('event')->get();

        return view('User.reviews', compact('reviews'));
    }




}
