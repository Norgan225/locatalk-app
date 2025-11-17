<x-app-layout>
    <style>
        .subscriptions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.25);
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
            color: rgba(255, 255, 255, 0.75);
            font-size: 14px;
            font-weight: 500;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .plan-card {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .plan-card:hover {
            transform: translateY(-4px);
            border-color: rgba(251, 187, 42, 0.3);
            background: rgba(30, 41, 59, 0.35);
        }

        .plan-card.popular::before {
            content: '⭐ Populaire';
            position: absolute;
            top: 16px;
            right: -30px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 4px 40px;
            font-size: 11px;
            font-weight: 700;
            transform: rotate(45deg);
            box-shadow: 0 4px 12px rgba(223, 85, 38, 0.4);
        }

        .plan-name {
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
        }

        .plan-price {
            font-size: 32px;
            font-weight: 900;
            color: white;
            margin-bottom: 4px;
        }

        .plan-price-currency {
            font-size: 18px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.75);
        }

        .plan-period {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .plan-features li {
            padding: 8px 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 400;
        }

        .plan-features li::before {
            content: '✓';
            color: #34d399;
            font-weight: 700;
            font-size: 16px;
        }

        .plan-count {
            background: rgba(255, 255, 255, 0.08);
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            margin-top: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .plan-count-number {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }

        .plan-count-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
        }

        .subscriptions-table {
            background: rgba(30, 41, 59, 0.25);
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
            background: rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.3s ease;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 16px 20px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .subscriptions-header h1 {
                font-size: 20px !important;
            }

            .stats-grid {
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

            .plans-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .subscriptions-table {
                overflow-x: auto;
            }

            table {
                min-width: 700px;
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
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .plans-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <!-- Header -->
    <div class="subscriptions-header">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">Plans & Abonnements</h1>
    </div>

    @if(session('success'))
        <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="stat-value">{{ $subscriptions['total'] }}</div>
            <div class="stat-label">Total Abonnements</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(52, 211, 153, 0.2); color: #34d399;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $subscriptions['active'] }}</div>
            <div class="stat-label">{{ org_trans('active') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(251, 187, 42, 0.2); color: #fbbb2a;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $subscriptions['trial'] }}</div>
            <div class="stat-label">En essai</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(248, 113, 113, 0.2); color: #f87171;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value">{{ $subscriptions['expired'] }}</div>
            <div class="stat-label">Expirés</div>
        </div>
    </div>

    <!-- Plans -->
    <h2 class="section-title">Plans disponibles</h2>
    <div class="plans-grid">
        @foreach($plans as $plan)
        <div class="plan-card {{ isset($plan['popular']) ? 'popular' : '' }}">
            <div class="plan-name">{{ $plan['name'] }}</div>
            <div>
                @if($plan['price'] === null)
                    <div class="plan-price">Sur mesure</div>
                    <div class="plan-period">Contactez-nous</div>
                @elseif($plan['price'] === 0)
                    <div class="plan-price">Gratuit</div>
                    <div class="plan-period">Pour toujours</div>
                @else
                    <div class="plan-price">{{ number_format($plan['price'], 0, ',', ' ') }} <span class="plan-price-currency">FCFA</span></div>
                    <div class="plan-period">par mois</div>
                @endif
            </div>

            <ul class="plan-features">
                @foreach($plan['features'] as $feature)
                    <li>{{ $feature }}</li>
                @endforeach
            </ul>

            <div class="plan-count">
                <div class="plan-count-number">{{ $subscriptions['by_plan'][$plan['slug']] }}</div>
                <div class="plan-count-label">organisation(s)</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Recent Subscriptions -->
    <h2 class="section-title">Abonnements récents</h2>
    @if($recentSubscriptions->count() > 0)
    <div class="subscriptions-table">
        <table>
            <thead>
                <tr>
                    <th>Organisation</th>
                    <th>Plan</th>
                    <th>Statut</th>
                    <th>Utilisateurs</th>
                    <th class="hide-mobile">Date début</th>
                    <th class="hide-mobile">Expire le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSubscriptions as $org)
                <tr style="cursor: pointer;" onclick="window.location.href='{{ route('web.organizations.show', $org->id) }}'">
                    <td>
                        <div style="font-weight: 600; color: white;">{{ $org->name }}</div>
                        @if($org->slug)
                            <div style="font-size: 12px; color: rgba(255, 255, 255, 0.5);">{{ $org->slug }}</div>
                        @endif
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
                        <span style="font-weight: 600; color: white;">{{ $org->users->count() }}</span>
                        <span style="color: rgba(255, 255, 255, 0.5); font-size: 12px;">/ {{ $org->max_users ?? '∞' }}</span>
                    </td>
                    <td class="hide-mobile">
                        <span style="color: rgba(255, 255, 255, 0.6); font-size: 14px;">
                            {{ $org->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="hide-mobile">
                        @if($org->subscription_expires_at)
                            <span style="color: rgba(255, 255, 255, 0.6); font-size: 14px;">
                                {{ $org->subscription_expires_at->format('d/m/Y') }}
                            </span>
                        @else
                            <span style="color: rgba(255, 255, 255, 0.4); font-size: 14px;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 60px 20px; color: rgba(255, 255, 255, 0.5);">
        <svg style="width: 64px; height: 64px; margin: 0 auto 16px; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 style="color: white; margin-bottom: 8px;">Aucun abonnement</h3>
        <p>Aucun abonnement n'a été créé pour le moment.</p>
    </div>
    @endif

</x-app-layout>
