<x-app-layout>
    <style>
        .form-container {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label .required {
            color: #df5526;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: rgba(251, 187, 42, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(251, 187, 42, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-select {
            cursor: pointer;
        }

        .form-select option {
            background: #1a1a1a;
            color: white;
        }

        .form-error {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(223, 85, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .form-helper {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-top: 4px;
        }

        .task-info {
            background: rgba(251, 187, 42, 0.1);
            border: 1px solid rgba(251, 187, 42, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .task-info-title {
            color: #fbbb2a;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .task-info-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }
    </style>

    <div class="p-8">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title">✏️ {{ org_trans('edit_task') }}</h1>
                <p class="form-subtitle">Mettez à jour les informations de la tâche</p>
            </div>

            <div class="task-info">
                <div class="task-info-title">📌 Tâche actuelle</div>
                <div class="task-info-text">
                    Créée le {{ $task->created_at->format('d/m/Y') }}
                    par {{ $task->creator->name ?? 'Système' }}
                    • Projet: {{ $task->project->name }}
                </div>
            </div>

            @if ($errors->any())
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li style="color: #ef4444; font-size: 14px; margin-bottom: 4px;">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('web.tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Task Title -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('task_title') }} <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        class="form-input"
                        placeholder="Ex: Finaliser le rapport mensuel"
                        value="{{ old('title', $task->title) }}"
                        required
                    >
                    @error('title')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label">
                        Description
                    </label>
                    <textarea
                        name="description"
                        class="form-textarea"
                        placeholder="Décrivez la tâche en détail..."
                    >{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Priority -->
                    <div class="form-group">
                        <label class="form-label">
                            Priorité
                        </label>
                        <select name="priority" class="form-select">
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>{{ org_trans('low') }}</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>{{ org_trans('medium') }}</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>{{ org_trans('high') }}</option>
                            <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>{{ org_trans('urgent') }}</option>
                        </select>
                        @error('priority')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">
                            Statut
                        </label>
                        <select name="status" class="form-select">
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>{{ org_trans('todo') }}</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>{{ org_trans('in_progress') }}</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>{{ org_trans('completed') }}</option>
                        </select>
                        @error('status')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Assigned To -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ org_trans('assigned_to') }}
                        </label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Non assigné</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ org_trans('due_date_label') }}
                        </label>
                        <input
                            type="date"
                            name="due_date"
                            class="form-input"
                            value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                        >
                        @error('due_date')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div>
                        @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by)
                            <button
                                type="button"
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
                    <div style="display: flex; gap: 16px;">
                        <a href="{{ route('web.tasks.show', $task->id) }}" class="btn btn-secondary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                            </svg>
                            {{ org_trans('cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                            </svg>
                            {{ org_trans('save') }}
                        </button>
                    </div>
                </div>
            </form>

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
    </div>
</x-app-layout>
