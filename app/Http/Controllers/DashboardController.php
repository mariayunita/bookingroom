<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $approvedBookings = Booking::where('is_approved', true)->get();
        return view('dashboard', compact('approvedBookings'));
    }
}
