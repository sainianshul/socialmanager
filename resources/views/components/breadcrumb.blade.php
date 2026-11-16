@props(['items' => []])

@php
if (empty($items)) {
    $routeName = request()->route() ? request()->route()->getName() : '';
    $map = [
        'admin.dashboard' => [
            ['label' => 'Dashboard']
        ],
        'admin.accounts.index' => [
            ['label' => 'Channels', 'url' => route('admin.accounts.index')],
            ['label' => 'Connected Accounts']
        ],
        'admin.posts.index' => [
            ['label' => 'Content'],
            ['label' => 'All Posts']
        ],
        'admin.posts.instagram' => [
            ['label' => 'Content'],
            ['label' => 'Instagram Posts']
        ],
        'admin.posts.facebook' => [
            ['label' => 'Content'],
            ['label' => 'Facebook Posts']
        ],
        'admin.posts.youtube' => [
            ['label' => 'Content'],
            ['label' => 'YouTube Videos']
        ],
        'admin.posts.schedule' => [
            ['label' => 'Content'],
            ['label' => 'Schedule & Calendar']
        ],
        'admin.comments.index' => [
            ['label' => 'Engagement'],
            ['label' => 'Comments']
        ],
        'admin.comments.instagram' => [
            ['label' => 'Engagement'],
            ['label' => 'Instagram Comments']
        ],
        'admin.comments.facebook' => [
            ['label' => 'Engagement'],
            ['label' => 'Facebook Comments']
        ],
        'admin.comments.youtube' => [
            ['label' => 'Engagement'],
            ['label' => 'YouTube Comments']
        ],
        'admin.auto_dm.index' => [
            ['label' => 'Engagement'],
            ['label' => 'Auto DM']
        ],
        'admin.users.index' => [
            ['label' => 'Users']
        ],
        'admin.users.create' => [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Add User']
        ],
        'admin.users.edit' => [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Edit User']
        ],
        'admin.users.show' => [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'User Details']
        ],
        'admin.users.blocked' => [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Blocked Users']
        ],
        'admin.users.deleted' => [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Trash']
        ],
        'admin.profile.index' => [
            ['label' => 'Settings'],
            ['label' => 'Profile']
        ],
    ];
    $items = $map[$routeName] ?? [];
}
@endphp

<ol {{ $attributes->merge(['class' => 'breadcrumb m-0 p-0 bg-transparent align-items-center']) }} aria-label="breadcrumbs" style="font-size: 0.8125rem;">
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none d-inline-flex align-items-center" title="Dashboard">
            <i class="ti ti-home" style="font-size: 1rem;"></i>
        </a>
    </li>
    @foreach ($items as $item)
        @if (!$loop->last)
            <li class="breadcrumb-item">
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="text-secondary text-decoration-none">{{ $item['label'] }}</a>
                @else
                    <span class="text-secondary">{{ $item['label'] }}</span>
                @endif
            </li>
        @else
            <li class="breadcrumb-item active text-body fw-semibold" aria-current="page">
                {{ $item['label'] }}
            </li>
        @endif
    @endforeach
</ol>
