<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
