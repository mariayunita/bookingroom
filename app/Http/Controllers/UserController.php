<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Tampilkan halaman untuk melakukan booking
    public function index()
    {
        $rooms = Room::all();  // Ambil semua room yang tersedia
        $bookings = Booking::where('user_id', Auth::id())->get(); // Booking yang dibuat oleh user

        return view('user.bookings', compact('rooms', 'bookings'));
    }

    // Store booking dari user
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
        ]);

        Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'description' => $request->description,
            'is_approved' => false, // Booking menunggu persetujuan admin
        ]);

        return redirect()->back()->with('success', 'Booking berhasil diajukan, menunggu persetujuan admin.');
    }
}
