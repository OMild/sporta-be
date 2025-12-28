<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Player\HomeController as PlayerHomeController;

// Player home page (default)
Route::get('/', [PlayerHomeController::class, 'index'])->name('player.home');

// Player routes (public)
Route::get('/venues/{venue}', [PlayerHomeController::class, 'showVenue'])->name('player.venue');
Route::get('/rewards', [PlayerHomeController::class, 'rewards'])->name('player.rewards');

// Player routes (auth optional - will show login prompt if not authenticated)
Route::get('/schedule', [PlayerHomeController::class, 'schedule'])->name('player.schedule');
Route::get('/profile', [PlayerHomeController::class, 'profile'])->name('player.profile');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected admin routes
Route::middleware(['auth', 'role:' . \App\Models\User::ROLE_SUPER_ADMIN])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/financial', [DashboardController::class, 'financial'])->name('admin.financial');
    
    // Owner management routes
    Route::resource('owners', \App\Http\Controllers\Admin\OwnerController::class)->names([
        'index' => 'admin.owners.index',
        'create' => 'admin.owners.create',
        'store' => 'admin.owners.store',
        'show' => 'admin.owners.show',
        'edit' => 'admin.owners.edit',
        'update' => 'admin.owners.update',
        'destroy' => 'admin.owners.destroy',
    ]);
    
    // Venue management routes
    // Note: specific routes must come before resource routes to avoid conflicts
    Route::get('venues/pending', [\App\Http\Controllers\Admin\VenueController::class, 'pending'])->name('admin.venues.pending');
    Route::patch('venues/{venue}/approve', [\App\Http\Controllers\Admin\VenueController::class, 'approve'])->name('admin.venues.approve');
    Route::patch('venues/{venue}/reject', [\App\Http\Controllers\Admin\VenueController::class, 'reject'])->name('admin.venues.reject');
    Route::patch('venues/{venue}/suspend', [\App\Http\Controllers\Admin\VenueController::class, 'suspend'])->name('admin.venues.suspend');
    Route::patch('venues/{venue}/reactivate', [\App\Http\Controllers\Admin\VenueController::class, 'reactivate'])->name('admin.venues.reactivate');
    
    Route::resource('venues', \App\Http\Controllers\Admin\VenueController::class)->names([
        'index' => 'admin.venues.index',
        'show' => 'admin.venues.show',
    ])->only(['index', 'show']);
});

// Protected venue owner routes
Route::middleware(['auth', 'role:' . \App\Models\User::ROLE_VENUE_OWNER])->prefix('owner')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('owner.dashboard');
    
    // Venue management for owners
    Route::resource('venues', \App\Http\Controllers\Owner\VenueController::class)->names([
        'index' => 'owner.venues.index',
        'create' => 'owner.venues.create',
        'store' => 'owner.venues.store',
        'show' => 'owner.venues.show',
        'edit' => 'owner.venues.edit',
        'update' => 'owner.venues.update',
        'destroy' => 'owner.venues.destroy',
    ]);
    
    // Booking management for owners
    Route::get('/bookings', [\App\Http\Controllers\Owner\BookingController::class, 'index'])->name('owner.bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Owner\BookingController::class, 'show'])->name('owner.bookings.show');
    
    // Calendar view for venues
    Route::get('/venues/{venue}/calendar', [\App\Http\Controllers\Owner\BookingController::class, 'calendar'])->name('owner.venues.calendar');
    
    // Block time slot
    Route::post('/venues/{venue}/block-time', [\App\Http\Controllers\Owner\BookingController::class, 'blockTimeSlot'])->name('owner.venues.block-time');
});
