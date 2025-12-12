<x-app-layout>
        <!-- Toast Notification -->
        <div id="toast" style="position:fixed;top:32px;right:32px;z-index:9999;min-width:220px;max-width:400px;padding:18px 32px;background:linear-gradient(135deg,#df5526,#fbbb2a);color:white;font-weight:600;border-radius:12px;box-shadow:0 4px 24px rgba(251,187,42,0.18);display:none;align-items:center;gap:12px;font-size:16px;transition:all 0.4s;">
            <span id="toast-message"></span>
            <button onclick="hideToast()" style="background:none;border:none;color:white;font-size:18px;cursor:pointer;margin-left:16px;">&times;</button>
        </div>
    <style>
        .create-header {
            margin-bottom: 32px;
        }

        .create-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
        }

        .create-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

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

        .form-card {
            background: rgba(30, 41, 59, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            max-width: 800px;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .required {
            color: #f87171;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #fbbb2a;
            background: rgba(255, 255, 255, 0.08);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-hint {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 4px;
        }

        .radio-group {
            display: flex;
            gap: 16px;
            margin-top: 8px;
        }

        .radio-option {
            flex: 1;
            position: relative;
        }

        .radio-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .radio-label {
            display: block;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .radio-option input[type="radio"]:checked + .radio-label {
            border-color: #fbbb2a;
            background: rgba(251, 187, 42, 0.1);
        }

        .radio-label-title {
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-label-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .members-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding: 12px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .member-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .member-checkbox:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .member-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .member-checkbox label {
            color: white;
            font-size: 14px;
            cursor: pointer;
            flex: 1;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(223, 85, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .create-header h1 {
                font-size: 22px;
            }

            .radio-group {
                flex-direction: column;
            }

            .members-list {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <!-- Back Button -->
    <a href="{{ route('web.channels') }}" class="back-button">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux canaux
    </a>

    <!-- Header -->
    <div class="create-header">
        <h1>
            <svg style="width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
            </svg>
            {{ org_trans('create_new_channel') }}
        </h1>
        <p>{{ org_trans('configure_channel_description') }}</p>
    </div>

    <!-- Form -->
    <div class="form-card">
        @if ($errors->any())
            <div class="error-message">
                <strong>Erreur :</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('web.channels.store') }}" method="POST">
            @csrf

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ org_trans('basic_information') }}
                </h3>

                <div class="form-group">
                    <label for="name" class="form-label">
                        {{ org_trans('channel_name') }} <span class="required">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="{{ org_trans('channel_name_placeholder') }}" value="{{ old('name') }}" required>
                    <div class="form-hint">{{ org_trans('choose_clear_descriptive_name') }}</div>
                    @error('name')
                        <div class="form-hint" style="color: #f87171;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">{{ org_trans('description') }}</label>
                    <textarea id="description" name="description" class="form-textarea" placeholder="{{ org_trans('describe_channel_purpose') }}">{{ old('description') }}</textarea>
                    <div class="form-hint">{{ org_trans('optional_help_understand_purpose') }}</div>
                </div>
            </div>

            <!-- Channel Type -->
            <div class="form-section">
                <h3 class="section-title">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    {{ org_trans('channel_type') }}
                </h3>

                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="type_public" name="type" value="public" {{ old('type', 'public') === 'public' ? 'checked' : '' }}>
                        <label for="type_public" class="radio-label">
                            <div class="radio-label-title">
                                <svg style="width: 18px; height: 18px; color: #4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ org_trans('public_channel') }}
                            </div>
                            <div class="radio-label-desc">{{ org_trans('public_channel_desc') }}</div>
                        </label>
                    </div>

                    <div class="radio-option">
                        <input type="radio" id="type_private" name="type" value="private" {{ old('type') === 'private' ? 'checked' : '' }}>
                        <label for="type_private" class="radio-label">
                            <div class="radio-label-title">
                                <svg style="width: 18px; height: 18px; color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ org_trans('private_channel') }}
                            </div>
                            <div class="radio-label-desc">{{ org_trans('private_channel_desc') }}</div>
                        </label>
                    </div>

                    <div class="radio-option">
                        <input type="radio" id="type_department" name="type" value="department" {{ old('type') === 'department' ? 'checked' : '' }}>
                        <label for="type_department" class="radio-label">
                            <div class="radio-label-title">
                                <svg style="width: 18px; height: 18px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ org_trans('department_channel') }}
                            </div>
                            <div class="radio-label-desc">{{ org_trans('department_channel_desc') }}</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Department Selection (conditional) -->
            <div class="form-section" id="departmentSection" style="display: none;">
                <div class="form-group">
                    <label for="department_id" class="form-label">
                        {{ org_trans('select_department') }} <span class="required">*</span>
                    </label>
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">{{ org_trans('select_department_option') }}</option>
                        @foreach(\App\Models\Department::where('organization_id', auth()->user()->organization_id)->get() as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="form-hint" style="color: #f87171;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Members Selection (for private channels) -->
            <div class="form-section" id="membersSection" style="display: none;">
                <h3 class="section-title">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ org_trans('channel_members') }}
                </h3>

                <div class="members-list">
                    @foreach(\App\Models\User::where('organization_id', auth()->user()->organization_id)->where('role', '!=', 'super_admin')->get() as $user)
                        <div class="member-checkbox">
                            <input type="checkbox" id="user_{{ $user->id }}" name="user_ids[]" value="{{ $user->id }}"
                                {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}
                                {{ $user->id === auth()->id() ? 'checked disabled' : '' }}>
                            <label for="user_{{ $user->id }}">{{ $user->name }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="form-hint" style="margin-top: 8px;">{{ org_trans('select_channel_members') }}</div>
            </div>

            <!-- Hidden Fields -->
            <input type="hidden" name="organization_id" value="{{ auth()->user()->organization_id }}">

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ org_trans('create') }} {{ org_trans('channel') }}
                </button>
                <a href="{{ route('web.channels') }}" class="btn btn-secondary">
                    {{ org_trans('cancel') }}
                </a>
            </div>
        </form>
    </div>

    <script>
                // Toast notification system
                function showToast(message, duration = 3500) {
                    const toast = document.getElementById('toast');
                    document.getElementById('toast-message').textContent = message;
                    toast.style.display = 'flex';
                    setTimeout(hideToast, duration);
                }
                function hideToast() {
                    document.getElementById('toast').style.display = 'none';
                }
        // Show/hide department and members sections based on channel type
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const departmentSection = document.getElementById('departmentSection');
        const membersSection = document.getElementById('membersSection');
        const departmentSelect = document.getElementById('department_id');

        function updateSections() {
            const selectedType = document.querySelector('input[name="type"]:checked').value;

            if (selectedType === 'department') {
                departmentSection.style.display = 'block';
                membersSection.style.display = 'none';
                departmentSelect.required = true;
            } else if (selectedType === 'private') {
                departmentSection.style.display = 'none';
                membersSection.style.display = 'block';
                departmentSelect.required = false;
            } else {
                departmentSection.style.display = 'none';
                membersSection.style.display = 'none';
                departmentSelect.required = false;
            }
        }

        typeRadios.forEach(radio => {
            radio.addEventListener('change', updateSections);
        });

        // Initialize on page load
        updateSections();
    </script>
    @if(session('success'))
        <script>showToast(@json(session('success')));</script>
    @endif
    @if(session('error'))
        <script>showToast(@json(session('error')), 4000);</script>
    @endif
</x-app-layout>
