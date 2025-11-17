<x-app-layout>
    <style>
        .demo-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .demo-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .demo-item {
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .demo-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            margin-bottom: 4px;
        }

        .demo-value {
            color: white;
            font-size: 16px;
            font-weight: 600;
        }

        .color-preview {
            width: 100%;
            height: 60px;
            border-radius: 12px;
            margin-bottom: 12px;
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="color: white; font-size: 28px; font-weight: 800; margin-bottom: 24px;">
            🎨 Démonstration des Paramètres
        </h1>

        <!-- Branding Colors Demo -->
        <div class="demo-card">
            <h2 class="demo-title">🎨 Identité Visuelle Appliquée</h2>
            <p style="color: rgba(255, 255, 255, 0.6); margin-bottom: 20px;">
                Voici comment vos couleurs personnalisées sont appliquées dans l'interface :
            </p>

            @php
                $branding = auth()->user()->organization->branding ?? [];
                $primaryColor = $branding['primary_color'] ?? '#df5526';
                $secondaryColor = $branding['secondary_color'] ?? '#fbbb2a';
                $accentColor = $branding['accent_color'] ?? '#60a5fa';
            @endphp

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
                <div>
                    <div class="demo-label">Couleur Principale</div>
                    <div class="color-preview" style="background: {{ $primaryColor }};"></div>
                    <div class="demo-value">{{ $primaryColor }}</div>
                </div>
                <div>
                    <div class="demo-label">Couleur Secondaire</div>
                    <div class="color-preview" style="background: {{ $secondaryColor }};"></div>
                    <div class="demo-value">{{ $secondaryColor }}</div>
                </div>
                <div>
                    <div class="demo-label">Couleur d'Accent</div>
                    <div class="color-preview" style="background: {{ $accentColor }};"></div>
                    <div class="demo-value">{{ $accentColor }}</div>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <div class="demo-label">Gradient appliqué (logo, boutons, etc.)</div>
                <div style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }}); height: 80px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 24px;">
                    LocaTalk
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; color: #34d399;">
                ✅ Ces couleurs sont automatiquement appliquées au logo, aux boutons actifs, aux badges owner, et aux dégradés de fond.
            </div>
        </div>

        <!-- Language Demo -->
        <div class="demo-card">
            <h2 class="demo-title">🌍 Langue de l'Interface</h2>

            @php
                $language = org_setting('language', 'fr');
                $languageNames = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'];
            @endphp

            <div class="demo-item">
                <div class="demo-label">Langue actuelle</div>
                <div class="demo-value">{{ $languageNames[$language] ?? $language }}</div>
            </div>

            <div style="margin-top: 20px;">
                <div class="demo-label">Exemples de traduction :</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                    <div class="demo-item">
                        <div class="demo-label">Dashboard</div>
                        <div class="demo-value">{{ org_trans('dashboard') }}</div>
                    </div>
                    <div class="demo-item">
                        <div class="demo-label">Projects</div>
                        <div class="demo-value">{{ org_trans('projects') }}</div>
                    </div>
                    <div class="demo-item">
                        <div class="demo-label">Tasks</div>
                        <div class="demo-value">{{ org_trans('tasks') }}</div>
                    </div>
                    <div class="demo-item">
                        <div class="demo-label">Users</div>
                        <div class="demo-value">{{ org_trans('users') }}</div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 12px; color: #60a5fa;">
                💡 Changez la langue dans les Paramètres Avancés pour voir tous les menus traduits automatiquement.
            </div>
        </div>

        <!-- Date/Time Format Demo -->
        <div class="demo-card">
            <h2 class="demo-title">🕐 Formats de Date et Heure</h2>

            @php
                $now = now();
                $dateFormat = org_setting('date_format', 'd/m/Y');
                $timeFormat = org_setting('time_format', 'H:i');
                $timezone = org_setting('timezone', 'Africa/Casablanca');
            @endphp

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div class="demo-item">
                    <div class="demo-label">Format de date</div>
                    <div class="demo-value">{{ $dateFormat }}</div>
                </div>
                <div class="demo-item">
                    <div class="demo-label">Format d'heure</div>
                    <div class="demo-value">{{ $timeFormat }}</div>
                </div>
                <div class="demo-item">
                    <div class="demo-label">Fuseau horaire</div>
                    <div class="demo-value">{{ $timezone }}</div>
                </div>
                <div class="demo-item">
                    <div class="demo-label">Date/Heure actuelle</div>
                    <div class="demo-value">{{ org_date($now, true) }}</div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <div class="demo-label">Exemples d'application :</div>
                <div style="margin-top: 12px;">
                    <div class="demo-item">
                        <div class="demo-label">Réunion créée le</div>
                        <div class="demo-value">{{ org_date($now) }}</div>
                    </div>
                    <div class="demo-item">
                        <div class="demo-label">Tâche à compléter avant</div>
                        <div class="demo-value">{{ org_date($now->addDays(7), true) }}</div>
                    </div>
                    <div class="demo-item">
                        <div class="demo-label">Dernière connexion</div>
                        <div class="demo-value">{{ org_time($now) }}</div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; background: rgba(251, 187, 42, 0.1); border: 1px solid rgba(251, 187, 42, 0.3); border-radius: 12px; color: #fbbb2a;">
                📅 Tous les affichages de dates dans les projets, tâches, réunions utilisent automatiquement ce format.
            </div>
        </div>

        <div style="text-align: center; margin-top: 32px;">
            <a href="{{ route('web.settings') }}" style="display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 700;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Modifier les paramètres
            </a>
        </div>
    </div>
</x-app-layout>
