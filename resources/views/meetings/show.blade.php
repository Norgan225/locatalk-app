<x-app-layout>
    <style>
        * { box-sizing: border-box; }

        .meeting-show-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem 1rem;
        }

        .meeting-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Back button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .back-btn:hover { color: #e2e8f0; }

        /* Main card wrapper */
        .meeting-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        /* Header */
        .meeting-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .meeting-title-section h1 {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0 0 0.75rem 0;
        }

        .badges {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .badge {
            padding: 0.375rem 0.875rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-scheduled { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .badge-ongoing { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .badge-completed { background: rgba(148, 163, 184, 0.2); color: #cbd5e1; }
        .badge-recorded { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

        .badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .meeting-actions {
            display: flex;
            gap: 0.5rem;
        }

        .icon-btn {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border: none;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .icon-btn.danger:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-content span {
            display: block;
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .info-content strong {
            display: block;
            color: white;
            font-weight: 600;
        }

        /* Content layout */
        .content-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }

        /* Description section */
        .description-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .section-heading {
            font-size: 1rem;
            font-weight: 600;
            color: white;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .description-text {
            color: #cbd5e1;
            line-height: 1.6;
        }

        /* Participants */
        .participants-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        .participant-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: background 0.2s;
        }

        .participant-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .participant-item:not(:last-child) {
            margin-bottom: 0.5rem;
        }

        .participant-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            overflow: hidden;
            flex-shrink: 0;
        }

        .participant-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .participant-details h4 {
            margin: 0;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .participant-details p {
            margin: 0;
            color: #94a3b8;
            font-size: 0.75rem;
        }

        /* Sidebar action card */
        .action-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            position: sticky;
            top: 2rem;
        }

        .action-card-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .action-card h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: white;
            margin: 0 0 0.5rem 0;
        }

        .action-card p {
            color: #94a3b8;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .action-btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            border: none;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .action-btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .action-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .action-btn-secondary {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            margin-top: 0.75rem;
        }

        .action-btn-secondary:hover {
            background: rgba(239, 68, 68, 0.25);
        }

        .action-btn-blue {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .action-btn-blue:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
        }

        .meeting-link-box {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.875rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 0.8125rem;
            color: #a5b4fc;
        }

        .copy-link-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .copy-link-btn:hover {
            color: white;
        }

        .countdown-timer {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin: 1rem 0;
        }

        @media (max-width: 900px) {
            .content-layout {
                grid-template-columns: 1fr;
            }

            .action-card {
                position: relative;
                top: 0;
            }
        }

        /* Modal for adding participants */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 1.5rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.08);
            border: none;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .user-select-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }

        .user-select-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .user-select-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .user-select-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .modal-actions {
            display: flex;
            gap: 0.75rem;
        }

        .modal-btn {
            flex: 1;
            padding: 0.75rem;
            border-radius: 0.75rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .modal-btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
        }

        .modal-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
        }
    </style>

    <div class="meeting-show-page">
        <div class="meeting-container">
            <!-- Back Button -->
            <a href="{{ route('web.meetings') }}" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour
            </a>

            <!-- Main Card -->
            <div class="meeting-card">
                <!-- Header -->
                <div class="meeting-header">
                    <div class="meeting-title-section">
                        <h1>{{ $meeting->title }}</h1>
                        <div class="badges">
                            <span class="badge badge-{{ $meeting->status }}" @if(auth()->id() === $meeting->created_by || auth()->user()->can('manage-users')) onclick="showStatusModal()" style="cursor: pointer;" title="Cliquer pour changer le statut" @endif>
                                <span class="dot"></span>
                                @if($meeting->status === 'scheduled') Planifiée
                                @elseif($meeting->status === 'ongoing') En cours
                                @elseif($meeting->status === 'completed') Terminée
                                @else Annulée
                                @endif
                            </span>
                            @if($meeting->is_recorded)
                                <span class="badge badge-recorded">
                                    <span class="dot"></span>
                                    Enregistrée
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="meeting-actions">
                        @if(auth()->id() === $meeting->created_by || auth()->user()->can('manage-users'))
                            <button onclick="showStatusModal()" class="icon-btn" title="Changer le statut">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </button>
                            <a href="{{ route('web.meetings.edit', $meeting->id) }}" class="icon-btn" title="Modifier">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <form action="{{ route('web.meetings.destroy', $meeting->id) }}" method="POST" onsubmit="return confirm('Supprimer cette réunion ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn danger" title="Supprimer">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <span>Date</span>
                            <strong>{{ $meeting->start_time->format('d M Y') }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <span>Horaire</span>
                            <strong>{{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}</strong>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <span>Organisateur</span>
                            <strong>{{ $meeting->creator->name }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Content Layout -->
                <div class="content-layout">
                    <!-- Left Side -->
                    <div>
                        <!-- Description -->
                        <div class="description-section">
                            <h2 class="section-heading">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"/>
                                    <line x1="8" y1="12" x2="21" y2="12"/>
                                    <line x1="8" y1="18" x2="21" y2="18"/>
                                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                                </svg>
                                Description
                            </h2>
                            <p class="description-text">
                                @if($meeting->description)
                                    {{ $meeting->description }}
                                @else
                                    <em style="opacity: 0.6;">Aucune description fournie.</em>
                                @endif
                            </p>
                        </div>

                        <!-- Participants -->
                        <div class="participants-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h2 class="section-heading" style="margin: 0;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Participants ({{ $meeting->participants->unique('id')->count() }})
                                </h2>
                                @if(in_array($meeting->status, ['scheduled', 'ongoing']) && (auth()->id() === $meeting->created_by || auth()->user()->can('manage-users')))
                                    <button onclick="showAddParticipantModal()" class="icon-btn" title="Ajouter un participant" style="background: rgba(16, 185, 129, 0.15); color: #4ade80;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="8.5" cy="7" r="4"/>
                                            <line x1="20" y1="8" x2="20" y2="14"/>
                                            <line x1="23" y1="11" x2="17" y2="11"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            @foreach($meeting->participants->unique('id') as $participant)
                                <div class="participant-item">
                                    <div class="participant-avatar">
                                        @if($participant->avatar)
                                            <img src="{{ $participant->avatar }}" alt="{{ $participant->name }}">
                                        @else
                                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="participant-details" style="flex: 1;">
                                        <h4>{{ $participant->name }}</h4>
                                        <p>{{ $participant->email }}</p>
                                    </div>
                                    @if(in_array($meeting->status, ['scheduled', 'ongoing']) && (auth()->id() === $meeting->created_by || auth()->user()->can('manage-users')))
                                        <form action="{{ route('web.meetings.participants.remove', ['meeting' => $meeting->id, 'user' => $participant->id]) }}" method="POST" onsubmit="return confirm('Retirer {{ $participant->name }} de cette réunion ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" style="width: 2rem; height: 2rem; background: rgba(239, 68, 68, 0.1); color: #fca5a5;" title="Retirer">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div>
                        @if($meeting->status === 'ongoing')
                            <div class="action-card">
                                <div class="action-card-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M23 7 16 12 23 17 23 7"/>
                                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                    </svg>
                                </div>
                                <h3>Réunion en cours</h3>
                                <p>Cliquez pour rejoindre la conversation en direct</p>

                                <a href="{{ route('web.meetings.room', $meeting->id) }}" class="action-btn action-btn-primary">
                                    Rejoindre
                                </a>

                                @if(auth()->id() === $meeting->created_by)
                                    <form action="{{ route('web.meetings.end', $meeting->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-secondary">
                                            Terminer
                                        </button>
                                    </form>
                                @endif
                            </div>

                        @elseif($meeting->status === 'scheduled')
                            <div class="action-card">
                                <div class="action-card-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <h3>Commence dans</h3>
                                <div class="countdown-timer">{{ $meeting->start_time->diffForHumans(null, true) }}</div>

                                @if($meeting->meeting_link)
                                    <div class="meeting-link-box">{{ $meeting->meeting_link }}</div>
                                    <button onclick="navigator.clipboard.writeText('{{ $meeting->meeting_link }}')" class="copy-link-btn">
                                        Copier le lien
                                    </button>
                                @endif

                                @if(auth()->id() === $meeting->created_by)
                                    <form action="{{ route('web.meetings.start', $meeting->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-blue">
                                            Démarrer maintenant
                                        </button>
                                    </form>
                                @endif
                            </div>

                        @else
                            <div class="action-card" style="opacity: 0.6;">
                                <div class="action-card-icon" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                </div>
                                <h3>Réunion terminée</h3>
                                <p>Cette réunion est maintenant close</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding participants -->
    <div id="addParticipantModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ajouter des participants</h3>
                <button onclick="closeAddParticipantModal()" class="modal-close">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('web.meetings.participants.add', $meeting->id) }}" method="POST">
                @csrf
                <div class="user-select-list">
                    @php
                        $currentParticipantIds = $meeting->participants->pluck('id')->toArray();
                        $availableUsers = auth()->user()->isSuperAdmin()
                            ? \App\Models\User::whereNotIn('id', array_merge($currentParticipantIds, [$meeting->created_by]))->orderBy('name')->get()
                            : \App\Models\User::where('organization_id', $meeting->organization_id)
                                ->whereNotIn('id', array_merge($currentParticipantIds, [$meeting->created_by]))
                                ->orderBy('name')->get();
                    @endphp

                    @forelse($availableUsers as $user)
                        <label class="user-select-item">
                            <input type="checkbox" name="participants[]" value="{{ $user->id }}">
                            <div class="participant-avatar" style="width: 2rem; height: 2rem; font-size: 0.75rem;">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="color: white; font-size: 0.875rem; font-weight: 500;">{{ $user->name }}</div>
                                <div style="color: #94a3b8; font-size: 0.75rem;">{{ $user->email }}</div>
                            </div>
                        </label>
                    @empty
                        <p style="color: #94a3b8; text-align: center; padding: 2rem;">Tous les utilisateurs sont déjà participants</p>
                    @endforelse
                </div>

                @if($availableUsers->count() > 0)
                    <div class="modal-actions">
                        <button type="button" onclick="closeAddParticipantModal()" class="modal-btn modal-btn-secondary">Annuler</button>
                        <button type="submit" class="modal-btn modal-btn-primary">Ajouter</button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Modal for changing status -->
    <div id="statusModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Changer le statut</h3>
                <button onclick="closeStatusModal()" class="modal-close">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('web.meetings.update-status', $meeting->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #94a3b8; font-size: 0.875rem; margin-bottom: 0.5rem;">Statut actuel: <strong style="color: white;">{{ $meeting->status === 'scheduled' ? 'Planifiée' : ($meeting->status === 'ongoing' ? 'En cours' : ($meeting->status === 'completed' ? 'Terminée' : 'Annulée')) }}</strong></label>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
                        <label class="status-option" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; border-radius: 0.75rem; background: rgba(255, 255, 255, 0.03); cursor: pointer; transition: all 0.2s;">
                            <input type="radio" name="status" value="scheduled" {{ $meeting->status === 'scheduled' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <div style="flex: 1;">
                                <div style="color: white; font-weight: 500; margin-bottom: 0.25rem;">Planifiée</div>
                                <div style="color: #94a3b8; font-size: 0.75rem;">La réunion est programmée</div>
                            </div>
                        </label>

                        <label class="status-option" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; border-radius: 0.75rem; background: rgba(255, 255, 255, 0.03); cursor: pointer; transition: all 0.2s;">
                            <input type="radio" name="status" value="ongoing" {{ $meeting->status === 'ongoing' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <div style="flex: 1;">
                                <div style="color: white; font-weight: 500; margin-bottom: 0.25rem;">En cours</div>
                                <div style="color: #94a3b8; font-size: 0.75rem;">La réunion est active</div>
                            </div>
                        </label>

                        <label class="status-option" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; border-radius: 0.75rem; background: rgba(255, 255, 255, 0.03); cursor: pointer; transition: all 0.2s;">
                            <input type="radio" name="status" value="completed" {{ $meeting->status === 'completed' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <div style="flex: 1;">
                                <div style="color: white; font-weight: 500; margin-bottom: 0.25rem;">Terminée</div>
                                <div style="color: #94a3b8; font-size: 0.75rem;">La réunion est terminée</div>
                            </div>
                        </label>

                        <label class="status-option" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; border-radius: 0.75rem; background: rgba(255, 255, 255, 0.03); cursor: pointer; transition: all 0.2s;">
                            <input type="radio" name="status" value="cancelled" {{ $meeting->status === 'cancelled' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <div style="flex: 1;">
                                <div style="color: white; font-weight: 500; margin-bottom: 0.25rem;">Annulée</div>
                                <div style="color: #94a3b8; font-size: 0.75rem;">La réunion est annulée</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeStatusModal()" class="modal-btn modal-btn-secondary">Annuler</button>
                    <button type="submit" class="modal-btn modal-btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddParticipantModal() {
            document.getElementById('addParticipantModal').classList.add('active');
        }

        function closeAddParticipantModal() {
            document.getElementById('addParticipantModal').classList.remove('active');
        }

        function showStatusModal() {
            document.getElementById('statusModal').classList.add('active');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('active');
        }

        // Close modals on overlay click
        document.getElementById('addParticipantModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddParticipantModal();
            }
        });

        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });

        // Hover effect for status options
        document.querySelectorAll('.status-option').forEach(option => {
            option.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 0.08)';
            });
            option.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.03)';
            });
        });
    </script>
</x-app-layout>
