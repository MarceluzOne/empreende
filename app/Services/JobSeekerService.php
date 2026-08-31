<?php

namespace App\Services;

use App\Models\JobSeeker;
use App\Models\User;
use App\Support\Document;
use Illuminate\Support\Facades\Auth;

class JobSeekerService
{
    /**
     * Currículo do usuário: pelo vínculo direto, senão pelo e-mail da conta ou
     * pelo CPF. As duas últimas alternativas reconhecem o currículo cadastrado
     * pela equipe, ou com outro e-mail, antes de a pessoa ter conta.
     */
    public function forUser(User $user): ?JobSeeker
    {
        $linked = JobSeeker::where('user_id', $user->id)->first();

        if ($linked) {
            return $linked;
        }

        return JobSeeker::where(function ($query) use ($user) {
            $query->where('email', $user->email);

            foreach (Document::cpfVariants($user->cpf) as $cpf) {
                $query->orWhere('cpf', $cpf);
            }
        })->first();
    }

    public function store(array $data): JobSeeker
    {
        $data['user_id'] = Auth::id();
        $data['phone']   = isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null;

        return JobSeeker::create($data);
    }

    public function update(JobSeeker $seeker, array $data): JobSeeker
    {
        $data['phone'] = isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null;

        $seeker->update($data);
        return $seeker;
    }

    public function destroy(JobSeeker $seeker): void
    {
        $seeker->delete();
    }
}
