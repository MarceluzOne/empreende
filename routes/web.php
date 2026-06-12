<?php

use App\Http\Controllers\Auth\EmpresaAuthController;
use App\Http\Controllers\Auth\UsuarioAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Público
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/agendamento/disponibilidade', [PublicAttendanceController::class, 'availability'])->name('public.attendance.availability');
Route::post('/agendamento', [PublicAttendanceController::class, 'store'])->name('public.attendance.store');
Route::get('/contato', [LandingController::class, 'contato'])->name('contato');
Route::get('/cursos', [LandingController::class, 'cursos'])->name('cursos');
Route::get('/servicos', [LandingController::class, 'servicos'])->name('servicos');
Route::get('/empresas-locais', [LandingController::class, 'empresasLocais'])->name('empresas-locais');

// Auth funcionário
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Reset de senha
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Setup
Route::get('/setup-inicial', [RoleController::class, 'scriptConfiguration']);

// Auth usuário (candidatos)
Route::get('/login/usuario', [UsuarioAuthController::class, 'showLogin'])->name('usuario.login');
Route::post('/login/usuario', [UsuarioAuthController::class, 'login'])->name('usuario.login.post');
Route::get('/cadastro/usuario', [UsuarioAuthController::class, 'showRegister'])->name('usuario.register');
Route::post('/cadastro/usuario', [UsuarioAuthController::class, 'register'])->name('usuario.register.post');
Route::post('/logout/usuario', [UsuarioAuthController::class, 'logout'])->name('usuario.logout');

// Auth empresa
Route::get('/login/empresa', [EmpresaAuthController::class, 'showLogin'])->name('empresa.login');
Route::post('/login/empresa', [EmpresaAuthController::class, 'login'])->name('empresa.login.post');
Route::get('/cadastro/empresa', [EmpresaAuthController::class, 'showRegister'])->name('empresa.register');
Route::post('/cadastro/empresa', [EmpresaAuthController::class, 'register'])->name('empresa.register.post');
Route::post('/logout/empresa', [EmpresaAuthController::class, 'logout'])->name('empresa.logout');
