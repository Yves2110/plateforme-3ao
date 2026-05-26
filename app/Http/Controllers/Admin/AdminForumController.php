<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;

class AdminForumController extends Controller
{
    public function index(Request $request)
    {
        $threads = ForumThread::with('author', 'replies')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->validated !== null, fn($q) => $q->where('is_validated', (bool)$request->validated))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.forum.index', compact('threads'));
    }

    public function validateThread(ForumThread $thread)
    {
        $thread->update(['is_validated' => true]);
        return back()->with('success', 'Discussion validée.');
    }

    public function destroyThread(ForumThread $thread)
    {
        $thread->delete();
        return back()->with('success', 'Discussion supprimée.');
    }

    public function destroyReply(ForumReply $reply)
    {
        $reply->delete();
        return back()->with('success', 'Réponse supprimée.');
    }
}
