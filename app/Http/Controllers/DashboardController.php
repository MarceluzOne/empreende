<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\ServiceProvider;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayAttendances = Attendance::whereDate('scheduled_at', $today)
            ->orderBy('scheduled_at')
            ->get();

        $pendingProviders = ServiceProvider::where('status', 'pending')->count();

        $todayBookingsCount = Booking::whereDate('booking_date', $today)->count();

        return view('dashboard', compact(
            'todayAttendances',
            'pendingProviders',
            'todayBookingsCount',
        ));
    }
}
