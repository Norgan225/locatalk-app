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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .projects-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem;
            font-family: 'Inter', sans-serif;
        }

        .project-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 28px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .project-card:hover::before {
            transform: scaleX(1);
        }

        .project-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.4);
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(223, 85, 38, 0.3);
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
            color: var(--text-muted);
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
            color: var(--text-muted);
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
            background: var(--primary-gradient);
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
            background: var(--primary-gradient);
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
            background: var(--primary-gradient);
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
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-tab:hover, .filter-tab.active {
            background: rgba(251, 187, 42, 0.1);
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
            color: var(--text-muted);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            opacity: 0.3;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-owner {
            background: rgba(251, 187, 42, 0.15);
            color: #fbbb2a;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .role-manager {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .role-member {
            background: rgba(139, 92, 246, 0.15);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .search-bar {
            position: relative;
            max-width: 400px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.5);
            box-shadow: 0 0 0 3px rgba(251, 187, 42, 0.1);
        }

        .search-bar input::placeholder {
            color: var(--text-muted);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-muted);
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.15);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-top: 0;
        }

        .deadline-warning {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            color: #f87171;
            font-size: 11px;
            font-weight: 600;
        }

        .deadline-ok {
            color: var(--text-muted);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .project-card {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .project-card:nth-child(1) { animation-delay: 0.05s; }
        .project-card:nth-child(2) { animation-delay: 0.1s; }
        .project-card:nth-child(3) { animation-delay: 0.15s; }
        .project-card:nth-child(4) { animation-delay: 0.2s; }
        .project-card:nth-child(5) { animation-delay: 0.25s; }
        .project-card:nth-child(6) { animation-delay: 0.3s; }
    </style>

    <div class="projects-page">
        <!-- Header -->
        <div class="section-header">
            <div>
                <h1 class="section-title">📁 {{ org_trans('my') }} {{ org_trans('projects') }}</h1>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">
                    @if(auth()->user()->isSuperAdmin())
                        Tous les projets de toutes les organisations
                    @else
                        Projets de votre organisation où vous êtes membre
                    @endif
                </p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="projectSearch" placeholder="Rechercher un projet..." onkeyup="filterProjects()">
                </div>
                @if(auth()->user()->canManageUsers())
                <a href="{{ route('web.projects.create') }}" class="btn-create">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ org_trans('new') }} {{ org_trans('projects') }}
                </a>
                @endif
            </div>
        </div>

        <!-- Stats Overview -->
        @php
            $totalProjects = $stats['total_projects'] ?? $projects->count();
            $activeProjects = $stats['active_projects'] ?? $projects->where('status', 'active')->count();
            $completedProjects = $stats['completed_projects'] ?? $projects->where('status', 'completed')->count();
            $totalTasks = $stats['total_tasks'] ?? $projects->sum('total_tasks');
            $completedTasks = $stats['completed_tasks'] ?? $projects->sum('completed_tasks');
            $avgCompletion = $stats['avg_completion'] ?? ($projects->count() > 0 ? round($projects->avg('completion_percentage')) : 0);
        @endphp
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-value" id="stat-total-projects">{{ $totalProjects }}</div>
                <div class="stat-label">Total Projets</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-active-projects">{{ $activeProjects }}</div>
                <div class="stat-label">Projets en cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-completed-projects">{{ $completedProjects }}</div>
                <div class="stat-label">Projets terminés</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-tasks-summary">{{ $completedTasks }} / {{ $totalTasks }}</div>
                <div class="stat-label">Tâches terminées</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-avg-completion">{{ $avgCompletion }}%</div>
                <div class="stat-label">Progression moyenne</div>
            </div>
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
        <div class="projects-grid" id="projectsGrid">
            @foreach($projects as $project)
            <div class="project-card"
                 data-project-name="{{ strtolower($project->name) }}"
                 data-status="{{ $project->status }}"
                 data-total-tasks="{{ $project->total_tasks ?? 0 }}"
                 data-completed-tasks="{{ $project->completed_tasks ?? 0 }}"
                 data-completion="{{ $project->completion_percentage ?? 0 }}"
                 onclick="window.location.href='{{ route('web.projects.show', $project->id) }}'">
                <div class="project-header">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <h3 class="project-title">{{ $project->name }}</h3>
                            @if($project->is_owner)
                                <span class="role-badge role-owner">
                                    👑 Owner
                                </span>
                            @elseif($project->user_role === 'manager')
                                <span class="role-badge role-manager">
                                    🎯 Manager
                                </span>
                            @elseif($project->user_role === 'member')
                                <span class="role-badge role-member">
                                    👤 Membre
                                </span>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span class="status-badge status-{{ $project->status }}">
                                @if($project->status == 'active')
                                    ⚡ {{ org_trans('active') }}
                                @elseif($project->status == 'completed')
                                    ✅ {{ org_trans('done') }}
                                @elseif($project->status == 'on_hold')
                                    ⏸️ {{ org_trans('inactive') }}
                                @else
                                    {{ $project->status }}
                                @endif
                            </span>
                            @if($project->deadline)
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $deadline = \Carbon\Carbon::parse($project->deadline);
                                    $daysRemaining = (int) $now->diffInDays($deadline, false);
                                    $isOverdue = $daysRemaining < 0;
                                    $isUrgent = $daysRemaining >= 0 && $daysRemaining <= 7;
                                @endphp
                                @if($isOverdue && $project->status !== 'completed')
                                    <span class="deadline-warning">
                                        ⚠️ En retard de {{ abs($daysRemaining) }}j
                                    </span>
                                @elseif($isUrgent && $project->status !== 'completed')
                                    <span class="deadline-warning">
                                        🔥 {{ $daysRemaining }}j restants
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($project->users && $project->users->count() > 0)
                    <div class="avatar-stack">
                        @foreach($project->users->take(4) as $user)
                        <div class="avatar" title="{{ $user->name }} - {{ $user->pivot->role }}">
                            @if($user->avatar)
                                <img src="/storage/{{ $user->avatar }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        @endforeach
                        @if($project->users->count() > 4)
                        <div class="avatar" title="{{ $project->users->count() }} {{ org_trans('members') }}">
                            +{{ $project->users->count() - 4 }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <p class="project-description">
                    {{ $project->description ? Str::limit($project->description, 140) : 'Aucune description disponible pour ce projet.' }}
                </p>

                <div class="project-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ $project->completed_tasks ?? 0 }}/{{ $project->total_tasks ?? 0 }} tâches
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ $project->members_count ?? 0 }} membre(s)
                    </div>
                    @if($project->deadline)
                    <div class="meta-item {{ $isOverdue ? 'deadline-warning' : 'deadline-ok' }}">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                    </div>
                    @endif
                    <div class="meta-item">
                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Créé par {{ $project->creator->name ?? 'Inconnu' }}
                    </div>
                </div>

                <!-- Progress Bar -->
                <div style="margin-top: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">Progression</span>
                        <span style="font-size: 13px; font-weight: 700; color: {{ $project->completion_percentage >= 75 ? '#34d399' : ($project->completion_percentage >= 50 ? '#fbbb2a' : 'rgba(255, 255, 255, 0.6)') }};">
                            {{ round($project->completion_percentage ?? 0) }}%
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $project->completion_percentage ?? 0 }}%"></div>
                    </div>
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

    <script>
        function filterProjects() {
            const searchValue = document.getElementById('projectSearch').value.toLowerCase();
            const projectCards = document.querySelectorAll('.project-card');
            let visibleCount = 0;

            // Variables pour les stats dynamiques
            let totalProjects = 0;
            let activeProjects = 0;
            let completedProjects = 0;
            let totalTasks = 0;
            let completedTasks = 0;
            let sumCompletion = 0;

            projectCards.forEach(card => {
                const projectName = card.getAttribute('data-project-name');
                if (projectName.includes(searchValue)) {
                    card.style.display = 'block';
                    visibleCount++;

                    // Mise à jour des stats
                    totalProjects++;
                    const status = card.getAttribute('data-status');
                    if (status === 'active') activeProjects++;
                    if (status === 'completed') completedProjects++;

                    totalTasks += parseInt(card.getAttribute('data-total-tasks') || 0);
                    completedTasks += parseInt(card.getAttribute('data-completed-tasks') || 0);
                    sumCompletion += parseFloat(card.getAttribute('data-completion') || 0);
                } else {
                    card.style.display = 'none';
                }
            });

            // Mise à jour de l'affichage des stats
            document.getElementById('stat-total-projects').textContent = totalProjects;
            document.getElementById('stat-active-projects').textContent = activeProjects;
            document.getElementById('stat-completed-projects').textContent = completedProjects;
            document.getElementById('stat-tasks-summary').textContent = completedTasks + ' / ' + totalTasks;

            const avgCompletion = totalProjects > 0 ? Math.round(sumCompletion / totalProjects) : 0;
            document.getElementById('stat-avg-completion').textContent = avgCompletion + '%';

            // Afficher un message si aucun résultat
            const grid = document.getElementById('projectsGrid');
            let noResults = document.getElementById('noSearchResults');

            if (visibleCount === 0 && searchValue !== '') {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.id = 'noSearchResults';
                    noResults.className = 'empty-state';
                    noResults.innerHTML = `
                        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 style="color: white; margin-bottom: 12px; font-size: 20px;">Aucun projet trouvé</h3>
                        <p style="color: rgba(255, 255, 255, 0.5);">Aucun projet ne correspond à votre recherche "${searchValue}"</p>
                    `;
                    grid.parentElement.appendChild(noResults);
                }
                grid.style.display = 'none';
            } else {
                if (noResults) {
                    noResults.remove();
                }
                grid.style.display = 'grid';
            }
        }

        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.project-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                }, index * 50);
            });
        });
    </script>

</x-app-layout>
