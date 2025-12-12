<x-app-layout>
    <style>
        /* --- Design System Premium --- */
        :root {
            --primary-gradient: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.5);
            --card-radius: 24px;
        }

        .analytics-container {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
            font-family: 'Inter', sans-serif;
        }

        /* Header */
        .page-header {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .date-filter {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 0.5rem 0.75rem;
            backdrop-filter: blur(10px);
        }

        .date-filter input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .date-filter input:focus {
            border-color: #fbbb2a;
            background: rgba(255, 255, 255, 0.08);
        }

        .date-filter input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        .date-filter button {
            background: var(--primary-gradient);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(223, 85, 38, 0.2);
        }

        .date-filter button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(223, 85, 38, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--card-radius);
            padding: 1.75rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            background: rgba(255, 255, 255, 0.05);
        }

        .stat-icon-wrapper svg {
            width: 28px;
            height: 28px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-change {
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .stat-change.positive {
            background: rgba(52, 211, 153, 0.1);
            color: #34d399;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .chart-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--card-radius);
            padding: 2rem;
            backdrop-filter: blur(20px);
        }

        .chart-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .chart-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbb2a;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .data-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .data-value {
            color: #ffffff;
            font-weight: 700;
            font-size: 16px;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .analytics-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .date-filter {
                width: 100%;
                flex-wrap: wrap;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="analytics-container">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">{{ org_trans('analytics') }}</h1>

            <form method="GET" action="{{ route('web.analytics') }}" class="date-filter">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                <span style="color: rgba(255,255,255,0.5); font-size: 14px;">{{ org_trans('to') }}</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                <button type="submit">{{ org_trans('apply') }}</button>
            </form>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Projects -->
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="color: #3b82f6;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('total_projects') }}</div>
                <div class="stat-value">{{ $projectsStats['total'] }}</div>
                <div class="stat-change positive">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    {{ $projectsStats['active'] }} {{ org_trans('active') }}
                </div>
            </div>

            <!-- Tasks -->
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="color: #10b981;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('total_tasks') }}</div>
                <div class="stat-value">{{ $tasksStats['total'] }}</div>
                <div class="stat-change positive">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $tasksStats['completed'] }} {{ org_trans('completed') }}
                </div>
            </div>

            <!-- Users -->
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="color: #8b5cf6;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('users') }}</div>
                <div class="stat-value">{{ $usersStats['total'] }}</div>
                <div class="stat-change positive">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $usersStats['active'] }} {{ org_trans('active') }}
                </div>
            </div>

            <!-- Meetings -->
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="color: #f59e0b;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="stat-label">{{ org_trans('meetings') }}</div>
                <div class="stat-value">{{ $meetingsStats['total'] }}</div>
                <div class="stat-change positive">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $meetingsStats['upcoming'] }} {{ org_trans('upcoming') }}
                </div>
            </div>
        </div>

        <!-- Detailed Charts Grid -->
        <div class="charts-grid">
            <!-- Project Status -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-icon">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="chart-title">{{ org_trans('project_status') }}</h3>
                </div>

                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #22c55e;"></div>
                        {{ org_trans('active_projects') }}
                    </div>
                    <div class="data-value">{{ $projectsStats['active'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #3b82f6;"></div>
                        {{ org_trans('completed_projects') }}
                    </div>
                    <div class="data-value">{{ $projectsStats['completed'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #f59e0b;"></div>
                        {{ org_trans('on_hold') }}
                    </div>
                    <div class="data-value">{{ $projectsStats['on_hold'] }}</div>
                </div>
            </div>

            <!-- Tasks Priority -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-icon">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="chart-title">{{ org_trans('tasks_by_priority') }}</h3>
                </div>

                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #ef4444;"></div>
                        {{ org_trans('urgent') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['by_priority']['urgent'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #f59e0b;"></div>
                        {{ org_trans('high') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['by_priority']['high'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #3b82f6;"></div>
                        {{ org_trans('medium') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['by_priority']['medium'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #10b981;"></div>
                        {{ org_trans('low') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['by_priority']['low'] }}</div>
                </div>
            </div>

            <!-- Tasks Status -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-icon">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="chart-title">{{ org_trans('task_status') }}</h3>
                </div>

                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #6b7280;"></div>
                        {{ org_trans('todo') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['todo'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #f59e0b;"></div>
                        {{ org_trans('in_progress') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['in_progress'] }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">
                        <div class="data-dot" style="background: #10b981;"></div>
                        {{ org_trans('completed') }}
                    </div>
                    <div class="data-value">{{ $tasksStats['completed'] }}</div>
                </div>
            </div>

            <!-- Users by Department -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-icon">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="chart-title">{{ org_trans('users_by_department') }}</h3>
                </div>

                @if($usersStats['by_department']->isNotEmpty())
                    @foreach($usersStats['by_department'] as $dept)
                        <div class="data-row">
                            <div class="data-label">
                                <div class="data-dot" style="background: #8b5cf6;"></div>
                                {{ $dept['name'] }}
                            </div>
                            <div class="data-value">{{ $dept['count'] }}</div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 2rem; color: rgba(255,255,255,0.5);">
                        {{ org_trans('no_data_available') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
