<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ParticipantController as AdminParticipantController;
use App\Http\Controllers\Admin\ConferenceDayController as AdminConferenceDayController;
use App\Http\Controllers\Usher\DashboardController as UsherDashboardController;
use App\Http\Controllers\Usher\RegisterController as UsherRegisterController;
use App\Http\Controllers\Usher\CheckInController as UsherCheckInController;
use App\Http\Controllers\Usher\PaymentController as UsherPaymentController;
use App\Http\Controllers\Usher\TicketNotificationController as UsherTicketNotificationController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Usher\TicketController;
use App\Http\Controllers\Usher\MealController;
use App\Http\Controllers\Usher\RegistrationController;

// Redirect root to login
Route::redirect('/', 'login');

// Authentication Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// Password Change Routes
Route::middleware(['auth'])->group(function () {
    // Admin password change
    Route::get('admin/password/change', [PasswordChangeController::class, 'showChangeForm'])
        ->name('admin.password.change');
    Route::post('admin/password/change', [PasswordChangeController::class, 'changePassword'])
        ->name('admin.password.update');
    
    // Usher password change
    Route::get('usher/password/change', [PasswordChangeController::class, 'showChangeForm'])
        ->name('usher.password.change');
    Route::post('usher/password/change', [PasswordChangeController::class, 'changePassword'])
        ->name('usher.password.update');
});

// Admin Routes
Route::middleware(['auth', 'role:admin', 'first.login'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User management routes
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.delete');
    
    // Participant management routes
    Route::get('/participants', [AdminParticipantController::class, 'index'])->name('participants');
    Route::get('/participants/{category}', [AdminParticipantController::class, 'category'])->name('participants.category');
    Route::get('/participants/{category}/create', [AdminParticipantController::class, 'create'])->name('participants.create');
    Route::post('/participants/{category}', [AdminParticipantController::class, 'store'])->name('participants.store');
    Route::get('/participants/{category}/import', [AdminParticipantController::class, 'showImport'])->name('participants.import');
    Route::post('/participants/{category}/import', [AdminParticipantController::class, 'import'])->name('participants.import.process');
    
    // Conference days management routes
    Route::get('/conference-days', [AdminConferenceDayController::class, 'index'])->name('conference-days.index');
    Route::get('/conference-days/create', [AdminConferenceDayController::class, 'create'])->name('conference-days.create');
    Route::post('/conference-days', [AdminConferenceDayController::class, 'store'])->name('conference-days.store');
    Route::get('/conference-days/{id}/edit', [AdminConferenceDayController::class, 'edit'])->name('conference-days.edit');
    Route::put('/conference-days/{id}', [AdminConferenceDayController::class, 'update'])->name('conference-days.update');
    Route::delete('/conference-days/{id}', [AdminConferenceDayController::class, 'destroy'])->name('conference-days.destroy');
});

