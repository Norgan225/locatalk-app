
<x-app-layout>
    <style>
        * {
            box-sizing: border-box;
        }

        .analytics-container {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, rgba(255, 255, 255, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .date-filter {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.5rem 1rem;
        }

        .date-filter input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            color: #ffffff;
            font-size: 13px;
        }

        .date-filter button {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .date-filter button:hover {
            transform: translateY(-2px);
        }

        /* Stats Grid - Top Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color, linear-gradient(135deg, #df5526, #fbbb2a));
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--card-color, linear-gradient(135deg, #df5526, #fbbb2a));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
            color: white;
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #fff;
        }

        .stat-change.positive {
            color: #34d399;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Charts Grid - Analytics Cards */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-title svg {
            width: 24px;
            height: 24px;
            color: #fff;
        }

        .progress-bar-container {
            margin-bottom: 1rem;
        }        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 13px;
        }

        .progress-label-name {
            color: rgba(255, 255, 255, 0.7);
        }

        .progress-label-value {
            color: #ffffff;
            font-weight: 600;
        }

        .progress-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--progress-color, linear-gradient(135deg, #df5526, #fbbb2a));
            transition: width 0.5s ease;
        }

        .pie-chart {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .pie-visual {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pie-legend {
            flex: 1;
            min-width: 200px;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .legend-item:last-child {
            border-bottom: none;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 0.5rem;
        }

        .legend-label {
            flex: 1;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
        }

        .legend-value {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }

        /* Tablet */
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .analytics-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 22px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .date-filter {
                width: 100%;
                flex-wrap: wrap;
            }

            .chart-card {
                padding: 1.25rem;
            }

            .chart-title {
                font-size: 16px;
            }

            .pie-chart {
                flex-direction: column;
            }

            .pie-visual {
                width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .analytics-container {
                padding: 0.75rem;
            }

            .page-title {
                font-size: 20px;
            }

            .date-filter input {
                font-size: 12px;
                padding: 0.4rem 0.6rem;
            }

            .date-filter button {
                font-size: 12px;
                padding: 0.4rem 0.8rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-value {
                font-size: 28px;
            }

            .chart-card {
                padding: 1rem;
            }

            .chart-title {
                font-size: 15px;
            }
        }
    </style>

    <div class="analytics-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ org_trans('analytics') }}</h1>

            <form method="GET" action="{{ route('web.analytics') }}" class="date-filter">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                <span style="color: rgba(255,255,255,0.5);">{{ org_trans('to') }}</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                <button type="submit">{{ org_trans('apply') }}</button>
            </form>
        </div>

        <!-- Main Stats Grid -->
        <div class="stats-grid">
            <!-- Projects -->
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #3b82f6, #2563eb);">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('total_projects') }}</div>
                <div class="stat-value">{{ $projectsStats['total'] }}</div>
                <div class="stat-change positive">
                    {{ $projectsStats['active'] }} {{ org_trans('active') }}
                </div>
            </div>

            <!-- Tasks -->
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #10b981, #059669);">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('total_tasks') }}</div>
                <div class="stat-value">{{ $tasksStats['total'] }}</div>
                <div class="stat-change positive">
                    {{ $tasksStats['completed'] }} {{ org_trans('completed') }}
                </div>
            </div>

            <!-- Users -->
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('users') }}</div>
                <div class="stat-value">{{ $usersStats['total'] }}</div>
                <div class="stat-change positive">
                    {{ $usersStats['active'] }} {{ org_trans('active') }}
                </div>
            </div>

            <!-- Meetings -->
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #f59e0b, #d97706);">
                <div class="stat-icon" style="color: #fff;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="stat-label" style="color: #fff;">{{ org_trans('meetings') }}</div>
                <div class="stat-value" style="color: #fff;">{{ $meetingsStats['total'] }}</div>
                <div class="stat-change positive">
                    <span style="color: #22c55e;">{{ $meetingsStats['upcoming'] }} {{ org_trans('upcoming') }}</span>
                </div>
            </div>

            <!-- Messages -->
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #ec4899, #db2777);">
                <div class="stat-icon" style="color: #fff;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div class="stat-label" style="color: #fff;">{{ org_trans('messages') }}</div>
                <div class="stat-value" style="color: #fff;">{{ $messagesStats['total'] }}</div>
                <div class="stat-change positive">
                    <span style="color: #22c55e;">{{ $messagesStats['today'] }} {{ org_trans('today') }}</span>
                </div>
            </div>

            <!-- Channels -->
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #10b981, #059669);">
                <div class="stat-icon" style="color: #fff;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <div class="stat-label" style="color: #fff;">{{ org_trans('channels') }}</div>
                <div class="stat-value" style="color: #fff;">{{ $channelsStats['total'] }}</div>
                <div class="stat-change positive">
                    <span style="color: #22c55e;">{{ $channelsStats['public'] }} {{ org_trans('public') }}</span>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <div class="analytics-responsive-grid">
                <!-- Projects Status -->
                <div class="chart-card analytic-block" style="padding: 24px 20px; border-radius: 18px; background: rgba(255,255,255,0.03); margin-bottom: 0;">
                <h3 class="chart-title" style="color: #fff; display: flex; align-items: center; gap: 10px; font-size: 20px; margin-bottom: 18px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff; width: 28px; height: 28px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ org_trans('project_status') }}
                </h3>
                <div class="analytic-content" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #22c55e; font-weight: 600;">{{ org_trans('active_projects') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $projectsStats['active'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #3b82f6; font-weight: 600;">{{ org_trans('completed_projects') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $projectsStats['completed'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #f59e0b; font-weight: 600;">{{ org_trans('on_hold') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $projectsStats['on_hold'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Tasks by Priority -->
            <div class="chart-card analytic-block" style="padding: 24px 20px; border-radius: 18px; background: rgba(255,255,255,0.03); margin-bottom: 0;">
                <h3 class="chart-title" style="color: #fff; display: flex; align-items: center; gap: 10px; font-size: 20px; margin-bottom: 18px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff; width: 28px; height: 28px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ org_trans('tasks_by_priority') }}
                </h3>
                <div class="analytic-content" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #ef4444; font-weight: 600;">{{ org_trans('urgent') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['by_priority']['urgent'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #f59e0b; font-weight: 600;">{{ org_trans('high') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['by_priority']['high'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #3b82f6; font-weight: 600;">{{ org_trans('medium') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['by_priority']['medium'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #10b981; font-weight: 600;">{{ org_trans('low') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['by_priority']['low'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Tasks Status -->
            <div class="chart-card analytic-block" style="padding: 24px 20px; border-radius: 18px; background: rgba(255,255,255,0.03); margin-bottom: 0;">
                <h3 class="chart-title" style="color: #fff; display: flex; align-items: center; gap: 10px; font-size: 20px; margin-bottom: 18px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff; width: 28px; height: 28px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    {{ org_trans('task_status') }}
                </h3>
                <div class="analytic-content" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #6b7280; font-weight: 600;">{{ org_trans('todo') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['todo'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #f59e0b; font-weight: 600;">{{ org_trans('in_progress') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['in_progress'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #10b981; font-weight: 600;">{{ org_trans('completed') }}</span>
                        <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $tasksStats['completed'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Users by Department -->
            <div class="chart-card analytic-block" style="padding: 24px 20px; border-radius: 18px; background: rgba(255,255,255,0.03); margin-bottom: 0;">
                <h3 class="chart-title" style="color: #fff; display: flex; align-items: center; gap: 10px; font-size: 20px; margin-bottom: 18px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff; width: 28px; height: 28px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ org_trans('users_by_department') }}
                </h3>
                @if($usersStats['by_department']->isNotEmpty())
                    <div class="analytic-content" style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($usersStats['by_department'] as $dept)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #22c55e; font-weight: 600;">{{ $dept['name'] }}</span>
                            <span style="color: #fff; font-weight: 700; font-size: 18px;">{{ $dept['count'] }}</span>
                        </div>
                    @endforeach
                    </div>
                @else
                    <p style="color: rgba(255,255,255,0.5); text-align: center; padding: 2rem;">{{ org_trans('no_data_available') }}</p>
                @endif
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="chart-card">
            <h3 class="chart-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ org_trans('recent_activities') }}
            </h3>
            <div class="activity-list">
                @forelse($recentActivities as $activity)
                <div class="activity-item">
                    <div class="activity-icon">
                        @if($activity->type === 'project')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        @elseif($activity->type === 'task')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @endif
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">{{ ucfirst($activity->type) }}: {{ $activity->title }}</div>
                        <div class="activity-time">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <p style="color: rgba(255,255,255,0.5); text-align: center; padding: 2rem;">{{ org_trans('no_recent_activity') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
