<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $service) {}

    public function index(Request $request)
    {
        $bookings = Booking::with('user')
            ->when($request->search, fn($q) => $q->where('responsible_name', 'ilike', '%'.$request->search.'%'))
            ->when($request->resource_type, fn($q) => $q->where('resource_type', $request->resource_type))
            ->when($request->date, fn($q) => $q->whereDate('booking_date', $request->date))
            ->orderBy('booking_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'resource_type'    => 'required|in:auditorio,reuniao',
            'responsible_name' => 'required|string|max:255',
            'type'             => 'required|in:single,consecutive,alternated',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
        ]);

        try {
            $count = $this->service->createBookings($request->all());
            $msg = $count > 1 ? "{$count} agendamentos realizados!" : 'Agendamento realizado!';

            return redirect()->route('bookings.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit(Booking $booking)
    {
        return view('bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'responsible_name' => 'required|string|max:255',
            'resource_type'    => 'required|in:auditorio,reuniao',
            'date'             => 'required|date',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'guests_count'     => 'required|integer|min:1',
        ]);

        try {
            $this->service->update($booking, $request->all());

            return redirect()->route('bookings.index')->with('success', 'Agendamento atualizado!');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Agendamento removido com sucesso!');
    }
}
