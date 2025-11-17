<x-app-layout>
    <style>
        .project-details {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
        }

        .project-title-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .project-title {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
        }

        .project-actions {
            display: flex;
            gap: 12px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-action:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(251, 187, 42, 0.3);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }

        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 24px;
        }

        .tab {
            padding: 12px 24px;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .tab.active {
            color: #fbbb2a;
            border-bottom-color: #fbbb2a;
        }

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .task-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .task-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.2);
        }

        .task-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .task-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            cursor: pointer;
        }

        .task-name {
            color: white;
            font-weight: 500;
        }

        .member-card {
            background: rgba(255, 255, 255, 0.03);
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
            margin-bottom: 4px;
        }

        .member-role {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .grid-2col {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
    </style>

    <!-- Back Button -->
    <a href="/projects" style="display: inline-flex; align-items: center; gap: 8px; color: rgba(255, 255, 255, 0.7); margin-bottom: 24px; text-decoration: none; transition: color 0.3s ease;">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux projets
    </a>

    <!-- Project Details -->
    <div class="project-details">
        <div class="project-title-section">
            <div>
                <h1 class="project-title">{{ $project->name ?? 'Nom du Projet' }}</h1>
                <span class="status-badge status-{{ $project->status ?? 'active' }}">{{ $project->status ?? 'active' }}</span>
            </div>
            <div class="project-actions">
                @if(auth()->user()->canManageUsers())
                <button class="btn-action">
                    <svg style="width: 16px; height: 16px; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </button>
                @endif
            </div>
        </div>

        <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 32px; line-height: 1.6;">
            {{ $project->description ?? 'Description du projet à venir...' }}
        </p>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Date de début</div>
                <div class="info-value">{{ isset($project->start_date) ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : 'Non définie' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Date de fin</div>
                <div class="info-value">{{ isset($project->end_date) ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : 'Non définie' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Progression</div>
                <div class="info-value">{{ $project->progress ?? 0 }}%</div>
            </div>
            <div class="info-item">
                <div class="info-label">Membres</div>
                <div class="info-value">{{ $project->users->count() ?? 0 }} personnes</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar" style="height: 12px;">
            <div class="progress-fill" style="width: {{ $project->progress ?? 0 }}%"></div>
        </div>
    </div>

    <!-- Tabs & Content -->
    <div class="grid-2col">
        <div>
            <div class="tabs">
                <div class="tab active">Tâches</div>
                <div class="tab">Activité</div>
                <div class="tab">Fichiers</div>
            </div>

            <!-- Tasks List -->
            <div class="task-list">
                @if(isset($project->tasks) && count($project->tasks) > 0)
                    @foreach($project->tasks as $task)
                    <div class="task-item">
                        <div class="task-info">
                            <div class="task-checkbox"></div>
                            <div>
                                <div class="task-name">{{ $task->title }}</div>
                                <div style="color: rgba(255, 255, 255, 0.4); font-size: 13px; margin-top: 4px;">
                                    Assigné à {{ $task->assignedTo->name ?? 'Non assigné' }}
                                </div>
                            </div>
                        </div>
                        <span class="status-badge status-{{ $task->status }}">{{ $task->status }}</span>
                    </div>
                    @endforeach
                @else
                <div style="text-align: center; padding: 40px; color: rgba(255, 255, 255, 0.5);">
                    <p>Aucune tâche pour ce projet</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Members Sidebar -->
        <div>
            <h3 style="color: white; font-weight: 700; margin-bottom: 16px; font-size: 18px;">Membres de l'équipe</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @if(isset($project->users) && count($project->users) > 0)
                    @foreach($project->users as $user)
                    <div class="member-card">
                        <div class="member-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div class="member-info">
                            <div class="member-name">{{ $user->name }}</div>
                            <div class="member-role">{{ $user->role }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div style="text-align: center; padding: 20px; color: rgba(255, 255, 255, 0.5);">
                    <p>Aucun membre</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
