<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user')->orderBy('booking_date', 'asc')->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function edit(Booking $booking)
    {
        return view('bookings.edit', compact('booking'));
    }

    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request, \App\Services\BookingService $bookingService)
    {
        // A validação que já fizemos permanece aqui para garantir o formato dos dados
        $validated = $request->validate([
            'resource_type' => 'required|in:auditorio,reuniao',
            'responsible_name' => 'required|string|max:255',
            'type' => 'required|in:single,consecutive,alternated',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            // ... (demais regras de data que já temos)
        ]);

        try {
            $count = $bookingService->createBookings($request->all());

            $msg = $count > 1 ? "$count agendamentos realizados!" : 'Agendamento realizado!';

            return redirect()->route('bookings.index')->with('success', $msg);

        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        // Criamos o campo 'booking_date' e 'end_date' a partir dos inputs da view
        $request->merge([
            'booking_date' => $request->date.' '.$request->start_time,
            'end_date' => $request->date.' '.$request->end_time,
        ]);

        $validated = $request->validate([
            'responsible_name' => 'required|string|max:255',
            'resource_type' => 'required|in:auditorio,reuniao',
            'booking_date' => 'required|date',
            'end_date' => 'required|date|after:booking_date',
            'guests_count' => 'required|integer|min:1',
            // ... outras validações
        ]);

        $booking = Booking::findOrFail($id);

        // Validar conflito (ignorando o ID atual para não dar erro com ele mesmo)
        $hasConflict = Booking::where('resource_type', $request->resource_type)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->where('booking_date', '<', $request->end_date)
                    ->where('end_date', '>', $request->booking_date);
            })->exists();

        if ($hasConflict) {
            return back()->withErrors('Este horário já está ocupado por outro agendamento.')->withInput();
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Agendamento atualizado!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Agendamento removido com sucesso!');
    }
}
