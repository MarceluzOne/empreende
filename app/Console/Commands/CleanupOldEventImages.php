<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldEventImages extends Command
{
    protected $signature   = 'events:cleanup-images';
    protected $description = 'Apaga imagens de eventos encerrados há mais de 20 dias';

    public function handle(): int
    {
        $cutoff = Carbon::today()->subDays(20);
        $count  = 0;

        Event::whereNotNull('image_path')->each(function (Event $event) use ($cutoff, &$count) {
            $lastDate = Carbon::parse(max($event->allDates()));

            if ($lastDate->lt($cutoff)) {
                Storage::disk('public')->delete($event->image_path);
                $event->update(['image_path' => null]);
                $count++;
                $this->line("Imagem removida: {$event->title} (último dia: {$lastDate->format('d/m/Y')})");
            }
        });

        $this->info("{$count} imagem(ns) removida(s).");

        return self::SUCCESS;
    }
}
