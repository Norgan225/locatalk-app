<x-app-layout>
    <style>
        /* Base Styles */
        .project-show-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem;
            color: white;
            font-family: 'Inter', sans-serif;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 24px;
            transition: color 0.2s;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .back-btn:hover {
            color: white;
            background: rgba(255, 255, 255, 0.08);
        }

        /* Hero Section */
        .hero-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .project-title {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .project-meta-row {
            display: flex;
            gap: 24px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            align-items: center;
        }

        .meta-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Status Badges */
        .status-badge {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .status-active { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-completed { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-on_hold { background: rgba(251, 187, 42, 0.15); color: #fbbb2a; border: 1px solid rgba(251, 187, 42, 0.3); }
        .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

        /* Task Statuses */
        .status-todo { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
        .status-in_progress { background: rgba(251, 187, 42, 0.15); color: #fbbb2a; border: 1px solid rgba(251, 187, 42, 0.3); }

        /* Progress Bar */
        .hero-progress {
            margin-top: 32px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .progress-track {
            height: 12px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            overflow: hidden;
            margin: 12px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #df5526, #fbbb2a);
            border-radius: 6px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }

        /* Tabs */
        .tabs-container {
            margin-top: 40px;
        }

        .tabs-nav {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1px;
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #fbbb2a;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .tab-btn:hover {
            color: white;
        }

        .tab-btn.active {
            color: #fbbb2a;
        }

        .tab-btn.active::after {
            transform: scaleX(1);
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .stat-box-value {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin: 8px 0;
            background: linear-gradient(135deg, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-box-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Team Grid */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .member-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .member-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
        }

        .member-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 14px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Task List */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .task-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            transition: all 0.2s;
        }

        .task-row:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .priority-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .priority-high { background: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.4); }
        .priority-medium { background: #f59e0b; box-shadow: 0 0 8px rgba(245, 158, 11, 0.4); }
        .priority-low { background: #3b82f6; box-shadow: 0 0 8px rgba(59, 130, 246, 0.4); }

        /* Action Buttons */
        .action-btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            box-shadow: 0 4px 15px rgba(223, 85, 38, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(223, 85, 38, 0.4);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>

    <div class="project-show-page">
        <!-- Navigation -->
        <a href="{{ route('web.projects') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux projets
        </a>

        <!-- Hero Section -->
        <div class="glass-panel animate-fade-in">
            <div class="hero-header">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                        @php
                            $canEdit = auth()->user()->isSuperAdmin() || auth()->user()->id === $project->created_by || $project->users->contains(auth()->id());
                        @endphp

                        @if($canEdit)
                            <form action="{{ route('web.projects.update', $project->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="status-badge status-{{ $project->status }}" style="appearance: none; -webkit-appearance: none; cursor: pointer; padding-right: 32px; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23FFFFFF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 12px top 50%; background-size: 10px auto;">
                                    <option value="active" {{ $project->status == 'active' ? 'selected' : '' }} style="color: black;">⚡ {{ org_trans('active') }}</option>
                                    <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }} style="color: black;">✅ {{ org_trans('completed') }}</option>
                                    <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }} style="color: black;">⏸️ {{ org_trans('on_hold') }}</option>
                                    <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }} style="color: black;">❌ {{ org_trans('cancelled') }}</option>
                                </select>
                            </form>
                        @else
                            <span class="status-badge status-{{ $project->status }}">
                                @if($project->status == 'active') ⚡ {{ org_trans('active') }}
                                @elseif($project->status == 'completed') ✅ {{ org_trans('completed') }}
                                @elseif($project->status == 'on_hold') ⏸️ {{ org_trans('on_hold') }}
                                @elseif($project->status == 'cancelled') ❌ {{ org_trans('cancelled') }}
                                @else {{ $project->status }}
                                @endif
                            </span>
                        @endif

                        @if($project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() && $project->status !== 'completed')
                            <span style="color: #ef4444; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 4px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                En retard
                            </span>
                        @endif
                    </div>
                    <h1 class="project-title">{{ $project->name }}</h1>
                    <div class="project-meta-row">
                        <div class="meta-pill">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Deadline: {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Non définie' }}
                        </div>
                        <div class="meta-pill">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $project->department ? $project->department->name : 'Aucun département' }}
                        </div>
                        <div class="meta-pill">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Owner: {{ $project->creator->name ?? 'Inconnu' }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    @if(auth()->user()->canManageUsers() || auth()->id() == $project->created_by)
                    <a href="{{ route('web.projects.edit', $project->id) }}" class="action-btn btn-glass">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Modifier
                    </a>
                    @endif
                    <button class="action-btn btn-primary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nouvelle Tâche
                    </button>
                </div>
            </div>

            <div class="hero-progress">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-weight: 600; color: rgba(255,255,255,0.8);">Progression globale</span>
                    <span id="global-progress-text" style="font-weight: 700; color: #fbbb2a;">{{ round($project->statistics['completion_percentage'] ?? 0) }}%</span>
                </div>
                <div class="progress-track">
                    <div id="global-progress-bar" class="progress-fill" style="width: {{ $project->statistics['completion_percentage'] ?? 0 }}%"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: rgba(255,255,255,0.4);">
                    <span>Démarrage</span>
                    <span>Objectif: 100%</span>
                </div>
            </div>
        </div>

        <!-- Tabs & Content -->
        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('overview')">Vue d'ensemble</button>
                <button class="tab-btn" onclick="switchTab('tasks')">Tâches ({{ $project->tasks->count() }})</button>
                <button class="tab-btn" onclick="switchTab('team')">Équipe ({{ $project->users->count() }})</button>
                <button class="tab-btn" onclick="switchTab('comments')">Commentaires ({{ $project->comments->count() }})</button>
            </div>

            <!-- Tab: Overview -->
            <div id="tab-overview" class="tab-content animate-fade-in">
                <div class="content-grid">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <!-- Description -->
                        <div class="glass-card">
                            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: white;">À propos du projet</h3>
                            <p style="color: rgba(255,255,255,0.7); line-height: 1.7;">
                                {{ $project->description ?? 'Aucune description détaillée disponible pour ce projet.' }}
                            </p>
                        </div>

                        <!-- Stats Row -->
                        <div class="stats-row">
                            <div class="stat-box">
                                <div class="stat-box-label">Total Tâches</div>
                                <div id="stat-total-tasks" class="stat-box-value">{{ $project->statistics['total_tasks'] ?? 0 }}</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-box-label">Terminées</div>
                                <div id="stat-completed-tasks" class="stat-box-value" style="background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                    {{ $project->statistics['completed_tasks'] ?? 0 }}
                                </div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-box-label">En cours</div>
                                <div id="stat-pending-tasks" class="stat-box-value" style="background: linear-gradient(135deg, #fbbb2a, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                    {{ $project->statistics['pending_tasks'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div class="glass-card">
                            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: white;">Membres clés</h3>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @foreach($project->users->take(3) as $user)
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="member-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 600; color: white;">{{ $user->name }}</div>
                                        <div style="font-size: 12px; color: rgba(255,255,255,0.5);">{{ $user->pivot->role ?? 'Membre' }}</div>
                                    </div>
                                </div>
                                @endforeach
                                @if($project->users->count() > 3)
                                <div style="padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center;">
                                    <a href="#" onclick="switchTab('team'); return false;" style="font-size: 13px; color: #fbbb2a; text-decoration: none;">Voir tout l'équipe →</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Tasks -->
            <div id="tab-tasks" class="tab-content animate-fade-in" style="display: none;">
                <div class="glass-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: white;">Liste des tâches</h3>
                        <div style="display: flex; gap: 8px;">
                            <div style="position: relative;">
                                <select id="taskFilter" onchange="filterTasks()" class="action-btn btn-glass" style="padding-right: 32px; appearance: none; -webkit-appearance: none;">
                                    <option value="all" style="color: black;">Tous les statuts</option>
                                    <option value="todo" style="color: black;">À faire</option>
                                    <option value="in_progress" style="color: black;">En cours</option>
                                    <option value="completed" style="color: black;">Terminé</option>
                                </select>
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <div style="position: relative;">
                                <select id="taskSort" onchange="sortTasks()" class="action-btn btn-glass" style="padding-right: 32px; appearance: none; -webkit-appearance: none;">
                                    <option value="date_desc" style="color: black;">Plus récent</option>
                                    <option value="date_asc" style="color: black;">Plus ancien</option>
                                    <option value="priority" style="color: black;">Priorité</option>
                                </select>
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    @if($project->tasks && $project->tasks->count() > 0)
                        <div class="task-list" id="projectTaskList">
                            @foreach($project->tasks as $task)
                            <div class="task-row"
                                 data-status="{{ $task->status }}"
                                 data-date="{{ $task->created_at->timestamp }}"
                                 data-priority="{{ $task->priority == 'urgent' ? 4 : ($task->priority == 'high' ? 3 : ($task->priority == 'medium' ? 2 : 1)) }}"
                                 onclick="if(!event.target.closest('.status-select-container')) window.location.href='{{ route('web.tasks.show', $task->id) }}'"
                                 style="cursor: pointer;">

                                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                                    <div class="priority-dot priority-{{ $task->priority ?? 'medium' }}" title="Priorité {{ $task->priority }}"></div>
                                    <div>
                                        <div style="font-weight: 600; color: white; margin-bottom: 4px; font-size: 15px;">{{ $task->title }}</div>
                                        <div style="font-size: 12px; color: rgba(255,255,255,0.5); display: flex; gap: 12px; align-items: center;">
                                            @if($task->assignee)
                                            <span style="display: flex; align-items: center; gap: 4px;">
                                                <div style="width: 16px; height: 16px; border-radius: 50%; background: linear-gradient(135deg, #df5526, #fbbb2a); display: flex; align-items: center; justify-content: center; font-size: 8px; color: white; font-weight: bold;">
                                                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                                </div>
                                                {{ $task->assignee->name }}
                                            </span>
                                            @endif
                                            @if($task->due_date)
                                            <span style="display: flex; align-items: center; gap: 4px;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div class="status-select-container" onclick="event.stopPropagation()">
                                        @if(auth()->user()->canManageUsers() || auth()->id() == $project->created_by)
                                            <select onchange="updateTaskStatus(this, {{ $task->id }})"
                                                    class="status-badge status-{{ $task->status }}"
                                                    style="appearance: none; -webkit-appearance: none; border: none; cursor: pointer; padding-right: 24px; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23FFFFFF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 8px center; background-size: 8px;">
                                                <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }} style="color: black;">À faire</option>
                                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }} style="color: black;">En cours</option>
                                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }} style="color: black;">Terminé</option>
                                            </select>
                                        @else
                                            <span class="status-badge status-{{ $task->status }}" style="font-size: 11px; padding: 4px 10px;">
                                                @if($task->status == 'todo') À faire
                                                @elseif($task->status == 'in_progress') En cours
                                                @elseif($task->status == 'completed') Terminé
                                                @else {{ $task->status }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>

                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: rgba(255,255,255,0.3);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px; color: rgba(255,255,255,0.4);">
                            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 16px; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p>Aucune tâche pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab: Team -->
            <div id="tab-team" class="tab-content animate-fade-in" style="display: none;">
                <div class="glass-card">
                    <h3 style="font-size: 18px; font-weight: 700; color: white; margin-bottom: 24px;">Membres du projet</h3>
                    <div class="team-grid">
                        @foreach($project->users as $member)
                        <div class="member-card">
                            <div class="member-avatar">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: white;">{{ $member->name }}</div>
                                <div style="font-size: 12px; color: rgba(255,255,255,0.5);">
                                    {{ $member->pivot->role ?? 'Membre' }}
                                    @if($member->id == $project->created_by) • Créateur @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tab: Comments -->
            <div id="tab-comments" class="tab-content animate-fade-in" style="display: none;">
                <div class="glass-card">
                    <h3 style="font-size: 18px; font-weight: 700; color: white; margin-bottom: 24px;">Discussion du projet</h3>

                    <!-- Main Comment Form -->
                    <form onsubmit="submitComment(event, this)" action="{{ route('web.projects.comments.store', $project->id) }}" method="POST" style="margin-bottom: 32px;">
                        @csrf
                        <div style="display: flex; gap: 16px;">
                            <div class="member-avatar" style="width: 40px; height: 40px; flex-shrink: 0;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div style="flex: 1;">
                                <textarea name="content" rows="2" placeholder="Ajouter un commentaire..."
                                    style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; color: white; font-family: inherit; resize: vertical; margin-bottom: 12px; min-height: 50px;"></textarea>
                                <div style="text-align: right;">
                                    <button type="submit" class="action-btn btn-primary" style="padding: 8px 20px;">
                                        Envoyer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Comments List Container -->
                    <div id="comments-container" style="display: flex; flex-direction: column; gap: 24px; max-height: 600px; overflow-y: auto; padding-right: 8px;">
                        @forelse($project->comments as $comment)
                        <div class="comment-thread" id="comment-{{ $comment->id }}">
                            <div style="display: flex; gap: 16px;">
                                <div class="member-avatar" style="width: 32px; height: 32px; font-size: 12px; flex-shrink: 0;">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px;">
                                        <span style="font-weight: 600; color: white; font-size: 14px;">{{ $comment->user->name }}</span>
                                        <span style="font-size: 12px; color: rgba(255,255,255,0.4);">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    <div class="comment-content" style="color: rgba(255,255,255,0.8); font-size: 14px; line-height: 1.5; margin-bottom: 8px;">
                                        <div class="text-content" id="text-{{ $comment->id }}" style="{{ strlen($comment->content) > 200 ? 'max-height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;' : '' }}">
                                            {{ $comment->content }}
                                        </div>
                                        @if(strlen($comment->content) > 200)
                                            <button onclick="toggleReadMore('{{ $comment->id }}')" id="btn-more-{{ $comment->id }}" style="background: none; border: none; color: #fbbb2a; font-size: 12px; cursor: pointer; padding: 0; margin-top: 4px;">Voir plus</button>
                                        @endif
                                    </div>

                                    <div style="display: flex; gap: 16px; align-items: center;">
                                        <button onclick="toggleReplyForm('{{ $comment->id }}')" style="background: none; border: none; color: rgba(255,255,255,0.5); font-size: 12px; cursor: pointer; padding: 0; font-weight: 600;">Répondre</button>
                                    </div>

                                    <!-- Reply Form -->
                                    <div id="reply-form-{{ $comment->id }}" style="display: none; margin-top: 12px;">
                                        <form onsubmit="submitComment(event, this)" action="{{ route('web.projects.comments.store', $project->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div style="display: flex; gap: 12px;">
                                                <div class="member-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </div>
                                                <div style="flex: 1;">
                                                    <textarea name="content" rows="1" placeholder="Ajouter une réponse..."
                                                        style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px; color: white; font-family: inherit; resize: vertical; margin-bottom: 8px; font-size: 13px;"></textarea>
                                                    <div style="text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                                        <button type="button" onclick="toggleReplyForm('{{ $comment->id }}')" style="background: none; border: none; color: rgba(255,255,255,0.5); font-size: 12px; cursor: pointer;">Annuler</button>
                                                        <button type="submit" class="action-btn btn-primary" style="padding: 4px 12px; font-size: 12px;">Répondre</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Replies -->
                                    @if($comment->replies->count() > 0)
                                        <div style="margin-top: 12px;">
                                            <button onclick="toggleReplies('{{ $comment->id }}')" id="btn-replies-{{ $comment->id }}" style="background: none; border: none; color: #3b82f6; font-size: 13px; cursor: pointer; padding: 0; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                {{ $comment->replies->count() }} réponse(s)
                                            </button>

                                            <div id="replies-{{ $comment->id }}" style="display: none; margin-top: 12px; padding-left: 0;">
                                                @foreach($comment->replies as $reply)
                                                <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                                                    <div class="member-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 2px;">
                                                            <span style="font-weight: 600; color: white; font-size: 13px;">{{ $reply->user->name }}</span>
                                                            <span style="font-size: 11px; color: rgba(255,255,255,0.4);">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div style="color: rgba(255,255,255,0.8); font-size: 13px; line-height: 1.5;">
                                                            {{ $reply->content }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div id="no-comments" style="text-align: center; padding: 40px; color: rgba(255,255,255,0.4);">
                            <p>Aucun commentaire pour le moment. Soyez le premier à discuter !</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });

            // Deactivate all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById('tab-' + tabName).style.display = 'block';

            // Activate button
            // Find the button that calls this function with this tabName
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                if(btn.getAttribute('onclick').includes(tabName)) {
                    btn.classList.add('active');
                }
            });
        }

        function updateTaskStatus(selectElement, taskId) {
            const newStatus = selectElement.value;

            // Visual feedback
            selectElement.disabled = true;
            selectElement.style.opacity = '0.7';

            const url = "{{ route('web.tasks.updateStatus', ':id') }}".replace(':id', taskId);

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Update class for color
                selectElement.className = 'status-badge status-' + newStatus;
                selectElement.disabled = false;
                selectElement.style.opacity = '1';

                // Update data attribute for filtering
                const row = selectElement.closest('.task-row');
                if(row) row.setAttribute('data-status', newStatus);

                // Update Project Statistics
                if (data.project_statistics) {
                    const stats = data.project_statistics;

                    // Update Global Progress
                    const progressBar = document.getElementById('global-progress-bar');
                    const progressText = document.getElementById('global-progress-text');
                    if (progressBar) progressBar.style.width = stats.completion_percentage + '%';
                    if (progressText) progressText.textContent = Math.round(stats.completion_percentage) + '%';

                    // Update Overview Stats
                    const totalTasks = document.getElementById('stat-total-tasks');
                    const completedTasks = document.getElementById('stat-completed-tasks');
                    const pendingTasks = document.getElementById('stat-pending-tasks');

                    if (totalTasks) totalTasks.textContent = stats.total_tasks;
                    if (completedTasks) completedTasks.textContent = stats.completed_tasks;
                    if (pendingTasks) pendingTasks.textContent = stats.pending_tasks;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour du statut');
                selectElement.disabled = false;
                selectElement.style.opacity = '1';
            });
        }        function filterTasks() {
            const status = document.getElementById('taskFilter').value;
            const rows = document.querySelectorAll('.task-row');

            rows.forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function sortTasks() {
            const sortBy = document.getElementById('taskSort').value;
            const container = document.getElementById('projectTaskList');
            const rows = Array.from(container.querySelectorAll('.task-row'));

            rows.sort((a, b) => {
                if (sortBy === 'date_desc') {
                    return b.getAttribute('data-date') - a.getAttribute('data-date');
                } else if (sortBy === 'date_asc') {
                    return a.getAttribute('data-date') - b.getAttribute('data-date');
                } else if (sortBy === 'priority') {
                    return b.getAttribute('data-priority') - a.getAttribute('data-priority');
                }
            });

            rows.forEach(row => container.appendChild(row));
        }

        // Comment System Functions
        function toggleReplyForm(commentId) {
            const form = document.getElementById('reply-form-' + commentId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
                form.querySelector('textarea').focus();
            } else {
                form.style.display = 'none';
            }
        }

        function toggleReplies(commentId) {
            const replies = document.getElementById('replies-' + commentId);
            const btn = document.getElementById('btn-replies-' + commentId);

            if (replies.style.display === 'none') {
                replies.style.display = 'block';
                btn.innerHTML = '<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg> Masquer les réponses';
            } else {
                replies.style.display = 'none';
                // Count children divs to get count
                const count = replies.children.length;
                btn.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg> ${count} réponse(s)`;
            }
        }

        function toggleReadMore(commentId) {
            const textDiv = document.getElementById('text-' + commentId);
            const btn = document.getElementById('btn-more-' + commentId);

            // Check if currently collapsed (has max-height style)
            if (textDiv.style.maxHeight) {
                // Expand
                textDiv.style.maxHeight = null;
                textDiv.style.overflow = 'visible';
                textDiv.style.display = 'block';
                textDiv.style.webkitLineClamp = 'unset';
                textDiv.style.webkitBoxOrient = 'unset';
                btn.textContent = 'Voir moins';
            } else {
                // Collapse
                textDiv.style.maxHeight = '60px';
                textDiv.style.overflow = 'hidden';
                textDiv.style.display = '-webkit-box';
                textDiv.style.webkitLineClamp = '3';
                textDiv.style.webkitBoxOrient = 'vertical';
                btn.textContent = 'Voir plus';
            }
        }

        // Improved Read More Logic
        // We need to initialize the read more state.
        // For now, let's just use a simple toggle class approach.

        function submitComment(event, form) {
            event.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = 'Envoi...';

            const formData = new FormData(form);
            const url = form.action;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Reset form
                form.reset();
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;

                if (data.comment) {
                    const comment = data.comment;
                    const parentId = comment.parent_id;

                    if (parentId) {
                        // Append to replies
                        const repliesContainer = document.getElementById('replies-' + parentId);
                        const repliesBtn = document.getElementById('btn-replies-' + parentId);

                        // Create reply HTML
                        const replyHtml = `
                            <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                                <div class="member-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
                                    ${comment.user.name.charAt(0).toUpperCase()}
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 2px;">
                                        <span style="font-weight: 600; color: white; font-size: 13px;">${comment.user.name}</span>
                                        <span style="font-size: 11px; color: rgba(255,255,255,0.4);">À l'instant</span>
                                    </div>
                                    <div style="color: rgba(255,255,255,0.8); font-size: 13px; line-height: 1.5;">
                                        ${comment.content}
                                    </div>
                                </div>
                            </div>
                        `;

                        // If replies container doesn't exist (first reply), we need to create the structure
                        // But in our blade, we only render the container if count > 0.
                        // So we might need to reload or handle this edge case.
                        // For simplicity, if container exists, append. If not, reload page to show structure.
                        if (repliesContainer) {
                            repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                            repliesContainer.style.display = 'block'; // Show replies

                            // Update count
                            const count = repliesContainer.children.length;
                            repliesBtn.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg> Masquer les réponses`;
                        } else {
                            // Fallback for first reply: reload page to render structure
                            window.location.reload();
                            return;
                        }

                        // Hide reply form
                        toggleReplyForm(parentId);
                    } else {
                        // Append to main list
                        const container = document.getElementById('comments-container');
                        const noComments = document.getElementById('no-comments');
                        if (noComments) noComments.remove();

                        const commentHtml = `
                        <div class="comment-thread" id="comment-${comment.id}">
                            <div style="display: flex; gap: 16px;">
                                <div class="member-avatar" style="width: 32px; height: 32px; font-size: 12px; flex-shrink: 0;">
                                    ${comment.user.name.charAt(0).toUpperCase()}
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px;">
                                        <span style="font-weight: 600; color: white; font-size: 14px;">${comment.user.name}</span>
                                        <span style="font-size: 12px; color: rgba(255,255,255,0.4);">À l'instant</span>
                                    </div>

                                    <div class="comment-content" style="color: rgba(255,255,255,0.8); font-size: 14px; line-height: 1.5; margin-bottom: 8px;">
                                        <div class="text-content" id="text-${comment.id}" style="${comment.content.length > 200 ? 'max-height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;' : ''}">
                                            ${comment.content}
                                        </div>
                                        ${comment.content.length > 200 ?
                                            `<button onclick="toggleReadMore('${comment.id}')" id="btn-more-${comment.id}" style="background: none; border: none; color: #fbbb2a; font-size: 12px; cursor: pointer; padding: 0; margin-top: 4px;">Voir plus</button>`
                                            : ''}
                                    </div>

                                    <div style="display: flex; gap: 16px; align-items: center;">
                                        <button onclick="toggleReplyForm('${comment.id}')" style="background: none; border: none; color: rgba(255,255,255,0.5); font-size: 12px; cursor: pointer; padding: 0; font-weight: 600;">Répondre</button>
                                    </div>

                                    <div id="reply-form-${comment.id}" style="display: none; margin-top: 12px;">
                                        <form onsubmit="submitComment(event, this)" action="${url}" method="POST">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                            <input type="hidden" name="parent_id" value="${comment.id}">
                                            <div style="display: flex; gap: 12px;">
                                                <div class="member-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
                                                    ${comment.user.name.charAt(0).toUpperCase()}
                                                </div>
                                                <div style="flex: 1;">
                                                    <textarea name="content" rows="1" placeholder="Ajouter une réponse..."
                                                        style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px; color: white; font-family: inherit; resize: vertical; margin-bottom: 8px; font-size: 13px;"></textarea>
                                                    <div style="text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                                        <button type="button" onclick="toggleReplyForm('${comment.id}')" style="background: none; border: none; color: rgba(255,255,255,0.5); font-size: 12px; cursor: pointer;">Annuler</button>
                                                        <button type="submit" class="action-btn btn-primary" style="padding: 4px 12px; font-size: 12px;">Répondre</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;

                        container.insertAdjacentHTML('afterbegin', commentHtml);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'envoi du commentaire');
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            });
        }
    </script>
</x-app-layout>
