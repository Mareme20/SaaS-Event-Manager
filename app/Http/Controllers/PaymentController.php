<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Create a payment session.
     */
    public function checkout(Request $request, Event $event)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:100',
        ]);

        $member = Member::find($validated['member_id']);

        // 1. Enregistrer la tentative de paiement localement
        $payment = Payment::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'amount' => $validated['amount'],
            'currency' => $event->currency,
            'status' => 'pending',
        ]);

        // 2. Simuler l'appel à une API de paiement (ex: Stripe/FedaPay)
        // Normalement ici on génère un URL de redirection
        $external_payment_url = "https://checkout.stripe.com/pay/" . uniqid(); 
        
        // On simule l'external_id reçu de l'API
        $payment->update(['external_id' => 'tr_' . uniqid()]);

        return response()->json([
            'url' => $external_payment_url
        ]);
    }

    /**
     * Success page after payment.
     */
    public function success(Request $request)
    {
        return Inertia::render('Payments/Success', [
            'message' => 'Merci ! Votre cotisation a été enregistrée avec succès.'
        ]);
    }

    /**
     * Webhook : Cette méthode est appelée par Stripe/FedaPay en arrière-plan.
     */
    public function webhook(Request $request)
    {
        // Dans la réalité, on vérifie la signature du webhook ici
        $externalId = $request->input('id'); // ID de la transaction
        $status = $request->input('status'); // approved, captured, etc.

        $payment = Payment::where('external_id', $externalId)->first();

        if ($payment && $status === 'success' && $payment->status !== 'completed') {
            // 1. Marquer le paiement comme complété
            $payment->update(['status' => 'completed']);

            // 2. Créer automatiquement la cotisation
            Contribution::create([
                'event_id' => $payment->event_id,
                'member_id' => $payment->member_id,
                'amount' => $payment->amount,
                'date' => now(),
                'description' => "Paiement en ligne (Réf: {$externalId})",
            ]);

            return response()->json(['message' => 'Contribution created']);
        }

        return response()->json(['message' => 'Payment not found or already processed']);
    }
}
