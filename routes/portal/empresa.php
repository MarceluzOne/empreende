<?php

use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\PortalEmpresaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check.user.type:empresa'])->group(function () {
    Route::get('/portal/empresa', [PortalEmpresaController::class, 'index'])->name('portal.empresa');

    // CRUD de vagas
    Route::get('/portal/empresa/vagas/criar', [PortalEmpresaController::class, 'createVaga'])->name('portal.empresa.vagas.create');
    Route::post('/portal/empresa/vagas', [PortalEmpresaController::class, 'storeVaga'])->name('portal.empresa.vagas.store');
    Route::get('/portal/empresa/vagas/{vaga}/editar', [PortalEmpresaController::class, 'editVaga'])->name('portal.empresa.vagas.edit');
    Route::put('/portal/empresa/vagas/{vaga}', [PortalEmpresaController::class, 'updateVaga'])->name('portal.empresa.vagas.update');
    Route::delete('/portal/empresa/vagas/{vaga}', [PortalEmpresaController::class, 'destroyVaga'])->name('portal.empresa.vagas.destroy');
    Route::patch('/portal/empresa/vagas/{vaga}/encerrar', [PortalEmpresaController::class, 'encerrarVaga'])->name('portal.empresa.vagas.encerrar');

    // Candidaturas
    Route::get('job-vacancies/{jobVacancy}/applicants', [JobApplicationController::class, 'applicants'])->name('job-vacancies.applicants');
    Route::patch('/portal/empresa/job-applications/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('empresa.job-applications.status');

    // Perfil do candidato (somente-leitura para empresa)
    Route::get('/portal/empresa/candidatos/{jobSeeker}', [PortalEmpresaController::class, 'showCandidato'])->name('portal.empresa.candidato.show');
});
