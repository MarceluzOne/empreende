<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventoApiController extends Controller
{
    private static array $meses = [
        1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',
        7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez',
    ];

    private static array $dias = [
        0=>'Dom',1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sáb',
    ];

    public function __construct(private EventService $events) {}

    public function index(Request $request): JsonResponse
    {
        $today  = Carbon::today();
        $tab    = $request->input('tab', 'proximos');
        $search = trim($request->input('search', ''));

        $events = $this->eventsFor($tab, $search, $today)->load('participants');
        $colors = ['blue', 'green', 'purple'];

        $data = $events->map(function (Event $e) use ($colors) {
            $colorIndex = abs(crc32($e->id)) % 3;
            $speakerName = optional($e->speaker)->name ?? '';
            $parts    = explode(' ', $speakerName);
            $initials = strtoupper(substr($parts[0] ?? '', 0, 1))
                      . strtoupper(substr($parts[1] ?? '', 0, 1));

            $month   = (int) $e->date->format('n');
            $weekday = (int) $e->date->format('w');

            return [
                'id'                => $e->id,
                'title'             => $e->title,
                'date_day'          => $e->date->format('d'),
                'date_month'        => self::$meses[$month] ?? '',
                'date_full'         => self::$dias[$weekday] . ', ' . $e->date->format('d/m/Y'),
                'start_time'        => substr($e->start_time, 0, 5),
                'end_time'          => $e->endTime(),
                'max_capacity'      => $e->max_capacity,
                'available_spots'   => $e->availableSpots(),
                'participants_count'=> $e->participants->count(),
                'is_full'           => $e->isFull(),
                'status'            => $e->status,
                'image_url'         => $e->image_url,
                'color'             => $colors[$colorIndex],
                'speaker_name'      => $speakerName,
                'speaker_bio'       => optional($e->speaker)->bio ?? '',
                'speaker_initials'  => $initials,
            ];
        });

        $featured = null;
        if ($tab === 'proximos' && !$search && $data->isNotEmpty()) {
            $featured = $data->first();
        }

        return response()->json([
            'events'  => $data->values(),
            'featured' => $featured,
            'total'   => $data->count(),
        ]);
    }

    /**
     * Eventos da aba pedida. Só públicos, e o estado real vem do EventService
     * (concluído pela data conta como concluído) — filtrar pela coluna
     * `status` listava como próximos eventos que já tinham acabado.
     */
    private function eventsFor(string $tab, string $search, Carbon $today)
    {
        $busca = function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('speaker', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        };

        $scope = fn (?Closure $periodo = null) => function ($q) use ($search, $busca, $periodo) {
            $q->when($search, $busca)->when($periodo, $periodo);
        };

        switch ($tab) {
            case 'semana':
                return $this->events->publicOpen($scope(fn ($q) => $q->whereBetween('date', [$today, $today->copy()->endOfWeek()])));
            case 'mes':
                return $this->events->publicOpen($scope(fn ($q) => $q->whereYear('date', $today->year)->whereMonth('date', $today->month)));
            case 'realizados':
                return $this->events->publicCompleted(12, $scope());
            default: // proximos — todos com inscrição aberta
                return $this->events->publicOpen($scope());
        }
    }
}
