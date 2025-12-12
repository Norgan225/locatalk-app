<x-app-layout>
    <style>
        /* Base Styles */
        .task-detail-page {
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
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin-bottom: 32px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        /* Activity Scrollbar */
        .activity-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .activity-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .activity-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--color-primary, #df5526), var(--color-secondary, #fbbb2a));
            border-radius: 10px;
        }

        /* Navigation */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            margin-bottom: 24px;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(-4px);
        }

        /* Hero Section */
        .task-hero {
            position: relative;
            overflow: hidden;
        }

        .task-header-content {
            position: relative;
            z-index: 2;
        }

        .task-badges {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-todo { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
        .status-in_progress { background: rgba(251, 187, 42, 0.15); color: #fbbb2a; border: 1px solid rgba(251, 187, 42, 0.3); }
        .status-completed { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }

        .priority-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-urgent { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .priority-high { background: rgba(249, 115, 22, 0.15); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3); }
        .priority-medium { background: rgba(251, 187, 42, 0.15); color: #fbbb2a; border: 1px solid rgba(251, 187, 42, 0.3); }
        .priority-low { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }

        .task-title {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 24px;
            line-height: 1.2;
            letter-spacing: -1px;
        }

        /* Meta Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .meta-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .meta-value {
            color: white;
            font-size: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Content Section */
        .content-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        @media (max-width: 1024px) {
            .content-section {
                grid-template-columns: 1fr;
            }
        }

        .description-box {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            line-height: 1.8;
        }

        .description-box h3 {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Actions */
        .actions-panel {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .action-btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            font-size: 14px;
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            color: #34d399;
            display: flex;
            align-items: center;
            gap: 12px;
        }
    </style>

    <div class="task-detail-page">
        <!-- Navigation -->
        <a href="{{ route('web.tasks') }}" class="back-button">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ org_trans('back_to_tasks') ?? 'Retour aux tâches' }}
        </a>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert-success">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <!-- Hero Section -->
        <div class="glass-panel task-hero">
            <div class="task-header-content">
                <div class="task-badges">
                    <span class="status-badge status-{{ $task->status }}">
                        @if($task->status == 'todo')
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/></svg>
                            {{ org_trans('todo') }}
                        @elseif($task->status == 'in_progress')
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/></svg>
                            {{ org_trans('in_progress') }}
                        @elseif($task->status == 'completed')
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                            {{ org_trans('completed') }}
                        @else
                            {{ $task->status }}
                        @endif
                    </span>
                    <span class="priority-badge priority-{{ $task->priority }}">
                        {{ strtoupper($task->priority) }}
                    </span>
                </div>

                <h1 class="task-title">{{ $task->title }}</h1>

                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">{{ org_trans('project_label') }}</div>
                        <div class="meta-value">
                            @if($task->project)
                                <a href="{{ route('web.projects.show', $task->project->id) }}" style="color: #60a5fa; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    {{ $task->project->name }}
                                </a>
                            @else
                                <span style="color: rgba(255,255,255,0.4)">Aucun projet</span>
                            @endif
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-label">{{ org_trans('assigned_to') }}</div>
                        <div class="meta-value">
                            @if($task->assignee)
                                <div class="user-avatar">
                                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                </div>
                                {{ $task->assignee->name }}
                            @else
                                <span style="color: rgba(255,255,255,0.4)">Non assigné</span>
                            @endif
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-label">{{ org_trans('due_date_label') }}</div>
                        <div class="meta-value" style="{{ $task->is_overdue ? 'color: #f87171;' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                @if($task->is_overdue)
                                    <span style="font-size: 12px; background: rgba(239,68,68,0.2); padding: 2px 6px; border-radius: 4px;">En retard</span>
                                @endif
                            @else
                                <span style="color: rgba(255,255,255,0.4)">--</span>
                            @endif
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-label">Créé par</div>
                        <div class="meta-value">
                            <div class="user-avatar" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);">
                                {{ strtoupper(substr($task->creator->name ?? '?', 0, 1)) }}
                            </div>
                            {{ $task->creator->name ?? 'Inconnu' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content & Actions -->
        <div class="content-section">
            <!-- Main Content -->
            <div style="flex: 1;">
                <div class="glass-panel description-box">
                    <h3>
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        Description
                    </h3>
                    @if($task->description)
                        <div style="white-space: pre-line;">{{ $task->description }}</div>
                    @else
                        <p style="color: rgba(255,255,255,0.4); font-style: italic;">Aucune description fournie.</p>
                    @endif
                </div>

                <!-- Checklist Section -->
                <div class="glass-panel">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Checklist
                        </h3>
                        @php
                            $totalChecklist = $task->checklists->count();
                            $completedChecklist = $task->checklists->where('is_completed', true)->count();
                            $progress = $totalChecklist > 0 ? round(($completedChecklist / $totalChecklist) * 100) : 0;
                        @endphp
                        <span id="checklist-stats" style="color: rgba(255,255,255,0.5); font-size: 14px;">
                            {{ $completedChecklist }}/{{ $totalChecklist }} ({{ $progress }}%)
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; margin-bottom: 24px; overflow: hidden;">
                        <div id="checklist-progress" style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, #34d399, #10b981); transition: width 0.3s ease;"></div>
                    </div>

                    <!-- Checklist Items -->
                    <div id="checklist-items" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                        @foreach($task->checklists as $item)
                        <div class="checklist-item" id="checklist-item-{{ $item->id }}" style="display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 8px; transition: background 0.2s;">
                            <input type="checkbox"
                                onchange="toggleChecklistItem('{{ $item->id }}', this.checked)"
                                {{ $item->is_completed ? 'checked' : '' }}
                                style="width: 18px; height: 18px; cursor: pointer; accent-color: #34d399;">

                            <span class="item-title" style="flex: 1; color: {{ $item->is_completed ? 'rgba(255,255,255,0.4)' : 'white' }}; text-decoration: {{ $item->is_completed ? 'line-through' : 'none' }}; transition: all 0.2s;">
                                {{ $item->title }}
                            </span>

                            @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by || auth()->id() == $task->assigned_to)
                            <button onclick="deleteChecklistItem('{{ $item->id }}')" style="background: none; border: none; color: rgba(239, 68, 68, 0.5); cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Add Item Form -->
                    @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by || auth()->id() == $task->assigned_to)
                    <form onsubmit="addChecklistItem(event, this)" style="display: flex; gap: 12px;">
                        <input type="text" name="title" placeholder="Ajouter un élément..." required
                            style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 12px; color: white; font-size: 14px;">
                        <button type="submit" class="btn-secondary" style="padding: 8px 16px; border-radius: 8px; font-size: 13px;">
                            Ajouter
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Attachments Section -->
                <div class="glass-panel" style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            Pièces jointes
                        </h3>
                    </div>

                    <div id="attachments-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
                        @foreach($task->attachments as $attachment)
                        <div class="attachment-item {{ $loop->index >= 3 ? 'extra-attachment' : '' }}"
                             id="attachment-{{ $attachment->id }}"
                             style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; overflow: hidden; transition: all 0.2s; display: {{ $loop->index >= 3 ? 'none' : 'flex' }}; flex-direction: column;">
                            @if(Str::startsWith($attachment->file_type, 'image/'))
                                <div style="height: 140px; width: 100%; background-image: url('{{ Storage::url($attachment->file_path) }}'); background-size: cover; background-position: center; border-bottom: 1px solid rgba(255,255,255,0.1);"></div>
                            @else
                                <div style="height: 140px; width: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    <svg width="48" height="48" fill="none" stroke="rgba(255,255,255,0.2)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                            @endif

                            <div style="padding: 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div style="margin-bottom: 8px;">
                                    <div style="font-size: 13px; font-weight: 500; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px;" title="{{ $attachment->file_name }}">
                                        {{ $attachment->file_name }}
                                    </div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.5);">
                                        {{ $attachment->formatted_size }}
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px;">
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" style="color: rgba(255,255,255,0.6); transition: color 0.2s; padding: 4px;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ Storage::url($attachment->file_path) }}" download style="color: rgba(255,255,255,0.6); transition: color 0.2s; padding: 4px;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @if(auth()->user()->canManageUsers() || auth()->id() == $task->created_by || auth()->id() == $attachment->user_id)
                                    <button onclick="deleteAttachment('{{ $attachment->id }}')" style="background: none; border: none; color: rgba(239, 68, 68, 0.6); cursor: pointer; padding: 4px; transition: color 0.2s;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($task->attachments->count() > 3)
                    <div id="show-more-attachments" style="text-align: center; margin-top: 10px;">
                        <button onclick="toggleAttachments()" data-expanded="false" style="background: none; border: none; color: #fbbb2a; cursor: pointer; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                            <span>Voir plus ({{ $task->attachments->count() - 3 }})</span>
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    @endif                    <div id="upload-area"
                        onclick="document.getElementById('file-upload').click()"
                        ondrop="handleDrop(event)"
                        ondragover="handleDragOver(event)"
                        ondragleave="handleDragLeave(event)"
                        style="border: 2px dashed rgba(255,255,255,0.1); border-radius: 12px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.2s;">
                        <input type="file" id="file-upload" style="display: none" onchange="handleFileSelect(event)">
                        <div style="color: rgba(255,255,255,0.5); margin-bottom: 8px;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto; display: block; margin-bottom: 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span style="font-size: 14px;">Cliquez ou glissez un fichier ici</span>
                        </div>
                        <div style="font-size: 12px; color: rgba(255,255,255,0.3);">Max 10MB</div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="glass-panel">
                    <h3 style="margin-bottom: 24px;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Discussion
                    </h3>

                    <!-- Comment Form -->
                    <form onsubmit="submitComment(event, this)" action="{{ route('web.tasks.comments.store', $task->id) }}" method="POST" style="margin-bottom: 32px;">
                        @csrf
                        <div style="display: flex; gap: 16px;">
                            <div class="user-avatar" style="width: 40px; height: 40px; flex-shrink: 0;">
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

                    <!-- Comments List -->
                    <div id="comments-container" style="display: flex; flex-direction: column; gap: 24px; max-height: 600px; overflow-y: auto; padding-right: 8px;">
                        @forelse($task->comments as $comment)
                        <div class="comment-thread" id="comment-{{ $comment->id }}">
                            <div style="display: flex; gap: 16px;">
                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; flex-shrink: 0;">
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
                                        <form onsubmit="submitComment(event, this)" action="{{ route('web.tasks.comments.store', $task->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div style="display: flex; gap: 12px;">
                                                <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
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
                                                    <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
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
                            <p>Aucun commentaire pour le moment.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Activity Log Section -->
                <div class="glass-panel" style="margin-top: 24px;">
                    <h3 style="margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Journal d'activité
                    </h3>

                    <div class="activity-scroll" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                        <div class="activity-feed" style="position: relative; padding-left: 20px;">
                            <div style="position: absolute; left: 9px; top: 0; bottom: 0; width: 2px; background: rgba(255,255,255,0.1);"></div>

                            @if(method_exists($task, 'activities'))
                                @forelse($task->activities()->with('user')->latest()->get() as $activity)
                                <div class="activity-item" style="position: relative; margin-bottom: 20px; padding-left: 20px;">
                                    <div style="position: absolute; left: 4px; top: 6px; width: 12px; height: 12px; background: #3b82f6; border-radius: 50%; border: 2px solid #1e293b; z-index: 1;"></div>
                                    <div style="font-size: 13px; color: rgba(255,255,255,0.8);">
                                        <span style="font-weight: 600; color: white;">{{ $activity->user->name }}</span>
                                        {{ $activity->description }}
                                    </div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 4px;">
                                        {{ $activity->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                @empty
                                <div style="color: rgba(255,255,255,0.4); font-style: italic; padding-left: 20px;">
                                    Aucune activité récente.
                                </div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div class="actions-panel">
                @php
                    $isProjectManager = auth()->user()->projects()->where('project_id', $task->project_id)->wherePivot('role', 'manager')->exists();
                    $canManage = auth()->user()->canManageUsers() || auth()->id() == $task->created_by || $isProjectManager;
                @endphp

                @if($canManage)
                <div class="glass-card">
                    <h4 style="color: white; font-weight: 700; margin-bottom: 16px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Actions</h4>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <!-- Status Select -->
                        <div style="margin-bottom: 4px;">
                            <label style="display: block; color: rgba(255,255,255,0.5); font-size: 12px; margin-bottom: 8px;">État de la tâche</label>
                            <form action="{{ route('web.tasks.updateStatus', $task->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div style="position: relative;">
                                    <select name="status" onchange="this.form.submit()"
                                        style="width: 100%; padding: 10px 16px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; color: white; appearance: none; cursor: pointer; font-size: 13px; font-weight: 500;">
                                        <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }} style="color: #60a5fa; background: #1e293b;">🔵 À faire</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }} style="color: #fbbb2a; background: #1e293b;">🟡 En cours</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }} style="color: #34d399; background: #1e293b;">🟢 Terminée</option>
                                        <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }} style="color: #f87171; background: #1e293b;">🔴 Annulée</option>
                                    </select>
                                    <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: rgba(255,255,255,0.5);">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 8px 0;"></div>

                        <a href="{{ route('web.tasks.edit', $task->id) }}" class="action-btn btn-primary">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            {{ org_trans('edit_task') }}
                        </a>

                        <button onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) { document.getElementById('delete-form').submit(); }" class="action-btn btn-danger">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Supprimer
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tags Section -->
            <div class="glass-card" style="margin-top: 24px;">
                <h4 style="color: white; font-weight: 700; margin-bottom: 16px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Étiquettes</h4>

                <div id="tags-container" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;">
                    @foreach($task->tags as $tag)
                    <span class="tag-badge" id="tag-{{ $tag->id }}" style="background: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        {{ $tag->name }}
                        @if($canManage)
                        <button onclick="removeTag('{{ $tag->id }}')" style="background: none; border: none; color: currentColor; opacity: 0.6; cursor: pointer; padding: 0; display: flex;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        @endif
                    </span>
                    @endforeach
                </div>

                @if($canManage)
                <form onsubmit="addTag(event, this)" style="position: relative;">
                    <input type="text" name="name" placeholder="Ajouter un tag..."
                        style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: white; font-size: 13px;">
                    <input type="color" name="color" value="#3B82F6"
                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; border: none; background: none; cursor: pointer; padding: 0;">
                </form>
                @endif
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="delete-form" action="{{ route('web.tasks.destroy', $task->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
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
                const count = replies.children.length;
                btn.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg> ${count} réponse(s)`;
            }
        }

        function toggleReadMore(commentId) {
            const textDiv = document.getElementById('text-' + commentId);
            const btn = document.getElementById('btn-more-' + commentId);

            if (textDiv.style.maxHeight) {
                textDiv.style.maxHeight = null;
                textDiv.style.overflow = 'visible';
                textDiv.style.display = 'block';
                textDiv.style.webkitLineClamp = 'unset';
                textDiv.style.webkitBoxOrient = 'unset';
                btn.textContent = 'Voir moins';
            } else {
                textDiv.style.maxHeight = '60px';
                textDiv.style.overflow = 'hidden';
                textDiv.style.display = '-webkit-box';
                textDiv.style.webkitLineClamp = '3';
                textDiv.style.webkitBoxOrient = 'vertical';
                btn.textContent = 'Voir plus';
            }
        }

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
                form.reset();
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;

                if (data.comment) {
                    const comment = data.comment;
                    const parentId = comment.parent_id;

                    if (parentId) {
                        const repliesContainer = document.getElementById('replies-' + parentId);
                        const repliesBtn = document.getElementById('btn-replies-' + parentId);

                        const replyHtml = `
                            <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                                <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
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

                        if (repliesContainer) {
                            repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                            repliesContainer.style.display = 'block';
                            const count = repliesContainer.children.length;
                            repliesBtn.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg> Masquer les réponses`;
                        } else {
                            window.location.reload();
                            return;
                        }
                        toggleReplyForm(parentId);
                    } else {
                        const container = document.getElementById('comments-container');
                        const noComments = document.getElementById('no-comments');
                        if (noComments) noComments.remove();

                        const commentHtml = `
                        <div class="comment-thread" id="comment-${comment.id}">
                            <div style="display: flex; gap: 16px;">
                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; flex-shrink: 0;">
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
                                                <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
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

        // Checklist Functions
        function updateProgress(stats) {
            console.log('Updating progress:', stats);
            const progressBar = document.getElementById('checklist-progress');
            const progressText = document.getElementById('checklist-stats');

            if (progressBar) {
                progressBar.style.width = stats.percentage + '%';
            }
            if (progressText) {
                progressText.textContent = `${stats.completed}/${stats.total} (${Math.round(stats.percentage)}%)`;
            }
        }

        function toggleChecklistItem(id, isChecked) {
            console.log('Toggling item:', id, isChecked);
            const itemContainer = document.getElementById('checklist-item-' + id);
            const titleSpan = itemContainer.querySelector('.item-title');

            fetch(`/tasks/checklist/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    is_completed: isChecked
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Toggle response:', data);
                if (data.success) {
                    if (isChecked) {
                        titleSpan.style.textDecoration = 'line-through';
                        titleSpan.style.color = 'rgba(255,255,255,0.4)';
                    } else {
                        titleSpan.style.textDecoration = 'none';
                        titleSpan.style.color = 'white';
                    }
                    updateProgress(data.progress);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox state if error
                const checkbox = itemContainer.querySelector('input[type="checkbox"]');
                checkbox.checked = !isChecked;
            });
        }

        function addChecklistItem(event, form) {
            event.preventDefault();
            console.log('Adding item...');
            const input = form.querySelector('input[name="title"]');
            const title = input.value.trim();

            if (!title) return;

            const taskId = '{{ $task->id }}';

            fetch(`/tasks/${taskId}/checklist`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title: title
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Add response:', data);
                if (data.success) {
                    const list = document.getElementById('checklist-items');
                    const itemHtml = `
                        <div class="checklist-item" id="checklist-item-${data.item.id}" style="display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 8px; transition: background 0.2s;">
                            <input type="checkbox"
                                onchange="toggleChecklistItem('${data.item.id}', this.checked)"
                                style="width: 18px; height: 18px; cursor: pointer; accent-color: #34d399;">

                            <span class="item-title" style="flex: 1; color: white; transition: all 0.2s;">
                                ${data.item.title}
                            </span>

                            <button onclick="deleteChecklistItem('${data.item.id}')"
                                style="background: none; border: none; color: rgba(239, 68, 68, 0.5); cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    `;
                    list.insertAdjacentHTML('beforeend', itemHtml);
                    input.value = '';
                    updateProgress(data.progress);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function deleteChecklistItem(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) return;
            console.log('Deleting item:', id);

            fetch(`/tasks/checklist/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Delete response:', data);
                if (data.success) {
                    const row = document.getElementById('checklist-item-' + id);
                    if (row) {
                        row.remove();
                    } else {
                        console.error('Row not found for id:', id);
                    }
                    updateProgress(data.progress);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Tag Functions
        function addTag(event, form) {
            event.preventDefault();
            const nameInput = form.querySelector('input[name="name"]');
            const colorInput = form.querySelector('input[name="color"]');
            const name = nameInput.value.trim();

            if (!name) return;

            const taskId = '{{ $task->id }}';

            fetch(`/tasks/${taskId}/tags`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    color: colorInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('tags-container');
                    if (!document.getElementById('tag-' + data.tag.id)) {
                        const tagHtml = `
                            <span class="tag-badge" id="tag-${data.tag.id}" style="background: ${data.tag.color}20; color: ${data.tag.color}; border: 1px solid ${data.tag.color}40; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                ${data.tag.name}
                                <button onclick="removeTag('${data.tag.id}')" style="background: none; border: none; color: currentColor; opacity: 0.6; cursor: pointer; padding: 0; display: flex;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        `;
                        container.insertAdjacentHTML('beforeend', tagHtml);
                    }
                    nameInput.value = '';
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function removeTag(tagId) {
            if (!confirm('Retirer ce tag ?')) return;

            const taskId = '{{ $task->id }}';

            fetch(`/tasks/${taskId}/tags/${tagId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tag = document.getElementById('tag-' + tagId);
                    if (tag) tag.remove();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Attachment Functions
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) uploadFile(file);
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = '#3b82f6';
            event.currentTarget.style.background = 'rgba(59, 130, 246, 0.1)';
        }

        function handleDragLeave(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = 'rgba(255,255,255,0.1)';
            event.currentTarget.style.background = 'transparent';
        }

        function handleDrop(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = 'rgba(255,255,255,0.1)';
            event.currentTarget.style.background = 'transparent';

            const file = event.dataTransfer.files[0];
            if (file) uploadFile(file);
        }

        function uploadFile(file) {
            if (file.size > 10 * 1024 * 1024) {
                alert('Le fichier est trop volumineux (Max 10MB)');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            const taskId = '{{ $task->id }}';
            const uploadArea = document.getElementById('upload-area');
            const originalContent = uploadArea.innerHTML;

            uploadArea.innerHTML = '<div style="color: white;">Téléchargement en cours...</div>';

            fetch(`/tasks/${taskId}/attachments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (data.errors) {
                        const messages = Object.values(data.errors).flat().join('\n');
                        throw new Error(messages);
                    }
                    throw new Error(data.message || 'Erreur lors du téléchargement');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('attachments-list');

                    const isImage = data.attachment.file_type.startsWith('image/');
                    const previewHtml = isImage
                        ? `<div style="height: 140px; width: 100%; background-image: url('${data.url}'); background-size: cover; background-position: center; border-bottom: 1px solid rgba(255,255,255,0.1);"></div>`
                        : `<div style="height: 140px; width: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <svg width="48" height="48" fill="none" stroke="rgba(255,255,255,0.2)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                           </div>`;

                    const attachmentHtml = `
                        <div class="attachment-item" id="attachment-${data.attachment.id}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; overflow: hidden; transition: all 0.2s; display: flex; flex-direction: column;">
                            ${previewHtml}
                            <div style="padding: 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div style="margin-bottom: 8px;">
                                    <div style="font-size: 13px; font-weight: 500; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px;" title="${data.attachment.file_name}">
                                        ${data.attachment.file_name}
                                    </div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.5);">
                                        ${data.formatted_size}
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px;">
                                    <a href="${data.url}" target="_blank" style="color: rgba(255,255,255,0.6); transition: color 0.2s; padding: 4px;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="${data.url}" download style="color: rgba(255,255,255,0.6); transition: color 0.2s; padding: 4px;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    <button onclick="deleteAttachment('${data.attachment.id}')" style="background: none; border: none; color: rgba(239, 68, 68, 0.6); cursor: pointer; padding: 4px; transition: color 0.2s;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', attachmentHtml);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            })
            .finally(() => {
                uploadArea.innerHTML = originalContent;
            });
        }

        function deleteAttachment(attachmentId) {
            if (!confirm('Supprimer ce fichier ?')) return;

            const taskId = '{{ $task->id }}';

            fetch(`/tasks/${taskId}/attachments/${attachmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const el = document.getElementById('attachment-' + attachmentId);
                    if (el) el.remove();

                    // Update "See more" button if needed
                    const hiddenItems = document.querySelectorAll('.extra-attachment');
                    const btnContainer = document.getElementById('show-more-attachments');

                    if (btnContainer) {
                        const btn = btnContainer.querySelector('button');
                        const isExpanded = btn.getAttribute('data-expanded') === 'true';

                        if (hiddenItems.length === 0) {
                            btnContainer.remove();
                        } else if (!isExpanded) {
                            btn.querySelector('span').innerText = `Voir plus (${hiddenItems.length})`;
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function toggleAttachments() {
            const hiddenItems = document.querySelectorAll('.extra-attachment');
            const btn = document.querySelector('#show-more-attachments button');
            const isExpanded = btn.getAttribute('data-expanded') === 'true';
            const icon = btn.querySelector('svg');
            const text = btn.querySelector('span');

            hiddenItems.forEach(item => {
                item.style.display = isExpanded ? 'none' : 'flex';
            });

            if (isExpanded) {
                text.innerText = 'Voir plus (' + hiddenItems.length + ')';
                btn.setAttribute('data-expanded', 'false');
                icon.style.transform = 'rotate(0deg)';
            } else {
                text.innerText = 'Voir moins';
                btn.setAttribute('data-expanded', 'true');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
</x-app-layout>
