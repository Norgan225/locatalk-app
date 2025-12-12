<x-app-layout>
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 48px;
            position: relative;
            cursor: pointer;
        }

        .avatar-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid #0f172a;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
        }

        .profile-email {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            margin-bottom: 16px;
        }

        .profile-stats {
            display: flex;
            gap: 32px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: rgba(251, 187, 42, 0.5);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .btn-save {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.4);
        }

        .activity-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(251, 187, 42, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbb2a;
        }

        .activity-details {
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

        .settings-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .settings-label {
            color: white;
            font-weight: 500;
        }

        .settings-description {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            margin-top: 4px;
        }

        .toggle-switch {
            width: 50px;
            height: 26px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 13px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .toggle-switch.active {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
        }

        .toggle-thumb {
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: transform 0.3s ease;
        }

        .toggle-switch.active .toggle-thumb {
            transform: translateX(24px);
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .profile-avatar-large {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }

            .avatar-upload {
                width: 32px;
                height: 32px;
            }

            .avatar-upload svg {
                width: 16px !important;
                height: 16px !important;
            }

            .profile-name {
                font-size: 24px !important;
            }

            .profile-email {
                font-size: 14px;
            }

            .profile-stats {
                flex-direction: column;
                gap: 12px;
                width: 100%;
            }

            .stat-item {
                flex-direction: row;
                justify-content: space-between;
                width: 100%;
                padding: 12px;
                background: rgba(255, 255, 255, 0.03);
                border-radius: 10px;
            }

            .stat-value {
                font-size: 20px;
            }

            .stat-label {
                font-size: 12px;
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-card {
                padding: 16px;
            }

            .card-title {
                font-size: 18px;
            }

            .form-input, .btn-save {
                font-size: 14px;
            }

            .activity-icon {
                width: 32px;
                height: 32px;
            }

            .activity-text {
                font-size: 13px;
            }

            .settings-label {
                font-size: 14px;
            }

            .settings-description {
                font-size: 12px;
            }

            .role-badge {
                align-self: center !important;
                margin-top: 12px;
            }
        }

        /* 💻 Tablet Responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .profile-header {
                padding: 24px;
            }

            .profile-avatar-large {
                width: 100px;
                height: 100px;
                font-size: 42px;
            }

            .profile-name {
                font-size: 28px !important;
            }

            .profile-stats {
                gap: 20px;
            }

            .stat-value {
                font-size: 20px;
            }
        }

        /* Avatar Preview */
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #avatar-input {
            display: none;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(52, 211, 153, 0.1);
            border: 1px solid rgba(52, 211, 153, 0.3);
            color: #34d399;
        }

        .alert-error {
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #f87171;
        }
    </style>

    <div class="profile-container">
        @if(session('success'))
            <div class="alert alert-success">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Profile Header -->
        <div class="profile-header">
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                @csrf
                <div class="profile-avatar-large" onclick="document.getElementById('avatar-input').click()" style="cursor: pointer;">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="avatar-img">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="avatar-upload">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <input type="file" id="avatar-input" name="avatar" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
            </form>

            <div class="profile-info">
                <h1 class="profile-name">{{ $user->name }}</h1>
                <p class="profile-email">{{ $user->email }}</p>
                @if($user->organization)
                    <p class="profile-email">{{ $user->organization->name }}</p>
                @endif
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $stats['projects'] }}</span>
                        <span class="stat-label">Projets</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $stats['tasks'] }}</span>
                        <span class="stat-label">Tâches</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $stats['messages'] }}</span>
                        <span class="stat-label">Messages</span>
                    </div>
                </div>
            </div>

            <span class="role-badge role-{{ $user->role }}" style="align-self: flex-start;">
                @if($user->isSuperAdmin())
                    SUPER ADMIN
                @else
                    {{ ucfirst($user->role) }}
                @endif
            </span>
        </div>

        <!-- Profile Grid -->
        <div class="profile-grid">
            <!-- Left Column - Edit Profile -->
            <div>
                <div class="profile-card">
                    <h3 class="card-title">{{ org_trans('personal_information') }}</h3>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('name') }}</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" placeholder="{{ org_trans('name') }}" required>
                            @error('name')
                                <span style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('email') }}</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" placeholder="votre@email.com" required>
                            @error('email')
                                <span style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('phone') }}</label>
                            <input type="tel" name="phone" class="form-input" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+225 XX XX XX XX XX">
                            @error('phone')
                                <span style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('role') }}</label>
                            <input type="text" class="form-input" value="@if($user->isSuperAdmin()) Super Admin @else {{ ucfirst($user->role) }} @endif" disabled style="opacity: 0.6; cursor: not-allowed;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('organizations') }}</label>
                            <input type="text" class="form-input" value="{{ $user->organization ? $user->organization->name : '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                        </div>

                        @if($user->department)
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('department') }}</label>
                            <input type="text" class="form-input" value="{{ $user->department->name }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                        </div>
                        @endif

                        <button type="submit" class="btn-save">{{ org_trans('save_changes') }}</button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="profile-card" style="margin-top: 24px;">
                    <h3 class="card-title">{{ org_trans('change_password') }}</h3>
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('current_password') }}</label>
                            <input type="password" name="current_password" class="form-input" placeholder="••••••••" required>
                            @error('current_password')
                                <span style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('new_password') }}</label>
                            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                            @error('password')
                                <span style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('confirm_password') }}</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn-save">{{ org_trans('update_password') }}</button>
                    </form>
                </div>
            </div>

            <!-- Right Column - Activity & Settings -->
            <div>
                <!-- Recent Activity -->
                <div class="profile-card">
                    <h3 class="card-title">{{ org_trans('recent_activities') }}</h3>
                    <div>
                        @forelse($recentActivity as $activity)
                        <div class="activity-item" style="{{ $loop->last ? 'border-bottom: none;' : '' }}">
                            <div class="activity-icon">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="activity-details">
                                <div class="activity-text">{{ $activity->action }}</div>
                                <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="activity-item" style="border-bottom: none;">
                            <div class="activity-icon" style="background: rgba(251, 187, 42, 0.1); color: #fbbb2a;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="activity-details">
                                <div class="activity-text">{{ org_trans('login_to_account') }}</div>
                                <div class="activity-time">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : org_trans('recently') }}</div>
                            </div>
                        </div>

                        <div class="activity-item" style="border-bottom: none;">
                            <div class="activity-icon" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="activity-details">
                                <div class="activity-text">{{ org_trans('account_created') }}</div>
                                <div class="activity-time">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Settings -->
                <div class="profile-card" style="margin-top: 24px;">
                    <h3 class="card-title">{{ org_trans('preferences') }}</h3>
                    <div>
                        <div class="settings-item">
                            <div>
                                <div class="settings-label">{{ org_trans('email_notifications') }}</div>
                                <div class="settings-description">{{ org_trans('receive_important_notifications') }}</div>
                            </div>
                            <div class="toggle-switch active">
                                <div class="toggle-thumb"></div>
                            </div>
                        </div>

                        <div class="settings-item">
                            <div>
                                <div class="settings-label">{{ org_trans('push_notifications') }}</div>
                                <div class="settings-description">{{ org_trans('real_time_alerts') }}</div>
                            </div>
                            <div class="toggle-switch active">
                                <div class="toggle-thumb"></div>
                            </div>
                        </div>

                        <div class="settings-item">
                            <div>
                                <div class="settings-label">{{ org_trans('online_status') }}</div>
                                <div class="settings-description">{{ org_trans('show_presence') }}</div>
                            </div>
                            <div class="toggle-switch">
                                <div class="toggle-thumb"></div>
                            </div>
                        </div>

                        <div class="settings-item" style="border-bottom: none;">
                            <div>
                                <div class="settings-label">{{ org_trans('dark_mode') }}</div>
                                <div class="settings-description">{{ org_trans('night_mode_interface') }}</div>
                            </div>
                            <div class="toggle-switch active">
                                <div class="toggle-thumb"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
