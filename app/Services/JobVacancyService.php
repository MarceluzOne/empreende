<?php

namespace App\Services;

use App\Mail\JobVacancyMail;
use App\Models\JobSeeker;
use App\Models\JobVacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JobVacancyService
{
    public function store(array $data): JobVacancy
    {
        $data['user_id']  = Auth::id();
        $data['cnpj']     = preg_replace('/[^0-9]/', '', $data['cnpj']);
        $data['benefits'] = $data['benefits'] ?? [];

        return JobVacancy::create($data);
    }

    public function update(JobVacancy $vacancy, array $data): JobVacancy
    {
        $data['cnpj']     = preg_replace('/[^0-9]/', '', $data['cnpj']);
        $data['benefits'] = $data['benefits'] ?? [];

        $vacancy->update($data);
        return $vacancy;
    }

    public function destroy(JobVacancy $vacancy): void
    {
        $vacancy->delete();
    }

    public function notifyMatchingJobSeekers(JobVacancy $vacancy): int
    {
        $seekers = JobSeeker::where('interest_area', $vacancy->interest_area)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('status', 'active')
            ->get();

        foreach ($seekers as $seeker) {
            Mail::to($seeker->email)->send(new JobVacancyMail($vacancy, $seeker));
        }

        return $seekers->count();
    }
}
