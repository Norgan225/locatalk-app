<x-app-layout>
    <style>
        /* Premium Container */
        .settings-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Premium Header */
        .settings-header {
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.1), rgba(251, 187, 42, 0.1));
            border: 1px solid rgba(251, 187, 42, 0.3);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 32px;
            backdrop-filter: blur(20px);
        }

        .settings-title {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .settings-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
        }

        /* Tabs Navigation */
        .tabs-container {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .tabs-container::-webkit-scrollbar {
            height: 4px;
        }

        .tabs-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
        }

        .tabs-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-radius: 2px;
        }

        .tab-btn {
            padding: 14px 24px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .tab-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(251, 187, 42, 0.1), transparent);
            transition: left 0.5s;
        }

        .tab-btn:hover::before {
            left: 100%;
        }

        .tab-btn:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fbbb2a;
            transform: translateY(-2px);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.3), rgba(251, 187, 42, 0.3));
            border-color: rgba(251, 187, 42, 0.6);
            color: #fbbb2a;
            box-shadow: 0 4px 20px rgba(251, 187, 42, 0.3);
        }

        .tab-icon {
            width: 20px;
            height: 20px;
        }

        /* Tab Content */
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Premium Card */
        .premium-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title-icon {
            width: 28px;
            height: 28px;
            color: #fbbb2a;
        }

        .card-description {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            margin-bottom: 24px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.5);
            box-shadow: 0 0 0 3px rgba(251, 187, 42, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-select option {
            background: #1a1a1a;
            color: white;
        }

        /* Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .tabs-container {
                flex-direction: column;
            }

            .settings-header {
                padding: 24px;
            }

            .settings-title {
                font-size: 24px;
            }
        }

        /* Logo Upload */
        .logo-upload-area {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 24px;
            background: rgba(255, 255, 255, 0.02);
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .logo-upload-area:hover {
            border-color: rgba(251, 187, 42, 0.5);
            background: rgba(255, 255, 255, 0.05);
        }

        .logo-preview {
            width: 120px;
            height: 120px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid rgba(251, 187, 42, 0.3);
        }

        .logo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logo-preview-placeholder {
            font-size: 48px;
            font-weight: 900;
            color: #fbbb2a;
        }

        /* Color Picker */
        .color-picker-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .color-preview {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .color-preview:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Stats Grid */
        .stats-grid-settings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card-settings {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .stat-card-settings:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .stat-value-settings {
            font-size: 32px;
            font-weight: 900;
            color: white;
            margin-bottom: 8px;
        }

        .stat-label-settings {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            font-weight: 600;
        }

        /* Plan Badge */
        .plan-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.2), rgba(251, 187, 42, 0.2));
            border: 2px solid rgba(251, 187, 42, 0.4);
            border-radius: 12px;
            color: #fbbb2a;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.8);
            padding: 14px 32px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        /* Success Message */
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            color: #34d399;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Storage Bar */
        .storage-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            height: 12px;
            overflow: hidden;
            margin-top: 12px;
        }

        .storage-progress {
            height: 100%;
            background: linear-gradient(90deg, #df5526, #fbbb2a);
            border-radius: 8px;
            transition: width 0.5s ease;
        }

        .storage-info {
            display: flex;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            margin-top: 8px;
        }
    </style>

    <div class="settings-container">
        <!-- Premium Header -->
        <div class="settings-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 class="settings-title">⚙️ {{ org_trans('organization_settings') }}</h1>
                    <p class="settings-subtitle">{{ org_trans('manage_settings_preferences') }} {{ $organization->name }}</p>
                </div>
                <a href="{{ route('web.settings.demo') }}" style="background: rgba(255, 255, 255, 0.1); color: white; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.15)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ org_trans('view_demo') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert-success">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <!-- Tabs Navigation -->
        <div class="tabs-container">
                <button class="tab-btn active" data-tab="general">
                    <svg class="tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ org_trans('general_information') }}
                </button>            <button class="tab-btn" data-tab="branding">
                <svg class="tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                {{ org_trans('visual_identity') }}
            </button>

            <button class="tab-btn" data-tab="subscription">
                <svg class="tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                {{ org_trans('plan_billing') }}
            </button>

            <button class="tab-btn" data-tab="statistics">
                <svg class="tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ org_trans('statistics') }}
            </button>

            <button class="tab-btn" data-tab="advanced">
                <svg class="tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                {{ org_trans('advanced_settings') }}
            </button>
        </div>

        <!-- Tab: General Information -->
        <div class="tab-content active" id="general">
            <div class="premium-card">
                <h2 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ org_trans('organization_information') }}
                </h2>
                <p class="card-description">{{ org_trans('modify_basic_organization_info') }}</p>

                <form action="{{ route('web.settings.general') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('organization_name') }} *</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $organization->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('contact_email') }} *</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email', $organization->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('phone') }}</label>
                            <input type="tel" name="phone" class="form-input" value="{{ old('phone', $organization->phone) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('website') }}</label>
                            <input type="url" name="website" class="form-input" value="{{ old('website', $organization->website) }}" placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ org_trans('address') }}</label>
                        <input type="text" name="address" class="form-input" value="{{ old('address', $organization->address) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ org_trans('organization_description') }}</label>
                        <textarea name="description" class="form-textarea">{{ old('description', $organization->description) }}</textarea>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn-primary">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ org_trans('save_changes') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Logo Upload -->
            <div class="premium-card">
                <h2 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ org_trans('organization_logo') }}
                </h2>
                <p class="card-description">{{ org_trans('upload_organization_logo') }}</p>

                <form action="{{ route('web.settings.logo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="logo-upload-area">
                        <div class="logo-preview">
                            @if($organization->logo)
                            <img src="{{ Storage::url($organization->logo) }}" alt="Logo">
                            @else
                            <div class="logo-preview-placeholder">{{ substr($organization->name, 0, 1) }}</div>
                            @endif
                        </div>

                        <div style="flex: 1;">
                            <input type="file" name="logo" id="logo" class="form-input" accept="image/png,image/jpeg,image/jpg,image/svg+xml">
                            <p style="color: rgba(255, 255, 255, 0.4); font-size: 13px; margin-top: 8px;">
                                {{ org_trans('accepted_formats') }}
                            </p>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn-primary">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            {{ org_trans('upload_logo') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab: Branding -->
        <div class="tab-content" id="branding">
            <div class="premium-card">
                <h2 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    {{ org_trans('brand_colors') }}
                </h2>
                <p class="card-description">{{ org_trans('customize_organization_colors') }}</p>

                <form action="{{ route('web.settings.branding') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">{{ org_trans('primary_color') }}</label>
                        <div class="color-picker-group">
                            <input type="color" name="primary_color" id="primary_color" class="color-preview" value="{{ $organization->branding['primary_color'] ?? '#df5526' }}">
                            <input type="text" class="form-input" value="{{ $organization->branding['primary_color'] ?? '#df5526' }}" readonly style="flex: 1;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ org_trans('secondary_color') }}</label>
                        <div class="color-picker-group">
                            <input type="color" name="secondary_color" id="secondary_color" class="color-preview" value="{{ $organization->branding['secondary_color'] ?? '#fbbb2a' }}">
                            <input type="text" class="form-input" value="{{ $organization->branding['secondary_color'] ?? '#fbbb2a' }}" readonly style="flex: 1;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ org_trans('accent_color') }}</label>
                        <div class="color-picker-group">
                            <input type="color" name="accent_color" id="accent_color" class="color-preview" value="{{ $organization->branding['accent_color'] ?? '#60a5fa' }}">
                            <input type="text" class="form-input" value="{{ $organization->branding['accent_color'] ?? '#60a5fa' }}" readonly style="flex: 1;">
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn-primary">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ org_trans('save_colors') }}
                        </button>
                        <button type="button" class="btn-secondary" onclick="resetColors()">{{ org_trans('reset') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab: Subscription -->
        <div class="tab-content" id="subscription">
            <div class="premium-card">
                <h2 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    {{ org_trans('your_current_plan') }}
                </h2>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <div class="plan-badge">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            {{ org_trans('plan') }} {{ ucfirst($organization->subscription_plan) }}
                        </div>
                        <p style="color: rgba(255, 255, 255, 0.5); font-size: 14px; margin-top: 12px;">
                            @if($organization->subscription_status === 'active')
                            ✅ {{ org_trans('active_subscription_until') }} {{ $organization->subscription_end_date ? \Carbon\Carbon::parse($organization->subscription_end_date)->format('d/m/Y') : 'N/A' }}
                            @else
                            ⚠️ {{ org_trans('status') }}: {{ $organization->subscription_status }}
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('web.subscriptions') }}" class="btn-primary">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        {{ org_trans('upgrade_my_plan') }}
                    </a>
                </div>

                <!-- Storage Usage -->
                <div style="margin-top: 32px;">
                    <h3 style="color: white; font-size: 16px; font-weight: 700; margin-bottom: 16px;">📦 {{ org_trans('storage_used') }}</h3>
                    <div class="storage-bar">
                        <div class="storage-progress" style="width: {{ ($stats['storage_used'] / $stats['storage_limit']) * 100 }}%;"></div>
                    </div>
                    <div class="storage-info">
                        <span>{{ $stats['storage_used'] }} {{ org_trans('mb_used') }}</span>
                        <span>{{ $stats['storage_limit'] }} {{ org_trans('mb_available') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Statistics -->
        <div class="tab-content" id="statistics">
            <div class="premium-card">
                <h2 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ org_trans('organization_overview') }}
                </h2>
                <p class="card-description">{{ org_trans('real_time_organization_stats') }}</p>

                <div class="stats-grid-settings">
                    <div class="stat-card-settings">
                        <div class="stat-value-settings" style="color: #60a5fa;">{{ $stats['total_users'] }}</div>
                        <div class="stat-label-settings">{{ org_trans('total_users') }}</div>
                    </div>

                    <div class="stat-card-settings">
                        <div class="stat-value-settings" style="color: #34d399;">{{ $stats['active_users'] }}</div>
                        <div class="stat-label-settings">{{ org_trans('active_users') }}</div>
                    </div>

                    <div class="stat-card-settings">
                        <div class="stat-value-settings" style="color: #fbbb2a;">{{ $stats['total_projects'] }}</div>
                        <div class="stat-label-settings">{{ org_trans('total_projects') }}</div>
                    </div>

                    <div class="stat-card-settings">
                        <div class="stat-value-settings" style="color: #df5526;">{{ $stats['active_projects'] }}</div>
                        <div class="stat-label-settings">{{ org_trans('active_projects') }}</div>
                    </div>

                    <div class="stat-card-settings">
                        <div class="stat-value-settings" style="color: #a78bfa;">{{ $stats['total_departments'] }}</div>
                        <div class="stat-label-settings">{{ org_trans('departments') }}</div>
                    </div>

                    <div class="stat-card-settings">
                        <div class="stat-value-settings" style="color: #f472b6;">{{ $stats['total_channels'] }}</div>
                        <div class="stat-label-settings">{{ org_trans('channels') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Advanced Settings -->
        <div class="tab-content" id="advanced">
            <div class="premium-card">
                <h2 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    {{ org_trans('advanced_settings') }}
                </h2>
                <p class="card-description">{{ org_trans('regional_configuration') }}</p>

                <form action="{{ route('web.settings.advanced') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">{{ org_trans('timezone') }}</label>
                            <select name="timezone" class="form-select">
                                <option value="Africa/Casablanca" {{ ($organization->settings['timezone'] ?? 'Africa/Casablanca') === 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca (GMT)</option>
                                <option value="Europe/Paris" {{ ($organization->settings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (GMT+1)</option>
                                <option value="America/New_York" {{ ($organization->settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('language') }}</label>
                            <select name="language" class="form-select">
                                <option value="fr" {{ ($organization->settings['language'] ?? 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ ($organization->settings['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ ($organization->settings['language'] ?? '') === 'es' ? 'selected' : '' }}>Español</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('date_format') }}</label>
                            <select name="date_format" class="form-select">
                                <option value="d/m/Y" {{ ($organization->settings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>{{ org_trans('dd_mm_yyyy') }}</option>
                                <option value="m/d/Y" {{ ($organization->settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>{{ org_trans('mm_dd_yyyy') }}</option>
                                <option value="Y-m-d" {{ ($organization->settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>{{ org_trans('yyyy_mm_dd') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ org_trans('time_format') }}</label>
                            <select name="time_format" class="form-select">
                                <option value="H:i" {{ ($organization->settings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>{{ org_trans('24_hours') }}</option>
                                <option value="h:i A" {{ ($organization->settings['time_format'] ?? '') === 'h:i A' ? 'selected' : '' }}>{{ org_trans('12_hours_ampm') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn-primary">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ org_trans('save_settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab Switching
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active from all tabs and contents
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                // Add active to clicked tab and its content
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Color Picker Sync
        document.querySelectorAll('input[type="color"]').forEach(picker => {
            picker.addEventListener('input', (e) => {
                const input = e.target.parentElement.querySelector('input[type="text"]');
                if (input) {
                    input.value = e.target.value;
                }
            });
        });

        // Reset Colors
        function resetColors() {
            document.getElementById('primary_color').value = '#df5526';
            document.getElementById('secondary_color').value = '#fbbb2a';
            document.getElementById('accent_color').value = '#60a5fa';
        }

        // Logo Preview
        document.getElementById('logo')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.querySelector('.logo-preview');
                    preview.innerHTML = `<img src="${event.target.result}" alt="Logo Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-app-layout>
