<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ProfileController;

// ─── AUTH ───────────────────────────────────────────────────
Route::get('/', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── MAIN HOME ───────────────────────────────────────────────
Route::get('/home', [PlaceController::class, 'index'])->name('home');

// ─── EXPLORE: PROVINCES & REGIONS ───────────────────────────
Route::get('/explore', [ProvinceController::class, 'index'])->name('explore');
Route::get('/explore/{slug}', [ProvinceController::class, 'show'])->name('province.show');
Route::get('/explore/{province}/{region}', [ProvinceController::class, 'region'])->name('region.show');

// ─── PLACES ─────────────────────────────────────────────────
Route::get('/places', [PlaceController::class, 'browse'])->name('places.browse');
Route::get('/place/{id}', [PlaceController::class, 'show'])->name('place.show');

// ─── ITINERARIES (public) ───────────────────────────────────
Route::get('/itineraries', [ItineraryController::class, 'index'])->name('itinerary.index');
Route::get('/itinerary/{slug}', [ItineraryController::class, 'show'])->name('itinerary.show');

// ─── AUTHENTICATED ROUTES ────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Places interactions
    Route::post('/place/{id}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::post('/place/{id}/favorite', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // Itinerary CRUD
    Route::get('/my-itineraries', [ItineraryController::class, 'myPosts'])->name('itinerary.my');
    Route::get('/itinerary/create', [ItineraryController::class, 'create'])->name('itinerary.create');
    Route::post('/itinerary', [ItineraryController::class, 'store'])->name('itinerary.store');
    Route::get('/itinerary/{slug}/edit', [ItineraryController::class, 'edit'])->name('itinerary.edit');
    Route::put('/itinerary/{slug}', [ItineraryController::class, 'update'])->name('itinerary.update');
    Route::delete('/itinerary/{slug}', [ItineraryController::class, 'destroy'])->name('itinerary.destroy');

    // Itinerary social actions
    Route::post('/itinerary/{id}/like', [ItineraryController::class, 'toggleLike'])->name('itinerary.like');
    Route::post('/itinerary/{id}/save', [ItineraryController::class, 'toggleSave'])->name('itinerary.save');
    Route::post('/itinerary/{id}/comment', [ItineraryController::class, 'comment'])->name('itinerary.comment');

    // Profile
    Route::get('/profile/{id?}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Follow system
    Route::post('/follow/{id}', [ProfileController::class, 'toggleFollow'])->name('follow.toggle');
});
