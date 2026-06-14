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
| Convenção: URLs em português (CLAUDE.md). Os NOMES das rotas (ex.:
| 'attendances.index') são mantidos como identificadores internos para não
| quebrar as chamadas route() espalhadas em views/controllers.
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
Route::get('/entrar', [AuthController::class, 'showLogin'])->name('login');
Route::post('/entrar', [AuthController::class, 'login'])->name('login.post');
Route::post('/sair', [AuthController::class, 'logout'])->name('logout');

/*
| Reset de senha
*/
Route::get('/esqueci-senha', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/esqueci-senha', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/redefinir-senha/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/redefinir-senha', [PasswordResetController::class, 'reset'])->name('password.update');

/*
| Setup (migrations via rota — ambiente FTP sem artisan)
*/
Route::get('/setup-inicial', [RoleController::class, 'scriptConfiguration']);

/*
| Auth usuário (candidatos)
*/
Route::get('/entrar/usuario', [UsuarioAuthController::class, 'showLogin'])->name('usuario.login');
Route::post('/entrar/usuario', [UsuarioAuthController::class, 'login'])->name('usuario.login.post');
Route::get('/cadastro/usuario', [UsuarioAuthController::class, 'showRegister'])->name('usuario.register');
Route::post('/cadastro/usuario', [UsuarioAuthController::class, 'register'])->name('usuario.register.post');
Route::post('/sair/usuario', [UsuarioAuthController::class, 'logout'])->name('usuario.logout');

/*
| Auth empresa
*/
Route::get('/entrar/empresa', [EmpresaAuthController::class, 'showLogin'])->name('empresa.login');
Route::post('/entrar/empresa', [EmpresaAuthController::class, 'login'])->name('empresa.login.post');
Route::get('/cadastro/empresa', [EmpresaAuthController::class, 'showRegister'])->name('empresa.register');
Route::post('/cadastro/empresa', [EmpresaAuthController::class, 'register'])->name('empresa.register.post');
Route::post('/sair/empresa', [EmpresaAuthController::class, 'logout'])->name('empresa.logout');

/*
|--------------------------------------------------------------------------
| Portal do Funcionário (admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.user.type:funcionario'])->group(function () {
    Route::get('/painel', [DashboardController::class, 'index'])->name('panel');
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('reservas/disponibilidade', [BookingController::class, 'availability'])->name('bookings.availability');
    Route::resource('reservas', BookingController::class)->names('bookings')->parameters(['reservas' => 'booking']);
    Route::delete('reservas', [BookingController::class, 'destroyMultiple'])->name('bookings.destroyMultiple');

    Route::get('atendimentos/disponibilidade', [AttendanceController::class, 'availability'])->name('attendances.availability');
    Route::resource('atendimentos', AttendanceController::class)->names('attendances')->parameters(['atendimentos' => 'attendance']);
    Route::patch('atendimentos/{attendance}/concluir', [AttendanceController::class, 'complete'])->name('attendances.complete');

    Route::resource('prestadores', ServiceProviderController::class)->names('services')->parameters(['prestadores' => 'service']);

    Route::resource('vagas', JobVacancyController::class)->names('job-vacancies')->parameters(['vagas' => 'jobVacancy']);
    Route::post('vagas/{jobVacancy}/notificar', [JobVacancyController::class, 'notify'])->name('job-vacancies.notify');

    Route::resource('candidatos', JobSeekerController::class)->names('job-seekers')->parameters(['candidatos' => 'jobSeeker']);
    Route::get('candidatos/{jobSeeker}/curriculo', [JobSeekerController::class, 'curriculo'])->name('job-seekers.curriculo');

    // Cidadãos: lista consolidada por CPF (candidatos + atendidos)
    Route::get('cidadaos', [CitizenController::class, 'index'])->name('citizens.index');
    Route::get('cidadaos/{uuid}', [CitizenController::class, 'show'])->name('citizens.show');

    Route::get('vagas/{jobVacancy}/candidatos', [JobApplicationController::class, 'applicants'])->name('job-vacancies.applicants');
    Route::patch('candidaturas/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('job-applications.status');

    Route::resource('eventos', EventController::class)->names('events')->parameters(['eventos' => 'event']);
    Route::post('eventos/{event}/participantes', [EventController::class, 'storeParticipant'])->name('events.participants.store');
    Route::put('eventos/{event}/participantes/{participant}', [EventController::class, 'updateParticipant'])->name('events.participants.update');
    Route::delete('eventos/{event}/participantes/{participant}', [EventController::class, 'destroyParticipant'])->name('events.participants.destroy');
    Route::get('eventos/{event}/pdf', [EventController::class, 'pdf'])->name('events.pdf');
    Route::patch('eventos/{event}/status', [EventController::class, 'updateStatus'])->name('events.status');
    Route::get('eventos/{event}/participantes/{participant}/certificado', [EventController::class, 'certificate'])->name('events.certificate');

    Route::resource('palestrantes', SpeakerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('speakers')->parameters(['palestrantes' => 'speaker']);
    Route::post('palestrantes/cadastro-rapido', [SpeakerController::class, 'quickStore'])->name('speakers.quick-store');

    Route::middleware(['can:admin-only'])->group(function () {
        Route::resource('equipe', UserController::class)->names('users')->parameters(['equipe' => 'user']);
        Route::get('auditoria', [AuditController::class, 'index'])->name('audit.index');
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
    Route::post('vagas/{jobVacancy}/candidatar', [JobApplicationController::class, 'store'])->name('job-vacancies.apply');
    Route::delete('vagas/{jobVacancy}/candidatar', [JobApplicationController::class, 'destroy'])->name('job-vacancies.unapply');

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
    Route::get('vagas/{jobVacancy}/candidatos', [JobApplicationController::class, 'applicants'])->name('job-vacancies.applicants');
    Route::patch('/portal/empresa/candidaturas/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('empresa.job-applications.status');

    // Perfil do candidato (somente-leitura para empresa)
    Route::get('/portal/empresa/candidatos/{jobSeeker}', [PortalEmpresaController::class, 'showCandidato'])->name('portal.empresa.candidato.show');
});
