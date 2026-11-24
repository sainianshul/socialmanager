<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Users\UserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Account;
use App\Models\ScheduledPost;
use App\Models\SystemActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.index');
    }

    public function data(UserDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        $data['role'] = $data['role'] ?? User::ROLE_USER;
        $data['created_by'] = auth()->id() ?? User::CREATED_BY_ADMIN;
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Optional: If no password is provided, generate a random one
            $data['password'] = Hash::make(Str::random(12));
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $accountsCount = Account::where('user_id', $user->id)->count();
        $postsCount = ScheduledPost::where('user_id', $user->id)->count();
        $activityCount = SystemActivityLog::where('user_id', $user->id)->count();
        $devicesCount = $user->tokens()->count();

        return view('admin.users.show', compact('user', 'accountsCount', 'postsCount', 'activityCount', 'devicesCount'));
    }

    /**
     * AJAX DataTable for user connected social accounts
     */
    public function accountsData(User $user, Request $request)
    {
        $query = Account::where('user_id', $user->id);

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('channel', function ($acc) {
                $avatar = $acc->profile_pic_url
                    ? '<img src="' . e($acc->profile_pic_url) . '" class="avatar avatar-sm rounded-circle me-2" onerror="this.src=\'https://ui-avatars.com/api/?name=' . urlencode($acc->name) . '\'">'
                    : '<span class="avatar avatar-sm rounded-circle bg-primary-lt me-2">' . strtoupper(substr($acc->name, 0, 1)) . '</span>';
                
                $badge = match((int) $acc->account_type) {
                    Account::TYPE_FACEBOOK  => '<span class="badge bg-facebook-lt ms-2"><i class="ti ti-brand-facebook me-1"></i>Facebook</span>',
                    Account::TYPE_INSTAGRAM => '<span class="badge bg-pink-lt ms-2"><i class="ti ti-brand-instagram me-1"></i>Instagram</span>',
                    Account::TYPE_YOUTUBE   => '<span class="badge bg-danger-lt ms-2"><i class="ti ti-brand-youtube me-1"></i>YouTube</span>',
                    default                 => '',
                };

                return '
                    <div class="d-flex align-items-center">
                        ' . $avatar . '
                        <div>
                            <div class="fw-bold">' . e($acc->name) . ' ' . $badge . '</div>
                            <div class="text-secondary small">' . e($acc->username ? '@' . $acc->username : ($acc->category ?? 'Social Account')) . '</div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('followers', function ($acc) {
                return '<span class="fw-bold">' . number_format($acc->followers_count ?? 0) . '</span>';
            })
            ->addColumn('status', function ($acc) {
                if ($acc->is_active) {
                    return '<span class="badge border border-success bg-transparent text-success"><i class="ti ti-circle-filled text-success me-1" style="font-size: 7px;"></i>Connected</span>';
                }
                return '<span class="badge border border-secondary bg-transparent text-secondary">Disconnected</span>';
            })
            ->addColumn('token_status', function ($acc) {
                if ($acc->token_expires_at) {
                    $isExpired = $acc->token_expires_at->isPast();
                    if ($isExpired) {
                        return '<span class="badge border border-danger bg-transparent text-danger"><i class="ti ti-x me-1"></i>Expired</span>';
                    }
                    return '<span class="badge border border-info bg-transparent text-info" title="' . $acc->token_expires_at->format('d M Y, h:i A') . '">Valid (' . $acc->token_expires_at->diffForHumans() . ')</span>';
                }
                return '<span class="badge border border-success bg-transparent text-success"><i class="ti ti-infinity me-1"></i>Long-Lived</span>';
            })
            ->addColumn('last_synced', function ($acc) {
                if ($acc->last_synced_at) {
                    return '<div class="small">' . $acc->last_synced_at->diffForHumans() . '</div>';
                }
                return '<span class="text-muted small">Never</span>';
            })
            ->rawColumns(['channel', 'followers', 'status', 'token_status', 'last_synced'])
            ->toJson();
    }

    /**
     * AJAX DataTable for user posts and scheduled content
     */
    public function postsData(User $user, Request $request)
    {
        $query = ScheduledPost::with('account')->where('user_id', $user->id);

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('platform', function ($post) {
                return match((int) $post->platform) {
                    Account::TYPE_FACEBOOK  => '<span class="badge bg-facebook-lt"><i class="ti ti-brand-facebook me-1"></i>Facebook</span>',
                    Account::TYPE_INSTAGRAM => '<span class="badge bg-pink-lt"><i class="ti ti-brand-instagram me-1"></i>Instagram</span>',
                    Account::TYPE_YOUTUBE   => '<span class="badge bg-danger-lt"><i class="ti ti-brand-youtube me-1"></i>YouTube</span>',
                    default                 => '<span class="badge bg-secondary-lt">Other</span>',
                };
            })
            ->addColumn('media', function ($post) {
                $thumb = $post->thumbnail_url ?: $post->first_media_url;
                if ($thumb) {
                    return '<a href="' . e($thumb) . '" target="_blank"><img src="' . e($thumb) . '" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.style.display=\'none\'"></a>';
                }
                return '<span class="avatar avatar-sm bg-secondary-lt rounded"><i class="ti ti-photo"></i></span>';
            })
            ->addColumn('content_preview', function ($post) {
                $title = $post->title ? '<div class="fw-bold text-truncate" style="max-width: 250px;">' . e($post->title) . '</div>' : '';
                $desc = e(\Illuminate\Support\Str::limit($post->description ?: $post->content, 50));
                $channel = $post->account ? '<div class="text-muted small mt-1"><i class="ti ti-device-tv me-1"></i>' . e($post->account->name) . '</div>' : '';
                return $title . '<div class="small text-secondary text-truncate" style="max-width: 250px;">' . ($desc ?: 'No content') . '</div>' . $channel;
            })
            ->addColumn('type', function ($post) {
                return '<span class="badge border border-secondary bg-transparent text-secondary">' . e(ucfirst($post->post_type ?? 'Post')) . '</span>';
            })
            ->editColumn('status', function ($post) {
                return match((int) $post->status) {
                    ScheduledPost::STATUS_PUBLISHED  => '<span class="badge border border-success bg-transparent text-success"><i class="ti ti-check me-1"></i>Published</span>',
                    ScheduledPost::STATUS_SCHEDULED  => '<span class="badge border border-azure bg-transparent text-azure"><i class="ti ti-clock me-1"></i>Scheduled</span>',
                    ScheduledPost::STATUS_DRAFT      => '<span class="badge border border-secondary bg-transparent text-secondary"><i class="ti ti-file me-1"></i>Draft</span>',
                    ScheduledPost::STATUS_FAILED     => '<span class="badge border border-danger bg-transparent text-danger"><i class="ti ti-alert-triangle me-1"></i>Failed</span>',
                    ScheduledPost::STATUS_PUBLISHING => '<span class="badge border border-warning bg-transparent text-warning"><i class="ti ti-loader me-1"></i>Publishing</span>',
                    default                          => '<span class="badge border border-secondary bg-transparent text-secondary">Unknown</span>',
                };
            })
            ->addColumn('time', function ($post) {
                $time = $post->published_at ?? $post->scheduled_at ?? $post->created_at;
                return '
                    <div class="small">' . ($time ? $time->diffForHumans() : '—') . '</div>
                    <div class="text-secondary" style="font-size: 0.72rem;">' . ($time ? $time->format('d M Y, h:i A') : '—') . '</div>
                ';
            })
            ->rawColumns(['platform', 'media', 'content_preview', 'type', 'status', 'time'])
            ->toJson();
    }

    /**
     * AJAX DataTable for user system activity logs with server-side pagination
     */
    public function activityData(User $user, Request $request)
    {
        $query = SystemActivityLog::with('account')->where('user_id', $user->id);

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($log) {
                return '
                    <div class="small">' . $log->created_at->diffForHumans() . '</div>
                    <div class="text-secondary" style="font-size: 0.72rem;">' . $log->created_at->format('d M Y, h:i A') . '</div>
                ';
            })
            ->editColumn('event_type', function ($log) {
                $badgeColor = match($log->event_type) {
                    'account_sync'    => 'primary',
                    'post_publish'    => 'success',
                    'canva'           => 'cyan',
                    'auto_dm'         => 'purple',
                    'comment_process' => 'teal',
                    'webhook'         => 'azure',
                    'queue_job'       => 'warning',
                    default           => 'secondary',
                };
                $label = ucwords(str_replace('_', ' ', $log->event_type));
                return '<span class="badge border border-' . $badgeColor . ' bg-transparent text-' . $badgeColor . '">' . $label . '</span>';
            })
            ->editColumn('action', function ($log) {
                $html = '<div class="fw-semibold text-truncate" style="max-width: 280px;" title="' . e($log->action) . '">' . e($log->action) . '</div>';
                if ($log->status === 'failed' && $log->response) {
                    $html .= '<div class="text-danger small text-truncate" style="max-width: 280px;"><i class="ti ti-alert-circle me-1"></i>' . e(\Illuminate\Support\Str::limit(strip_tags($log->response), 60)) . '</div>';
                }
                return $html;
            })
            ->addColumn('target', function ($log) {
                if ($log->account) {
                    $icon = match((int) $log->account->account_type) {
                        Account::TYPE_FACEBOOK  => '<i class="ti ti-brand-facebook text-facebook me-1"></i>',
                        Account::TYPE_INSTAGRAM => '<i class="ti ti-brand-instagram text-pink me-1"></i>',
                        Account::TYPE_YOUTUBE   => '<i class="ti ti-brand-youtube text-danger me-1"></i>',
                        default                 => '<i class="ti ti-plug text-muted me-1"></i>',
                    };
                    return '<div class="d-flex align-items-center">' . $icon . '<span class="fw-medium text-truncate" style="max-width: 140px;">' . e($log->account->name) . '</span></div>';
                }
                return '<span class="text-muted small">—</span>';
            })
            ->editColumn('duration_ms', function ($log) {
                return $log->duration_ms ? '<span class="badge border border-secondary bg-transparent text-muted">' . $log->duration_ms . 'ms</span>' : '—';
            })
            ->editColumn('status', function ($log) {
                if ($log->status === 'success') {
                    return '<span class="badge border border-success bg-transparent text-success"><i class="ti ti-check me-1"></i>Success</span>';
                } elseif ($log->status === 'failed') {
                    return '<span class="badge border border-danger bg-transparent text-danger"><i class="ti ti-x me-1"></i>Failed</span>';
                }
                return '<span class="badge border border-warning bg-transparent text-warning">' . ucfirst($log->status) . '</span>';
            })
            ->addColumn('actions', function ($log) {
                $eventLabel = ucwords(str_replace('_', ' ', $log->event_type));
                return '
                    <button type="button" 
                            class="btn btn-sm btn-ghost-secondary px-2 view-user-log-btn"
                            data-action="' . e($log->action) . '"
                            data-event="' . e($eventLabel) . '"
                            data-status="' . e($log->status) . '"
                            data-time="' . e($log->created_at->format('d M Y, h:i:s A')) . '"
                            data-duration="' . ($log->duration_ms ? $log->duration_ms . ' ms' : 'N/A') . '"
                            data-account="' . e($log->account->name ?? 'System') . '"
                            data-ip="' . e($log->ip_address ?? 'N/A') . '"
                            data-payload="' . e(json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '"
                            data-response="' . e($log->response) . '"
                            title="View Payload & Response">
                        <i class="ti ti-code fs-3"></i>
                    </button>
                ';
            })
            ->rawColumns(['created_at', 'event_type', 'action', 'target', 'duration_ms', 'status', 'actions'])
            ->toJson();
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->back()
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|integer|in:' . implode(',', array_keys(User::getStatusList())),
        ]);

        $user->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.'
        ]);
    }

    public function blocked(\App\DataTables\Users\BlockedUserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.blocked');
    }

    public function blockedData(\App\DataTables\Users\BlockedUserDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function unblock(User $user)
    {
        $user->update([
            'status' => User::STATUS_ACTIVE,
            'blocked_reason' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully.'
        ]);
    }

    public function deleted(\App\DataTables\Users\DeletedUserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.deleted');
    }

    public function deletedData(\App\DataTables\Users\DeletedUserDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $user->restore();

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully.'
        ]);
    }

    public function revokeToken(Request $request, User $user)
    {
        $request->validate([
            'token_id' => 'required|integer',
        ]);

        $token = $user->tokens()->where('id', $request->token_id)->first();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token not found.'], 404);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device session revoked successfully.'
        ]);
    }


    public function search(Request $request)
    {
        $search = $request->input('q');
        
        $query = User::users();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->limit(50)->get();
        
        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'text' => ($user->name ?? 'Unknown') . ' (' . ($user->phone ?? 'N/A') . ')'
            ];
        }
        
        return response()->json([
            'results' => $results
        ]);
    }
}
