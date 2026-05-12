<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $events = App\Models\Event::with(['media', 'organizer'])->latest()->take(6)->get();
    
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'events' => App\Http\Resources\EventResource::collection($events),
    ]);
})->name('welcome');

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $events = $request->user()->events()->with(['expenses', 'contributions'])->get();
        
        $stats = [
            'total_events' => $events->count(),
            'total_budget' => $events->sum('budget_estimated'),
            'total_spent' => $events->map(fn($e) => $e->budget_real)->sum(),
            'total_contributions' => $events->map(fn($e) => $e->total_contributions)->sum(),
            'upcoming' => $request->user()->events()->where('date', '>=', now())->orderBy('date')->take(3)->get(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    })->name('dashboard');

    // Routes Événements
    Route::get('events/calendar', [EventController::class, 'calendar'])->name('events.calendar')->middleware('subscribed');
    Route::resource('events', EventController::class);
    Route::get('events/{event}/qrcode', [EventController::class, 'qrCode'])->name('events.qrcode');
    Route::get('events/{event}/report', [EventController::class, 'exportPdf'])->name('events.report');
    Route::get('events/{event}/analytics', [EventController::class, 'reports'])->name('events.analytics')->middleware('subscribed');
    Route::get('events/{event}/export-csv', [EventController::class, 'exportCsv'])->name('events.export-csv');

    // Routes Membres
    Route::resource('members', MemberController::class);

    // Routes Tâches
    Route::get('events/{event}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('events/{event}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Routes Dépenses
    Route::get('events/{event}/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('events/{event}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::patch('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Routes Cotisations
    Route::get('events/{event}/contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::post('events/{event}/contributions', [ContributionController::class, 'store'])->name('contributions.store');
    Route::patch('contributions/{contribution}', [ContributionController::class, 'update'])->name('contributions.update');
    Route::delete('contributions/{contribution}', [ContributionController::class, 'destroy'])->name('contributions.destroy');

    // Routes Paiement en ligne
    Route::post('events/{event}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::get('payments/success', [PaymentController::class, 'success'])->name('payments.success');

    // Routes Médias
    Route::post('events/{event}/media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Routes Commentaires
    Route::post('comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    // Routes Notifications
    Route::post('notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllAsRead');

// Swagger API Documentation
});

// Route publique pour l'affichage de l'événement (QR Code Target)
Route::get('/shared/event/{slug}', [EventController::class, 'showBySlug'])->name('events.public');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
