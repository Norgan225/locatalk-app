<x-app-layout>
    <style>
        .meetings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }

        .stat-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info h3 {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 8px 0;
        }

        .stat-info p {
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 28px;
            height: 28px;
        }

        .filters-section {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.5);
        }

        .filter-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .meetings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .meeting-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .meeting-card:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .meeting-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.scheduled {
            background: rgba(96, 165, 250, 0.2);
            color: #60a5fa;
        }

        .status-badge.ongoing {
            background: rgba(251, 187, 42, 0.2);
            color: #fbbb2a;
            animation: pulse 2s infinite;
        }

        .status-badge.completed {
            background: rgba(74, 222, 128, 0.2);
            color: #4ade80;
        }

        .status-badge.cancelled {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }

        .recording-indicator {
            width: 24px;
            height: 24px;
            background: rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
        }

        .meeting-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .meeting-description {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin: 0 0 16px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .meeting-meta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        .meta-item svg {
            width: 16px;
            height: 16px;
            color: rgba(251, 187, 42, 0.8);
        }

        .meeting-footer {
            display: flex;
            gap: 8px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
        }

        .btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(251, 187, 42, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            color: rgba(255, 255, 255, 0.3);
            margin: 0 auto 20px;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin: 0 0 12px 0;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.6);
            margin: 0 0 24px 0;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        @media (max-width: 768px) {
            .meetings-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Header -->
    <div class="meetings-header">
        <div class="header-title">
            <h1>📹 {{ org_trans('meetings') }}</h1>
        </div>
        <div class="header-actions">
            @can('manage-users')
            <a href="{{ route('web.meetings.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ org_trans('new_meeting') }}
            </a>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ org_trans('total') }}</h3>
                    <p>{{ $stats['total'] }}</p>
                </div>
                <div class="stat-icon" style="background: rgba(96, 165, 250, 0.2);">
                    <svg style="color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ org_trans('upcoming') }}</h3>
                    <p>{{ $stats['upcoming'] }}</p>
                </div>
                <div class="stat-icon" style="background: rgba(74, 222, 128, 0.2);">
                    <svg style="color: #4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ org_trans('ongoing') }}</h3>
                    <p>{{ $stats['ongoing'] }}</p>
                </div>
                <div class="stat-icon" style="background: rgba(251, 187, 42, 0.2);">
                    <svg style="color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ org_trans('completed') }}</h3>
                    <p>{{ $stats['completed'] }}</p>
                </div>
                <div class="stat-icon" style="background: rgba(156, 163, 175, 0.2);">
                    <svg style="color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('web.meetings') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>{{ org_trans('search_meetings') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ org_trans('meeting_title') }}, {{ org_trans('description') }}...">
                </div>

                <div class="filter-group">
                    <label>{{ org_trans('status') }}</label>
                    <select name="status">
                        <option value="">{{ org_trans('all_statuses') }}</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ org_trans('scheduled_meetings') }}</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>{{ org_trans('ongoing_meetings') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ org_trans('completed_meetings') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ org_trans('cancelled_meetings') }}</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>{{ org_trans('date') }}</label>
                    <input type="date" name="date" value="{{ request('date') }}">
                </div>

                @if(auth()->user()->isSuperAdmin())
                <div class="filter-group">
                    <label>Organisation</label>
                    <select name="organization_id">
                        <option value="">Toutes</option>
                        @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        {{ org_trans('filter') }}
                    </button>
                    <a href="{{ route('web.meetings') }}" class="btn btn-secondary">{{ org_trans('reset') }}</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Meetings Grid -->
    @if($meetings->count() > 0)
    <div class="meetings-grid">
        @foreach($meetings as $meeting)
        <div class="meeting-card" onclick="window.location='{{ route('web.meetings.show', $meeting->id) }}'">
            <div class="meeting-header">
                <span class="status-badge {{ $meeting->status }}">
                    @if($meeting->status === 'scheduled')
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        {{ org_trans('scheduled') }}
                    @elseif($meeting->status === 'ongoing')
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        {{ org_trans('ongoing') }}
                    @elseif($meeting->status === 'completed')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ org_trans('completed') }}
                    @else
                        {{ org_trans('cancelled') }}
                    @endif
                </span>

                @if($meeting->is_recorded)
                <div class="recording-indicator" title="Enregistrement activé">
                    <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                        <circle cx="10" cy="10" r="4"/>
                    </svg>
                </div>
                @endif
            </div>

            <h3 class="meeting-title">{{ $meeting->title }}</h3>

            @if($meeting->description)
            <p class="meeting-description">{{ $meeting->description }}</p>
            @endif

            <div class="meeting-meta">
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $meeting->start_time->format('d/m/Y à H:i') }}
                </div>

                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $meeting->creator->name }}
                </div>

                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{ $meeting->participants->count() }} {{ org_trans('participants_count') }}
                </div>
            </div>

            <div class="meeting-footer">
                <a href="{{ route('web.meetings.show', $meeting->id) }}" class="btn btn-primary" onclick="event.stopPropagation()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ org_trans('view_details') }}
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px;">
        {{ $meetings->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <h3>{{ org_trans('no_meetings_found') }}</h3>
        <p>{{ request()->has('search') || request()->has('status') || request()->has('date') ? org_trans('no_meetings_match_criteria') : org_trans('create_first_meeting') }}</p>
        @can('manage-users')
        <a href="{{ route('web.meetings.create') }}" class="btn btn-primary" style="display: inline-flex; width: auto;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ org_trans('create_meeting') }}
        </a>
        @endcan
    </div>
    @endif

</x-app-layout>
