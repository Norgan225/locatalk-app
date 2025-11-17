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

        .task-header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
        }

        .task-title {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 16px;
        }

        .task-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .task-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .meta-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .meta-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .meta-value {
            color: white;
            font-size: 16px;
            font-weight: 600;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }

        .status-todo {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-in_progress {
            background: rgba(251, 187, 42, 0.2);
            color: #fbbb2a;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .priority-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .priority-urgent {
            background: rgba(220, 38, 38, 0.2);
            color: #fca5a5;
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

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .actions-bar {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .avatar-large {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 24px;
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
        <a href="{{ route('web.tasks') }}" class="back-button">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Retour aux tâches
        </a>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        <!-- Actions Bar -->
        <div class="actions-bar">
            <div style="display: flex; gap: 12px;">
                @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by)
                <a href="{{ route('web.tasks.edit', $task->id) }}" class="btn btn-secondary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                    </svg>
                    {{ org_trans('edit_task') }}
                </a>
                @endif
            </div>
            <div>
                @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by)
                <button
                    class="btn btn-danger"
                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) { document.getElementById('delete-form').submit(); }"
                >
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    Supprimer
                </button>
                @endif
            </div>
        </div>

        <!-- Task Header -->
        <div class="task-header">
            <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                <span class="status-badge status-{{ $task->status }}">
                    @if($task->status == 'todo') 📝 {{ org_trans('todo') }}
                    @elseif($task->status == 'in_progress') ⏳ {{ org_trans('in_progress') }}
                    @elseif($task->status == 'completed') ✅ {{ org_trans('completed') }}
                    @else {{ $task->status }}
                    @endif
                </span>
                <span class="priority-badge priority-{{ $task->priority }}">
                    {{ strtoupper($task->priority) }}
                </span>
            </div>

            <h1 class="task-title">{{ $task->title }}</h1>

            @if($task->description)
            <div class="task-description">
                {{ $task->description }}
            </div>
            @endif

            <!-- Meta Grid -->
            <div class="task-meta-grid">
                <div class="meta-item">
                    <div class="meta-label">{{ org_trans('project_label') }}</div>
                    <div class="meta-value">
                        @if($task->project)
                        <a href="{{ route('web.projects.show', $task->project->id) }}" style="color: #60a5fa; text-decoration: none;">
                            {{ $task->project->name }}
                        </a>
                        @else
                        Aucun
                        @endif
                    </div>
                </div>

                <div class="meta-item">
                    <div class="meta-label">{{ org_trans('assigned_to') }}</div>
                    <div class="meta-value">
                        @if($task->assignee)
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="avatar-large" style="width: 40px; height: 40px; font-size: 16px;">
                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                            </div>
                            {{ $task->assignee->name }}
                        </div>
                        @else
                        Non assigné
                        @endif
                    </div>
                </div>

                <div class="meta-item">
                    <div class="meta-label">{{ org_trans('due_date_label') }}</div>
                    <div class="meta-value" style="{{ $task->is_overdue ? 'color: #f87171;' : '' }}">
                        @if($task->due_date)
                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                        @if($task->is_overdue)
                            <span style="font-size: 12px;">(En retard)</span>
                        @elseif($task->days_remaining !== null && $task->days_remaining >= 0)
                            <span style="font-size: 12px;">({{ $task->days_remaining }} jours)</span>
                        @endif
                        @else
                        Aucune
                        @endif
                    </div>
                </div>

                <div class="meta-item">
                    <div class="meta-label">Créé par</div>
                    <div class="meta-value">
                        {{ $task->creator->name ?? 'Inconnu' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Form (hidden) -->
        @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by)
        <form
            id="delete-form"
            action="{{ route('web.tasks.destroy', $task->id) }}"
            method="POST"
            style="display: none;"
        >
            @csrf
            @method('DELETE')
        </form>
        @endif
    </div>
</x-app-layout>
