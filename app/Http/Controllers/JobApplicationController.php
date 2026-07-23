<?php

namespace App\Http\Controllers;

use App\Mail\CandidaturaAceitaMail;
use App\Models\JobApplication;
use App\Models\JobSeeker;
use App\Models\JobVacancy;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class JobApplicationController extends Controller
{
    public function store(Request $request, JobVacancy $jobVacancy)
    {
        $user = auth()->user();

        $seeker = JobSeeker::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        if (! $seeker) {
            return redirect()->route('portal.usuario.curriculo.create')
                ->with('error', 'Você precisa criar seu currículo antes de se candidatar.');
        }

        $already = JobApplication::where('job_vacancy_id', $jobVacancy->id)
            ->where('job_seeker_id', $seeker->id)
            ->exists();

        if ($already) {
            return back()->with('info', 'Você já se candidatou a esta vaga.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        JobApplication::create([
            'job_vacancy_id' => $jobVacancy->id,
            'job_seeker_id'  => $seeker->id,
            'user_id'        => $user->id,
            'message'        => $request->message,
            'status'         => 'pending',
        ]);

        return back()->with('success', "Candidatura enviada para a vaga \"{$jobVacancy->position}\"!");
    }

    public function destroy(JobVacancy $jobVacancy)
    {
        $user   = auth()->user();
        $seeker = JobSeeker::where('user_id', $user->id)->orWhere('email', $user->email)->first();

        if ($seeker) {
            JobApplication::where('job_vacancy_id', $jobVacancy->id)
                ->where('job_seeker_id', $seeker->id)
                ->delete();
        }

        return back()->with('success', 'Candidatura cancelada.');
    }

    public function applicants(JobVacancy $jobVacancy)
    {
        $this->authorizeVacancyOwner($jobVacancy);

        $applications = $jobVacancy->applications()
            ->with('seeker', 'user')
            ->latest()
            ->get();

        return view('job-vacancies.applicants', compact('jobVacancy', 'applications'));
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        $this->authorizeVacancyOwner($application->vacancy);

        $request->validate(['status' => 'required|in:pending,accepted,rejected']);

        $oldStatus = $application->status;
        $newStatus = $request->status;

        $application->update(['status' => $newStatus]);
        $application->load('vacancy', 'seeker');
        $vacancy = $application->vacancy;

        if ($newStatus === 'accepted' && $oldStatus !== 'accepted') {
            Mail::to($application->seeker->email)->send(new CandidaturaAceitaMail($application));
            $vacancy->quantity = max(0, $vacancy->quantity - 1);
            if ($vacancy->quantity <= 0) {
                $vacancy->status = 'filled';
            }
            $vacancy->save();
        }

        if ($newStatus !== 'accepted' && $oldStatus === 'accepted') {
            $vacancy->quantity = $vacancy->quantity + 1;
            if ($vacancy->status === 'filled') {
                $vacancy->status = 'active';
            }
            $vacancy->save();
        }

        // Audita apenas ações de funcionário (a mesma rota também é usada pela empresa).
        if (auth()->user()?->type === 'funcionario') {
            $statusLabel = ['pending' => 'Pendente', 'accepted' => 'Aceita', 'rejected' => 'Recusada'][$newStatus] ?? $newStatus;
            AuditService::log('updated', $application, null,
                "Alterou o status da candidatura de {$application->seeker->name} (vaga: {$vacancy->position}) para \"{$statusLabel}\"");
        }

        return back()->with('success', 'Status da candidatura atualizado.');
    }

    private function authorizeVacancyOwner(JobVacancy $vacancy): void
    {
        $user = auth()->user();
        if ($vacancy->user_id !== $user->id && ! $user->hasPermission('admin-only')) {
            abort(403);
        }
    }
}
