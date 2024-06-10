<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TeamPlayerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;




// Login and Registration
Route::get('/login', [AuthManager::class, 'login']) -> name('login');
Route::post('/login', [AuthManager::class, 'loginPost']) -> name('login.post');
Route::get('/registration', [AuthManager::class, 'registration']) -> name('registration');
Route::post('/registration', [AuthManager::class, 'registrationPost']) -> name('registration.post');
Route::get('/logout', [AuthManager::class, 'logout']) -> name('logout');


// Home
Route::get('/', [EventController::class, 'showWelcomePage'])->name('home');


// Footer
Route::get('/team_member_details', function () {
    return view('team_member_details');
})->name('team_member_details');


Route::group(['middleware' => 'auth'], function()
{

    // USERS Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/orders', [OrderController::class, 'userOrders'])->name('profile.orders');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update']);
    Route::get('/profile/my-favorites', [ProfileController::class, 'showFavorites'])->name('profile.favorites');
    Route::get('/profile/votes-details', [ProfileController::class, 'showVotingDetails'])->name('profile.votes');
    Route::get('/profile/reviews', [ReviewController::class, 'userReviews'])->name('profile.reviews');
    Route::get('/profile/upcoming-events', [ProfileController::class, 'showUpcomingEvents'])->name('profile.upcoming_events');
    Route::get('/profile/registered-events', [ProfileController::class, 'showRegisteredEvents'])->name('profile.registered_events');

    // ADMIN Profile
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('profile.admin');
    Route::get('/admin/profile/edit', [ProfileController::class, 'edit'])->name('profile.admin.edit');
    Route::get('/admin/profile/user-list', [UserController::class, 'index'])->name('profile.user.list');
    Route::get('/admin/profile/manage-organizer-requests', [ProfileController::class, 'showOrganizerRequests'])->name('profile.organizer.manage');
    Route::get('/admin/profile/recent-orders', [ProfileController::class, 'recentOrders'])->name('profile.recentOrders');
    Route::get('/admin/profile/recent-orders/order/{id}', [OrderController::class, 'adminOrderDetails'])->name('profile.order.details');
    Route::get('/admin/profile/event-statistics', [ProfileController::class, 'showEventStatistics'])->name('profile.event.statistics');
    Route::get('/admin/profile/event-category-statistics', [ProfileController::class, 'showEventCategoryStatistics'])->name('profile.eventCategory.statistics');
    Route::get('/admin/profile/sessions', [SessionController::class, 'listAllSessions'])->name('profile.sessions.list');
    Route::get('/admin/profile/team-players', [TeamPlayerController::class, 'list'])->name('profile.team-players.list');

    // ORGANIZERS Profile
    Route::get('/organizer/profile', [ProfileController::class, 'index'])->name('profile.organizer');
    Route::get('/organizer/profile/edit', [ProfileController::class, 'edit'])->name('profile.organizer.edit');
    Route::post('/organizer/update-details', [ProfileController::class, 'updateOrganizerDetails'])->name('organizer.updateDetails');
    Route::get('/organizer/profile/sessions', [ProfileController::class, 'showOrganizerSessions'])->name('profile.sessions');
    Route::get('/organizer/profile/events', [ProfileController::class, 'showOrganizerEvents'])->name('profile.events');
    Route::get('/organizer/profile/total-registrations', [ProfileController::class, 'showTotalRegistrations'])->name('profile.total-registrations');
    Route::get('/organizer/profile/events/popularity', [ProfileController::class, 'showEventPopularity'])->name('profile.events.popularity');
    Route::get('/organizer/profile/revenues', [ProfileController::class, 'showTotalRevenuesByEvent'])->name('profile.revenues');
    Route::get('/organizer/profile/reviews-ratings', [ProfileController::class, 'showReviewsRatingsReceived'])->name('profile.reviews-ratings');


    // Organizer
    Route::get('/manage-organizer-requests', [ProfileController::class, 'showOrganizerRequests'])->name('organizer.manage');
    Route::get('/request-organizer', [ProfileController::class, 'requestOrganizerForm'])->name('organizer.request.form');
    Route::post('/request-organizer', [ProfileController::class, 'storeOrganizerRequest'])->name('organizer.request');
    Route::post('/approve-organizer/{userId}', [ProfileController::class, 'approveOrganizerRequest'])->name('organizer.approve');
    Route::post('/deny-organizer/{userId}', [ProfileController::class, 'denyOrganizerRequest'])->name('organizer.deny');
    Route::get('/notification/mark-as-read/{id}', [ProfileController::class, 'markNotificationAsRead'])->name('notification.markAsRead');
    Route::get('/organizers', [ProfileController::class, 'showOrganizers'])->name('organizers.show');
    Route::delete('/organizers/delete/{userId}', [ProfileController::class, 'deleteOrganizer'])->name('organizer.delete');


    // User List
    Route::get('/user-list', [UserController::class, 'index'])->name('user.list');


    // Event
    Route::resource('events', EventController::class);
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/favorite', [EventController::class, 'favorite'])->name('events.favorite');
    Route::delete('/events/{event}/unfavorite', [EventController::class, 'unfavorite'])->name('events.unfavorite');
    Route::get('/organizer/{id}/events', [EventController::class, 'eventsByOrganizer'])->name('organizer.events');


    // Session
    Route::get('/sessions', [SessionController::class, 'listAllSessions'])->name('sessions.list');
    Route::get('/sessions/{sessionId}/select-winner', [SessionController::class, 'showSelectWinner'])->name('sessions.showSelectWinner');
    Route::post('/sessions/{sessionId}/update-winner', [SessionController::class, 'updateWinner'])->name('sessions.updateWinner');


    // Seat
    Route::get('/seat-details/{seat}', [SessionController::class, 'showSeatDetails']);
    Route::get('/session/{session}/select-seat', [SessionController::class, 'selectSeat'])->name('session.select-seat');
    Route::post('/session/{session}/confirm-seat', [SessionController::class, 'confirmSeat'])->name('session.confirm-seat');


    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/confirm', [CartController::class, 'confirmCart'])->name('cart.confirm');


    // Payment
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/success/{paymentId}', [PaymentController::class, 'success'])
        ->name('payment.success');


    // Order
    Route::get('/order/{id}/details', [OrderController::class, 'details'])->name('order.details');


    // Teamplayers
    Route::get('/sessions/{sessionId}/add-team-player', [TeamPlayerController::class, 'create'])->name('team-players.create');
    Route::get('/sessions/{sessionId}/team-players/{playerId}/edit', [TeamPlayerController::class, 'edit'])->name('team-players.edit');
    Route::post('/sessions/{sessionId}/team-players', [TeamPlayerController::class, 'store'])->name('team-players.store');
    Route::put('/team-players/{playerId}', [TeamPlayerController::class, 'update'])->name('team-players.update');
    Route::delete('/team-players/{playerId}', [TeamPlayerController::class, 'destroy'])->name('team-players.destroy');
    Route::post('/vote/{sessionId}/{playerId}', [TeamPlayerController::class, 'vote'])->name('team-players.vote');
    Route::post('/unvote/{sessionId}/{playerId}', [TeamPlayerController::class, 'unvote'])->name('team-players.unvote');
    Route::get('/team-players', [TeamPlayerController::class, 'list'])->name('team-players.list');


    // Review
    Route::post('/reviews', [ReviewController::class, 'storeReview'])->name('reviews.store');

});

















