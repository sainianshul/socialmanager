<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AutoDmRule;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AutoDmController extends Controller
{
    /**
     * Display Auto DM rules & automation settings
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Only Facebook and Instagram accounts support comment-to-DM
        $accountsQuery = Account::whereIn('account_type', [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM])->orderBy('name');
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->get();

        // Base query for rules
        $rulesQuery = AutoDmRule::with(['account', 'post'])->latest();
        if (!$user->isAdmin()) {
            $rulesQuery->where('user_id', $user->id);
        }

        // Filter by Account if requested
        if ($request->filled('account_id')) {
            $rulesQuery->where('account_id', $request->account_id);
        }

        // Filter by Status if requested
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $rulesQuery->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $rulesQuery->where('is_active', false);
            }
        }

        // Search by Rule Name or Keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $rulesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%")
                  ->orWhere('dm_message', 'like', "%{$search}%");
            });
        }

        $rules = $rulesQuery->paginate(15)->withQueryString();

        return view('admin.auto_dm.index', compact('rules', 'accounts'));
    }

    /**
     * Show form to create a new Auto DM rule
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        $accountsQuery = Account::whereIn('account_type', [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM])->orderBy('name');
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->get();

        $selectedAccountId = (int) $request->get('account_id', old('account_id', $accounts->first()?->id));
        $posts = collect();
        if ($selectedAccountId) {
            $posts = Post::where('account_id', $selectedAccountId)->latest('published_at')->limit(50)->get();
        }

        return view('admin.auto_dm.create', compact('accounts', 'selectedAccountId', 'posts'));
    }

    /**
     * Store a newly created Auto DM rule
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:120'],
            'target_type' => ['required', 'in:all,specific'],
            'post_id' => ['nullable', 'exists:posts,id'],
            'keywords' => ['required', 'string'],
            'match_type' => ['required', 'in:contains,exact'],
            'dm_message' => ['required', 'string', 'max:1000'],
            'public_reply' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable'],
        ]);

        $account = Account::findOrFail($validated['account_id']);
        if (!$user->isAdmin() && $account->user_id !== $user->id) {
            abort(403, 'Unauthorized account selection.');
        }

        $postId = null;
        if ($validated['target_type'] === 'specific' && !empty($request->post_id)) {
            $post = Post::where('account_id', $account->id)->findOrFail($request->post_id);
            $postId = $post->id;
        }

        $keywords = $this->parseKeywords($validated['keywords']);
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['keywords' => 'Please provide at least one valid trigger keyword.'])->withInput();
        }

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'post_id' => $postId,
            'name' => $validated['name'],
            'keywords' => $keywords,
            'match_type' => $validated['match_type'],
            'dm_message' => $validated['dm_message'],
            'public_reply' => !empty($validated['public_reply']) ? $validated['public_reply'] : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Auto DM rule created successfully.',
                'rule' => $rule->load(['account', 'post']),
            ]);
        }

        return redirect()->route('admin.auto-dm.index')->with('success', "Auto DM rule '{$rule->name}' created successfully!");
    }

    /**
     * Show form to edit an existing Auto DM rule (or return JSON if requested)
     */
    public function edit(Request $request, AutoDmRule $autoDmRule)
    {
        $this->authorizeRule($autoDmRule);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'rule' => [
                    'id' => $autoDmRule->id,
                    'name' => $autoDmRule->name,
                    'account_id' => $autoDmRule->account_id,
                    'post_id' => $autoDmRule->post_id,
                    'target_type' => $autoDmRule->post_id ? 'specific' : 'all',
                    'keywords_string' => is_array($autoDmRule->keywords) ? implode(', ', $autoDmRule->keywords) : '',
                    'match_type' => $autoDmRule->match_type,
                    'dm_message' => $autoDmRule->dm_message,
                    'public_reply' => $autoDmRule->public_reply ?? '',
                    'is_active' => (bool) $autoDmRule->is_active,
                ]
            ]);
        }

        $user = auth()->user();
        $accountsQuery = Account::whereIn('account_type', [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM])->orderBy('name');
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->get();

        $posts = Post::where('account_id', $autoDmRule->account_id)->latest('published_at')->limit(50)->get();

        return view('admin.auto_dm.edit', compact('autoDmRule', 'accounts', 'posts'));
    }

    /**
     * Update an existing Auto DM rule
     */
    public function update(Request $request, AutoDmRule $autoDmRule)
    {
        $this->authorizeRule($autoDmRule);
        $user = auth()->user();

        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:120'],
            'target_type' => ['required', 'in:all,specific'],
            'post_id' => ['nullable', 'exists:posts,id'],
            'keywords' => ['required', 'string'],
            'match_type' => ['required', 'in:contains,exact'],
            'dm_message' => ['required', 'string', 'max:1000'],
            'public_reply' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable'],
        ]);

        $account = Account::findOrFail($validated['account_id']);
        if (!$user->isAdmin() && $account->user_id !== $user->id) {
            abort(403, 'Unauthorized account selection.');
        }

        $postId = null;
        if ($validated['target_type'] === 'specific' && !empty($request->post_id)) {
            $post = Post::where('account_id', $account->id)->findOrFail($request->post_id);
            $postId = $post->id;
        }

        $keywords = $this->parseKeywords($validated['keywords']);
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['keywords' => 'Please provide at least one valid trigger keyword.'])->withInput();
        }

        $autoDmRule->update([
            'account_id' => $account->id,
            'post_id' => $postId,
            'name' => $validated['name'],
            'keywords' => $keywords,
            'match_type' => $validated['match_type'],
            'dm_message' => $validated['dm_message'],
            'public_reply' => !empty($validated['public_reply']) ? $validated['public_reply'] : null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Auto DM rule updated successfully.',
                'rule' => $autoDmRule->fresh()->load(['account', 'post']),
            ]);
        }

        return redirect()->route('admin.auto-dm.index')->with('success', "Auto DM rule '{$autoDmRule->name}' updated successfully!");
    }

    /**
     * Toggle active status of an Auto DM rule
     */
    public function toggle(Request $request, AutoDmRule $autoDmRule)
    {
        $this->authorizeRule($autoDmRule);

        $autoDmRule->is_active = !$autoDmRule->is_active;
        $autoDmRule->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $autoDmRule->is_active,
                'message' => $autoDmRule->is_active ? 'Rule activated.' : 'Rule paused.',
            ]);
        }

        return redirect()->back()->with('success', $autoDmRule->is_active ? 'Rule activated.' : 'Rule paused.');
    }

    /**
     * Delete an Auto DM rule
     */
    public function destroy(AutoDmRule $autoDmRule)
    {
        $this->authorizeRule($autoDmRule);

        $name = $autoDmRule->name;
        $autoDmRule->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Auto DM rule '{$name}' deleted.",
            ]);
        }

        return redirect()->route('admin.auto-dm.index')->with('success', "Auto DM rule '{$name}' deleted successfully.");
    }

    /**
     * Fetch recent posts for an account for the post selector in modal
     */
    public function getPosts(Request $request, Account $account)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $account->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        $posts = Post::where('account_id', $account->id)
            ->latest('published_at')
            ->limit(50)
            ->get(['id', 'meta_post_id', 'message', 'media_type', 'published_at'])
            ->map(function ($post) {
                $caption = $post->message ? Str::limit(trim($post->message), 75) : 'Post #' . $post->id;
                $date = $post->published_at ? $post->published_at->format('M d, Y') : 'Recent';
                return [
                    'id' => $post->id,
                    'title' => "[{$date}] {$caption}",
                ];
            });

        return response()->json(['posts' => $posts]);
    }

    /**
     * Ensure current user has permission to access the rule
     */
    private function authorizeRule(AutoDmRule $rule): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $rule->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this Auto DM rule.');
        }
    }

    /**
     * Parse keywords from string or array into normalized lowercase array
     */
    private function parseKeywords($input): array
    {
        if (is_array($input)) {
            $raw = $input;
        } else {
            $raw = preg_split('/[,\r\n]+/', (string) $input);
        }

        $keywords = [];
        foreach ($raw as $item) {
            $trimmed = mb_strtolower(trim($item));
            if ($trimmed !== '' && !in_array($trimmed, $keywords, true)) {
                $keywords[] = $trimmed;
            }
        }

        return $keywords;
    }
}
