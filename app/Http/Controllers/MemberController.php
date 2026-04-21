<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = auth()->user()->members()->latest()->get();
        
        return Inertia::render('Members/Index', [
            'members' => MemberResource::collection($members)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->members()->create($validated);

        return redirect()->back()->with('success', 'Membre ajouté avec succès');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        if ($member->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $member->update($validated);

        return redirect()->back()->with('success', 'Membre mis à jour');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        if ($member->user_id !== auth()->id()) {
            abort(403);
        }

        $member->delete();

        return redirect()->back()->with('success', 'Membre supprimé');
    }
}