// Usher Routes
Route::middleware(['auth', 'role:usher', 'first.login'])->prefix('usher')->name('usher.')->group(function () {
    Route::get('/dashboard', [UsherDashboardController::class, 'index'])->name('dashboard');
    
    // Registration routes - Multi-step process
    Route::get('/registration', [UsherRegisterController::class, 'index'])->name('register');
    Route::get('/registration/roles', [UsherRegisterController::class, 'getRolesByCategory'])->name('registration.roles');
    
    // Multi-step registration routes
    Route::get('/registration/step1', [UsherRegisterController::class, 'showStep1'])->name('registration.step1');
    Route::post('/registration/step1', [UsherRegisterController::class, 'processStep1'])->name('registration.process_step1');
    Route::get('/registration/step2', [UsherRegisterController::class, 'showStep2'])->name('registration.step2');
    Route::post('/registration/step2', [UsherRegisterController::class, 'processStep2'])->name('registration.process_step2');
    Route::post('/registration/check-existing', [UsherRegisterController::class, 'checkExistingParticipant'])->name('registration.check-existing');
    
    // Payment form for general category
    Route::get('/registration/payment', [UsherPaymentController::class, 'showPaymentForm'])->name('registration.payment');
    Route::post('/registration/payment', [UsherPaymentController::class, 'processPayment'])->name('registration.process_payment');
    
    Route::get('/registration/step3', [UsherRegisterController::class, 'showStep3'])->name('registration.step3');
    Route::post('/registration/step3', [UsherRegisterController::class, 'processStep3'])->name('registration.process_step3');
    Route::get('/registration/step4', [UsherRegisterController::class, 'showStep4'])->name('registration.step4');
    Route::post('/registration/step4', [UsherRegisterController::class, 'processStep4'])->name('registration.process_step4');
    Route::get('/registration/step5', [UsherRegisterController::class, 'showStep5'])->name('registration.step5');
    Route::post('/registration/step5', [UsherRegisterController::class, 'processStep5'])->name('registration.process_step5');
    Route::post('/registration/complete', [UsherRegisterController::class, 'complete'])->name('registration.complete');
    
    // Legacy registration route - kept for backward compatibility
    Route::post('/registration', [UsherRegisterController::class, 'store'])->name('registration.store');
    
    Route::get('/registration/ticket/{ticket}', [UsherRegisterController::class, 'showTicket'])->name('registration.ticket');
    Route::get('/my-registrations', [UsherRegisterController::class, 'myRegistrations'])->name('registration.my-registrations');
    
    // Check-in routes
    Route::get('/check-in', [UsherCheckInController::class, 'index'])->name('check-in');
    Route::post('/check-in', [UsherCheckInController::class, 'checkIn'])->name('check-in.process');
    Route::get('/check-in/search', [UsherCheckInController::class, 'search'])->name('check-in.search');
    Route::get('/check-ins', [UsherCheckInController::class, 'viewCheckIns'])->name('check-ins');
    
    // Ticket search routes
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::post('/tickets/search', [TicketController::class, 'search'])->name('tickets.search');
    
    // Meal serving routes
    Route::get('/meals', [MealController::class, 'index'])->name('meals');
    Route::post('/meals/select', [MealController::class, 'selectMeal'])->name('meals.select');
    Route::post('/meals/serve', [MealController::class, 'serve'])->name('meals.serve');
    Route::get('/meals/stats', [MealController::class, 'stats'])->name('meals.stats');
    Route::get('/meals/search-participants', [MealController::class, 'searchParticipants'])->name('meals.search-participants');
    
    // Participant details
    Route::get('/participant/{id}/details', [UsherRegisterController::class, 'getParticipantDetails'])->name('participant.details');
    Route::get('/participant/{id}/view', [UsherRegisterController::class, 'viewParticipant'])->name('participant.view');
    
    // Ticket notification routes
    Route::post('/ticket/{id}/send-email', [UsherTicketNotificationController::class, 'sendEmail'])->name('ticket.send-email');
    Route::post('/ticket/{id}/send-sms', [UsherTicketNotificationController::class, 'sendSms'])->name('ticket.send-sms');
    Route::post('/check-in/{participantId}/send-ticket', [UsherTicketNotificationController::class, 'sendAfterCheckIn'])->name('check-in.send-ticket');
    // Backend PDF and print routes
    Route::get('/ticket/{id}/download-pdf', [UsherTicketNotificationController::class, 'downloadPdf'])->name('ticket.download-pdf');
    Route::get('/ticket/{id}/print-view', [UsherTicketNotificationController::class, 'printView'])->name('ticket.print-view');

    // Additional Day Payment Routes
    Route::get('/registration/additional-day-payment', [RegistrationController::class, 'showAdditionalDayPayment'])
        ->name('registration.additional_day_payment');
    Route::post('/registration/process-additional-day-payment', [RegistrationController::class, 'processAdditionalDayPayment'])
        ->name('registration.process_additional_day_payment');
});
