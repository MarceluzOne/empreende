<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'job_vacancy_id',
        'job_seeker_id',
        'user_id',
        'message',
        'status',
    ];

    protected $appends = ['status_label'];

    public function vacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function seeker()
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Pendente',
            'accepted' => 'Aceito',
            'rejected' => 'Recusado',
            default    => $this->status,
        };
    }
}
