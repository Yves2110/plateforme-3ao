<?php

namespace App\Http\Controllers;

use App\Mail\ForumReplyNotificationMail;
use App\Models\ForumPoll;
use App\Models\ForumPollVote;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    const CATEGORIES = [
        'pratiques'   => 'Pratiques agroécologiques',
        'semences'    => 'Semences & Biodiversité',
        'politique'   => 'Politique & Plaidoyer',
        'marches'     => 'Marchés & Commercialisation',
        'formation'   => 'Formation & Éducation',
        'financement' => 'Financement & Projets',
    ];

    public function index()
    {
        $stats = [
            'threads' => ForumThread::validated()->count(),
            'replies' => ForumReply::where('is_validated', true)->count(),
            'members' => \App\Models\User::count(),
        ];

        $recentThreads = ForumThread::validated()
            ->with('author', 'replies')
            ->orderByDesc('last_reply_at')
            ->take(5)
            ->get();

        $pinnedThreads = ForumThread::validated()
            ->where('is_pinned', true)
            ->with('author')
            ->take(3)
            ->get();

        return view('communaute.index', compact('stats', 'recentThreads', 'pinnedThreads'));
    }

    public function category(string $category)
    {
        abort_unless(array_key_exists($category, self::CATEGORIES), 404);

        $threads = ForumThread::validated()
            ->byCategory($category)
            ->with('author', 'replies')
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_reply_at')
            ->paginate(20);

        $categoryName = self::CATEGORIES[$category];

        return view('communaute.category', compact('threads', 'category', 'categoryName'));
    }

    public function thread(string $category, string $thread)
    {
        $forumThread = ForumThread::validated()
            ->byCategory($category)
            ->where('slug', $thread)
            ->with('author', 'poll.votes')
            ->firstOrFail();

        $forumThread->increment('views');

        $replies = ForumReply::where('thread_id', $forumThread->id)
            ->where('is_validated', true)
            ->whereNull('parent_id')
            ->with('author', 'children.author')
            ->orderBy('created_at')
            ->paginate(15);

        $userVote = auth()->check()
            ? optional($forumThread->poll)->votes()->where('user_id', auth()->id())->first()
            : null;

        $categoryName = self::CATEGORIES[$category] ?? $category;

        return view('communaute.thread', compact('forumThread', 'replies', 'category', 'categoryName', 'userVote'));
    }

    public function create()
    {
        abort_unless(auth()->check(), 401);
        return view('communaute.create', ['categories' => self::CATEGORIES]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->check(), 401);

        $data = $request->validate([
            'title'          => 'required|string|min:5|max:200',
            'category'       => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'body'           => 'required|string|min:20',
            'poll_question'  => 'nullable|string|max:255',
            'poll_options'   => 'nullable|array|min:2|max:6',
            'poll_options.*' => 'required|string|max:100',
            'poll_closes_at' => 'nullable|date|after:today',
        ]);

        $thread = ForumThread::create([
            'title'        => $data['title'],
            'slug'         => Str::slug($data['title']) . '-' . Str::random(6),
            'category'     => $data['category'],
            'body'         => $data['body'],
            'user_id'      => auth()->id(),
            'is_validated' => true,
            'last_reply_at' => now(),
        ]);

        if (!empty($data['poll_question']) && !empty($data['poll_options'])) {
            ForumPoll::create([
                'thread_id'  => $thread->id,
                'question'   => $data['poll_question'],
                'options'    => $data['poll_options'],
                'closes_at'  => $data['poll_closes_at'] ?? null,
            ]);
        }

        return redirect()->route('communaute.thread', [$data['category'], $thread->slug])
            ->with('success', 'Discussion créée avec succès !');
    }

    public function reply(Request $request, string $category, string $thread)
    {
        abort_unless(auth()->check(), 401);

        $data = $request->validate([
            'body'      => 'required|string|min:5',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);

        $forumThread = ForumThread::validated()
            ->byCategory($category)
            ->where('slug', $thread)
            ->firstOrFail();

        abort_if($forumThread->is_locked, 403, 'Cette discussion est verrouillée.');

        $reply = ForumReply::create([
            'thread_id'    => $forumThread->id,
            'user_id'      => auth()->id(),
            'body'         => $data['body'],
            'parent_id'    => $data['parent_id'] ?? null,
            'is_validated' => true,
        ]);

        $forumThread->update(['last_reply_at' => now()]);

        $threadAuthor = $forumThread->author;
        if ($threadAuthor && $threadAuthor->id !== auth()->id()) {
            $reply->load('user');
            Mail::to($threadAuthor)->queue(
                new ForumReplyNotificationMail($forumThread, $reply, $threadAuthor)
            );
        }

        return back()->with('success', 'Réponse publiée !');
    }

    public function vote(Request $request, string $category, string $thread)
    {
        abort_unless(auth()->check(), 401);

        $data = $request->validate(['option_index' => 'required|integer|min:0']);

        $forumThread = ForumThread::validated()
            ->byCategory($category)
            ->where('slug', $thread)
            ->with('poll')
            ->firstOrFail();

        $poll = $forumThread->poll;
        abort_unless($poll, 404);

        $alreadyVoted = $poll->votes()->where('user_id', auth()->id())->exists();
        abort_if($alreadyVoted, 403, 'Vous avez déjà voté.');

        abort_unless(isset($poll->options[$data['option_index']]), 422);

        ForumPollVote::create([
            'poll_id'      => $poll->id,
            'user_id'      => auth()->id(),
            'option_index' => $data['option_index'],
        ]);

        return back()->with('success', 'Vote enregistré !');
    }

    public function moderate(Request $request, string $action, int $id)
    {
        abort_unless(auth()->user()?->hasPermissionTo('moderer-forum'), 403);

        match ($action) {
            'pin'    => ForumThread::findOrFail($id)->update(['is_pinned' => true]),
            'unpin'  => ForumThread::findOrFail($id)->update(['is_pinned' => false]),
            'lock'   => ForumThread::findOrFail($id)->update(['is_locked' => true]),
            'unlock' => ForumThread::findOrFail($id)->update(['is_locked' => false]),
            'delete' => ForumThread::findOrFail($id)->delete(),
            'delete-reply' => ForumReply::findOrFail($id)->delete(),
            default  => abort(422),
        };

        return back()->with('success', 'Action de modération effectuée.');
    }
}
