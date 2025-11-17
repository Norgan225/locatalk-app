<x-app-layout>
    <style>
        .show-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
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
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
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
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
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
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(223, 85, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
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
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-title svg {
            width: 20px;
            height: 20px;
        }

        .description-text {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }

        .info-value {
            color: #ffffff;
            font-weight: 500;
            font-size: 13px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active {
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .badge-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .members-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .member-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .member-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
        }

        .member-info {
            flex: 1;
        }

        .member-name {
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .member-role {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
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
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <div class="color-bar" style="background: {{ $department->color ?? 'linear-gradient(135deg, #df5526, #fbbb2a)' }};"></div>
                <h1 class="page-title">{{ $department->name }}</h1>
                <div class="page-subtitle">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="stat-value" style="color: #fbbb2a;">{{ $department->statistics['total_members'] }}</div>
                            <div class="stat-label">{{ org_trans('members') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" style="color: #34d399;">{{ $department->statistics['active_projects'] }}</div>
                            <div class="stat-label">{{ org_trans('active_projects') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" style="color: #3b82f6;">{{ $department->statistics['total_projects'] }}</div>
                            <div class="stat-label">{{ org_trans('total_projects') }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" style="color: #8b5cf6;">{{ $department->statistics['channels_count'] }}</div>
                            <div class="stat-label">{{ org_trans('channels') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Members List -->
                <div class="card">
                    <h2 class="card-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        {{ org_trans('department_members') }} ({{ $department->users->count() }})
                    </h2>

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
                            @if($user->id === $department->head_user_id)
                            <span class="badge badge-active">{{ org_trans('head') }}</span>
                            @endif
                        </div>
                        @empty
                        <div class="empty-state">
                            {{ org_trans('no_members_in_department') }}
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

                    <form action="{{ route('web.departments.toggleStatus', $department->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.75rem;">
                            @if($department->is_active)
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            {{ org_trans('deactivate_department') }}
                            @else
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ org_trans('activate_department') }}
                            @endif
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
