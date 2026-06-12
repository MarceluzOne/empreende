<?php

use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\PortalUsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check.user.type:usuario'])->group(function () {
    Route::get('/portal/usuario', [PortalUsuarioController::class, 'index'])->name('portal.usuario');

    // CRUD de currículo
    Route::get('/portal/usuario/curriculo/criar', [PortalUsuarioController::class, 'createCurriculo'])->name('portal.usuario.curriculo.create');
    Route::post('/portal/usuario/curriculo', [PortalUsuarioController::class, 'storeCurriculo'])->name('portal.usuario.curriculo.store');
    Route::get('/portal/usuario/curriculo/editar', [PortalUsuarioController::class, 'editCurriculo'])->name('portal.usuario.curriculo.edit');
    Route::put('/portal/usuario/curriculo', [PortalUsuarioController::class, 'updateCurriculo'])->name('portal.usuario.curriculo.update');
    Route::delete('/portal/usuario/curriculo', [PortalUsuarioController::class, 'destroyCurriculo'])->name('portal.usuario.curriculo.destroy');

    // Candidaturas a vagas
    Route::post('job-vacancies/{jobVacancy}/apply', [JobApplicationController::class, 'store'])->name('job-vacancies.apply');
    Route::delete('job-vacancies/{jobVacancy}/apply', [JobApplicationController::class, 'destroy'])->name('job-vacancies.unapply');

    // Inscrição em eventos
    Route::post('/portal/usuario/eventos/{event}/inscrever', [PortalUsuarioController::class, 'inscreverEvento'])->name('portal.usuario.eventos.inscrever');
    Route::delete('/portal/usuario/eventos/{event}/cancelar', [PortalUsuarioController::class, 'cancelarEvento'])->name('portal.usuario.eventos.cancelar');
});
