<x-app-layout>
    <style>
        /* Glass Card */
        .glass-card {
            background: rgba(30, 41, 59, 0.25); /* bleu foncé glassmorphism */
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

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .glass-card {
                padding: 16px;
                border-radius: 12px;
            }
        }

        /* Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .page-header {
                margin-bottom: 24px;
            }

            .page-title {
                font-size: 24px;
            }

            .page-subtitle {
                font-size: 14px;
            }
        }

        /* Platform Stats Grid */
        .platform-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .platform-stats {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 24px;
            }

            .page-header {
                margin-bottom: 24px;
            }
        }

        /* Responsive: Tablet */
        @media (min-width: 769px) and (max-width: 1024px) {
            .platform-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-value {
            font-size: 40px;
            font-weight: 900;
            color: white;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .stat-value {
                font-size: 32px;
            }

            .stat-label {
                font-size: 13px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                margin-bottom: 12px;
            }

            .stat-icon svg {
                width: 24px !important;
                height: 24px !important;
            }
        }

        /* Organizations Table */
        .org-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .org-table thead th {
            color: rgba(255, 255, 255, 0.92);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 16px;
            text-align: left;
            background: rgba(255, 255, 255, 0.03);
        }

        .org-table tbody tr {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .org-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateX(5px);
        }

        .org-table tbody td {
            padding: 16px;
            color: rgba(255, 255, 255, 0.98);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }

        .org-table tbody td:first-child {
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px 0 0 12px;
        }

        .org-table tbody td:last-child {
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 0 12px 12px 0;
        }

        /* Table wrapper for scroll on mobile */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive: Mobile - Hide less important columns */
        @media (max-width: 768px) {
            .org-table {
                border-spacing: 0 6px;
            }

            .org-table thead th {
                font-size: 11px;
                padding: 10px 12px;
            }

            .org-table tbody td {
                padding: 12px;
                font-size: 13px;
            }

            /* Hide departments and tasks columns on mobile */
            .org-table .hide-mobile {
                display: none;
            }
        }

        /* Responsive: Tablet */
        @media (min-width: 769px) and (max-width: 1024px) {
            .org-table tbody td {
                padding: 14px;
                font-size: 13px;
            }
        }

        .org-name {
            font-weight: 700;
            font-size: 15px;
            color: #fff;
            letter-spacing: 0.3px;
        }

        .org-plan {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
        }

        /* Responsive: Mobile - Badges */
        @media (max-width: 768px) {
            .org-plan,
            .status-badge {
                padding: 5px 10px;
                font-size: 12px;
            }
        }

        .plan-free {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .plan-pro {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .plan-business {
            background: rgba(251, 187, 42, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .plan-enterprise {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Activity Log */
        .activity-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.3);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .activity-meta {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .activity-item {
                gap: 12px;
                padding: 12px;
                margin-bottom: 10px;
            }

            .activity-icon {
                width: 36px;
                height: 36px;
            }

            .activity-icon svg {
                width: 18px !important;
                height: 18px !important;
            }

            .activity-title {
                font-size: 13px;
            }

            .activity-meta {
                font-size: 11px;
            }
        }

        .section-title {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: 0.3px;
        }

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .section-title {
                font-size: 18px;
                margin-bottom: 16px;
                gap: 8px;
            }

            .section-title svg {
                width: 20px !important;
                height: 20px !important;
            }
        }
        /* Responsive: Container padding */
        @media (max-width: 768px) {
            .responsive-container {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }

            .responsive-inner {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }
    </style>

    <div class="py-12 responsive-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 responsive-inner">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title">🚀 Dashboard Super Admin</h1>
                <p class="page-subtitle">Vue complète de la plateforme LocaTalk</p>
            </div>

            <!-- Platform Stats -->
            <div class="platform-stats">
                <div class="glass-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));">
                        <svg style="width: 30px; height: 30px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="stat-value">{{ $stats['platform']['total_organizations'] ?? 0 }}</div>
                    <div class="stat-label">Organisations</div>
                </div>

                <div class="glass-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2));">
                        <svg style="width: 30px; height: 30px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="stat-value">{{ $stats['platform']['total_users'] ?? 0 }}</div>
                    <div class="stat-label">Utilisateurs Total</div>
                </div>

                <div class="glass-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, rgba(251, 187, 42, 0.2), rgba(245, 158, 11, 0.2));">
                        <svg style="width: 30px; height: 30px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="stat-value">{{ $stats['platform']['total_projects'] ?? 0 }}</div>
                    <div class="stat-label">Projets Total</div>
                </div>

                <div class="glass-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));">
                        <svg style="width: 30px; height: 30px; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="stat-value">{{ $stats['platform']['total_tasks'] ?? 0 }}</div>
                    <div class="stat-label">Tâches Total</div>
                </div>
            </div>

            <!-- Organizations Overview -->
            <div class="glass-card" style="margin-bottom: 32px;">
                <h2 class="section-title">
                    <svg style="width: 24px; height: 24px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Organisations
                </h2>

                @if(count($stats['organizations'] ?? []) > 0)
                <div class="table-wrapper">
                    <table class="org-table">
                        <thead>
                            <tr>
                                <th>Organisation</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Utilisateurs</th>
                                <th class="hide-mobile">Départements</th>
                                <th>Projets</th>
                                <th class="hide-mobile">Tâches</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['organizations'] as $org)
                            <tr>
                                <td>
                                    <div class="org-name">{{ $org['name'] }}</div>
                                </td>
                                <td>
                                    <span class="org-plan plan-{{ strtolower($org['plan']) }}">
                                        {{ ucfirst($org['plan']) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $org['is_active'] ? 'active' : 'inactive' }}">
                                        <span class="status-indicator"></span>
                                        {{ $org['is_active'] ? org_trans('active') : org_trans('inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #fbbb2a; font-weight: 700;">{{ $org['active_users'] }}</strong>
                                    <span style="color: rgba(255, 255, 255, 0.5);"> / {{ $org['users_count'] }}</span>
                                </td>
                                <td class="hide-mobile" style="color: rgba(255, 255, 255, 0.8);">{{ $org['departments_count'] }}</td>
                                <td style="color: rgba(255, 255, 255, 0.8);">{{ $org['projects_count'] }}</td>
                                <td class="hide-mobile" style="color: rgba(255, 255, 255, 0.8);">{{ $org['tasks_count'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align: center; padding: 60px 20px; color: rgba(255, 255, 255, 0.4);">
                    <svg style="width: 64px; height: 64px; margin: 0 auto 16px; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p style="font-size: 16px; margin-bottom: 8px;">Aucune organisation</p>
                    <p style="font-size: 14px;">Créez votre première organisation pour commencer</p>
                </div>
                @endif
            </div>

            <!-- Recent Activity -->
            <div class="glass-card">
                <h2 class="section-title">
                    <svg style="width: 24px; height: 24px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Activité récente (Plateforme)
                </h2>

                @if(count($stats['recent_activity'] ?? []) > 0)
                    @foreach($stats['recent_activity'] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg style="width: 20px; height: 20px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity['description'] }}</div>
                            <div class="activity-meta">
                                {{ $activity['user'] }} • {{ $activity['time_ago'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div style="text-align: center; padding: 40px 20px; color: rgba(255, 255, 255, 0.4);">
                    <p>Aucune activité récente</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
