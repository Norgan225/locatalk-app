<x-app-layout>
    <style>
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            padding-bottom: 40px;
        }

        .kanban-column {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            min-height: 500px;
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .column-title {
            font-size: 16px;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .column-count {
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .task-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: grab;
            transition: all 0.3s ease;
        }

        .task-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.2);
        }

        .task-card:active {
            cursor: grabbing;
        }

        .task-card-title {
            font-size: 15px;
            font-weight: 600;
            color: white;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .task-card-description {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .task-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .task-priority {
            padding: 4px 10px;
            border-radius: 8px;
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

        .task-assignee {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 11px;
        }

        .task-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .task-tag {
            padding: 4px 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
        }

        .column-todo {
            border-top: 3px solid #60a5fa;
        }

        .column-in-progress {
            border-top: 3px solid #fbbb2a;
        }

        .column-done {
            border-top: 3px solid #34d399;
        }

        .btn-add-task {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-add-task:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.3);
            color: #fbbb2a;
        }

        .filters-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .task-due-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
        }
    </style>

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">Tableau des Tâches</h1>
        <button class="btn-create" onclick="alert('Modal de création de tâche')">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle Tâche
        </button>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <div class="filter-btn">{{ org_trans('all') }} {{ org_trans('tasks') }}</div>
        <div class="filter-btn">{{ org_trans('my') }} {{ org_trans('tasks') }}</div>
        <div class="filter-btn">{{ org_trans('high') }} {{ org_trans('priority') }}</div>
        <div class="filter-btn">{{ org_trans('overdue') }}</div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board">
        <!-- TODO Column -->
        <div class="kanban-column column-todo">
            <div class="column-header">
                <div class="column-title">
                    <svg style="width: 20px; height: 20px; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    {{ org_trans('todo') }}
                </div>
                <div class="column-count">5</div>
            </div>

            <div class="task-card">
                <div class="task-card-title">Créer le design de la page d'accueil</div>
                <div class="task-card-description">Préparer les maquettes Figma pour la nouvelle landing page</div>
                <div class="task-tags">
                    <span class="task-tag">Design</span>
                    <span class="task-tag">UI/UX</span>
                </div>
                <div class="task-due-date">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    15 Nov 2025
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-high">Haute</span>
                    <div class="task-assignee">MN</div>
                </div>
            </div>

            <div class="task-card">
                <div class="task-card-title">Rédiger la documentation API</div>
                <div class="task-card-description">Documentation complète des endpoints REST</div>
                <div class="task-tags">
                    <span class="task-tag">Documentation</span>
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-medium">Moyenne</span>
                    <div class="task-assignee">JD</div>
                </div>
            </div>

            <div class="task-card">
                <div class="task-card-title">Configurer CI/CD Pipeline</div>
                <div class="task-tags">
                    <span class="task-tag">DevOps</span>
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-low">Basse</span>
                    <div class="task-assignee">PB</div>
                </div>
            </div>

            <button class="btn-add-task">+ Ajouter une tâche</button>
        </div>

        <!-- IN PROGRESS Column -->
        <div class="kanban-column column-in-progress">
            <div class="column-header">
                <div class="column-title">
                    <svg style="width: 20px; height: 20px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ org_trans('in_progress') }}
                </div>
                <div class="column-count">3</div>
            </div>

            <div class="task-card">
                <div class="task-card-title">Développer le système d'authentification</div>
                <div class="task-card-description">Implémentation JWT + OAuth2</div>
                <div class="task-tags">
                    <span class="task-tag">Backend</span>
                    <span class="task-tag">Security</span>
                </div>
                <div class="task-due-date" style="color: #f87171;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    En retard
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-high">Haute</span>
                    <div class="task-assignee">ML</div>
                </div>
            </div>

            <div class="task-card">
                <div class="task-card-title">Tests unitaires des models</div>
                <div class="task-tags">
                    <span class="task-tag">Testing</span>
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-medium">Moyenne</span>
                    <div class="task-assignee">SD</div>
                </div>
            </div>

            <button class="btn-add-task">+ Ajouter une tâche</button>
        </div>

        <!-- DONE Column -->
        <div class="kanban-column column-done">
            <div class="column-header">
                <div class="column-title">
                    <svg style="width: 20px; height: 20px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ org_trans('done') }}
                </div>
                <div class="column-count">8</div>
            </div>

            <div class="task-card" style="opacity: 0.7;">
                <div class="task-card-title">Configuration base de données</div>
                <div class="task-tags">
                    <span class="task-tag">Database</span>
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-high">Haute</span>
                    <div class="task-assignee">LM</div>
                </div>
            </div>

            <div class="task-card" style="opacity: 0.7;">
                <div class="task-card-title">Design système de couleurs</div>
                <div class="task-tags">
                    <span class="task-tag">Design</span>
                </div>
                <div class="task-card-meta">
                    <span class="task-priority priority-medium">Moyenne</span>
                    <div class="task-assignee">MN</div>
                </div>
            </div>

            <div class="task-card" style="opacity: 0.7;">
                <div class="task-card-title">Setup environnement dev</div>
                <div class="task-card-meta">
                    <span class="task-priority priority-low">Basse</span>
                    <div class="task-assignee">JD</div>
                </div>
            </div>

            <button class="btn-add-task">+ Ajouter une tâche</button>
        </div>
    </div>

</x-app-layout>
