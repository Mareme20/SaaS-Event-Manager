<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Contribution;
use App\Models\Member;
use App\Http\Resources\ContributionResource;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $event->load(['contributions.member']);
        $members = auth()->user()->members()->orderBy('name')->get();

        return Inertia::render('Contributions/Index', [
            'event' => $event,
            'contributions' => ContributionResource::collection($event->contributions),
            'members' => MemberResource::collection($members),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $event->contributions()->create($validated);

        return redirect()->back()->with('success', 'Cotisation enregistrée avec succès');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contribution $contribution)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $contribution->update($validated);

        return redirect()->back()->with('success', 'Cotisation mise à jour');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->back()->with('success', 'Cotisation supprimée');
    }
}
