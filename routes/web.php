<?php
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Auth;

// Route untuk halaman utama
Route::get('/', function () {
    return redirect()->route('login');
});


// Route untuk authentication (login/register)
Auth::routes();

// Route untuk dashboard setelah login
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Group route yang hanya bisa diakses setelah user login
Route::group(['middleware' => 'auth'], function () {

    // Route untuk User yang bisa melakukan booking
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::put('/bookings/update', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/delete', [BookingController::class, 'destroy'])->name('bookings.delete');

    // Route untuk Admin (hanya admin yang bisa mengakses CRUD room dan approve booking)
    Route::get('/admin/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/admin/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/admin/rooms/store', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/admin/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/admin/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/admin/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    
    // Route untuk Admin untuk melihat dan approve booking
    Route::get('/admin/bookings', [BookingController::class, 'indexAdmin'])->name('admin.bookings.index');
    Route::post('/admin/bookings/{id}/approve', [BookingController::class, 'approve'])->name('admin.bookings.approve');
});

// Route untuk Google OAuth Login (menggunakan Socialite)
Route::get('login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// use Spatie\GoogleCalendar\Event;
// use Carbon\Carbon;

// Route::get('/test-calendar', function () {
//     $event = new Event;
//     $event->name = 'Test Event';
//     $event->startDateTime = Carbon::now();
//     $event->endDateTime = Carbon::now()->addHour();
//     $event->save();

//     return 'Event created in Google Calendar!';
// });
