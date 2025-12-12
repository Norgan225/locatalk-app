<x-app-layout>
    <style>
        /* --- Design System Premium --- */
        :root {
            --primary-gradient: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.5);
            --card-radius: 24px;
        }

        .show-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            font-family: 'Inter', sans-serif;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left {
            flex: 1;
        }

        .color-bar {
            height: 6px;
            border-radius: 3px;
            margin-bottom: 1rem;
            width: 100px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(223, 85, 38, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(223, 85, 38, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title svg {
            width: 24px;
            height: 24px;
            color: #fbbb2a;
        }

        .description-text {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.7;
            font-size: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .info-value {
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active {
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
            box-shadow: 0 0 10px rgba(52, 211, 153, 0.1);
        }

        .badge-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .members-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .member-item {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .member-item:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .member-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .member-info {
            flex: 1;
        }

        .member-name {
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .member-role {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 16px;
            border: 2px dashed rgba(255, 255, 255, 0.1);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .close-modal {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
            font-size: 14px;
        }

        .form-select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 14px;
        }

        .form-select:focus {
            outline: none;
            border-color: #fbbb2a;
            background: rgba(255, 255, 255, 0.08);
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .show-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="show-container">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; padding: 16px; border-radius: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 16px; border-radius: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <div class="color-bar" style="background: {{ $department->color ?? 'linear-gradient(135deg, #df5526, #fbbb2a)' }};"></div>
                <h1 class="page-title">{{ $department->name }}</h1>
                <div class="page-subtitle">
                    <svg style="width: 18px; height: 18px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ $department->organization->name }}
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ route('web.departments') }}" class="btn btn-secondary">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ org_trans('back') }}
                </a>

                @if(auth()->user()->canManageUsers())
                <a href="{{ route('web.departments.edit', $department->id) }}" class="btn btn-primary">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ org_trans('edit') }}
                </a>

                <form action="{{ route('web.departments.destroy', $department->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ org_trans('confirm_delete_department') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ org_trans('delete') }}
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Description -->
                @if($department->description)
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        {{ org_trans('description') }}
                    </h2>
                    <p class="description-text">{{ $department->description }}</p>
                </div>
                @endif

                <!-- Statistics -->
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        {{ org_trans('statistics') }}
                    </h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value" style="background: linear-gradient(135deg, #fbbb2a, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $department->statistics['total_members'] }}</div>
                            <div class="stat-label">{{ org_trans('members') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" style="background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $department->statistics['active_projects'] }}</div>
                            <div class="stat-label">{{ org_trans('active_projects') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" style="background: linear-gradient(135deg, #60a5fa, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $department->statistics['total_projects'] }}</div>
                            <div class="stat-label">{{ org_trans('total_projects') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" style="background: linear-gradient(135deg, #a78bfa, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $department->statistics['channels_count'] }}</div>
                            <div class="stat-label">{{ org_trans('channels') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Members List -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 class="card-title" style="margin-bottom: 0;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            {{ org_trans('department_members') }} ({{ $department->users->count() }})
                        </h2>
                        @if(auth()->user()->canManageUsers())
                        <button onclick="openAddMemberModal()" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter
                        </button>
                        @endif
                    </div>

                    <div class="members-list">
                        @forelse($department->users as $user)
                        <div class="member-item">
                            <div class="member-avatar">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="member-info">
                                <div class="member-name">{{ $user->name }}</div>
                                <div class="member-role">{{ $user->email }}</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($user->id === $department->head_user_id)
                                <span class="badge badge-active">{{ org_trans('head') }}</span>
                                @endif

                                @if(auth()->user()->canManageUsers())
                                <form action="{{ route('web.departments.members.remove', ['id' => $department->id, 'userId' => $user->id]) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment retirer cet utilisateur du département ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" style="padding: 6px; border-radius: 8px; cursor: pointer;" title="Retirer du département">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            {{ org_trans('no_members_in_department') }}
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Channels List -->
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        {{ org_trans('channels') }} ({{ $department->channels->count() }})
                    </h2>

                    <div class="members-list">
                        @forelse($department->channels as $channel)
                        <div class="member-item">
                            <div class="member-avatar" style="background: linear-gradient(135deg, #a78bfa, #8b5cf6);">
                                #
                            </div>
                            <div class="member-info">
                                <div class="member-name">{{ $channel->name }}</div>
                                <div class="member-role">{{ $channel->description ?? 'Aucune description' }}</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if(auth()->user()->canManageUsers())
                                <form action="{{ route('web.channels.destroy', $channel->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce canal ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" style="padding: 6px; border-radius: 8px; cursor: pointer;" title="Supprimer le canal">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            Aucun canal associé
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Projects List -->
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        {{ org_trans('projects') }} ({{ $department->projects->count() }})
                    </h2>

                    <div class="members-list">
                        @forelse($department->projects as $project)
                        <div class="member-item">
                            <div class="member-avatar" style="background: linear-gradient(135deg, #34d399, #10b981);">
                                P
                            </div>
                            <div class="member-info">
                                <div class="member-name">{{ $project->name }}</div>
                                <div class="member-role">{{ $project->description ?? 'Aucune description' }}</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="badge {{ $project->status === 'completed' ? 'badge-active' : 'badge-inactive' }}" style="font-size: 10px;">
                                    {{ $project->status }}
                                </span>
                                @if(auth()->user()->canManageUsers())
                                <form action="{{ route('web.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce projet ?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                                    <button type="submit" class="btn-danger" style="padding: 6px; border-radius: 8px; cursor: pointer;" title="Supprimer le projet">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            Aucun projet associé
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-content">
                <!-- Info Card -->
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ org_trans('information') }}
                    </h2>

                    <div class="info-item">
                        <span class="info-label">{{ org_trans('status') }}</span>
                        <span class="info-value">
                            <span class="badge {{ $department->is_active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $department->is_active ? org_trans('active') : org_trans('inactive') }}
                            </span>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">{{ org_trans('organization') }}</span>
                        <span class="info-value">{{ $department->organization->name }}</span>
                    </div>

                    @if($department->head)
                    <div class="info-item">
                        <span class="info-label">{{ org_trans('department_head') }}</span>
                        <span class="info-value">{{ $department->head->name }}</span>
                    </div>
                    @endif

                    <div class="info-item">
                        <span class="info-label">{{ org_trans('created_on') }}</span>
                        <span class="info-value">{{ $department->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">{{ org_trans('updated_on') }}</span>
                        <span class="info-value">{{ $department->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if(auth()->user()->canManageUsers())
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ org_trans('quick_actions') }}
                    </h2>

                    <form action="{{ route('web.departments.toggleStatus', $department->id) }}" method="POST" id="statusForm">
                        @csrf
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Statut du département</label>
                            <div style="position: relative;">
                                <select name="status" class="form-select" onchange="this.form.submit()" style="cursor: pointer; appearance: none; padding-right: 30px;">
                                    <option value="1" {{ $department->is_active ? 'selected' : '' }}>🟢 Actif</option>
                                    <option value="0" {{ !$department->is_active ? 'selected' : '' }}>🔴 Inactif</option>
                                </select>
                                <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: rgba(255,255,255,0.5);">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un membre</h3>
                <button onclick="closeAddMemberModal()" class="close-modal">&times;</button>
            </div>
            <form action="{{ route('web.departments.members.add', $department->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Sélectionner un utilisateur</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Choisir un utilisateur...</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeAddMemberModal()" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddMemberModal() {
            const modal = document.getElementById('addMemberModal');
            modal.style.display = 'flex';
            // Force reflow
            modal.offsetHeight;
            modal.classList.add('active');
        }

        function closeAddMemberModal() {
            const modal = document.getElementById('addMemberModal');
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('addMemberModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddMemberModal();
            }
        });
    </script>
</x-app-layout>
