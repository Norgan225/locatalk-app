<x-app-layout>
    <style>
        .user-profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .btn-cancel {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-create {
            padding: 10px 20px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(223, 85, 38, 0.3);
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        /* 🔴 Super Admin - Rouge dégradé avec ombre puissante */
        .role-super_admin {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: white;
            box-shadow: 0 4px 16px rgba(220, 38, 38, 0.6);
            border: 1px solid rgba(239, 68, 68, 0.8);
            font-weight: 800;
        }

        /* 🟠 Owner - Orange/Jaune dégradé */
        .role-owner {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            box-shadow: 0 4px 12px rgba(251, 187, 42, 0.4);
            border: 1px solid rgba(251, 187, 42, 0.5);
        }

        /* 🔵 Admin - Bleu glassmorphism */
        .role-admin {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.5);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            backdrop-filter: blur(10px);
        }

        /* 🟢 Responsable - Vert glassmorphism */
        .role-responsable {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.5);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            backdrop-filter: blur(10px);
        }

        /* 🟣 Employé - Violet glassmorphism */
        .role-employe {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.5);
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
            backdrop-filter: blur(10px);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .status-active {
            background: #34d399;
            box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);
        }

        .status-inactive {
            background: #f87171;
        }

        .status-suspended {
            background: #fbbb2a;
        }

        .priority-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-low {
            background: rgba(96, 165, 250, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(96, 165, 250, 0.3);
        }

        .priority-medium {
            background: rgba(251, 187, 42, 0.2);
            color: #fbbb2a;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .priority-high {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
            border: 1px solid rgba(251, 146, 60, 0.3);
        }

        .priority-urgent {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .profile-header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 36px;
            flex-shrink: 0;
        }

        .profile-info {
            flex: 1;
            min-width: 300px;
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .profile-email {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 12px;
        }

        .profile-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .profile-actions {
            display: flex;
            gap: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
        }

        .info-card-title {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .info-card-value {
            font-size: 20px;
            font-weight: 700;
            color: white;
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

        .activity-list {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
        }

        .activity-item {
            display: flex;
            align-items: start;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(96, 165, 250, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            color: white;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .activity-meta {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }
    </style>

    <div class="user-profile-container">
        <!-- Bouton retour -->
        <div style="margin-bottom: 24px;">
            <a href="{{ route('web.users') }}" class="btn-cancel" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la liste
            </a>
        </div>

        @if(session('success'))
            <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-large">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <h1 class="profile-name">{{ $user->name }}</h1>
                <div class="profile-email">{{ $user->email }}</div>
                <div class="profile-badges">
                    <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    <span class="status-badge status-{{ $user->status }}">
                        <span class="status-indicator status-{{ $user->status }}"></span>
                        {{ ucfirst($user->status) }}
                    </span>
                    @if($user->created_by == auth()->id())
                        <span style="padding: 4px 12px; background: rgba(96, 165, 250, 0.2); color: #60a5fa; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            {{ org_trans('created_by_you') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="profile-actions">
                @if((auth()->user()->canManageUsers() || auth()->id() == $user->id) && !($user->role === 'owner' && !auth()->user()->isOwner()))
                    <a href="{{ route('web.users.edit', $user->id) }}" class="btn-create">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ org_trans('edit') }}
                    </a>
                @endif
            </div>
        </div>

        <!-- Informations Grid -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-title">{{ org_trans('organization') }}</div>
                <div class="info-card-value">{{ $user->organization ? $user->organization->name : org_trans('none') }}</div>
            </div>

            <div class="info-card">
                <div class="info-card-title">{{ org_trans('department') }}</div>
                <div class="info-card-value">{{ $user->department ? $user->department->name : org_trans('none_f') }}</div>
            </div>

            <div class="info-card">
                <div class="info-card-title">{{ org_trans('phone') }}</div>
                <div class="info-card-value">{{ $user->phone ?? org_trans('not_specified') }}</div>
            </div>

            <div class="info-card">
                <div class="info-card-title">{{ org_trans('member_since') }}</div>
                <div class="info-card-value">{{ $user->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="section-title">
            <svg style="width: 24px; height: 24px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            {{ org_trans('statistics') }}
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-title">{{ org_trans('projects_created') }}</div>
                <div class="info-card-value">{{ $user->createdProjects ? $user->createdProjects->count() : 0 }}</div>
            </div>

            <div class="info-card">
                <div class="info-card-title">{{ org_trans('assigned_tasks') }}</div>
                <div class="info-card-value">{{ $user->assignedTasks ? $user->assignedTasks->count() : 0 }}</div>
            </div>

            <div class="info-card">
                <div class="info-card-title">{{ org_trans('users_created') }}</div>
                <div class="info-card-value">{{ $user->createdUsers ? $user->createdUsers->count() : 0 }}</div>
            </div>

            <div class="info-card">
                <div class="info-card-title">{{ org_trans('last_activity') }}</div>
                <div class="info-card-value" style="font-size: 14px;">
                    {{ $user->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @if($user->assignedTasks && $user->assignedTasks->count() > 0)
        <div class="section-title" style="margin-top: 32px;">
            <svg style="width: 24px; height: 24px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            {{ org_trans('recent_tasks') }}
        </div>

        <div class="activity-list">
            @foreach($user->assignedTasks->take(5) as $task)
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba({{ $task->status == 'completed' ? '52, 211, 153' : ($task->status == 'in_progress' ? '251, 187, 42' : '96, 165, 250') }}, 0.2);">
                    <svg style="width: 20px; height: 20px; color: {{ $task->status == 'completed' ? '#34d399' : ($task->status == 'in_progress' ? '#fbbb2a' : '#60a5fa') }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div class="activity-content">
                    <div class="activity-title">{{ $task->title }}</div>
                    <div class="activity-meta">
                        {{ $task->project ? $task->project->name : org_trans('no_project') }} •
                        <span class="priority-badge priority-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span> •
                        {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : org_trans('no_due_date') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

</x-app-layout>
