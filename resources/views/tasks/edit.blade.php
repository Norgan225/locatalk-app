<x-app-layout>
    <style>
        /* Base Styles */
        .task-form-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem;
            font-family: 'Inter', sans-serif;
            color: white;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            max-width: 800px;
            margin: 0 auto 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .form-label .required {
            color: #f87171;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(251, 187, 42, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(251, 187, 42, 0.1);
        }

        .form-control option {
            background: #1e293b;
            color: white;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            flex: 1;
            box-shadow: 0 4px 15px rgba(223, 85, 38, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(223, 85, 38, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Grid for smaller inputs */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="task-form-page">
        <div class="page-header">
            <h1 class="page-title">{{ org_trans('edit_task') }}</h1>
            <p class="page-subtitle">Modifier les détails de la tâche</p>
        </div>

        <div class="glass-panel">
            <form action="{{ route('web.tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('title') }} <span class="required">*</span>
                    </label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title', $task->title) }}">
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('description') }}
                    </label>
                    <textarea name="description" class="form-control">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="form-row">
                    <!-- Project -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ org_trans('project_label') }}
                        </label>
                        <select name="project_id" class="form-control">
                            <option value="">-- Aucun projet --</option>
                            @foreach(\App\Models\Project::where('organization_id', auth()->user()->organization_id)->get() as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assignee -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ org_trans('assigned_to') }}
                        </label>
                        <select name="assigned_to" class="form-control">
                            <option value="">-- Non assigné --</option>
                            @foreach(\App\Models\User::where('organization_id', auth()->user()->organization_id)->get() as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Priority -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ org_trans('priority') }} <span class="required">*</span>
                        </label>
                        <select name="priority" class="form-control" required>
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="form-group">
                        <label class="form-label">
                            {{ org_trans('due_date_label') }}
                        </label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('status') }}
                    </label>
                    <select name="status" class="form-control">
                        <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>{{ org_trans('todo') }}</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>{{ org_trans('in_progress') }}</option>
                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>{{ org_trans('completed') }}</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="{{ route('web.tasks.show', $task->id) }}" class="btn btn-secondary">
                        {{ org_trans('cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ org_trans('update_task') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
