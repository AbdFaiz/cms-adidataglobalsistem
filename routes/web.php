<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

// api email untuk dovecot
Route::post('/api-email', [EmailController::class, 'webhookFromDovecot']);

// Error routes
Route::view('/404', 'errors.404')->name('404');
Route::view('/500', 'errors.500')->name('500');

// Ganti Password
Route::get('/password/change', [PasswordController::class, 'showResetForm'])->name('password.change.form');
Route::post('/password/change', [PasswordController::class, 'changePassword'])->name('password.change');

Auth::routes();
Route::middleware(['auth', 'must_reset_password', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    // ->middleware('signed')
    ->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])
    // ->middleware('signed')
    ->name('profile');
    Route::put('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/transactions', [DashboardController::class, 'transactions'])
    // ->middleware('signed')
    ->name('transactions');
    Route::get('/map', [DashboardController::class, 'map'])
    // ->middleware('signed')
    ->name('map');

    // Role management
    Route::resource('roles', 'App\Http\Controllers\RoleController');
    // User management
    Route::resource('users', 'App\Http\Controllers\UserController');
    // Task management
    Route::resource('tasks', 'App\Http\Controllers\TaskController')->except((['show', 'edit', 'create']));
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    // Event management
    Route::get('/calendar', [EventController::class, 'index'])
    // ->middleware('signed')
    ->name('calendar');
    Route::resource('events', EventController::class)->except(['index', 'show', 'edit', 'create']);
    Route::get('/events/fetch', [EventController::class, 'fetchEvents']);

    Route::prefix('emails')->group(function () {
        Route::get('/', [EmailController::class, 'index'])
        // ->middleware('signed')
        ->name('email.index');
        // fetch
        // Route::post('/check-emails', [EmailController::class, 'fetchEmails'])->name('email.fetch');
        // compose
        Route::get('/compose', [EmailController::class, 'compose'])
        // ->middleware('signed')
        ->name('email.compose');
        
        // Single email actions
        Route::post('/{id}/read', [EmailController::class, 'markAsRead'])->name('emails.mark-read');
        Route::post('/{id}/unread', [EmailController::class, 'markAsUnread'])->name('emails.mark-unread');
        Route::post('/{id}/archive', [EmailController::class, 'archiveEmail'])->name('emails.archive');
        Route::post('/{id}/trash', [EmailController::class, 'moveToTrash'])->name('emails.trash');
        Route::post('/{id}/restore', [EmailController::class, 'restoreEmail'])->name('emails.restore');
        Route::post('/{id}/flag', [EmailController::class, 'toggleFlag'])->name('emails.flag');
        
        // Bulk actions
        Route::post('/bulk', [EmailController::class, 'bulkAction'])->name('emails.bulk');
        
        // Folder actions
        Route::post('/mark-all-read', [EmailController::class, 'markAllAsRead'])->name('emails.mark-all-read');
        Route::post('/empty-trash', [EmailController::class, 'emptyTrash'])->name('emails.empty-trash');

        // Other action
        Route::get('/{ticketNumber}', [EmailController::class, 'showReplyForm'])->name(name: 'email.detail');
        Route::post('/{ticketNumber}/reply', [EmailController::class, 'reply'])->name('email.reply');
        Route::get('/attachments/download/{attachment}', [EmailController::class, 'downloadAttachment'])
        ->name('email.attachment.download');
    });
    
    // Reset account password
    Route::put('/users/{user}/reset-password', [UserController::class, 'resetAccount'])->name('users.reset-password');
});

// Fallback untuk route yang tidak ditemukan
// Route::fallback(function () {
//     return response()->view('errors.404', [], 404);
// });
