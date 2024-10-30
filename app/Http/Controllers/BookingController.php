<?php
namespace App\Http\Controllers;

use App\Mail\BookingApprovedMail;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\GoogleCalendar\Event;
use Google_Client;
use Google_Service_Calendar;
use Carbon\Carbon;
class BookingController extends Controller
{
    // Hanya menampilkan form booking untuk user biasa
    public function create()
    {
        $accessToken = session('google_access_token');
        // dd($accessToken);
    // Inisialisasi Google Client
    $client = new Google_Client();
    $client->setAccessToken($accessToken);

    // Inisialisasi Google Calendar Service
    $service = new Google_Service_Calendar($client);

    // Ambil event dari Google Calendar utama
    $calendarId = 'primary';
    $events = $service->events->listEvents($calendarId, [
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c'), // Ambil event dari saat ini ke depan
    ]);
    // dd($events);
    $eventsz = $events->getItems();

        $rooms = Room::all();
        return view('bookings.create', compact('rooms', 'eventsz'));
    }

    // Menyimpan booking baru
    public function store(Request $request)
{
    // Validasi input form
    $request->validate([
        'room_id' => 'required',
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'description' => 'required',
        'nama' => 'required',
        'email' => 'required',
        'nip' => 'required',
        'department' => 'required',
    ]);

    // Pengecekan apakah ada booking lain di waktu yang sama
    $existingBooking = Booking::where('room_id', $request->room_id)
        ->where('date', $request->date)
        ->where(function($query) use ($request) {
            $query->where(function($query) use ($request) {
                $query->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>=', $request->start_time);
            })->orWhere(function($query) use ($request) {
                $query->where('start_time', '<=', $request->end_time)
                      ->where('end_time', '>=', $request->end_time);
            })->orWhere(function($query) use ($request) {
                $query->where('start_time', '>=', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
            });
        })->first();

    if ($existingBooking) {
        return redirect()->back()->with('error', 'The selected room is already booked for the chosen time.');
    }

    // Simpan booking baru jika tidak ada bentrok
    Booking::create([
        'user_id' => Auth::id(),
        'room_id' => $request->room_id,
        'date' => $request->date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'description' => $request->description,
        'nama' => $request->nama,
        'email' => $request->email,
        'nip' => $request->nip,
        'department' => $request->department,
        'approved' => false, // Menunggu approval
    ]);

    return redirect()->route('home')->with('success', 'Booking submitted, waiting for admin approval.');
}


    // Menampilkan booking untuk admin
    public function indexAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $bookings = Booking::all();
        return view('admin.bookings.index', compact('bookings'));
    }

    // Proses approve booking oleh admin
    public function approve($id)
{
    if (Auth::user()->role !== 'admin') {
        return redirect()->route('home')->with('error', 'Unauthorized access');
    }

    $booking = Booking::find($id);
    if ($booking) {
        $booking->approved = true;
        $booking->save();

        // Kurangi 7 jam dari waktu mulai dan selesai
        $startDateTime = Carbon::parse($booking->date . ' ' . $booking->start_time)->subHours(7);
        $endDateTime = Carbon::parse($booking->date . ' ' . $booking->end_time)->subHours(7);

        // Buat event di sistem Anda (misal dengan Spatie Google Calendar)
        $event = new Event;
        $event->name = 'Meeting Room Booking: ' . $booking->room->name;
        $event->startDateTime = $startDateTime;
        $event->endDateTime = $endDateTime;
        $event->description = $booking->description;
        $event->save();

        // Kirim email notifikasi setelah approve
        Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));

        // Ambil token OAuth dari session
        $accessToken = session('google_access_token');

        // Jika token tidak ada, arahkan pengguna untuk login ulang dengan Google
        if (!$accessToken) {
            return redirect()->route('login.google')->with('error', 'Please login with Google to sync your calendar.');
        }

        // Inisialisasi Google Client dengan token
        $client = new \Google_Client();
        $client->setAccessToken($accessToken);

        $service = new \Google_Service_Calendar($client);

        // Membuat event untuk disimpan di kalender pengguna
        $googleEvent = new \Google_Service_Calendar_Event([
            'summary' => 'Meeting Room Booking: ' . $booking->room->name,
            'start' => [
                'dateTime' => $startDateTime->toRfc3339String(),  // Gunakan waktu yang sudah dikurangi 7 jam
                'timeZone' => 'Asia/Jakarta',
            ],
            'end' => [
                'dateTime' => $endDateTime->toRfc3339String(),  // Gunakan waktu yang sudah dikurangi 7 jam
                'timeZone' => 'Asia/Jakarta',
            ],
            'attendees' => [
                ['email' => $booking->user->email],  // email pengguna yang diundang
            ],
        ]);

        // Simpan event ke kalender utama pengguna
        $service->events->insert('primary', $googleEvent);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking approved and event created in Google Calendar.');
    }

    return redirect()->route('admin.bookings.index')->with('error', 'Booking not found.');
}
public function update(Request $request)
{
    // Validasi input
    $request->validate([
        'title' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Ambil token OAuth dari sesi
    $accessToken = session('google_access_token');

    if (!$accessToken) {
        return redirect()->route('login.google')->with('error', 'Please login with Google to sync your calendar.');
    }

    // Inisialisasi Google Client dengan token
    $client = new \Google_Client();
    $client->setAccessToken($accessToken);

    $service = new \Google_Service_Calendar($client);

    // Ambil event ID dari request
    $eventId = $request->event_id;

    //format date
    
    $startDateTime = Carbon::parse($request->start_date)->subHours(7);
    $endDateTime = Carbon::parse($request->end_date)->subHours(7);
    // Ambil event dari Google Calendar
    $event = $service->events->get('primary', $eventId);

    // Perbarui detail event
    $event->setSummary($request->title);
    $event->setStart(new \Google_Service_Calendar_EventDateTime([
        'dateTime' => Carbon::parse($startDateTime)->toRfc3339String(),
        'timeZone' => 'Asia/Jakarta',
    ]));
    $event->setEnd(new \Google_Service_Calendar_EventDateTime([
        'dateTime' => Carbon::parse($endDateTime)->toRfc3339String(),
        'timeZone' => 'Asia/Jakarta',
    ]));

    // Simpan perubahan ke Google Calendar
    $updatedEvent = $service->events->update('primary', $eventId, $event);

    return redirect()->route('home')->with('success', 'Event updated successfully in Google Calendar.');
}
public function destroy(Request $request)
{
    // Ambil token OAuth dari sesi
    $accessToken = session('google_access_token');

    if (!$accessToken) {
        return redirect()->route('login.google')->with('error', 'Please login with Google to delete this event.');
    }

    // Inisialisasi Google Client dengan token
    $client = new \Google_Client();
    $client->setAccessToken($accessToken);

    $service = new \Google_Service_Calendar($client);

    // Ambil event ID dari request
    $eventId = $request->event_id;

    try {
        // Hapus event dari Google Calendar
        $service->events->delete('primary', $eventId);
        return redirect()->route('home')->with('success', 'Event deleted successfully from Google Calendar.');
    } catch (Exception $e) {
        return redirect()->route('home')->with('error', 'Failed to delete event: ' . $e->getMessage());
    }
}


}
