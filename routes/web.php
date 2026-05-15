<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::get('/setup-inicial', [RoleController::class, 'scriptConfiguration']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('bookings/availability', [BookingController::class, 'availability'])->name('bookings.availability');
    Route::resource('bookings', BookingController::class);
    Route::delete('bookings', [BookingController::class, 'destroyMultiple'])->name('bookings.destroyMultiple');
    Route::resource('attendances', AttendanceController::class);
    Route::resource('services', ServiceProviderController::class);
    Route::resource('job-vacancies', JobVacancyController::class)->parameters([
        'job-vacancies' => 'jobVacancy',
    ]);
    Route::post('job-vacancies/{jobVacancy}/notify', [JobVacancyController::class, 'notify'])->name('job-vacancies.notify');
    Route::resource('job-seekers', JobSeekerController::class)->parameters([
        'job-seekers' => 'jobSeeker',
    ]);
    Route::resource('events', EventController::class);
    Route::post('events/{event}/participants', [EventController::class, 'storeParticipant'])->name('events.participants.store');
    Route::put('events/{event}/participants/{participant}', [EventController::class, 'updateParticipant'])->name('events.participants.update');
    Route::delete('events/{event}/participants/{participant}', [EventController::class, 'destroyParticipant'])->name('events.participants.destroy');
    Route::get('events/{event}/pdf', [EventController::class, 'pdf'])->name('events.pdf');
    Route::resource('speakers', SpeakerController::class)->only(['index', 'create', 'store']);

    Route::middleware(['can:admin-only'])->group(function () {
        Route::resource('users', UserController::class);
    });
});