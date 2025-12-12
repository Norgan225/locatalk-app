<x-app-layout>
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
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

        .btn-delete {
            padding: 12px 24px;
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.3);
            border-radius: 10px;
            color: #f87171;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: rgba(248, 113, 113, 0.2);
            border-color: #f87171;
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

        .user-meta {
            background: rgba(96, 165, 250, 0.1);
            border: 1px solid rgba(96, 165, 250, 0.3);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-meta-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .user-meta-info {
            flex: 1;
        }

        .user-meta-name {
            color: white;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-meta-email {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
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
            justify-content: space-between;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-cancel {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.7);
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

        .btn-delete {
            padding: 12px 24px;
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.3);
            border-radius: 10px;
            color: #f87171;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: rgba(248, 113, 113, 0.2);
            border-color: #f87171;
        }

        .error-message {
            color: #f87171;
            font-size: 13px;
            margin-top: 6px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="form-container">
        <!-- Bouton retour -->
        <div style="margin-bottom: 24px;">
            <a href="{{ route('web.users.show', $user->id) }}" class="btn-cancel" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au profil
            </a>
        </div>

        <div class="form-card">
            <div class="form-header">
                <h1>{{ org_trans('edit_user') }}</h1>
                <p>{{ org_trans('update_user_info') }}</p>
            </div>

            <!-- User Info -->
            <div class="user-meta">
                <div class="user-meta-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-meta-info">
                    <div class="user-meta-name">{{ $user->name }}</div>
                    <div class="user-meta-email">
                        {{ org_trans('created_on') }} {{ $user->created_at->format('d/m/Y') }} •
                        {{ org_trans('last_modified') }} {{ $user->updated_at->diffForHumans() }}
                    </div>
                </div>
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

            <form method="POST" action="{{ route('web.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Nom complet</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Rôle</label>
                        <select name="role" class="form-select" required>
                            @if(auth()->user()->isSuperAdmin())
                                <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Owner</option>
                            @endif
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isOwner() || auth()->user()->isAdmin())
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                            <option value="responsable" {{ old('role', $user->role) == 'responsable' ? 'selected' : '' }}>Responsable</option>
                            <option value="employe" {{ old('role', $user->role) == 'employe' ? 'selected' : '' }}>Employé</option>
                        </select>
                        @error('role')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">{{ org_trans('status') }}</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>{{ org_trans('active') }}</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>{{ org_trans('inactive') }}</option>
                            <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                        @error('status')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                @if(auth()->user()->isSuperAdmin())
                <div class="form-group">
                    <label class="form-label">Organisation</label>
                    <select name="organization_id" class="form-select">
                        <option value="">Sélectionner une organisation</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id', $user->organization_id) == $org->id ? 'selected' : '' }}>
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
                    <label class="form-label">Département</label>
                    <select name="department_id" class="form-select">
                        <option value="">Sélectionner un département</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <div>
                        @if(auth()->user()->id !== $user->id && auth()->user()->canManageUsers())
                        <button type="button" class="btn-delete" onclick="if(confirm('{{ org_trans('confirm_delete_user') }}')) document.getElementById('delete-form').submit();">
                            <svg style="width: 20px; height: 20px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ org_trans('delete') }}
                        </button>
                        @endif
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('web.users.show', $user->id) }}" class="btn-cancel">
                            {{ org_trans('cancel') }}
                        </a>
                        <button type="submit" class="btn-create">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ org_trans('save') }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Form (hidden) -->
            @if(auth()->user()->id !== $user->id && auth()->user()->canManageUsers())
            <form id="delete-form" method="POST" action="{{ route('web.users.destroy', $user->id) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </div>
    </div>

</x-app-layout>
