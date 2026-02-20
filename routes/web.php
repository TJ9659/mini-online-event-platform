<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventManagementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;


// I'm not using these routes for now so I commented them out, maybe when I create a admin page I will use this.
// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canRegister' => Features::enabled(Features::registration()),
//     ]);
// })->name('home');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
});

Route::get('/terms', function () {
    return Inertia::render('Terms');
});

Route::get('/contact', function () {
    return Inertia::render('Contact');
});

Route::get('/about-us', function () {
    return Inertia::render('About');
});

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [EventController::class, 'search'])->name('events.search');

Route::group(['middleware' => ['auth', 'verified']], function () {
    // for event management
    // Route::get('/events/{event:slug}/edit', [EventController::class, 'edit'])->name('events.edit');
    // Route::put('/events/{event:slug}', [EventController::class, 'update'])->name('events.update');
    
    // for ticket management
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/events/{event}/tickets', [TicketController::class, 'store'])->name('tickets.store');
    // Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});
// for events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');


Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// for event management + ticket management
Route::middleware(['auth', 'verified'])->prefix('manage')->group(function () {
    
    Route::get('events', [EventManagementController::class, 'index'])->name('events.manage.index');
    Route::get('events/create', [EventManagementController::class, 'create'])->name('events.create');
    Route::post('/events', [EventManagementController::class, 'store'])->name('events.store');
    Route::get('events/drafts', [EventManagementController::class, 'draft'])->name('events.draft');
    Route::put('events/{event:id}', [EventManagementController::class, 'update'])->name('events.update');
    Route::delete('/events/{event:id}', [EventManagementController::class, 'destroy'])->name('events.destroy');
    Route::get('events/registrations', [EventController::class, 'registrationsIndex']);
    Route::get('events/{event:id}/edit', [EventManagementController::class, 'edit'])->name('events.edit');
    Route::put('/tickets/{ticket:id}/cancel', [TicketController::class, 'cancel']);
    
});

require __DIR__ . '/settings.php';
