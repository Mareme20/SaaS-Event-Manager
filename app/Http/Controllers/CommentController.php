<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
        ]);

        // Basic security check: ensure the type is allowed
        if ($validated['commentable_type'] !== Task::class) {
            return back()->withErrors(['message' => 'Type non supporté']);
        }

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'commentable_id' => $validated['commentable_id'],
            'commentable_type' => $validated['commentable_type'],
        ]);

        // Notify the organizer (user)
        auth()->user()->notify(new \App\Notifications\NewCommentNotification($comment));

        return redirect()->back()->with('success', 'Commentaire ajouté');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Commentaire supprimé');
    }
}
