<x-app-layout>
    <style>
        * { box-sizing: border-box; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .create-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 2rem 1rem;
        }

        .create-container {
            max-width: 900px;
            margin: 0 auto;
            animation: slideIn 0.5s ease-out;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 2rem;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .back-btn:hover { color: #e2e8f0; }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .title-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .page-subtitle {
            color: #94a3b8;
            font-size: 1rem;
            margin-left: 4rem;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .section-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            background: rgba(223, 85, 38, 0.15);
            color: #fb923c;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: #e2e8f0;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .required {
            color: #fb923c;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            color: white;
            font-size: 0.9375rem;
            transition: all 0.2s;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #fb923c;
            outline: none;
            box-shadow: 0 0 0 3px rgba(223, 85, 38, 0.1);
        }

        .form-input::placeholder, .form-textarea::placeholder {
            color: #64748b;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .participants-selector {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 1rem;
            max-height: 300px;
            overflow-y: auto;
        }

        .participant-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 0.5rem;
        }

        .participant-option:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .participant-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            cursor: pointer;
            transition: all 0.2s;
        }

        .checkbox-wrapper:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 0.375rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: transparent;
            appearance: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
            margin-top: 0.125rem;
            flex-shrink: 0;
        }

        .custom-checkbox:checked {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-color: #df5526;
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .info-box {
            background: rgba(223, 85, 38, 0.08);
            border: 1px solid rgba(223, 85, 38, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
            display: flex;
            gap: 0.875rem;
            margin-top: 2rem;
        }

        .info-icon {
            color: #fb923c;
            flex-shrink: 0;
        }

        .info-text {
            color: #cbd5e1;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .btn-submit {
            flex: 2;
            background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(223, 85, 38, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-cancel {
            flex: 1;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .error-msg {
            color: #fca5a5;
            font-size: 0.8125rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .help-text {
            color: #64748b;
            font-size: 0.8125rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .form-card { padding: 1.5rem; }
            .page-title { font-size: 2rem; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { flex: 1; }
        }
    </style>

    <div class="create-page">
        <div class="create-container">
            <a href="{{ route('web.meetings') }}" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour aux réunions
            </a>

            <div class="page-header">
                <h1 class="page-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </span>
                    Nouvelle réunion
                </h1>
                <p class="page-subtitle">Planifiez un nouvel espace de collaboration pour votre équipe</p>
            </div>

            <form action="{{ route('web.meetings.store') }}" method="POST" class="form-card">
                @csrf

                <!-- Informations générales -->
                <h3 class="section-title">
                    <span class="section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    Informations générales
                </h3>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        Titre de la réunion <span class="required">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           class="form-input" placeholder="Ex: Point hebdomadaire, Revue de sprint...">
                    @error('title')
                        <div class="error-msg">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-textarea"
                              placeholder="Ordre du jour, objectifs de la réunion, points à aborder...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error-msg">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Date & Time -->
                <h3 class="section-title" style="margin-top: 2rem;">
                    <span class="section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </span>
                    Planification
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time" class="form-label">
                            Date et heure de début <span class="required">*</span>
                        </label>
                        <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time') }}"
                               required min="{{ now()->format('Y-m-d\TH:i') }}" class="form-input">
                        @error('start_time')
                            <div class="error-msg">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="duration" class="form-label">
                            Durée estimée <span class="required">*</span>
                        </label>
                        <select id="duration" name="duration" required class="form-select">
                            <option value="15" {{ old('duration') == 15 ? 'selected' : '' }}>⏱️ 15 minutes</option>
                            <option value="30" {{ old('duration', 30) == 30 ? 'selected' : '' }}>⏱️ 30 minutes</option>
                            <option value="45" {{ old('duration') == 45 ? 'selected' : '' }}>⏱️ 45 minutes</option>
                            <option value="60" {{ old('duration') == 60 ? 'selected' : '' }}>⏱️ 1 heure</option>
                            <option value="90" {{ old('duration') == 90 ? 'selected' : '' }}>⏱️ 1h30</option>
                            <option value="120" {{ old('duration') == 120 ? 'selected' : '' }}>⏱️ 2 heures</option>
                            <option value="180" {{ old('duration') == 180 ? 'selected' : '' }}>⏱️ 3 heures</option>
                        </select>
                        @error('duration')
                            <div class="error-msg">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Participants -->
                <h3 class="section-title" style="margin-top: 2rem;">
                    <span class="section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    Participants
                </h3>

                <div class="form-group">
                    <div style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.75rem; padding: 1rem; max-height: 400px; overflow-y: auto;">
                        @foreach($users as $user)
                            <label class="participant-option">
                                <input type="checkbox" name="participants[]" value="{{ $user->id }}"
                                       {{ collect(old('participants'))->contains($user->id) ? 'checked' : '' }}
                                       class="custom-checkbox">
                                <div class="participant-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="color: white; font-weight: 500; font-size: 0.9375rem;">{{ $user->name }}</div>
                                    <div style="color: #64748b; font-size: 0.8125rem;">{{ $user->email }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="help-text" style="margin-top: 0.75rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                        Sélectionnez un ou plusieurs participants en cochant les cases
                    </p>
                    @error('participants')
                        <div class="error-msg">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Options -->
                <h3 class="section-title" style="margin-top: 2rem;">
                    <span class="section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v6m0 6v6M1 12h6m6 0h6"/>
                        </svg>
                    </span>
                    Options
                </h3>

                <div class="form-group">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="is_recorded" value="1" {{ old('is_recorded') ? 'checked' : '' }} class="custom-checkbox">
                        <div>
                            <span style="display: block; color: white; font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">
                                🎥 Enregistrer la réunion
                            </span>
                            <span style="display: block; color: #64748b; font-size: 0.8125rem;">
                                L'enregistrement vidéo sera disponible pour tous les participants après la fin de la réunion
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <svg class="info-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div class="info-text">
                        Un lien de visioconférence sécurisé (Jitsi Meet) sera automatiquement généré lors de la création de la réunion. Les participants pourront y accéder directement depuis l'application.
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ route('web.meetings') }}" class="btn-cancel">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Annuler
                    </a>
                    <button type="submit" class="btn-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer la réunion
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
