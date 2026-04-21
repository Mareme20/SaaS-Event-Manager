<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Task;
use App\Models\Member;
use App\Http\Resources\TaskResource;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $event->load(['tasks.member']);
        $members = auth()->user()->members()->orderBy('name')->get();

        return Inertia::render('Tasks/Index', [
            'event' => $event,
            'tasks' => TaskResource::collection($event->tasks()->latest()->get()),
            'members' => MemberResource::collection($members),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,doing,done',
        ]);

        $event->tasks()->create($validated);

        return redirect()->back()->with('success', 'Tâche ajoutée');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'member_id' => 'nullable|exists:members,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,doing,done',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Tâche mise à jour');
    }

    /**
     * Update only the status of the task.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,doing,done',
        ]);

        $task->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Statut mis à jour');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back()->with('success', 'Tâche supprimée');
    }
}
