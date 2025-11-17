<x-app-layout>
    <style>
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            margin-bottom: 20px;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .back-button:hover {
            color: white;
        }

        .channel-header {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
        }

        .channel-title-section {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .channel-icon-large {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            flex-shrink: 0;
        }

        .channel-icon-large.public {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .channel-icon-large.private {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .channel-icon-large.department {
            background: rgba(251, 187, 42, 0.2);
            color: #fbbb2a;
        }

        .channel-info {
            flex: 1;
        }

        .channel-name {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
        }

        .channel-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .channel-type-badge.public {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .channel-type-badge.private {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .channel-type-badge.department {
            background: rgba(251, 187, 42, 0.2);
            color: #fcd34d;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .channel-description {
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .channel-meta {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .channel-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(223, 85, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .btn-success:hover {
            background: rgba(34, 197, 94, 0.3);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: white;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .content-card {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 12px;
        }

        .member-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .member-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .member-details {
            flex: 1;
        }

        .member-name {
            font-weight: 600;
            color: white;
            font-size: 14px;
        }

        .member-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .messages-section {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            min-height: 400px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .empty-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 16px;
            background: rgba(251, 187, 42, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbb2a;
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .channel-header {
                padding: 20px;
            }

            .channel-title-section {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .channel-name {
                font-size: 24px;
            }

            .channel-icon-large {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }

            .channel-actions {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .members-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Back Button -->
    <a href="{{ route('web.channels') }}" class="back-button">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ org_trans('back_to_channels') }}
    </a>

    <!-- Channel Header -->
    <div class="channel-header">
        <div class="channel-title-section">
            <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
                <div class="channel-icon-large {{ $channel->type }}">
                    @if($channel->type === 'public')
                        <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    @elseif($channel->type === 'private')
                        <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @else
                        <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    @endif
                </div>

                <div class="channel-info">
                    <h1 class="channel-name">{{ $channel->name }}</h1>
                    <span class="channel-type-badge {{ $channel->type }}">
                        @if($channel->type === 'public')
                            🌐 {{ org_trans('public') }}
                        @elseif($channel->type === 'private')
                            🔒 {{ org_trans('private') }}
                        @else
                            🏢 {{ org_trans('department') }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="channel-actions">
                @if($channel->is_member)
                    <form action="{{ route('web.channels.leave', $channel->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            {{ org_trans('leave') }}
                        </button>
                    </form>
                @else
                    <form action="{{ route('web.channels.join', $channel->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            {{ org_trans('join') }}
                        </button>
                    </form>
                @endif

                @if(auth()->check() && auth()->user()->canManageUsers())
                <a href="{{ route('web.channels.edit', $channel->id) }}" class="btn btn-secondary">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ org_trans('edit') }}
                </a>
                @endif
            </div>
        </div>

        @if($channel->description)
        <div class="channel-description">
            {{ $channel->description }}
        </div>
        @endif

        <div class="channel-meta">
            <div class="meta-item">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ org_trans('created_on') }} {{ $channel->created_at->format('d/m/Y') }}
            </div>

            <div class="meta-item">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ org_trans('by') }} {{ $channel->creator->name ?? org_trans('unknown') }}
            </div>

            @if($channel->department)
            <div class="meta-item">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ $channel->department->name }}
            </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $channel->members_count ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('members') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-value">{{ $channel->messages_count ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('messages') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-value">
                @if($channel->is_member)
                    <span style="color: #4ade80;">✓ {{ org_trans('active') }}</span>
                @else
                    <span style="color: #fca5a5;">{{ org_trans('inactive') }}</span>
                @endif
            </div>
            <div class="stat-label">{{ org_trans('status') }}</div>
        </div>
    </div>

    <!-- Members -->
    <div class="content-card">
        <h3 class="section-title">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            {{ org_trans('members') }} ({{ $channel->members_count ?? 0 }})
        </h3>

        @if($channel->users->count() > 0)
        <div class="members-grid">
            @foreach($channel->users as $user)
            <div class="member-item">
                <div class="member-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="member-details">
                    <div class="member-name">{{ $user->name }}</div>
                    <div class="member-role">{{ ucfirst($user->role) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg style="width: 30px; height: 30px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <p style="font-weight: 500;">{{ org_trans('no_members_in_channel') }}</p>
        </div>
        @endif
    </div>

    <!-- Messages (Placeholder) -->
    <div class="messages-section">
        <h3 class="section-title">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            {{ org_trans('messages') }}
        </h3>

        <div class="empty-state">
            <div class="empty-icon">
                <svg style="width: 30px; height: 30px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <h4 style="color: white; font-weight: 700; margin-bottom: 8px;">{{ org_trans('chat_interface_coming_soon') }}</h4>
            <p style="font-size: 14px;">{{ org_trans('realtime_messaging_coming_soon') }}</p>
        </div>
    </div>

</x-app-layout>
