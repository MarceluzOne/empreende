<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ServiceProvider;
use App\Services\EventService;
use App\Support\BusinessDay;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function __construct(private EventService $events) {}

    public function index()
    {
        return view('landing', [
            'scheduleMinDate'   => BusinessDay::nextBookable()->format('Y-m-d'),
            'scheduleStartHour' => (int) substr(BusinessDay::openingTime(), 0, 2),
        ]);
    }

    public function contato()
    {
        return view('contato');
    }

    public function servicos()
    {
        $prestadores = ServiceProvider::where('status', 'active')
            ->where('provider_type', 'individual')
            ->orderBy('name')
            ->get();

        return view('servicos', compact('prestadores'));
    }

    public function empresasLocais()
    {
        $empresas = ServiceProvider::where('status', 'active')
            ->where('provider_type', 'company')
            ->orderBy('name')
            ->get();

        return view('empresas-locais', compact('empresas'));
    }

    public function cursos()
    {
        $today = Carbon::today();

        $upcoming = $this->events->publicOpen();

        $thisWeek = $upcoming->filter(fn($e) => $e->date->lte($today->copy()->endOfWeek()));
        $thisMonth = $upcoming->filter(fn($e) => $e->date->month === $today->month && $e->date->year === $today->year);

        $completed = $this->events->publicCompleted(9);

        $featured = $upcoming->first();

        return view('cursos', compact('upcoming', 'thisWeek', 'thisMonth', 'completed', 'featured'));
    }
}
