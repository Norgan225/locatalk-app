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

        .profile-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        /* --- Hero Section --- */
        .profile-hero {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--card-radius);
            padding: 40px;
            display: flex;
            align-items: center;
            gap: 32px;
            position: relative;
            overflow: hidden;
            margin-bottom: 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
        }

        .avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.05);
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 800;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            background: #1e293b;
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .avatar-edit-btn:hover {
            background: #df5526;
            transform: scale(1.1);
        }

        .hero-info {
            flex: 1;
        }

        .hero-name {
            font-size: 36px;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-meta {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }

        .role-badge {
            background: rgba(251, 187, 42, 0.15);
            color: #fbbb2a;
            border-color: rgba(251, 187, 42, 0.3);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
        }

        /* --- Stats Grid --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.15);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* --- Tabs Navigation --- */
        .tabs-container {
            margin-bottom: 32px;
        }

        .tabs-nav {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 16px;
            margin-bottom: 32px;
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 10px 20px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .tab-btn.active {
            color: #fbbb2a;
            background: rgba(251, 187, 42, 0.1);
        }

        /* --- Content Sections --- */
        .content-section {
            display: none;
            animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content-section.active {
            display: block;
        }

        .section-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--card-radius);
            padding: 32px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* --- Forms --- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background: rgba(0,0,0,0.2);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 14px 16px;
            color: white;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #fbbb2a;
            background: rgba(0,0,0,0.3);
            box-shadow: 0 0 0 4px rgba(251, 187, 42, 0.1);
        }

        .form-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-save {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(223, 85, 38, 0.3);
        }

        /* --- Activity List --- */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: rgba(255,255,255,0.02);
            border-radius: 16px;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255,255,255,0.04);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .activity-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbb2a;
            font-size: 20px;
        }

        .activity-content h4 {
            color: white;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .activity-time {
            color: var(--text-muted);
            font-size: 13px;
        }

        /* --- Alerts --- */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .profile-hero {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }
            .hero-meta {
                justify-content: center;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="profile-page">
        <!-- Notifications -->
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Hero Section -->
        <div class="profile-hero">
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                @csrf
                <div class="avatar-container">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="profile-avatar">
                    @else
                        <div class="profile-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <label for="avatar-input" class="avatar-edit-btn" title="Changer la photo">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </label>
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display: none;" onchange="document.getElementById('avatar-form').submit()">
                </div>
            </form>

            <div class="hero-info">
                <h1 class="hero-name">{{ $user->name }}</h1>
                <div class="hero-meta">
                    <span class="meta-badge role-badge">
                        @if($user->isSuperAdmin()) 👑 Super Admin @else {{ ucfirst($user->role) }} @endif
                    </span>
                    <span class="meta-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $user->email }}
                    </span>
                    @if($user->organization)
                    <span class="meta-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $user->organization->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['projects'] }}</div>
                <div class="stat-label">Projets Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['tasks'] }}</div>
                <div class="stat-label">Tâches Assignées</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['messages'] }}</div>
                <div class="stat-label">Messages Envoyés</div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="switchTab('overview')">Vue d'ensemble</button>
            <button class="tab-btn" onclick="switchTab('edit')">Modifier le profil</button>
            <button class="tab-btn" onclick="switchTab('security')">Sécurité</button>
        </div>

        <!-- Tab: Overview (Activity) -->
        <div id="tab-overview" class="content-section active">
            <div class="section-card">
                <h3 class="section-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activité Récente
                </h3>
                <div class="activity-list">
                    @forelse($recentActivity as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="activity-content">
                            <h4>{{ $activity->action }}</h4>
                            <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <div class="activity-content">
                            <h4>Bienvenue sur votre profil !</h4>
                            <span class="activity-time">Commencez à travailler pour voir votre activité ici.</span>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tab: Edit Profile -->
        <div id="tab-edit" class="content-section">
            <div class="section-card">
                <h3 class="section-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Informations Personnelles
                </h3>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('name') }}</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('email') }}</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('phone') }}</label>
                            <input type="tel" name="phone" class="form-input" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+225...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Département</label>
                            <input type="text" class="form-input" value="{{ $user->department ? $user->department->name : 'Aucun' }}" disabled>
                        </div>
                    </div>
                    <div style="text-align: right; margin-top: 16px;">
                        <button type="submit" class="btn-save">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab: Security -->
        <div id="tab-security" class="content-section">
            <div class="section-card">
                <h3 class="section-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Mot de passe
                </h3>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">{{ org_trans('current_password') }}</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('new_password') }}</label>
                            <input type="password" name="password" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('confirm_password') }}</label>
                            <input type="password" name="password_confirmation" class="form-input" required>
                        </div>
                    </div>
                    <div style="text-align: right; margin-top: 16px;">
                        <button type="submit" class="btn-save">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(el => el.classList.remove('active'));
            // Deactivate all buttons
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            // Show target
            document.getElementById('tab-' + tabId).classList.add('active');
            // Activate button
            document.querySelector(`[onclick="switchTab('${tabId}')"]`).classList.add('active');
        }
    </script>
</x-app-layout>

