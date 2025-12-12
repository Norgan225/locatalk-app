<x-app-layout>
    <style>
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            margin-bottom: 20px;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .back-button:hover {
            color: white;
        }

        .org-header {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .org-logo-large {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 40px;
        }

        .org-info {
            flex: 1;
        }

        .org-title {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
        }

        .org-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            font-weight: 500;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .info-card {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .info-value {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .content-card {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .users-list {
            display: grid;
            gap: 12px;
        }

        .user-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .user-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .user-details {
            flex: 1;
        }

        .user-name-detail {
            font-weight: 600;
            color: white;
            font-size: 14px;
        }

        .user-email-detail {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 400;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
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
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .org-header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .org-logo-large {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }

            .org-title {
                font-size: 24px !important;
            }

            .org-meta {
                flex-direction: column;
                gap: 12px;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .info-value {
                font-size: 20px;
            }

            .content-card {
                padding: 16px;
            }

            .section-title {
                font-size: 18px;
            }
        }

        /* 💻 Tablet Responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <!-- Back Button -->
    <a href="{{ route('web.organizations') }}" class="back-button">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux organisations
    </a>

    <!-- Organization Header -->
    <div class="org-header">
        <div class="org-logo-large">
            {{ strtoupper(substr($organization->name, 0, 1)) }}
        </div>
        <div class="org-info">
            <h1 class="org-title">{{ $organization->name }}</h1>
            @if($organization->slug)
                <div style="color: rgba(255, 255, 255, 0.8); font-size: 16px; font-weight: 500;">{{ $organization->slug }}</div>
            @endif
            <div class="org-meta">
                <div class="meta-item">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Créé le {{ $organization->created_at->format('d/m/Y') }}
                </div>
                @if($organization->phone)
                <div class="meta-item">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $organization->phone }}
                </div>
                @endif
                @if($organization->address)
                <div class="meta-item">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $organization->address }}
                </div>
                @endif
            </div>
        </div>
        <span class="plan-badge plan-{{ $organization->plan ?? 'starter' }}">
            {{ ucfirst($organization->plan ?? 'Starter') }}
        </span>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Utilisateurs
            </div>
            <div class="info-value">{{ $organization->users_count }}</div>
            <div style="font-size: 12px; color: rgba(255, 255, 255, 0.7); margin-top: 4px; font-weight: 500;">
                Max: {{ $organization->max_users ?? '∞' }}
            </div>
        </div>

        <div class="info-card">
            <div class="info-label">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Départements
            </div>
            <div class="info-value">{{ $organization->departments_count }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Projets
            </div>
            <div class="info-value">{{ $organization->projects_count }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Statut
            </div>
            <span class="status-badge status-{{ $organization->subscription_status ?? 'active' }}" style="margin-top: 8px;">
                <span class="status-dot"></span>
                {{ ucfirst($organization->subscription_status ?? 'Active') }}
            </span>
        </div>
    </div>

    <!-- Users List -->
    <div class="content-card">
        <h3 class="section-title">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Utilisateurs ({{ $organization->users_count }})
        </h3>
        @if($organization->users->count() > 0)
            <div class="users-list">
                @foreach($organization->users as $user)
                <div class="user-item">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name-detail">{{ $user->name }}</div>
                        <div class="user-email-detail">{{ $user->email }}</div>
                    </div>
                    <span class="role-badge role-{{ $user->role }}">
                        @if($user->isSuperAdmin())
                            SUPER ADMIN
                        @else
                            {{ ucfirst($user->role) }}
                        @endif
                    </span>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px 20px; color: rgba(255, 255, 255, 0.7);">
                <p style="font-weight: 500;">Aucun utilisateur dans cette organisation.</p>
            </div>
        @endif
    </div>

    <!-- Departments List -->
    <div class="content-card">
        <h3 class="section-title">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Départements ({{ $organization->departments_count }})
        </h3>
        @if($organization->departments->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
                @foreach($organization->departments as $dept)
                <div style="padding: 16px; background: rgba(255, 255, 255, 0.05); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div style="font-weight: 600; color: white; margin-bottom: 4px;">{{ $dept->name }}</div>
                    <div style="font-size: 12px; color: rgba(255, 255, 255, 0.7); font-weight: 500;">
                        {{ $dept->users_count ?? 0 }} utilisateur(s)
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px 20px; color: rgba(255, 255, 255, 0.7);">
                <p style="font-weight: 500;">Aucun département dans cette organisation.</p>
            </div>
        @endif
    </div>

</x-app-layout>
