<x-app-layout>
    <style>
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-bottom: 24px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .project-header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
        }

        .title-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .project-title {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
        }

        .project-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 16px;
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

        .actions-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 14px;
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
            box-shadow: 0 8px 20px rgba(223, 85, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            color: white;
            font-size: 28px;
            font-weight: 700;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .progress-section {
            margin-bottom: 32px;
        }

        .progress-bar {
            width: 100%;
            height: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-text {
            display: flex;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .team-section {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
        }

        .member-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .member-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .member-info {
            flex: 1;
        }

        .member-name {
            color: white;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .member-role {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            text-transform: capitalize;
        }

        .tasks-section {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
        }

        .task-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .task-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }

        .task-info {
            flex: 1;
        }

        .task-title {
            color: white;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .task-meta {
            display: flex;
            gap: 12px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-high {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .priority-medium {
            background: rgba(251, 187, 42, 0.2);
            color: #fbbb2a;
        }

        .priority-low {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: rgba(255, 255, 255, 0.5);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            color: #34d399;
        }
    </style>

    <div class="p-8">
        <!-- Back Button -->
        <a href="{{ route('web.projects') }}" class="back-button">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Retour aux projets
        </a>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        <!-- Project Header -->
        <div class="project-header">
            <div class="title-section">
                <div style="flex: 1;">
                    <div class="status-badge status-{{ $project->status }}">
                        @if($project->status == 'active') 🟢 {{ org_trans('active') }}
                        @elseif($project->status == 'completed') ✅ {{ org_trans('completed') }}
                        @elseif($project->status == 'on_hold') ⏸️ {{ org_trans('on_hold') }}
                        @elseif($project->status == 'cancelled') ❌ {{ org_trans('cancelled') }}
                        @endif
                    </div>
                    <h1 class="project-title">{{ $project->name }}</h1>
                    <p class="project-description">
                        {{ $project->description ?? org_trans('no_description_available') }}
                    </p>
                </div>
                <div class="actions-group">
                    @if(auth()->user()->canManageUsers() || auth()->id() == $project->created_by)
                    <a href="{{ route('web.projects.edit', $project->id) }}" class="btn btn-secondary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                        </svg>
                        {{ org_trans('edit') }}
                    </a>
                    @endif
                </div>
            </div>

            <!-- Progress -->
            <div class="progress-section">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $project->statistics['completion_percentage'] ?? 0 }}%"></div>
                </div>
                <div class="progress-text">
                    <span>{{ org_trans('project_progress') }}</span>
                    <span><strong>{{ round($project->statistics['completion_percentage'] ?? 0) }}%</strong> {{ org_trans('completed') }}</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" style="color: #fbbb2a;">
                            <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                            <path d="M8.646 6.646a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L10.293 9 8.646 7.354a.5.5 0 0 1 0-.708zm-1.292 0a.5.5 0 0 0-.708 0l-2 2a.5.5 0 0 0 0 .708l2 2a.5.5 0 0 0 .708-.708L5.707 9l1.647-1.646a.5.5 0 0 0 0-.708z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Total Tâches</div>
                    <div class="stat-value">{{ $project->statistics['total_tasks'] ?? 0 }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" style="color: #34d399;">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                        </svg>
                    </div>
                    <div class="stat-label">{{ org_trans('completed') }}</div>
                    <div class="stat-value">{{ $project->statistics['completed_tasks'] ?? 0 }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" style="color: #fbbb2a;">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                    </div>
                    <div class="stat-label">{{ org_trans('in_progress') }}</div>
                    <div class="stat-value">{{ $project->statistics['pending_tasks'] ?? 0 }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" style="color: #60a5fa;">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Membres</div>
                    <div class="stat-value">{{ $project->statistics['total_members'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        @if($project->users && $project->users->count() > 0)
        <div class="team-section">
            <h2 class="section-title">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                    <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                </svg>
                Équipe du projet ({{ $project->users->count() }})
            </h2>
            <div class="team-grid">
                @foreach($project->users as $member)
                <div class="member-card">
                    <div class="member-avatar">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="member-info">
                        <div class="member-name">{{ $member->name }}</div>
                        <div class="member-role">
                            @if($member->pivot && $member->pivot->role)
                                {{ $member->pivot->role }}
                            @else
                                {{ org_trans('member') }}
                            @endif
                            @if($member->id == $project->created_by)
                                <span style="color: #fbbb2a;">• {{ org_trans('creator') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tasks Section -->
        <div class="tasks-section">
            <h2 class="section-title">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2h-11zm5.21 7.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1h3zm0 2a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1h3zm0 2a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1h3zm5.29-7.5a.5.5 0 1 1-.708.708L10 2.707 8.854 3.854a.5.5 0 1 1-.708-.708l1.5-1.5a.5.5 0 0 1 .708 0l2 2z"/>
                </svg>
                {{ org_trans('project_tasks') }} ({{ $project->tasks->count() }})
            </h2>

            @if($project->tasks && $project->tasks->count() > 0)
                @foreach($project->tasks as $task)
                <div class="task-item">
                    <div class="task-info">
                        <div class="task-title">{{ $task->title }}</div>
                        <div class="task-meta">
                            <span class="priority-badge priority-{{ $task->priority ?? 'medium' }}">
                                {{ $task->priority ?? 'medium' }}
                            </span>
                            @if($task->assignee)
                            <span>👤 {{ $task->assignee->name }}</span>
                            @endif
                            @if($task->due_date)
                            <span>📅 {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="status-badge status-{{ $task->status }}">
                            @if($task->status == 'todo') {{ org_trans('todo') }}
                            @elseif($task->status == 'in_progress') {{ org_trans('in_progress') }}
                            @elseif($task->status == 'completed') {{ org_trans('completed') }}
                            @else {{ $task->status }}
                            @endif
                        </span>
                    </div>
                </div>
                @endforeach
            @else
            <div class="empty-state">
                <p>Aucune tâche pour ce projet</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
