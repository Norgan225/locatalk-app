<x-app-layout>
    <style>
        /* --- Animations --- */
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- Buttons --- */
        .btn-create {
            background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
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
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.4);
        }

        /* --- Layout & Structure --- */
        .meetings-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
            animation: slideIn 0.4s ease-out;
        }

        .meetings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-title h1 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-top: 4px;
        }

        /* --- Stats Cards --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .stat-card:hover::before {
            transform: translateX(100%);
        }

        .stat-content {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .stat-info h3 {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 8px 0;
        }

        .stat-value {
            color: white;
            font-size: 36px;
            font-weight: 700;
            line-height: 1;
            margin: 0;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* --- Filters --- */
        .filters-section {
            background: rgba(20, 20, 30, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 32px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-wrapper {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
        }

        .custom-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 16px 10px 42px;
            color: white;
            font-size: 14px;
            transition: all 0.2s;
        }

        .custom-input:focus {
            background: rgba(0, 0, 0, 0.4);
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .custom-select {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 36px 10px 16px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(255,255,255,0.5)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        /* --- Meetings List (Cards) --- */
        .meetings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .meeting-card {
            background: rgba(30, 30, 40, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 24px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .meeting-card:hover {
            background: rgba(40, 40, 55, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.6);
        }

        .meeting-card.ongoing {
            border-color: rgba(16, 185, 129, 0.3);
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.05) 0%, rgba(30, 30, 40, 0.6) 100%);
        }

        .meeting-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .date-badge {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 10px 14px;
            text-align: center;
            min-width: 65px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        .meeting-card:hover .date-badge {
            background: rgba(251, 187, 42, 0.1);
            border-color: rgba(251, 187, 42, 0.3);
        }

        .date-day {
            font-size: 20px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
        }

        .date-month {
            font-size: 11px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-scheduled {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .status-ongoing {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            animation: pulse-green 2s infinite;
        }
        .status-completed {
            background: rgba(107, 114, 128, 0.15);
            color: #9ca3af;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }
        .status-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .meeting-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .meeting-time {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            margin-bottom: 16px;
        }

        .participants-stack {
        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #0f172a;(255, 255, 255, 0.05);
        }

        .avatar-stack {
            display: flex;
            margin-right: 12px;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #0f172a;
            background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
            margin-left: -10px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            font-weight: 700;
            overflow: hidden;
        }

        .avatar-circle:first-child { margin-left: 0; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

        .action-btn {
            margin-left: auto;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .btn-join {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-join:hover {
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        /* --- Empty State --- */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-text {
            color: rgba(255, 255, 255, 0.5);
            font-size: 16px;
        }
    </style>

    <div class="meetings-container">
        <!-- Header -->
        <div class="meetings-header">
            <div class="header-title">
                <h1>Réunions & Visioconférences</h1>
                <div class="header-subtitle">Gérez vos plannings et rejoignez vos équipes</div>
            </div>
            <div class="header-actions">
                @can('manage-users')
                <a href="{{ route('web.meetings.create') }}" class="btn-create">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nouvelle réunion
                </a>
                @endcan
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-info">
                        <h3>Total Réunions</h3>
                        <p class="stat-value">{{ $stats['total'] }}</p>
                    </div>
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #818cf8;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-info">
                        <h3>À venir</h3>
                        <p class="stat-value">{{ $stats['upcoming'] }}</p>
                    </div>
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-info">
                        <h3>En cours</h3>
                        <p class="stat-value">{{ $stats['ongoing'] }}</p>
                    </div>
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-info">
                        <h3>Terminées</h3>
                        <p class="stat-value">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="stat-icon" style="background: rgba(107, 114, 128, 0.1); color: #9ca3af;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('web.meetings') }}" class="filters-section">
            <div class="search-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une réunion..." class="custom-input">
            </div>

            <select name="status" class="custom-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planifiée</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>En cours</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>

            <input type="date" name="date" value="{{ request('date') }}" class="custom-input" style="width: auto;" onchange="this.form.submit()">

            @if(auth()->user()->isSuperAdmin())
            <select name="organization_id" class="custom-select" onchange="this.form.submit()">
                <option value="">Toutes les organisations</option>
                @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
            @endif
        </form>

        <!-- Meetings Grid -->
        @if($meetings->count() > 0)
            <div class="meetings-grid">
                @foreach($meetings as $meeting)
                    <div class="meeting-card {{ $meeting->status === 'ongoing' ? 'ongoing' : '' }}">
                        <div class="meeting-header">
                            <div class="date-badge">
                                <div class="date-day">{{ $meeting->start_time->format('d') }}</div>
                                <div class="date-month">{{ $meeting->start_time->translatedFormat('M') }}</div>
                            </div>
                            <span class="status-badge status-{{ $meeting->status }}">
                                @if($meeting->status === 'scheduled') Planifiée
                                @elseif($meeting->status === 'ongoing') En cours
                                @elseif($meeting->status === 'completed') Terminée
                                @elseif($meeting->status === 'cancelled') Annulée
                                @endif
                            </span>
                        </div>

                        <h3 class="meeting-title" title="{{ $meeting->title }}">{{ $meeting->title }}</h3>

                        <div class="meeting-time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                            <span style="opacity: 0.5">•</span>
                            {{ $meeting->duration }} min
                        </div>

                        @if($meeting->description)
                            <p style="color: rgba(255,255,255,0.5); font-size: 13px; margin: 0 0 16px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $meeting->description }}
                            </p>
                        @endif

                        <div class="participants-stack" style="gap: 16px;">
                            <div class="avatar-stack">
                                @foreach($meeting->participants->unique('id')->take(4) as $participant)
                                    <div class="avatar-circle" title="{{ $participant->name }}">
                                        @if($participant->avatar)
                                            <img src="{{ $participant->avatar }}" alt="{{ $participant->name }}">
                                        @else
                                            {{ substr($participant->name, 0, 1) }}
                                        @endif
                                    </div>
                                @endforeach
                                @if($meeting->participants->unique('id')->count() > 4)
                                    <div class="avatar-circle" style="background: #4f46e5; font-size: 10px;">
                                        +{{ $meeting->participants->unique('id')->count() - 4 }}
                                    </div>
                                @endif
                            </div>

                            @if($meeting->status === 'ongoing')
                                <a href="{{ route('web.meetings.show', $meeting->id) }}" class="action-btn btn-join">
                                    Rejoindre
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                </a>
                            @else
                                <a href="{{ route('web.meetings.show', $meeting->id) }}" class="action-btn btn-outline">
                                    Détails
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 32px;">
                {{ $meetings->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>Aucune réunion trouvée</h3>
                <p class="empty-text">Vous n'avez aucune réunion prévue pour le moment.</p>
                @can('manage-users')
                <a href="{{ route('web.meetings.create') }}" class="action-btn btn-primary" style="margin-top: 16px; display: inline-flex;">
                    Planifier une réunion
                </a>
                @endcan
            </div>
        @endif
    </div>
</x-app-layout>
