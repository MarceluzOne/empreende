<?php

/*
|--------------------------------------------------------------------------
| Rotas do site Empreende Vitória
|--------------------------------------------------------------------------
|
| Carregadas pelo App\Providers\SiteServiceProvider, que já aplica:
|   - prefixo de URL:   /empreendevitoria
|   - grupo middleware: web
|
| Portanto NÃO declare aqui prefixo de slug nem o middleware 'web'.
|
*/

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\EmpresaAuthController;
use App\Http\Controllers\Auth\UsuarioAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PortalEmpresaController;
use App\Http\Controllers\PortalUsuarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
| Público
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/agendamento/disponibilidade', [PublicAttendanceController::class, 'availability'])->name('public.attendance.availability');
Route::post('/agendamento', [PublicAttendanceController::class, 'store'])->name('public.attendance.store');
Route::get('/contato', [LandingController::class, 'contato'])->name('contato');
Route::get('/cursos', [LandingController::class, 'cursos'])->name('cursos');
Route::get('/servicos', [LandingController::class, 'servicos'])->name('servicos');
Route::get('/empresas-locais', [LandingController::class, 'empresasLocais'])->name('empresas-locais');

/*
| Auth funcionário
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
| Reset de senha
*/
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

/*
| Setup (migrations via rota — ambiente FTP sem artisan)
*/
Route::get('/setup-inicial', [RoleController::class, 'scriptConfiguration']);

/*
| Auth usuário (candidatos)
*/
Route::get('/login/usuario', [UsuarioAuthController::class, 'showLogin'])->name('usuario.login');
Route::post('/login/usuario', [UsuarioAuthController::class, 'login'])->name('usuario.login.post');
Route::get('/cadastro/usuario', [UsuarioAuthController::class, 'showRegister'])->name('usuario.register');
Route::post('/cadastro/usuario', [UsuarioAuthController::class, 'register'])->name('usuario.register.post');
Route::post('/logout/usuario', [UsuarioAuthController::class, 'logout'])->name('usuario.logout');

/*
| Auth empresa
*/
Route::get('/login/empresa', [EmpresaAuthController::class, 'showLogin'])->name('empresa.login');
Route::post('/login/empresa', [EmpresaAuthController::class, 'login'])->name('empresa.login.post');
Route::get('/cadastro/empresa', [EmpresaAuthController::class, 'showRegister'])->name('empresa.register');
Route::post('/cadastro/empresa', [EmpresaAuthController::class, 'register'])->name('empresa.register.post');
Route::post('/logout/empresa', [EmpresaAuthController::class, 'logout'])->name('empresa.logout');

/*
|--------------------------------------------------------------------------
| Portal do Funcionário (admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.user.type:funcionario'])->group(function () {
    Route::get('/panel', [DashboardController::class, 'index'])->name('panel');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('bookings/availability', [BookingController::class, 'availability'])->name('bookings.availability');
    Route::resource('bookings', BookingController::class);
    Route::delete('bookings', [BookingController::class, 'destroyMultiple'])->name('bookings.destroyMultiple');

    Route::get('attendances/availability', [AttendanceController::class, 'availability'])->name('attendances.availability');
    Route::resource('attendances', AttendanceController::class);
    Route::patch('attendances/{attendance}/complete', [AttendanceController::class, 'complete'])->name('attendances.complete');

    Route::resource('services', ServiceProviderController::class);

    Route::resource('job-vacancies', JobVacancyController::class)->parameters(['job-vacancies' => 'jobVacancy']);
    Route::post('job-vacancies/{jobVacancy}/notify', [JobVacancyController::class, 'notify'])->name('job-vacancies.notify');

    Route::resource('job-seekers', JobSeekerController::class)->parameters(['job-seekers' => 'jobSeeker']);

    // Cidadãos: lista consolidada por CPF (candidatos + atendidos)
    Route::get('cidadaos', [CitizenController::class, 'index'])->name('citizens.index');
    Route::get('cidadaos/{cpf}', [CitizenController::class, 'show'])->name('citizens.show');

    Route::get('job-vacancies/{jobVacancy}/applicants', [JobApplicationController::class, 'applicants'])->name('job-vacancies.applicants');
    Route::patch('job-applications/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('job-applications.status');

    Route::resource('events', EventController::class);
    Route::post('events/{event}/participants', [EventController::class, 'storeParticipant'])->name('events.participants.store');
    Route::put('events/{event}/participants/{participant}', [EventController::class, 'updateParticipant'])->name('events.participants.update');
    Route::delete('events/{event}/participants/{participant}', [EventController::class, 'destroyParticipant'])->name('events.participants.destroy');
    Route::get('events/{event}/pdf', [EventController::class, 'pdf'])->name('events.pdf');
    Route::patch('events/{event}/status', [EventController::class, 'updateStatus'])->name('events.status');
    Route::get('events/{event}/participants/{participant}/certificate', [EventController::class, 'certificate'])->name('events.certificate');

    Route::resource('speakers', SpeakerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('speakers/quick-store', [SpeakerController::class, 'quickStore'])->name('speakers.quick-store');

    Route::middleware(['can:admin-only'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
    });
});

/*
|--------------------------------------------------------------------------
| Portal do Usuário (candidato/cidadão)
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Portal da Empresa (empregador)
|--------------------------------------------------------------------------
*/
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
