<x-app-layout>
    <style>
        .project-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .project-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(223, 85, 38, 0.2);
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .project-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .project-description {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .project-meta {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .meta-icon {
            width: 16px;
            height: 16px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-completed {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-on_hold {
            background: rgba(251, 187, 42, 0.2);
            color: #fbbb2a;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 12px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .avatar-stack {
            display: flex;
            margin-left: auto;
        }

        .avatar-stack .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 12px;
            margin-left: -8px;
            border: 2px solid #0f172a;
        }

        .btn-create {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.4);
        }

        .filter-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-tab:hover, .filter-tab.active {
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));
            border-color: rgba(251, 187, 42, 0.3);
            color: #fbbb2a;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            opacity: 0.3;
        }
    </style>

    <div class="p-8">
        <!-- Header -->
        <div class="section-header">
            <h1 class="section-title">📁 {{ org_trans('my') }} {{ org_trans('projects') }}</h1>
            @if(auth()->user()->canManageUsers())
            <a href="{{ route('web.projects.create') }}" class="btn-create">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ org_trans('new') }} {{ org_trans('projects') }}
            </a>
            @endif
        </div>

        <!-- Filters -->
        <div class="filter-tabs">
            <div class="filter-tab {{ request('status') == '' ? 'active' : '' }}"
                 onclick="window.location.href='{{ route('web.projects') }}'">
                {{ org_trans('all') }} ({{ $projects->count() }})
            </div>
            <div class="filter-tab {{ request('status') == 'active' ? 'active' : '' }}"
                 onclick="window.location.href='{{ route('web.projects', ['status' => 'active']) }}'">
                {{ org_trans('active') }} ({{ $projects->where('status', 'active')->count() }})
            </div>
            <div class="filter-tab {{ request('status') == 'completed' ? 'active' : '' }}"
                 onclick="window.location.href='{{ route('web.projects', ['status' => 'completed']) }}'">
                {{ org_trans('done') }} ({{ $projects->where('status', 'completed')->count() }})
            </div>
            <div class="filter-tab {{ request('status') == 'on_hold' ? 'active' : '' }}"
                 onclick="window.location.href='{{ route('web.projects', ['status' => 'on_hold']) }}'">
                {{ org_trans('inactive') }} ({{ $projects->where('status', 'on_hold')->count() }})
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #34d399;">
            ✓ {{ session('success') }}
        </div>
        @endif

        <!-- Projects Grid -->
        @if($projects && $projects->count() > 0)
        <div class="projects-grid">
            @foreach($projects as $project)
            <div class="project-card" onclick="window.location.href='{{ route('web.projects.show', $project->id) }}'">
                <div class="project-header">
                    <div style="flex: 1;">
                        <h3 class="project-title">{{ $project->name }}</h3>
                        <span class="status-badge status-{{ $project->status }}">
                            @if($project->status == 'active') {{ org_trans('active') }}
                            @elseif($project->status == 'completed') {{ org_trans('done') }}
                            @elseif($project->status == 'on_hold') {{ org_trans('inactive') }}
                            @else {{ $project->status }}
                            @endif
                        </span>
                    </div>
                    @if($project->users && $project->users->count() > 0)
                    <div class="avatar-stack">
                        @foreach($project->users->take(3) as $user)
                        <div class="avatar" title="{{ $user->name }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @endforeach
                        @if($project->users->count() > 3)
                        <div class="avatar" title="{{ $project->users->count() }} {{ org_trans('members') }}">
                            +{{ $project->users->count() - 3 }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <p class="project-description">
                    {{ $project->description ? Str::limit($project->description, 120) : org_trans('description') }}
                </p>

                <div class="project-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ $project->total_tasks ?? 0 }} tâche(s)
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ $project->members_count ?? 0 }} membre(s)
                    </div>
                    @if($project->deadline)
                    <div class="meta-item">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                    </div>
                    @endif
                </div>

                <!-- Progress Bar -->
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $project->completion_percentage ?? 0 }}%"></div>
                </div>
                <div style="text-align: right; margin-top: 6px; color: rgba(255, 255, 255, 0.5); font-size: 12px;">
                    {{ round($project->completion_percentage ?? 0) }}{{ org_trans('completed_percentage') }}
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 style="color: white; margin-bottom: 12px; font-size: 20px;">{{ org_trans('no_projects_found') }}</h3>
            <p>
                @if(request('status'))
                    {{ org_trans('no_projects_with_status') }} "{{ request('status') }}".
                @else
                    {{ org_trans('no_projects_yet') }}
                    @if(auth()->user()->canManageUsers())
                        <a href="{{ route('web.projects.create') }}" style="color: #fbbb2a; text-decoration: underline;">{{ org_trans('create_one_to_start') }}</a>
                    @endif
                @endif
            </p>
        </div>
        @endif
    </div>

</x-app-layout>
