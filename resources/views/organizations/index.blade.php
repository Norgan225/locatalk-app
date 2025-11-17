<x-app-layout>
    <style>
        .organizations-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .org-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 4px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .organizations-table {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.3s ease;
            cursor: pointer;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 16px 20px;
            color: rgba(255, 255, 255, 0.9);
        }

        .org-name {
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .org-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .plan-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-starter {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.5);
        }

        .plan-pro {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.5);
        }

        .plan-business {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.5);
        }

        .plan-enterprise {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            border: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: rgba(52, 211, 153, 0.2);
            color: #6ee7b7;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .status-inactive {
            background: rgba(248, 113, 113, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        .status-suspended {
            background: rgba(251, 187, 42, 0.2);
            color: #fcd34d;
            border: 1px solid rgba(251, 187, 42, 0.3);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .organizations-header h1 {
                font-size: 20px !important;
            }

            .org-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
            }

            .stat-value {
                font-size: 24px;
            }

            .stat-label {
                font-size: 12px;
            }

            .organizations-table {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            th, td {
                padding: 12px 10px;
                font-size: 12px;
            }

            .hide-mobile {
                display: none;
            }
        }

        /* 💻 Tablet Responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .org-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <!-- Header -->
    <div class="organizations-header">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">Gestion des Organisations</h1>
    </div>

    @if(session('success'))
        <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="org-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="stat-value">{{ $organizations->count() }}</div>
            <div class="stat-label">Total Organisations</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(52, 211, 153, 0.2); color: #34d399;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $organizations->where('subscription_status', 'active')->count() }}</div>
            <div class="stat-label">Actives</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $organizations->sum('users_count') }}</div>
            <div class="stat-label">Total Utilisateurs</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(251, 187, 42, 0.2); color: #fbbb2a;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $organizations->sum('projects_count') }}</div>
            <div class="stat-label">Total Projets</div>
        </div>
    </div>

    <!-- Organizations Table -->
    @if($organizations->count() > 0)
    <div class="organizations-table">
        <table>
            <thead>
                <tr>
                    <th>Organisation</th>
                    <th>Plan</th>
                    <th>Statut</th>
                    <th>Utilisateurs</th>
                    <th class="hide-mobile">Départements</th>
                    <th class="hide-mobile">Projets</th>
                    <th class="hide-mobile">Créé le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($organizations as $org)
                <tr onclick="window.location.href='{{ route('web.organizations.show', $org->id) }}'">
                    <td>
                        <div class="org-name">
                            <div class="org-logo">
                                {{ strtoupper(substr($org->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: white;">{{ $org->name }}</div>
                                @if($org->slug)
                                    <div style="font-size: 12px; color: rgba(255, 255, 255, 0.5);">{{ $org->slug }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="plan-badge plan-{{ $org->plan ?? 'starter' }}">
                            {{ ucfirst($org->plan ?? 'Starter') }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $org->subscription_status ?? 'active' }}">
                            <span class="status-dot"></span>
                            {{ ucfirst($org->subscription_status ?? 'Active') }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: white;">{{ $org->users_count }}</span>
                        <span style="color: rgba(255, 255, 255, 0.5); font-size: 12px;">/ {{ $org->max_users ?? '∞' }}</span>
                    </td>
                    <td class="hide-mobile">
                        <span style="color: rgba(255, 255, 255, 0.8);">{{ $org->departments_count }}</span>
                    </td>
                    <td class="hide-mobile">
                        <span style="color: rgba(255, 255, 255, 0.8);">{{ $org->projects_count }}</span>
                    </td>
                    <td class="hide-mobile">
                        <span style="color: rgba(255, 255, 255, 0.6); font-size: 14px;">
                            {{ $org->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 60px 20px; color: rgba(255, 255, 255, 0.5);">
        <svg style="width: 64px; height: 64px; margin: 0 auto 16px; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <h3 style="color: white; margin-bottom: 8px;">Aucune organisation</h3>
        <p>Aucune organisation n'a été créée pour le moment.</p>
    </div>
    @endif

</x-app-layout>
