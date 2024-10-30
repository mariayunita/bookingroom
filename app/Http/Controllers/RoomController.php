<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    // Menampilkan daftar room (Admin only)
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $rooms = Room::all();
        return view('rooms.index', compact('rooms'));
    }

    // Form untuk membuat room baru (Admin only)
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        return view('rooms.create');
    }

    // Simpan room baru (Admin only)
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    // Edit room (Admin only)
    public function edit(Room $room)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        return view('rooms.edit', compact('room'));
    }

    // Update room (Admin only)
    public function update(Request $request, Room $room)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required',
        ]);

        $room->update($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    // Hapus room (Admin only)
    public function destroy(Room $room)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
