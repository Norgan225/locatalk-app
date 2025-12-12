<x-app-layout>
    <style>
        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
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
            text-decoration: none;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(223, 85, 38, 0.3);
        }

        .filters-container {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-select, .filter-input {
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }

        .filter-input {
            flex: 1;
            max-width: 300px;
        }

        .filter-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .users-table {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.3s ease;
            cursor: pointer;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 16px 20px;
            color: rgba(255, 255, 255, 0.9);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: white;
            margin-bottom: 2px;
        }

        .user-email {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        /* 🔴 Super Admin - Rouge dégradé avec ombre puissante */
        .role-super_admin {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: white;
            box-shadow: 0 4px 16px rgba(220, 38, 38, 0.6);
            border: 1px solid rgba(239, 68, 68, 0.8);
            font-weight: 800;
        }

        /* 🟠 Owner - Orange/Jaune dégradé */
        .role-owner {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            box-shadow: 0 4px 12px rgba(251, 187, 42, 0.4);
            border: 1px solid rgba(251, 187, 42, 0.5);
        }

        /* 🔵 Admin - Bleu glassmorphism */
        .role-admin {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.5);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            backdrop-filter: blur(10px);
        }

        /* 🟢 Responsable - Vert glassmorphism */
        .role-responsable {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.5);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            backdrop-filter: blur(10px);
        }

        /* 🟣 Employé - Violet glassmorphism */
        .role-employe {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.5);
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
            backdrop-filter: blur(10px);
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-active {
            background: #34d399;
            box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);
        }

        .status-inactive {
            background: #f87171;
        }

        .status-suspended {
            background: #fbbb2a;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            opacity: 0.3;
        }

        /* 📱 Mobile Responsive */
        @media (max-width: 768px) {
            .users-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .users-header h1 {
                font-size: 20px !important;
            }

            .btn-create {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
                font-size: 14px;
            }

            .filters-container {
                flex-direction: column;
            }

            .filter-select, .filter-input {
                width: 100%;
                max-width: none;
            }

            .users-table {
                overflow-x: auto;
                border-radius: 12px;
            }

            table {
                min-width: 600px;
            }

            th, td {
                padding: 12px 10px;
                font-size: 12px;
            }

            /* Cacher certaines colonnes sur mobile */
            .hide-mobile {
                display: none;
            }

            .user-avatar-small {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .user-name {
                font-size: 13px;
            }

            .user-email {
                font-size: 11px;
            }

            .role-badge {
                font-size: 9px;
                padding: 3px 8px;
            }

            .btn-icon {
                padding: 6px;
            }

            .btn-icon svg {
                width: 16px !important;
                height: 16px !important;
            }

            .empty-state {
                padding: 40px 20px;
            }

            .empty-state svg {
                width: 48px;
                height: 48px;
            }
        }

        /* 💻 Tablet Responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .users-header h1 {
                font-size: 22px !important;
            }

            th, td {
                padding: 14px 16px;
                font-size: 13px;
            }

            .filter-input {
                max-width: 250px;
            }
        }
    </style>

    @if(!auth()->user()->canManageUsers())
        <div style="text-align: center; padding: 80px 20px;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 20px; opacity: 0.3; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h3 style="color: white; margin-bottom: 12px; font-size: 20px;">{{ org_trans('access_restricted') }}</h3>
            <p style="color: rgba(255, 255, 255, 0.5);">{{ org_trans('no_permissions_access_page') }}</p>
        </div>
    @else
    <!-- Header -->
    <div class="users-header">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">{{ org_trans('user_management') }}</h1>
        <a href="{{ route('web.users.create') }}" class="btn-create">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            {{ org_trans('new_user') }}
        </a>
    </div>

    @if(session('success'))
        <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.3); color: #f87171; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('web.users') }}" class="filters-container">
        <input type="text" name="search" class="filter-input" placeholder="{{ org_trans('search_by_name_email') }}" value="{{ request('search') }}">

        <select name="role" class="filter-select" onchange="this.form.submit()">
            <option value="">{{ org_trans('all_roles') }}</option>
            @if(auth()->user()->isSuperAdmin())
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>{{ org_trans('super_admin') }}</option>
            @endif
            <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>{{ org_trans('owner') }}</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ org_trans('admin') }}</option>
            <option value="responsable" {{ request('role') == 'responsable' ? 'selected' : '' }}>{{ org_trans('responsable') }}</option>
            <option value="employe" {{ request('role') == 'employe' ? 'selected' : '' }}>{{ org_trans('employe') }}</option>
        </select>

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">{{ org_trans('all_statuses') }}</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ org_trans('active') }}</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ org_trans('inactive') }}</option>
            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>{{ org_trans('suspended') }}</option>
        </select>

        @if(auth()->user()->isSuperAdmin())
        <select name="organization_id" class="filter-select" onchange="this.form.submit()">
            <option value="">{{ org_trans('all_organizations') }}</option>
            @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                    {{ $org->name }}
                </option>
            @endforeach
        </select>
        @endif

        <select name="department_id" class="filter-select" onchange="this.form.submit()">
            <option value="">{{ org_trans('all_departments') }}</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-create" style="padding: 10px 20px;">
            {{ org_trans('search') }}
        </button>
    </form>

    <!-- Users Table -->
    @if($users->count() > 0)
    <div class="users-table">
        <table>
            <thead>
                <tr>
                    <th>{{ org_trans('user') }}</th>
                    <th>{{ org_trans('role') }}</th>
                    <th class="hide-mobile">{{ org_trans('organization') }}</th>
                    <th class="hide-mobile">{{ org_trans('department') }}</th>
                    <th>{{ org_trans('status') }}</th>
                    <th class="hide-mobile">{{ org_trans('created_on') }}</th>
                    <th>{{ org_trans('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr onclick="window.location.href='{{ route('web.users.show', $user->id) }}'">
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-small">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ $user->name }}</span>
                                <span class="user-email">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge role-{{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="hide-mobile">
                        {{ $user->organization ? $user->organization->name : '-' }}
                    </td>
                    <td class="hide-mobile">
                        {{ $user->department ? $user->department->name : '-' }}
                    </td>
                    <td>
                        <span class="status-indicator status-{{ $user->status }}"></span>
                        <span>{{ ucfirst($user->status) }}</span>
                    </td>
                    <td class="hide-mobile">
                        <span style="color: rgba(255, 255, 255, 0.6); font-size: 14px;">
                            {{ $user->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <div class="actions-cell">
                            @if(!($user->role === 'owner' && !auth()->user()->isOwner()))
                            <a href="{{ route('web.users.edit', $user->id) }}" class="btn-icon" title="{{ org_trans('edit') }}">
                                <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endif
                            <a href="{{ route('web.users.show', $user->id) }}" class="btn-icon" title="{{ org_trans('view_profile') }}">
                                <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if(auth()->user()->id !== $user->id && !($user->role === 'owner' && !auth()->user()->isOwner()))
                            <form method="POST" action="{{ route('web.users.destroy', $user->id) }}" style="display: inline;" onsubmit="return confirm('{{ org_trans('confirm_delete_user') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" title="{{ org_trans('delete') }}" style="border: none;">
                                    <svg style="width: 16px; height: 16px; color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px;">
        {{ $users->links() }}
    </div>
    @else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <h3 style="color: white; margin-bottom: 8px; font-size: 18px;">{{ org_trans('no_users_found') }}</h3>
        <p>{{ org_trans('try_modify_filters_or_create_user') }}</p>
    </div>
    @endif
    @endif

</x-app-layout>
