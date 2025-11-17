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
            justify-content: flex-end;
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

        .form-helper {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-top: 4px;
        }
    </style>

    <div class="p-8">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title">📝 {{ org_trans('create_new_task') }}</h1>
                <p class="form-subtitle">{{ org_trans('fill_info_create_task') }}</p>
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

            <form action="{{ route('web.tasks.store') }}" method="POST">
                @csrf

                <!-- Task Title -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('task_title') }} <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        class="form-input"
                        placeholder="{{ org_trans('task_title_placeholder') }}"
                        value="{{ old('title') }}"
                        required
                    >
                    @error('title')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('description') }}
                    </label>
                    <textarea
                        name="description"
                        class="form-textarea"
                        placeholder="{{ org_trans('task_description_placeholder') }}"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('project') }} <span class="required">*</span>
                    </label>
                    <select name="project_id" class="form-select" required>
                        <option value="">{{ org_trans('select_project') }}</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
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
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>{{ org_trans('low') }}</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>{{ org_trans('medium') }}</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>{{ org_trans('high') }}</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>{{ org_trans('urgent') }}</option>
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
                            <option value="todo" {{ old('status', 'todo') == 'todo' ? 'selected' : '' }}>{{ org_trans('todo') }}</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>{{ org_trans('in_progress') }}</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>{{ org_trans('completed') }}</option>
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
                            <option value="">{{ org_trans('not_assigned') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
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
                            {{ org_trans('due_date') }}
                        </label>
                        <input
                            type="date"
                            name="due_date"
                            class="form-input"
                            value="{{ old('due_date') }}"
                            min="{{ date('Y-m-d') }}"
                        >
                        @error('due_date')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('web.tasks') }}" class="btn btn-secondary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                        </svg>
                        {{ org_trans('cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                        {{ org_trans('create_task') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
