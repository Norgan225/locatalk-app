<x-app-layout>
    <style>
        .departments-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, rgba(255, 255, 255, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
        }

        .departments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .department-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .department-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-4px);
        }

        .department-color-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .department-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .department-name {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .department-org {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .department-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .department-stats {
            display: flex;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .department-stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .department-stat-icon {
            width: 16px;
            height: 16px;
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

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(223, 85, 38, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.03);
            border: 2px dashed rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            color: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .departments-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 24px;
            }

            .departments-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="departments-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ org_trans('departments') }}</h1>
            @if(auth()->user()->canManageUsers())
            <a href="{{ route('web.departments.create') }}" class="btn-primary">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ org_trans('new_department') }}
            </a>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">{{ org_trans('total') }}</div>
                <div class="stat-value">{{ $departments->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ org_trans('active') }}</div>
                <div class="stat-value" style="color: #34d399;">{{ $departments->where('is_active', true)->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ org_trans('inactive') }}</div>
                <div class="stat-value" style="color: #ef4444;">{{ $departments->where('is_active', false)->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">{{ org_trans('total') }} {{ org_trans('members') }}</div>
                <div class="stat-value" style="color: #fbbb2a;">{{ $departments->sum('members_count') }}</div>
            </div>
        </div>

        <!-- Departments Grid -->
        <div class="departments-grid">
            @forelse($departments as $department)
            <div class="department-card" onclick="window.location.href='{{ route('web.departments.show', $department->id) }}'">
                <div class="department-color-bar" style="background: {{ $department->color ?? 'linear-gradient(135deg, #df5526, #fbbb2a)' }};"></div>

                <div class="department-header">
                    <div>
                        <h3 class="department-name">{{ $department->name }}</h3>
                        <div class="department-org">
                            <svg class="department-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $department->organization->name }}
                        </div>
                    </div>
                    <span class="badge {{ $department->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $department->is_active ? org_trans('active') : org_trans('inactive') }}
                    </span>
                </div>

                @if($department->description)
                <p class="department-description">{{ $department->description }}</p>
                @endif

                <div class="department-stats">
                    <div class="department-stat">
                        <svg class="department-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>{{ $department->members_count }} {{ $department->members_count > 1 ? org_trans('members') : org_trans('member') }}</span>
                    </div>

                    @if($department->head)
                    <div class="department-stat">
                        <svg class="department-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ org_trans('head') }}: {{ $department->head->name }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column: 1 / -1;">
                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 style="color: rgba(255, 255, 255, 0.7); margin-bottom: 8px;">{{ org_trans('no_departments_available') }}</h3>
                <p style="color: rgba(255, 255, 255, 0.5);">{{ org_trans('create_first_department') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
