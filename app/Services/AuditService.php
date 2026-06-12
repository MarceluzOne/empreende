<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    private static array $labels = [
        'created' => 'Criou',
        'updated' => 'Atualizou',
        'deleted' => 'Excluiu',
    ];

    private static array $modelNames = [
        'Attendance'      => 'Atendimento',
        'Booking'         => 'Reserva',
        'Event'           => 'Evento',
        'Speaker'         => 'Palestrante',
        'JobVacancy'      => 'Vaga de Emprego',
        'JobSeeker'       => 'Candidato',
        'ServiceProvider' => 'Prestador de Serviço',
        'User'            => 'Usuário',
    ];

    public static function log(string $action, Model $model, ?array $changes = null): void
    {
        $shortClass = class_basename($model);
        $modelName  = self::$modelNames[$shortClass] ?? $shortClass;
        $verb       = self::$labels[$action] ?? $action;

        $identifier = self::identifier($model);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => get_class($model),
            'model_id'   => (string) $model->getKey(),
            'description'=> "{$verb} {$modelName}{$identifier}",
            'changes'    => $changes,
            'ip'         => Request::ip(),
        ]);
    }

    private static function identifier(Model $model): string
    {
        foreach (['name', 'title', 'customer_name', 'responsible_name'] as $field) {
            if (!empty($model->{$field})) {
                return ': ' . $model->{$field};
            }
        }
        return '';
    }
}
