<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Notifications\BookingApprovedNotification;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use App\Mail\BookingApprovedMail;
use Illuminate\Support\Facades\Mail;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    // Tampilkan halaman untuk approve booking
    public function index()
    {
        // Menampilkan semua booking yang belum diapprove
        $bookings = Booking::where('is_approved', false)->get();
        return view('admin.bookings', compact('bookings'));
    }
    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
    $booking->is_approved = true;
    $booking->save();

    $event = new Event;
    $event->name = 'Meeting in ' . $booking->room->name;
    $event->startDateTime = Carbon::parse($booking->start_time);
    $event->endDateTime = Carbon::parse($booking->end_time);
    $event->description = $booking->description;
    $event->addAttendee([
        'email' => $booking->user->email,
    ]);
    $event->save();

    // Mengirim notifikasi ke user yang melakukan booking
    $booking->user->notify(new BookingApprovedNotification($booking));
    // Mengirim email ke user yang melakukan booking
    Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));
    return redirect()->back()->with('success', 'Booking telah disetujui.');
    }
}
