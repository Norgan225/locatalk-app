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

        .user-select-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding: 12px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        .user-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-checkbox:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .user-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .user-checkbox label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            cursor: pointer;
            flex: 1;
        }

        .project-info {
            background: rgba(251, 187, 42, 0.1);
            border: 1px solid rgba(251, 187, 42, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .project-info-title {
            color: #fbbb2a;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .project-info-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }
    </style>

    <div class="p-8">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title">✏️ {{ org_trans('edit_project') }}</h1>
                <p class="form-subtitle">{{ org_trans('update_project_info') }}</p>
            </div>

            <div class="project-info">
                <div class="project-info-title">📌 {{ org_trans('current_project') }}</div>
                <div class="project-info-text">
                    {{ org_trans('created_on') }} {{ $project->created_at->format('d/m/Y') }}
                    {{ org_trans('by') }} {{ $project->creator->name ?? 'Système' }}
                    • {{ $project->users->count() }} {{ org_trans('member') }}(s) • {{ $project->tasks->count() }} {{ org_trans('tasks') }}(s)
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

            <form action="{{ route('web.projects.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Project Name -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('project_name') }} <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        class="form-input"
                        placeholder="Ex: Campagne Marketing Q1"
                        value="{{ old('name', $project->name) }}"
                        required
                    >
                    @error('name')
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
                        placeholder="{{ org_trans('describe_goals_context') }}"
                    >{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div class="form-group">
                    <label class="form-label">
                        Département
                    </label>
                    <select name="department_id" class="form-select">
                        <option value="">Aucun département spécifique</option>
                        @foreach(\App\Models\Department::where('organization_id', $project->organization_id)->get() as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department_id', $project->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">
                            Statut
                        </label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>{{ org_trans('active') }}</option>
                            <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>{{ org_trans('on_hold') }}</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>{{ org_trans('completed') }}</option>
                            <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>{{ org_trans('cancelled') }}</option>
                        </select>
                        @error('status')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div class="form-group">
                        <label class="form-label">
                            Date limite
                        </label>
                        <input
                            type="date"
                            name="deadline"
                            class="form-input"
                            value="{{ old('deadline', $project->deadline ? $project->deadline->format('Y-m-d') : '') }}"
                        >
                        @error('deadline')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Team Members -->
                <div class="form-group">
                    <label class="form-label">
                        {{ org_trans('team_members') }}
                    </label>
                    <p class="form-helper" style="margin-bottom: 12px;">
                        {{ org_trans('manage_project_members') }}
                    </p>
                    <div class="user-select-grid">
                        @php
                            $currentUserIds = $project->users->pluck('id')->toArray();
                        @endphp
                        @foreach(\App\Models\User::where('organization_id', $project->organization_id)->get() as $user)
                            <div class="user-checkbox">
                                <input
                                    type="checkbox"
                                    name="user_ids[]"
                                    value="{{ $user->id }}"
                                    id="user_{{ $user->id }}"
                                    {{ in_array($user->id, old('user_ids', $currentUserIds)) ? 'checked' : '' }}
                                    {{ $user->id == $project->created_by ? 'disabled checked' : '' }}
                                >
                                <label for="user_{{ $user->id }}">
                                    {{ $user->name }}
                                    @if($user->id == $project->created_by)
                                        <span style="color: #fbbb2a; font-size: 11px;">(Créateur)</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('user_ids')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div>
                        @if(auth()->user()->isOwner() || auth()->id() == $project->created_by)
                            <button
                                type="button"
                                class="btn btn-danger"
                                onclick="if(confirm('{{ org_trans('confirm_delete_project') }}')) { document.getElementById('delete-form').submit(); }"
                            >
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                                {{ org_trans('delete') }}
                            </button>
                        @endif
                    </div>
                    <div style="display: flex; gap: 16px;">
                        <a href="{{ route('web.projects.show', $project->id) }}" class="btn btn-secondary">
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
            @if(auth()->user()->isOwner() || auth()->id() == $project->created_by)
                <form
                    id="delete-form"
                    action="{{ route('web.projects.destroy', $project->id) }}"
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
