<x-app-layout>
    <style>
        /* Base Styles */
        .tasks-page {
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
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        /* Action Buttons */
        .btn-create {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(223, 85, 38, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(223, 85, 38, 0.4);
        }

        /* Filters */
        .filters-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.03);
            padding: 8px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .filter-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 16px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 160px;
        }

        .filter-select:focus {
            outline: none;
            border-color: rgba(251, 187, 42, 0.5);
            background: rgba(255, 255, 255, 0.08);
        }

        .filter-select option {
            background: #1e293b;
            color: white;
        }

        /* Kanban Board */
        .kanban-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .kanban-container {
                grid-template-columns: 1fr;
            }
        }

        .kanban-column {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 20px;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 4px;
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
            color: rgba(255, 255, 255, 0.7);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Task Card */
        .task-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            cursor: grab;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .task-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .task-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
            transform: scale(0.95);
        }

        .task-title {
            color: white;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .task-description {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            margin-bottom: 16px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .priority-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-urgent { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .priority-high { background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3); }
        .priority-medium { background: rgba(251, 187, 42, 0.2); color: #fbbb2a; border: 1px solid rgba(251, 187, 42, 0.3); }
        .priority-low { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }

        .project-badge {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .task-avatar {
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
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .task-date {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .task-date.overdue {
            color: #f87171;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            border: 2px dashed rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }

        /* Column Colors */
        .column-todo .column-title svg { color: #60a5fa; }
        .column-in-progress .column-title svg { color: #fbbb2a; }
        .column-completed .column-title svg { color: #34d399; }

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

    <div class="tasks-page">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">📋 {{ org_trans('my_tasks') }}</h1>
            @if(auth()->user()->canManageUsers())
            <a href="{{ route('web.tasks.create') }}" class="btn-create">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ org_trans('new') }} {{ org_trans('tasks') }}
            </a>
            @endif
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert-success">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <!-- Filters -->
        <div class="filters-bar">
            <select class="filter-select" onchange="window.location.href='{{ route('web.tasks') }}?project_id=' + this.value">
                <option value="">{{ org_trans('all') }} {{ org_trans('projects') }}</option>
                @foreach(\App\Models\Project::where('organization_id', auth()->user()->organization_id)->get() as $proj)
                    <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                        {{ $proj->name }}
                    </option>
                @endforeach
            </select>

            <select class="filter-select" onchange="window.location.href='{{ route('web.tasks') }}?priority=' + this.value">
                <option value="">{{ org_trans('all') }} {{ org_trans('priority') }}</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
            </select>

            <select class="filter-select" onchange="window.location.href='{{ route('web.tasks') }}?assigned_to=' + this.value">
                <option value="">{{ org_trans('all') }} {{ org_trans('members') }}</option>
                <option value="{{ auth()->id() }}" {{ request('assigned_to') == auth()->id() ? 'selected' : '' }}>{{ org_trans('my') }} {{ org_trans('tasks') }}</option>
                @foreach(\App\Models\User::where('organization_id', auth()->user()->organization_id)->get() as $member)
                    <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>
                        {{ $member->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Kanban Board -->
        <div class="kanban-container">
            <!-- TODO Column -->
            <div class="kanban-column column-todo" data-status="todo">
                <div class="column-header">
                    <div class="column-title">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        {{ org_trans('todo') }}
                    </div>
                    <span class="column-count">{{ $tasks->where('status', 'todo')->count() }}</span>
                </div>

                @foreach($tasks->where('status', 'todo') as $task)
                <div class="task-card" data-task-id="{{ $task->id }}">
                    <div class="task-title">{{ $task->title }}</div>
                    @if($task->description)
                    <div class="task-description">{{ $task->description }}</div>
                    @endif
                    <div class="task-tags">
                        <span class="priority-badge priority-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span>
                        @if($task->project)
                        <span class="project-badge">{{ $task->project->name }}</span>
                        @endif
                    </div>
                    <div class="task-footer">
                        <div class="task-date {{ $task->is_overdue ? 'overdue' : '' }}">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}
                            @else
                                --
                            @endif
                        </div>
                        @if($task->assignee)
                        <div class="task-avatar" title="{{ $task->assignee->name }}">
                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($tasks->where('status', 'todo')->count() == 0)
                <div class="empty-state">
                    <p>Aucune tâche à faire</p>
                </div>
                @endif
            </div>

            <!-- IN PROGRESS Column -->
            <div class="kanban-column column-in-progress" data-status="in_progress">
                <div class="column-header">
                    <div class="column-title">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                        {{ org_trans('in_progress') }}
                    </div>
                    <span class="column-count">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                </div>

                @foreach($tasks->where('status', 'in_progress') as $task)
                <div class="task-card" data-task-id="{{ $task->id }}">
                    <div class="task-title">{{ $task->title }}</div>
                    @if($task->description)
                    <div class="task-description">{{ $task->description }}</div>
                    @endif
                    <div class="task-tags">
                        <span class="priority-badge priority-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span>
                        @if($task->project)
                        <span class="project-badge">{{ $task->project->name }}</span>
                        @endif
                    </div>
                    <div class="task-footer">
                        <div class="task-date {{ $task->is_overdue ? 'overdue' : '' }}">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}
                            @else
                                --
                            @endif
                        </div>
                        @if($task->assignee)
                        <div class="task-avatar" title="{{ $task->assignee->name }}">
                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($tasks->where('status', 'in_progress')->count() == 0)
                <div class="empty-state">
                    <p>Aucune tâche en cours</p>
                </div>
                @endif
            </div>

            <!-- DONE Column -->
            <div class="kanban-column column-completed" data-status="completed">
                <div class="column-header">
                    <div class="column-title">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        {{ org_trans('done') }}
                    </div>
                    <span class="column-count">{{ $tasks->where('status', 'completed')->count() }}</span>
                </div>

                @foreach($tasks->where('status', 'completed') as $task)
                <div class="task-card" data-task-id="{{ $task->id }}">
                    <div class="task-title">{{ $task->title }}</div>
                    @if($task->description)
                    <div class="task-description">{{ $task->description }}</div>
                    @endif
                    <div class="task-tags">
                        <span class="priority-badge priority-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span>
                        @if($task->project)
                        <span class="project-badge">{{ $task->project->name }}</span>
                        @endif
                    </div>
                    <div class="task-footer">
                        <div class="task-date">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}
                            @else
                                --
                            @endif
                        </div>
                        @if($task->assignee)
                        <div class="task-avatar" title="{{ $task->assignee->name }}">
                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($tasks->where('status', 'completed')->count() == 0)
                <div class="empty-state">
                    <p>Aucune tâche terminée</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Drag & Drop JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const taskCards = document.querySelectorAll('.task-card');
            const columns = document.querySelectorAll('.kanban-column');
            let draggedCard = null;
            let isDragging = false;
            let startX = 0;
            let startY = 0;

            // Make tasks draggable
            taskCards.forEach(card => {
                card.setAttribute('draggable', 'true');

                // Track mouse down position
                card.addEventListener('mousedown', function(e) {
                    startX = e.clientX;
                    startY = e.clientY;
                    isDragging = false;
                });

                card.addEventListener('dragstart', function(e) {
                    draggedCard = this;
                    isDragging = true;
                    this.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('task-id', this.dataset.taskId);
                });

                card.addEventListener('dragend', function(e) {
                    this.classList.remove('dragging');
                    draggedCard = null;

                    // Reset drag flag after a short delay
                    setTimeout(() => {
                        isDragging = false;
                    }, 100);
                });

                // Handle click for navigation (only if not dragging)
                card.addEventListener('click', function(e) {
                    // Calculate distance moved
                    const endX = e.clientX;
                    const endY = e.clientY;
                    const distance = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));

                    // If distance is small (< 5px), it's a click, not a drag
                    if (distance < 5 && !isDragging) {
                        const taskId = this.dataset.taskId;
                        window.location.href = '{{ route("web.tasks") }}/' + taskId;
                    }
                });
            });

            // Make columns droppable
            columns.forEach(column => {
                column.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                });

                column.addEventListener('drop', function(e) {
                    e.preventDefault();
                    const taskId = e.dataTransfer.getData('task-id');
                    const newStatus = this.dataset.status;

                    if (!taskId) return;

                    // Update task status via AJAX
                    fetch('{{ route("web.tasks") }}/' + taskId + '/status', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.message) {
                            // Reload page to update kanban
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('{{ org_trans('error_updating_status') }} ' + error.message);
                    });
                });
            });
        });
    </script>
</x-app-layout>
