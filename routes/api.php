<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\PaymentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API Événements
    Route::apiResource('events', EventController::class);
    
    // API Dépenses liées à un événement
    Route::get('events/{event}/expenses', [ExpenseController::class, 'index']);
    Route::post('events/{event}/expenses', [ExpenseController::class, 'store']);

    // API Cotisations liées à un événement
    Route::get('events/{event}/contributions', [ContributionController::class, 'index']);
    Route::post('events/{event}/contributions', [ContributionController::class, 'store']);
});

// Route publique pour le Webhook de paiement
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

// Route publique pour l'accès par lien/QR code (Slug)
Route::get('/events/slug/{slug}', [EventController::class, 'showBySlug']);
