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
        'Attendance'       => 'Atendimento',
        'Booking'          => 'Reserva',
        'Event'            => 'Evento',
        'EventParticipant' => 'Participante',
        'Speaker'          => 'Palestrante',
        'JobVacancy'       => 'Vaga de Emprego',
        'JobSeeker'        => 'Candidato',
        'JobApplication'   => 'Candidatura',
        'Empresa'          => 'Empresa',
        'ServiceProvider'  => 'Prestador de Serviço',
        'User'             => 'Usuário',
    ];

    /**
     * @param string|null $description Descrição customizada. Quando informada,
     *                                 sobrepõe a descrição padrão "{verbo} {modelo}: {id}".
     */
    public static function log(string $action, Model $model, ?array $changes = null, ?string $description = null): void
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
            'description'=> $description ?? "{$verb} {$modelName}{$identifier}",
            'changes'    => $changes,
            'ip'         => Request::ip(),
        ]);
    }

    private static function identifier(Model $model): string
    {
        foreach (['name', 'title', 'position', 'customer_name', 'responsible_name'] as $field) {
            if (!empty($model->{$field})) {
                return ': ' . $model->{$field};
            }
        }
        return '';
    }
}
