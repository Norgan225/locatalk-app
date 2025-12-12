<x-app-layout>
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .form-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label.required::after {
            content: " *";
            color: #f87171;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: rgba(251, 187, 42, 0.5);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-cancel {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .btn-create {
            padding: 12px 24px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(223, 85, 38, 0.3);
        }

        .error-message {
            color: #f87171;
            font-size: 13px;
            margin-top: 6px;
        }

        .info-box {
            background: rgba(96, 165, 250, 0.1);
            border: 1px solid rgba(96, 165, 250, 0.3);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            color: #60a5fa;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h1>{{ org_trans('create_new_user') }}</h1>
                <p>{{ org_trans('fill_info_create_user') }}</p>
            </div>

            <div class="info-box">
                <strong>ℹ️ {{ org_trans('information') }} :</strong> {{ org_trans('email_auto_sent_info') }}
            </div>

            @if($errors->any())
                <div style="background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.3); color: #f87171; padding: 16px; border-radius: 10px; margin-bottom: 24px;">
                    <strong>Erreurs :</strong>
                    <ul style="margin-top: 8px; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('web.users.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">{{ org_trans('full_name') }}</label>
                        <input type="text" name="name" class="form-input" placeholder="{{ org_trans('full_name_placeholder') }}" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">{{ org_trans('email') }}</label>
                        <input type="email" name="email" class="form-input" placeholder="{{ org_trans('email_placeholder') }}" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">{{ org_trans('role') }}</label>
                        <select name="role" class="form-select" required>
                            <option value="">{{ org_trans('select_role') }}</option>
                            @if(auth()->user()->isSuperAdmin())
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                            @endif
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isOwner() || auth()->user()->isAdmin())
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                            <option value="responsable" {{ old('role') == 'responsable' ? 'selected' : '' }}>Responsable</option>
                            <option value="employe" {{ old('role') == 'employe' ? 'selected' : '' }}>Employé</option>
                        </select>
                        @error('role')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ org_trans('phone') }}</label>
                        <input type="tel" name="phone" class="form-input" placeholder="{{ org_trans('phone_placeholder') }}" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(auth()->user()->isSuperAdmin())
                <div class="form-group">
                    <label class="form-label">{{ org_trans('organization') }}</label>
                    <select name="organization_id" class="form-select">
                        <option value="">{{ org_trans('select_organization') }}</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">{{ org_trans('department') }}</label>
                    <select name="department_id" class="form-select">
                        <option value="">{{ org_trans('select_department') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('web.users') }}" class="btn-cancel">
                        {{ org_trans('cancel') }}
                    </a>
                    <button type="submit" class="btn-create">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ org_trans('create_user') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
