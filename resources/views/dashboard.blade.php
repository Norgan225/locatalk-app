<x-app-layout>
    <style>
        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateY(-2px);
        }

        /* Stat Card */
        .stat-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 500;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .trend-up {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
        }

        .trend-down {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
        }

        /* Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Quick Actions */
        .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .quick-action:hover {
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.1), rgba(251, 187, 42, 0.1));
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateX(5px);
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbb2a;
        }

        /* Section Title */
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-radius: 2px;
        }

        /* Activity Item */
        .activity-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .activity-time {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
        }

        .actions-activity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 32px;
        }

        @media (max-width: 768px) {
            .actions-activity-grid {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 24px;
            }

            .stat-icon {
                width: 48px;
                height: 48px;
            }

            .section-title {
                font-size: 16px;
            }

            .page-title {
                font-size: 22px;
            }
        }
    </style>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Tâches -->
        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1);">
                <svg style="width: 28px; height: 28px; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['personal']['my_tasks']['total'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('my') }} {{ org_trans('tasks') }}</div>
            @if(isset($stats['personal']['my_tasks']['in_progress']) && $stats['personal']['my_tasks']['in_progress'] > 0)
            <div class="stat-trend trend-up">
                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                {{ $stats['personal']['my_tasks']['in_progress'] }} {{ org_trans('in_progress') }}
            </div>
            @endif
        </div>

        <!-- Projets -->
        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(251, 187, 42, 0.1);">
                <svg style="width: 28px; height: 28px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['personal']['my_projects']['total'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('my') }} {{ org_trans('projects') }}</div>
        </div>

        <!-- Messages non lus -->
        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                <svg style="width: 28px; height: 28px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['personal']['unread_messages'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('unread_messages') }}</div>
        </div>

        <!-- Canaux -->
        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1);">
                <svg style="width: 28px; height: 28px; color: #a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['personal']['my_channels']['total'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('my_channels') }}</div>
        </div>
    </div>

    @if(auth()->user()->canManageUsers())
    <!-- Management Stats (Admin & Owner) -->
    <h2 class="section-title" style="margin-top: 32px;">{{ org_trans('overview') }}</h2>
    <div class="stats-grid">
        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(223, 85, 38, 0.1);">
                <svg style="width: 28px; height: 28px; color: #df5526;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['organization']['total_users'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('total_users') }}</div>
        </div>

        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(251, 187, 42, 0.1);">
                <svg style="width: 28px; height: 28px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['organization']['total_departments'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('departments') }}</div>
        </div>

        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1);">
                <svg style="width: 28px; height: 28px; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['organization']['total_projects'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('active_projects') }}</div>
        </div>

        <div class="glass-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                <svg style="width: 28px; height: 28px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['organization']['total_tasks'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('total_tasks') }}</div>
        </div>
    </div>
    @endif

    @if(auth()->user()->isSuperAdmin())
    <!-- Platform Stats (Super Admin Only) -->
    <h2 class="section-title" style="margin-top: 32px;">{{ org_trans('platform_stats') }}</h2>
    <div class="stats-grid">
        <div class="glass-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));">
                <svg style="width: 28px; height: 28px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['platform']['total_organizations'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('total_organizations') }}</div>
            <div class="stat-trend trend-up">
                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                +8%
            </div>
        </div>

        <div class="glass-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));">
                <svg style="width: 28px; height: 28px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['platform']['total_users'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('total_platform_users') }}</div>
        </div>

        <div class="glass-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));">
                <svg style="width: 28px; height: 28px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['platform']['total_messages'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('sent_messages') }}</div>
        </div>

        <div class="glass-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));">
                <svg style="width: 28px; height: 28px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="stat-value">{{ $stats['platform']['active_projects'] ?? 0 }}</div>
            <div class="stat-label">{{ org_trans('active_projects') }}</div>
        </div>
    </div>
    @endif

    <!-- Analytics & Charts Section -->
    <div class="charts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-top: 32px;">
        <!-- Task Distribution Chart -->
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 class="section-title" style="margin-bottom: 0;">
                    <svg style="width: 20px; height: 20px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    Répartition des Tâches
                </h3>
                <span style="font-size: 12px; color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 20px;">Temps réel</span>
            </div>
            <div style="height: 300px; position: relative; display: flex; align-items: center; justify-content: center;">
                <canvas id="tasksChart"></canvas>
            </div>
        </div>

        <!-- Project Status Chart -->
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 class="section-title" style="margin-bottom: 0;">
                    <svg style="width: 20px; height: 20px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Performance Projets
                </h3>
                <span style="font-size: 12px; color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 20px;">Vue globale</span>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="projectsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Common Chart Options
            Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
            Chart.defaults.font.family = "'Inter', sans-serif";

            // Tasks Chart (Doughnut)
            const tasksCtx = document.getElementById('tasksChart').getContext('2d');

            @php
                $tasksSource = isset($stats['organization']['tasks_stats']) ? $stats['organization']['tasks_stats'] : ($stats['personal']['my_tasks'] ?? []);
            @endphp

            new Chart(tasksCtx, {
                type: 'doughnut',
                data: {
                    labels: ['À faire', 'En cours', 'Terminé', 'En retard'],
                    datasets: [{
                        data: [
                            {{ $tasksSource['todo'] ?? 0 }},
                            {{ $tasksSource['in_progress'] ?? 0 }},
                            {{ $tasksSource['completed'] ?? 0 }},
                            {{ $tasksSource['overdue'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(251, 187, 42, 0.8)',  // Warning/Todo
                            'rgba(59, 130, 246, 0.8)',  // Blue/In Progress
                            'rgba(16, 185, 129, 0.8)',  // Green/Completed
                            'rgba(239, 68, 68, 0.8)'    // Red/Overdue
                        ],
                        borderColor: 'rgba(0, 0, 0, 0)',
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });

            // Projects Chart (Bar)
            const projectsCtx = document.getElementById('projectsChart').getContext('2d');

            @php
                $projectsSource = isset($stats['organization']['projects_stats']) ? $stats['organization']['projects_stats'] : ($stats['personal']['my_projects'] ?? []);
            @endphp

            // Create gradient
            const gradientActive = projectsCtx.createLinearGradient(0, 0, 0, 400);
            gradientActive.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
            gradientActive.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

            const gradientCompleted = projectsCtx.createLinearGradient(0, 0, 0, 400);
            gradientCompleted.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
            gradientCompleted.addColorStop(1, 'rgba(16, 185, 129, 0.2)');

            new Chart(projectsCtx, {
                type: 'bar',
                data: {
                    labels: ['Actifs', 'Terminés', 'En pause'],
                    datasets: [{
                        label: 'Projets',
                        data: [
                            {{ $projectsSource['active'] ?? 0 }},
                            {{ $projectsSource['completed'] ?? 0 }},
                            {{ $projectsSource['on_hold'] ?? 0 }}
                        ],
                        backgroundColor: [
                            gradientActive,
                            gradientCompleted,
                            'rgba(107, 114, 128, 0.5)'
                        ],
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>

</x-app-layout>
