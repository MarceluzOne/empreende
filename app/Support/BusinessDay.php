<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Regras de dia útil para agendamento.
 */
class BusinessDay
{
    /**
     * Primeiro dia em que se pode agendar: o próximo dia útil a partir de
     * amanhã (pula sábado e domingo).
     *
     * Ex.: agendando numa segunda -> terça; numa sexta -> próxima segunda.
     */
    public static function nextBookable(?Carbon $from = null): Carbon
    {
        $date = ($from ? $from->copy() : Carbon::today())->addDay();

        while ($date->isWeekend()) {
            $date->addDay();
        }

        return $date->startOfDay();
    }

    /**
     * Hora mínima em que os atendimentos começam (formato H:i).
     */
    public static function openingTime(): string
    {
        return '09:00';
    }

    /**
     * Hora do último horário disponível para atendimento (formato H:i).
     */
    public static function closingTime(): string
    {
        return '14:30';
    }
}
