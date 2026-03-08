<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; // Já pensando no futuro
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Aqui você pode carregar dados diferentes dependendo do Role
        // Exemplo: O admin vê o total de salas, o funcionário vê só suas reservas
        $data = [
            'totalRooms' => 0,
            'myBookingsCount' => 0
        ];

        if ($user->hasPermission('rooms_view')) {
            // $data['totalRooms'] = Room::count();
        }

        return view('dashboard', compact('data'));
    }
}