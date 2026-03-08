<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function store(Request $request)
    {

        $validated = $request->validate([
            'responsible_name' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'booking_date' => 'required|date|after:now',
            'guests_count' => 'required|integer|min:1',
            'observation' => 'nullable|string',
        ], [
            'responsible_name.required' => 'O nome do responsável é obrigatório.',
            'booking_date.after' => 'A data do agendamento deve ser uma data futura.',
            'guests_count.min' => 'O agendamento deve ter pelo menos 1 pessoa.',
        ]);

        if ($validated['cpf']) {
            $validated['cpf'] = preg_replace('/[^0-9]/', '', $validated['cpf']);
        }

        $validated['user_id'] = Auth::id();

        Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Agendamento realizado com sucesso para '.$validated['responsible_name'].'!');
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'responsible_name' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'booking_date' => 'required|date',
            'guests_count' => 'required|integer|min:1',
            'observation' => 'nullable|string',
        ]);
        if ($validated['cpf']) {
            $validated['cpf'] = preg_replace('/[^0-9]/', '', $validated['cpf']);
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Agendamento removido com sucesso!');
    }
}
