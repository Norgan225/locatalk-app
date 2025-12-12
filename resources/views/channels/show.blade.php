<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/messaging-modern.css') }}?v={{ time() }}&r={{ rand() }}">
    <!-- Présence utilisateur (pastilles) -->
    <link rel="stylesheet" href="{{ asset('css/user-presence.css') }}?v={{ time() }}&r={{ rand() }}">
    <!-- Styles de chat en bulles modernes (WhatsApp-like) -->
    <link rel="stylesheet" href="{{ asset('css/channel-chat.css') }}?v={{ time() }}&r={{ rand() }}">
    @vite(['resources/js/app.js'])

    <!-- Viewport meta tag pour mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    @php
        $user = auth()->user();
        $plainToken = $user->createToken('channel-session')->plainTextToken;
    @endphp
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

        /* ========================================
           🎨 Interface de Chat pour Canaux
           ======================================== */

        .channel-chat-app {
            display: flex;
            flex-direction: column;
            height: 600px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.02);
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .chat-user-details h3 {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .user-status-container {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .status-badge {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ade80;
        }

        .status-badge.status-offline {
            background: #ef4444;
        }

        .status-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }

        .chat-actions {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            padding: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
        }

        .badge {
            background: #fbbb2a;
            color: #df5526;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 4px;
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: rgba(0, 0, 0, 0.1);
        }

        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .message {
            display: flex;
            gap: 12px;
            max-width: 70%;
        }

        .message.own {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .message-content {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .message.own .message-content {
            background: linear-gradient(135deg, #10b981, #059669);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .message-sender {
            font-weight: 600;
            color: white;
            font-size: 14px;
        }

        .message.own .message-sender {
            color: rgba(255, 255, 255, 0.9);
        }

        .message-time {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
        }

        .message-text {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message.own .message-text {
            color: white;
        }

        .message-input-area {
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 16px 24px;
        }

        .input-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .text-input-container {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        #messageTextarea {
            width: 100%;
            min-height: 44px;
            max-height: 120px;
            padding: 12px 16px;
            background: transparent;
            border: none;
            color: white;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            outline: none;
        }

        #messageTextarea::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .send-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 12px;
            color: white;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .send-btn:hover:not(:disabled) {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .empty-chat {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            padding: 40px;
            font-style: italic;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .messaging-app {
                height: 500px;
            }

            .chat-header {
                padding: 12px 16px;
            }

            .chat-user-details h3 {
                font-size: 16px;
            }

            .message-input-area {
                padding: 12px 16px;
            }

            .input-wrapper {
                gap: 8px;
            }

            .icon-btn {
                padding: 6px;
                font-size: 14px;
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

    <!-- Messages (Chat Interface Moderne) -->
    <div class="messages-section">
        <h3 class="section-title">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            {{ org_trans('messages') }}
        </h3>

        @if($channel->is_member)
        <!-- Interface de chat moderne pour canal -->
        <div class="channel-chat-app">
            <!-- Zone de chat -->
            <div class="chat-area">
                <!-- En-tête du chat -->
                <div id="chatHeader" class="chat-header">
                    <div class="chat-user-info">
                        <div class="chat-avatar">
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
                        <div class="chat-user-details">
                            <h3>{{ $channel->name }}</h3>
                            <div class="user-status-container">
                                <span class="status-badge status-online"></span>
                                <span class="status-text">{{ $channel->members_count ?? 0 }} membres</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div id="messagesContainer" class="messages-container">
                    <div class="channel-security-panel">
                        <div class="security-header">
                            <span class="security-icon">🔐</span>
                            <div>
                                <strong>Chiffrement de canal</strong>
                                <p class="security-desc">Partagez cette clé uniquement avec les membres autorisés.</p>
                            </div>
                        </div>
                        <div class="security-actions">
                            <button id="exportKeyBtn" class="btn btn-secondary">Exporter la clé</button>
                            <button id="importKeyBtn" class="btn btn-primary">Importer une clé</button>
                        </div>
                        <code id="channelKeyPreview" class="security-key hidden"></code>
                    </div>
                    <div id="pinnedMessagesBar" class="pinned-messages-container" style="display: none;"></div>
                    <div id="messagesList" class="messages-list">
                        <!-- Messages chargés dynamiquement -->
                    </div>
                    <div id="scrollToBottom" class="scroll-to-bottom" style="display: none;">
                        <button class="scroll-btn">⬇️ Nouveaux messages</button>
                    </div>
                </div>

                <!-- Zone de saisie -->
                <div id="messageInput" class="message-input-area">
                    <!-- Zone de réponse (affichée quand on répond à un message) -->
                    <div id="replyingTo" class="reply-preview" style="display: none;">
                        <div class="reply-preview-content">
                            <strong id="replyToUser" class="reply-preview-sender"></strong>
                            <p id="replyToContent" class="reply-preview-text"></p>
                        </div>
                        <button id="cancelReply" class="reply-preview-close" type="button" aria-label="Annuler la réponse">✕</button>
                    </div>

                    <!-- Preview des fichiers uploadés -->
                    <div id="filePreviewArea" class="file-preview-area hidden"></div>

                    <!-- Preview des liens -->
                    <div id="linkPreviewArea" class="link-preview-area hidden"></div>

                    <div class="input-wrapper">
                        <button class="icon-btn" id="emojiPickerBtn" title="Émojis">
                            😊
                        </button>
                        <button class="icon-btn" id="attachFileBtn" title="Joindre un fichier">
                            📎
                        </button>
                        <input type="file" id="fileInput" style="display: none;" multiple>

                        <div class="text-input-container">
                            <textarea
                                id="messageTextarea"
                                placeholder="Écrivez un message..."
                                rows="1"
                                maxlength="5000"></textarea>
                        </div>

                        <button class="icon-btn voice-btn" id="voiceRecordBtn" title="Message vocal">
                            🎤
                        </button>
                        <button class="send-btn" id="sendMessageBtn" title="Envoyer" disabled type="button">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Picker d'émojis (popup) -->
        <div id="emojiPicker" class="emoji-picker" style="display: none;">
            <div class="emoji-header">
                <input type="text" id="emojiSearch" placeholder="Rechercher un émoji...">
            </div>
            <div class="emoji-categories">
                <button data-category="recent">⏱️</button>
                <button data-category="smileys">😀</button>
                <button data-category="gestures">👋</button>
                <button data-category="animals">🐶</button>
                <button data-category="food">🍕</button>
                <button data-category="travel">✈️</button>
                <button data-category="objects">⚽</button>
                <button data-category="symbols">❤️</button>
            </div>
            <div id="emojiGrid" class="emoji-grid"></div>
        </div>

        <!-- Menu contextuel pour les messages -->
        <div id="messageContextMenu" class="context-menu" style="display: none;">
            <button data-action="reply">💬 Répondre</button>
            <button id="pinButton" data-action="pin">📌 Épingler</button>
            <button id="unpinButton" data-action="unpin" style="display: none;">📌 Désépingler</button>
            <button data-action="copy">📋 Copier</button>
            <button data-action="delete">🗑️ Supprimer</button>
        </div>

        <!-- Picker de réactions rapides -->
        <div id="reactionPicker" class="reaction-picker" style="display: none;">
            <button class="reaction-btn" data-emoji="👍">👍</button>
            <button class="reaction-btn" data-emoji="❤️">❤️</button>
            <button class="reaction-btn" data-emoji="😂">😂</button>
            <button class="reaction-btn" data-emoji="😮">😮</button>
            <button class="reaction-btn" data-emoji="😢">😢</button>
            <button class="reaction-btn" data-emoji="🎉">🎉</button>
            <button class="reaction-btn" data-emoji="🔥">🔥</button>
            <button class="reaction-btn" data-emoji="➕">➕</button>
        </div>

        <!-- Scripts pour l'interface moderne -->
        <script src="{{ asset('js/messaging-app.js') }}?v={{ time() }}&r={{ rand() }}"></script>
        <script src="{{ asset('js/voice-recorder.js') }}?v={{ time() }}&r={{ rand() }}"></script>
        <script src="{{ asset('js/link-preview.js') }}?v={{ time() }}&r={{ rand() }}"></script>
        <script src="{{ asset('js/user-presence.js') }}?v={{ time() }}&r={{ rand() }}"></script>
        <script src="{{ asset('js/e2e-encryption.js') }}?v={{ time() }}&r={{ rand() }}"></script>
        <script src="{{ asset('js/sound-manager.js') }}?v={{ time() }}&r={{ rand() }}"></script>

        <script>
            // Configuration pour les canaux
            let channelId = {{ $channel->id }};
            let currentUserId = {{ auth()->id() }};
            let authToken = "{{ $plainToken }}";

            // Configuration Pusher (même si non utilisé, pour compatibilité)
            window.PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key', '') }}";
            window.PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}";
            window.LARAVEL_ECHO_AVAILABLE = typeof Echo !== 'undefined';

            // Configuration Laravel Echo
            if (window.LARAVEL_ECHO_AVAILABLE && window.PUSHER_APP_KEY) {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: window.PUSHER_APP_KEY,
                    cluster: window.PUSHER_CLUSTER,
                    forceTLS: false,
                    wsHost: window.location.hostname,
                    wsPort: 6001,
                    disableStats: true,
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'Authorization': `Bearer ${authToken}`
                        }
                    }
                });
            }

            // Classe adaptée pour les canaux
            class ChannelChatApp {
                constructor(channelId, userId, authToken) {
                    this.channelId = channelId;
                    this.userId = userId;
                    this.authToken = authToken;
                    this.messages = [];
                    this.replyTo = null;
                    this.isRecording = false;
                    this.mediaRecorder = null;
                    this.recordedChunks = [];
                    this.selectedFiles = [];
                    this.emojis = this.getEmojisData();

                    // Éléments DOM
                    this.messagesContainer = document.getElementById('messagesContainer');
                    this.messageTextarea = document.getElementById('messageTextarea');
                    this.sendMessageBtn = document.getElementById('sendMessageBtn');
                    this.emojiPickerBtn = document.getElementById('emojiPickerBtn');
                    this.attachFileBtn = document.getElementById('attachFileBtn');
                    this.fileInput = document.getElementById('fileInput');
                    this.voiceRecordBtn = document.getElementById('voiceRecordBtn');
                    this.replyingTo = document.getElementById('replyingTo');
                    this.cancelReply = document.getElementById('cancelReply');
                    this.filePreviewArea = document.getElementById('filePreviewArea');
                    this.linkPreviewArea = document.getElementById('linkPreviewArea');
                    this.emojiPicker = document.getElementById('emojiPicker');
                    this.emojiSearch = document.getElementById('emojiSearch');
                    this.emojiGrid = document.getElementById('emojiGrid');
                    this.messageContextMenu = document.getElementById('messageContextMenu');
                    this.reactionPicker = document.getElementById('reactionPicker');

                    this.init();
                }

                init() {
                    this.setupEventListeners();
                    this.loadChannelMessages();
                    this.setupWebSocket();
                    this.setupEmojiPicker();
                    this.setupFileUpload();
                    this.setupVoiceRecording();
                    this.setupContextMenu();
                    this.setupReactionPicker();
                    this.updateSendButtonState();
                }

                setupEventListeners() {
                    console.log('🎧 Configuration des event listeners...');

                    // Boutons principaux
                    const sendBtn = document.getElementById('sendMessageBtn');
                    console.log('🔘 Bouton d\'envoi trouvé:', sendBtn);

                    document.getElementById('sendMessageBtn')?.addEventListener('click', () => {
                        console.log('🖱️ Clic sur bouton d\'envoi détecté');
                        this.sendMessage();
                    });

                    // Gestionnaire pour le textarea
                    const textarea = document.getElementById('messageTextarea');
                    if (textarea) {
                        textarea.addEventListener('input', () => {
                            this.updateSendButtonState();
                            this.checkForLinks();
                        });
                        textarea.addEventListener('keydown', (e) => this.handleKeyPress(e));
                    }

                    // Annuler la réponse
                    if (this.cancelReply) {
                        this.cancelReply.addEventListener('click', () => this.cancelReplyToMessage());
                    }

                    // Scroll pour charger plus de messages
                    if (this.messagesContainer) {
                        this.messagesContainer.addEventListener('scroll', () => {
                            if (this.messagesContainer.scrollTop === 0 && this.hasMoreMessages && !this.isLoading) {
                                this.loadMoreMessages();
                            }
                        });
                    }

                    console.log('✅ Event listeners configurés');
                }

                async loadChannelMessages() {
                    try {
                        const response = await fetch(`/api/channels/${this.channelId}/messages?page=1&per_page=50`, {
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Erreur de chargement des messages');
                        }

                        const data = await response.json();
                        this.messages = (data.messages || []).reverse();
                        this.renderMessages();
                        this.scrollToBottom();
                    } catch (error) {
                        console.error('Erreur chargement messages:', error);
                        this.showNotification('Erreur de chargement des messages', 'error');
                    }
                }

                renderMessages() {
                    const container = document.getElementById('messagesList');
                    if (!container) return;

                    if (!this.messages.length) {
                        container.innerHTML = '<div class="empty-chat">Aucun message dans ce canal pour le moment.</div>';
                        return;
                    }

                    container.innerHTML = this.messages.map(msg => this.renderMessage(msg)).join('');
                }

                renderMessage(msg) {
                    const isOwn = msg.user_id === this.userId;
                    const avatarHtml = msg.user_avatar
                        ? `<img src="/storage/${msg.user_avatar}" alt="${msg.user_name}">`
                        : `<span class="avatar-initial">${msg.user_name.charAt(0).toUpperCase()}</span>`;

                    return `
                        <div class="message ${isOwn ? 'own' : ''}" data-message-id="${msg.id}">
                            <div class="message-avatar">
                                ${avatarHtml}
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-sender">${msg.user_name}</span>
                                    <span class="message-time">${this.formatTime(msg.created_at)}</span>
                                </div>
                                <div class="message-text">${this.escapeHtml(msg.content)}</div>
                            </div>
                        </div>
                    `;
                }

                async sendMessage() {
                    console.log('🔥 sendMessage() appelée');

                    const textarea = document.getElementById('messageTextarea');
                    const content = textarea?.value.trim();

                    console.log('📝 Contenu du message:', content);

                    if (!content) {
                        console.log('❌ Pas de contenu, retour');
                        return;
                    }

                    try {
                        console.log('📤 Envoi du message vers API...');

                        const messageData = {
                            content: content,
                            type: 'text'
                        };

                        const response = await fetch(`/api/channels/${this.channelId}/messages`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(messageData)
                        });

                        console.log('📡 Réponse API:', response.status, response.statusText);

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('❌ Erreur API:', errorText);
                            throw new Error('Erreur d\'envoi du message');
                        }

                        const result = await response.json();
                        console.log('✅ Message envoyé avec succès:', result);

                        // Ajouter immédiatement le message à l'interface pour l'utilisateur actuel
                        if (result.data) {
                            console.log('📱 Ajout immédiat du message à l\'UI');
                            this.addMessageToUI(result.data);
                        }

                        // Vider le textarea
                        if (textarea) textarea.value = '';
                        this.updateSendButtonState();

                        // Le WebSocket synchronisera avec les autres utilisateurs
                        console.log('🔄 WebSocket synchronisera avec les autres utilisateurs...');
                    } catch (error) {
                        console.error('💥 Erreur envoi message:', error);
                        this.showNotification('Erreur d\'envoi du message', 'error');
                    }
                }

                setupWebSocket() {
                    console.log('🔌 Configuration du WebSocket pour le canal:', this.channelId);

                    if (window.Echo) {
                        console.log('📡 WebSocket Echo disponible, connexion au canal privé...');

                        window.Echo.private(`channel.${this.channelId}`)
                            .listen('.channel.message.sent', (e) => {
                                console.log('📨 Nouveau message reçu via WebSocket:', e);

                                // Vérifier si le message n'est pas déjà affiché (éviter les doublons)
                                const existingMessage = document.querySelector(`[data-message-id="${e.id}"]`);
                                if (!existingMessage) {
                                    console.log('➕ Ajout du message à l\'UI via WebSocket');
                                    this.addMessageToUI(e);
                                } else {
                                    console.log('⚠️ Message déjà affiché, ignoré');
                                }
                            });

                        console.log('✅ WebSocket configuré pour le canal');
                    } else {
                        console.warn('⚠️ WebSocket Echo non disponible');
                    }
                }

                addMessageToUI(messageData) {
                    const container = document.getElementById('messagesList');
                    if (!container) return;

                    const messageElement = this.renderMessage(messageData);
                    container.appendChild(messageElement);

                    // Scroll vers le bas si on est près du bas
                    const isNearBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 100;
                    if (isNearBottom) {
                        container.scrollTop = container.scrollHeight;
                    }
                }

                // ============ FONCTIONNALITÉS AVANCÉES ============

                setupEmojiPicker() {
                    if (this.emojiPickerBtn) {
                        this.emojiPickerBtn.addEventListener('click', () => this.toggleEmojiPicker());
                    }

                    if (this.emojiSearch) {
                        this.emojiSearch.addEventListener('input', (e) => this.filterEmojis(e.target.value));
                    }

                    // Fermer le picker en cliquant ailleurs
                    document.addEventListener('click', (e) => {
                        if (this.emojiPicker && !this.emojiPicker.contains(e.target) && e.target !== this.emojiPickerBtn) {
                            this.emojiPicker.style.display = 'none';
                        }
                    });

                    this.renderEmojis();
                }

                toggleEmojiPicker() {
                    if (!this.emojiPicker) return;

                    const isVisible = this.emojiPicker.style.display !== 'none';
                    this.emojiPicker.style.display = isVisible ? 'none' : 'block';

                    if (!isVisible) {
                        this.emojiSearch.value = '';
                        this.filterEmojis('');
                    }
                }

                getEmojisData() {
                    return {
                        recent: ['😀', '😂', '❤️', '👍', '🔥', '🎉'],
                        smileys: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😬', '🙄', '😯', '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐', '🥴', '🤢', '🤮', '🤧', '😷', '🤒', '🤕', '🤑', '🤠', '😈', '👿', '👹', '👺', '🤡', '💩', '👻', '💀', '☠️', '👽', '👾', '🤖', '🎃', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾'],
                        gestures: ['👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '👊', '✊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍️', '💅', '🤳', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', '🧠', '🫀', '🫁', '🦷', '🦴', '👀', '👁️', '👅', '👄', '💋', '🩸'],
                        animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐽', '🐸', '🐵', '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴', '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦗', '🕷️', '🦂', '🐢', '🐍', '🦎', '🦖', '🦕', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡', '🐠', '🐟', '🐬', '🐳', '🐋', '🦈', '🐊', '🐅', '🐆', '🦓', '🦍', '🦧', '🐘', '🦛', '🦏', '🐪', '🐫', '🦒', '🦘', '🐃', '🐂', '🐄', '🐎', '🐖', '🐏', '🐑', '🦙', '🐐', '🦌', '🐕', '🐩', '🦮', '🐕‍🦺', '🐈', '🐈‍⬛', '🐓', '🦃', '🦚', '🦜', '🦢', '🦩', '🕊️', '🐇', '🦝', '🦨', '🦡', '🦦', '🦥', '🐁', '🐀', '🐿️', '🦔'],
                        food: ['🍎', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶️', '🫑', '🌽', '🥕', '🫒', '🧄', '🧅', '🥔', '🍠', '🥐', '🥖', '🍞', '🥨', '🥯', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🦴', '🌭', '🍔', '🍟', '🍕', '🫓', '🥙', '🌮', '🌯', '🫔', '🥗', '🥘', '🫕', '🍝', '🍜', '🍲', '🍛', '🍣', '🍱', '🥟', '🦪', '🍤', '🍙', '🍚', '🍘', '🍥', '🥠', '🥮', '🍢', '🍡', '🍧', '🍨', '🍦', '🥧', '🧁', '🍰', '🎂', '🍮', '🍭', '🍬', '🍫', '🍿', '🍩', '🍪', '🌰', '🥜', '🍯', '🥛', '🍼', '☕', '🫖', '🍵', '🧃', '🥤', '🧋', '🍶', '🍺', '🍻', '🥂', '🍷', '🥃', '🍸', '🍹', '🧉', '🍾'],
                        travel: ['🚗', '🚕', '🚙', '🚌', '🚎', '🏎️', '🚓', '🚑', '🚒', '🚐', '🚚', '🚛', '🚜', '🏍️', '🛵', '🚲', '🛴', '🛹', '🚁', '🚟', '🚠', '🚡', '🛤️', '🛣️', '🗺️', '⛽', '🚨', '🚥', '🚦', '🛑', '🚧', '⚓', '⛵', '🛶', '🚤', '🛳️', '⛴️', '🛥️', '🚢', '✈️', '🛩️', '🛫', '🛬', '🪂', '💺', '🚀', '🛸', '🚁', '🚟', '🚠', '🚡', '🛤️', '🛣️', '🗺️', '⛽', '🚨', '🚥', '🚦', '🛑', '🚧', '⚓', '⛵', '🛶', '🚤', '🛳️', '⛴️', '🛥️', '🚢', '✈️', '🛩️', '🛫', '🛬', '🪂', '💺', '🚀', '🛸'],
                        objects: ['⌚', '📱', '📲', '💻', '⌨️', '🖥️', '🖨️', '🖱️', '🖲️', '🕹️', '🗜️', '💽', '💾', '💿', '📀', '📼', '📷', '📸', '📹', '🎥', '📽️', '🎞️', '📞', '☎️', '📟', '📠', '📺', '📻', '🎙️', '🎚️', '🎛️', '🧭', '⏱️', '⏲️', '⏰', '🕰️', '⌛', '⏳', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🪔', '🧯', '🛢️', '💸', '💵', '💴', '💶', '💷', '💰', '💳', '💎', '⚖️', '🪜', '🧰', '🧲', '🧪', '🧫', '🧬', '🔬', '🔭', '📭', '📬', '📫', '📪', '📮', '📯', '📜', '📃', '📄', '📑', '🧾', '📊', '📈', '📉', '🗒️', '🗓️', '📆', '📅', '🗑️', '📇', '🗃️', '🗳️', '🗄️', '📋', '📁', '📂', '🗂️', '📅', '🗓️', '🗒️', '🗑️', '📇', '🗃️', '🗳️', '🗄️', '📋', '📁', '📂', '🗂️'],
                        symbols: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❤️‍🔥', '❤️‍🩹', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⛎', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '🆔', '⚛️', '🉑', '☢️', '☣️', '📴', '📳', '🈶', '🈚', '🈸', '🈺', '🈷️', '✴️', '🆚', '💮', '🉐', '㊙️', '㊗️', '🈴', '🈵', '🈹', '🈲', '🅰️', '🅱️', '🆎', '🆑', '🅾️', '🆘', '❌', '⭕', '🛑', '⛔', '📛', '🚫', '💯', '🔟', '🔢', '▶️', '⏸️', '⏯️', '⏹️', '⏺️', '⏏️', '⏭️', '⏮️', '⏩', '⏪', '🔀', '🔁', '🔂', '🔄', '🔃', '🎵', '🎶', '➕', '➖', '➗', '✖️', '💲', '💱', '™️', '©️', '®️', '🔞', '‼️', '⁉️', '❓', '❔', '❕', '❗', '〰️', '💱', '💲', '⚕️', '♻️', '⚜️', '🔱', '📛', '🔰', '⭕', '✅', '☑️', '✔️', '❌', '❎', '➰', '➿', '〽️', '✳️', '✴️', '❇️', '™️', '🔠', '🔡', '🔢', '🔣', '🔤', '🅰️', '🅱️', '🅾️', '🅿️', '🆎', '🆑', '🆔', '🆕', '🆖', '🆗', '🆘', '🆙', '🆚', '🈁', '🈂️', '🈷️', '🈶', '🈯', '🉐', '🉑', '🌀', '🌐', '🏁', '🚩', '🎌', '🏴', '🏳️', '🏳️‍🌈', '🏳️‍⚧️', '🏴‍☠️', '🇦🇫', '🇦🇽', '🇦🇱', '🇩🇿', '🇦🇸', '🇦🇩', '🇦🇴', '🇦🇮', '🇦🇶', '🇦🇬', '🇦🇷', '🇦🇲', '🇦🇼', '🇦🇺', '🇦🇹', '🇦🇿', '🇧🇸', '🇧🇭', '🇧🇩', '🇧🇧', '🇧🇾', '🇧🇪', '🇧🇿', '🇧🇯', '🇧🇲', '🇧🇹', '🇧🇴', '🇧🇦', '🇧🇼', '🇧🇷', '🇧🇳', '🇧🇬', '🇧🇫', '🇧🇮', '🇰🇭', '🇨🇲', '🇨🇦', '🇮🇨', '🇨🇻', '🇧🇶', '🇰🇾', '🇨🇫', '🇹🇩', '🇨🇱', '🇨🇳', '🇨🇽', '🇨🇨', '🇨🇴', '🇰🇲', '🇨🇬', '🇨🇩', '🇨🇰', '🇨🇷', '🇭🇷', '🇨🇺', '🇨🇼', '🇨🇾', '🇨🇿', '🇩🇰', '🇩🇯', '🇩🇲', '🇩🇴', '🇪🇨', '🇪🇬', '🇸🇻', '🇬🇶', '🇪🇷', '🇪🇪', '🇸🇿', '🇪🇹', '🇪🇺', '🇫🇰', '🇫🇴', '🇫🇯', '🇫🇮', '🇫🇷', '🇬🇫', '🇵🇫', '🇹🇫', '🇬🇦', '🇬🇲', '🇬🇪', '🇩🇪', '🇬🇭', '🇬🇮', '🇬🇷', '🇬🇱', '🇬🇩', '🇬🇵', '🇬🇺', '🇬🇹', '🇬🇬', '🇬🇳', '🇬🇼', '🇬🇾', '🇭🇹', '🇭🇳', '🇭🇰', '🇭🇺', '🇮🇸', '🇮🇳', '🇮🇩', '🇮🇷', '🇮🇶', '🇮🇪', '🇮🇲', '🇮🇱', '🇮🇹', '🇯🇲', '🇯🇵', '🎌', '🇯🇪', '🇯🇴', '🇰🇿', '🇰🇪', '🇰🇮', '🇽🇰', '🇰🇼', '🇰🇬', '🇱🇦', '🇱🇻', '🇱🇧', '🇱🇸', '🇱🇷', '🇱🇾', '🇱🇮', '🇱🇹', '🇱🇺', '🇲🇴', '🇲🇰', '🇲🇬', '🇲🇼', '🇲🇾', '🇲🇻', '🇲🇱', '🇲🇹', '🇲🇭', '🇲🇶', '🇲🇷', '🇲🇺', '🇾🇹', '🇲🇽', '🇫🇲', '🇲🇩', '🇲🇨', '🇲🇳', '🇲🇪', '🇲🇸', '🇲🇦', '🇲🇿', '🇲🇲', '🇳🇦', '🇳🇷', '🇳🇵', '🇳🇱', '🇳🇨', '🇳🇿', '🇳🇮', '🇳🇪', '🇳🇬', '🇳🇺', '🇳🇫', '🇰🇵', '🇲🇵', '🇳🇴', '🇴🇲', '🇵🇰', '🇵🇼', '🇵🇸', '🇵🇦', '🇵🇬', '🇵🇾', '🇵🇪', '🇵🇭', '🇵🇳', '🇵🇱', '🇵🇹', '🇵🇷', '🇶🇦', '🇷🇪', '🇷🇴', '🇷🇺', '🇷🇼', '🇼🇸', '🇸🇲', '🇸🇹', '🇸🇦', '🇸🇳', '🇷🇸', '🇸🇨', '🇸🇱', '🇸🇬', '🇸🇽', '🇸🇰', '🇸🇮', '🇸🇧', '🇸🇴', '🇿🇦', '🇬🇸', '🇰🇷', '🇸🇸', '🇪🇸', '🇱🇰', '🇧🇱', '🇸🇭', '🇰🇳', '🇱🇨', '🇵🇲', '🇻🇨', '🇸🇩', '🇸🇷', '🇸🇪', '🇨🇭', '🇸🇾', '🇹🇼', '🇹🇯', '🇹🇿', '🇹🇭', '🇹🇱', '🇹🇬', '🇹🇰', '🇹🇴', '🇹🇹', '🇹🇳', '🇹🇷', '🇹🇲', '🇹🇨', '🇹🇻', '🇺🇬', '🇺🇦', '🇦🇪', '🇬🇧', '🇺🇸', '🇺🇾', '🇺🇿', '🇻🇺', '🇻🇦', '🇻🇪', '🇻🇳', '🇻🇬', '🇻🇮', '🇼🇫', '🇪🇭', '🇾🇪', '🇿🇲', '🇿🇼']
                    };
                }

                renderEmojis(category = 'recent') {
                    if (!this.emojiGrid) return;

                    const emojis = this.emojis[category] || [];
                    this.emojiGrid.innerHTML = '';

                    emojis.forEach(emoji => {
                        const emojiBtn = document.createElement('button');
                        emojiBtn.className = 'emoji-btn';
                        emojiBtn.textContent = emoji;
                        emojiBtn.addEventListener('click', () => this.insertEmoji(emoji));
                        this.emojiGrid.appendChild(emojiBtn);
                    });
                }

                filterEmojis(query) {
                    if (!this.emojiGrid) return;

                    const emojiBtns = this.emojiGrid.querySelectorAll('.emoji-btn');
                    emojiBtns.forEach(btn => {
                        const emoji = btn.textContent;
                        const shouldShow = query === '' || emoji.includes(query.toLowerCase());
                        btn.style.display = shouldShow ? 'block' : 'none';
                    });
                }

                insertEmoji(emoji) {
                    const textarea = document.getElementById('messageTextarea');
                    if (textarea) {
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const text = textarea.value;
                        const newText = text.substring(0, start) + emoji + text.substring(end);
                        textarea.value = newText;
                        textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
                        textarea.focus();
                        this.updateSendButtonState();
                    }
                    this.toggleEmojiPicker();
                }

                setupFileUpload() {
                    if (this.attachFileBtn && this.fileInput) {
                        this.attachFileBtn.addEventListener('click', () => this.fileInput.click());
                        this.fileInput.addEventListener('change', (e) => this.handleFileSelection(e.target.files));
                    }

                    // Drag & drop
                    const chatArea = document.querySelector('.chat-area');
                    if (chatArea) {
                        chatArea.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            chatArea.classList.add('drag-over');
                        });

                        chatArea.addEventListener('dragleave', (e) => {
                            e.preventDefault();
                            chatArea.classList.remove('drag-over');
                        });

                        chatArea.addEventListener('drop', (e) => {
                            e.preventDefault();
                            chatArea.classList.remove('drag-over');
                            this.handleFileSelection(e.dataTransfer.files);
                        });
                    }
                }

                handleFileSelection(files) {
                    Array.from(files).forEach(file => {
                        this.selectedFiles.push(file);
                        this.addFilePreview(file);
                    });
                    this.updateSendButtonState();
                }

                addFilePreview(file) {
                    if (!this.filePreviewArea) return;

                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'file-preview';
                    previewDiv.innerHTML = `
                        <span class="file-name">${file.name}</span>
                        <span class="file-size">(${this.formatFileSize(file.size)})</span>
                        <button class="remove-file" onclick="this.parentElement.remove(); window.channelChatApp.removeFile('${file.name}')">×</button>
                    `;
                    this.filePreviewArea.appendChild(previewDiv);
                    this.filePreviewArea.classList.remove('hidden');
                }

                removeFile(fileName) {
                    this.selectedFiles = this.selectedFiles.filter(file => file.name !== fileName);
                    this.updateSendButtonState();
                }

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                setupVoiceRecording() {
                    if (this.voiceRecordBtn) {
                        this.voiceRecordBtn.addEventListener('mousedown', () => this.startRecording());
                        this.voiceRecordBtn.addEventListener('mouseup', () => this.stopRecording());
                        this.voiceRecordBtn.addEventListener('mouseleave', () => this.stopRecording());
                    }
                }

                async startRecording() {
                    if (this.isRecording) return;

                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this.mediaRecorder = new MediaRecorder(stream);
                        this.recordedChunks = [];
                        this.isRecording = true;

                        this.mediaRecorder.addEventListener('dataavailable', event => {
                            if (event.data.size > 0) {
                                this.recordedChunks.push(event.data);
                            }
                        });

                        this.mediaRecorder.addEventListener('stop', () => {
                            this.processRecording();
                            stream.getTracks().forEach(track => track.stop());
                        });

                        this.mediaRecorder.start();
                        this.voiceRecordBtn.classList.add('recording');
                        this.updateSendButtonState();
                    } catch (error) {
                        console.error('Erreur accès micro:', error);
                        this.showNotification('Impossible d\'accéder au microphone', 'error');
                    }
                }

                stopRecording() {
                    if (!this.isRecording || !this.mediaRecorder) return;

                    this.mediaRecorder.stop();
                    this.isRecording = false;
                    this.voiceRecordBtn.classList.remove('recording');
                    this.updateSendButtonState();
                }

                processRecording() {
                    const blob = new Blob(this.recordedChunks, { type: 'audio/webm' });
                    const file = new File([blob], `voice-message-${Date.now()}.webm`, { type: 'audio/webm' });
                    this.handleFileSelection([file]);
                }

                setupContextMenu() {
                    const messagesList = document.getElementById('messagesList');
                    if (messagesList) {
                        messagesList.addEventListener('contextmenu', (e) => {
                            e.preventDefault();
                            const messageElement = e.target.closest('.message');
                            if (messageElement) {
                                this.showContextMenu(e, messageElement);
                            }
                        });
                    }

                    // Fermer le menu contextuel
                    document.addEventListener('click', () => {
                        if (this.messageContextMenu) {
                            this.messageContextMenu.style.display = 'none';
                        }
                    });
                }

                showContextMenu(e, messageElement) {
                    if (!this.messageContextMenu) return;

                    const messageId = messageElement.dataset.messageId;
                    this.messageContextMenu.style.display = 'block';
                    this.messageContextMenu.style.left = e.pageX + 'px';
                    this.messageContextMenu.style.top = e.pageY + 'px';

                    // Configurer les actions du menu
                    const replyBtn = this.messageContextMenu.querySelector('[data-action="reply"]');
                    const pinBtn = this.messageContextMenu.querySelector('[data-action="pin"]');
                    const unpinBtn = this.messageContextMenu.querySelector('[data-action="unpin"]');
                    const copyBtn = this.messageContextMenu.querySelector('[data-action="copy"]');
                    const deleteBtn = this.messageContextMenu.querySelector('[data-action="delete"]');

                    if (replyBtn) replyBtn.onclick = () => this.replyToMessage(messageId);
                    if (pinBtn) pinBtn.onclick = () => window.channelApp?.togglePin(messageId);
                    if (unpinBtn) unpinBtn.onclick = () => window.channelApp?.togglePin(messageId);
                    if (copyBtn) copyBtn.onclick = () => this.copyMessage(messageElement);
                    if (deleteBtn) deleteBtn.onclick = () => this.deleteMessage(messageId);
                }

                setupReactionPicker() {
                    if (this.reactionPicker) {
                        const reactionBtns = this.reactionPicker.querySelectorAll('.reaction-btn');
                        reactionBtns.forEach(btn => {
                            btn.addEventListener('click', () => {
                                const emoji = btn.dataset.emoji;
                                // Pour l'instant, juste masquer le picker
                                this.reactionPicker.style.display = 'none';
                            });
                        });
                    }
                }

                replyToMessage(messageId) {
                    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (!messageElement) return;

                    const author = messageElement.querySelector('.message-author')?.textContent || 'Utilisateur';
                    const content = messageElement.querySelector('.message-text')?.textContent || '';

                    this.replyTo = { id: messageId, author, content };

                    if (this.replyingTo) {
                        document.getElementById('replyToUser').textContent = author;
                        document.getElementById('replyToContent').textContent = content.substring(0, 100) + (content.length > 100 ? '...' : '');
                        this.replyingTo.style.display = 'flex';
                    }

                    document.getElementById('messageTextarea')?.focus();
                    if (this.messageContextMenu) {
                        this.messageContextMenu.style.display = 'none';
                    }
                }

                cancelReplyToMessage() {
                    this.replyTo = null;
                    if (this.replyingTo) {
                        this.replyingTo.style.display = 'none';
                    }
                }

                async togglePinMessage(messageId) {
                    await window.channelApp?.togglePin(messageId);

                    if (this.messageContextMenu) {
                        this.messageContextMenu.style.display = 'none';
                    }
                }

                copyMessage(messageElement) {
                    const textElement = messageElement.querySelector('.message-text');
                    if (textElement) {
                        navigator.clipboard.writeText(textElement.textContent);
                        this.showNotification('Message copié !', 'success');
                    }

                    if (this.messageContextMenu) {
                        this.messageContextMenu.style.display = 'none';
                    }
                }

                async deleteMessage(messageId) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) return;

                    try {
                        const response = await fetch(`/api/channels/${this.channelId}/messages/${messageId}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${this.authToken}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                            if (messageElement) {
                                messageElement.remove();
                            }
                            this.showNotification('Message supprimé', 'success');
                        }
                    } catch (error) {
                        console.error('Erreur suppression message:', error);
                        this.showNotification('Erreur lors de la suppression', 'error');
                    }

                    if (this.messageContextMenu) {
                        this.messageContextMenu.style.display = 'none';
                    }
                }

                checkForLinks() {
                    const textarea = document.getElementById('messageTextarea');
                    if (!textarea) return;

                    const text = textarea.value;
                    const urlRegex = /(https?:\/\/[^\s]+)/g;
                    const matches = text.match(urlRegex);

                    if (matches && matches.length > 0) {
                        this.showLinkPreview(matches[0]);
                    } else {
                        this.hideLinkPreview();
                    }
                }

                showLinkPreview(url) {
                    if (!this.linkPreviewArea) return;

                    this.linkPreviewArea.innerHTML = `
                        <div class="link-preview">
                            <a href="${url}" target="_blank">${url}</a>
                        </div>
                    `;
                    this.linkPreviewArea.classList.remove('hidden');
                }

                hideLinkPreview() {
                    if (this.linkPreviewArea) {
                        this.linkPreviewArea.classList.add('hidden');
                    }
                }

                // Méthodes utilitaires
                updateSendButtonState() {
                    const textarea = document.getElementById('messageTextarea');
                    const sendBtn = document.getElementById('sendMessageBtn');
                    const hasContent = textarea?.value.trim();
                    const hasFiles = this.selectedFiles.length > 0;

                    if (sendBtn) {
                        sendBtn.disabled = !hasContent && !hasFiles && !this.isRecording;
                    }
                }

                handleKeyPress(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.sendMessage();
                    }
                }

                scrollToBottom() {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }

                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString('fr-FR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }

                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                showNotification(message, type = 'info') {
                    console.log(`${type}: ${message}`);
                }
            }

            // Initialisation via client de chat indépendant (scopé au canal)
            // Utiliser le conteneur existant pour éviter la duplication

            // Charger le client si non chargé
            (function initChannelMessaging() {
                const scriptId = 'channel-messaging-app-js';
                if (!document.getElementById(scriptId)) {
                    const s = document.createElement('script');
                    s.id = scriptId;
                    s.src = `{{ asset('js/channel-messaging-app.js') }}?v={{ time() }}`;
                    s.onload = () => {
                        const config = {
                            channelId: channelId,
                            channelName: @json($channel->name),
                            channelType: @json($channel->type),
                            userId: currentUserId,
                            userName: @json(auth()->user()->name),
                            authToken: authToken,
                            isAdmin: {{ auth()->user()->isOwner() ? 'true' : 'false' }}
                        };
                        window.channelApp = new ChannelMessagingApp(config);
                    };
                    document.body.appendChild(s);
                } else {
                    const config = {
                        channelId: channelId,
                        channelName: @json($channel->name),
                        channelType: @json($channel->type),
                        userId: currentUserId,
                        userName: @json(auth()->user()->name),
                        authToken: authToken,
                        isAdmin: {{ auth()->user()->isOwner() ? 'true' : 'false' }}
                    };
                    window.channelApp = new ChannelMessagingApp(config);
                }
            })();
        </script>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg style="width: 30px; height: 30px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <h4 style="color: white; font-weight: 700; margin-bottom: 8px;">{{ org_trans('join_channel_to_chat') }}</h4>
            <p style="font-size: 14px;">{{ org_trans('join_channel_description') }}</p>
        </div>
        @endif
    </div>

</x-app-layout>
