<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LocaTalk') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    @php
        // Get organization branding colors (if owner/user has organization)
        $primaryColor = '#df5526';
        $secondaryColor = '#fbbb2a';
        $accentColor = '#60a5fa';

        if(auth()->check() && auth()->user()->organization) {
            $branding = auth()->user()->organization->branding ?? [];
            $primaryColor = $branding['primary_color'] ?? '#df5526';
            $secondaryColor = $branding['secondary_color'] ?? '#fbbb2a';
            $accentColor = $branding['accent_color'] ?? '#60a5fa';
        }
    @endphp

    <style>
        /* CSS Variables for Dynamic Branding */
        :root {
            --color-primary: {{ $primaryColor }};
            --color-secondary: {{ $secondaryColor }};
            --color-accent: {{ $accentColor }};
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
        }

        /* Animated Background */
        .floating-circle {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            animation: float 20s infinite ease-in-out;
            pointer-events: none;
            z-index: 0;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            background: var(--color-primary);
            top: -100px;
            left: -100px;
            animation-duration: 25s;
        }

        .circle-2 {
            width: 350px;
            height: 350px;
            background: var(--color-secondary);
            bottom: -100px;
            right: -100px;
            animation-duration: 30s;
            animation-delay: 5s;
        }

        .circle-3 {
            width: 300px;
            height: 300px;
            background: var(--color-primary);
            top: 50%;
            right: 10%;
            animation-duration: 35s;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Sidebar */
        .sidebar {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            z-index: 40;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            border-radius: 10px;
        }

        /* Navigation Items */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav-item:hover::before {
            left: 100%;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--color-secondary);
            transform: translateX(5px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 40%, transparent), color-mix(in srgb, var(--color-secondary) 40%, transparent));
            color: var(--color-secondary);
            border: 1px solid color-mix(in srgb, var(--color-secondary) 60%, transparent);
            font-weight: 600;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--color-secondary) 30%, transparent);
        }

        .nav-item.active .nav-icon {
            color: var(--color-secondary);
            filter: drop-shadow(0 0 8px color-mix(in srgb, var(--color-secondary) 50%, transparent));
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 24px;
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        /* Header */
        .top-bar {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 16px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Language Selector */
        .language-selector {
            margin-right: 16px;
        }

        .language-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .language-select:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(251, 187, 42, 0.3);
        }

        .language-select:focus {
            outline: none;
            border-color: #fbbb2a;
            box-shadow: 0 0 0 2px rgba(251, 187, 42, 0.2);
        }

        /* User Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-button:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: color-mix(in srgb, var(--color-secondary) 30%, transparent);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
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

        /* 🟠 Owner - Orange/Jaune dégradé (uses branding colors) */
        .role-owner {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--color-secondary) 40%, transparent);
            border: 1px solid color-mix(in srgb, var(--color-secondary) 50%, transparent);
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

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 24px;
            box-shadow: 0 8px 32px color-mix(in srgb, var(--color-primary) 30%, transparent);
        }

        .logo-text {
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Section Divider */
        .nav-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            margin: 16px 20px;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.4);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 20px 8px 20px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
            }

            .top-bar {
                padding: 12px 16px;
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
                margin-left: 60px; /* Space for hamburger button */
            }

            .top-bar h1 {
                font-size: 20px !important;
            }

            .top-bar p {
                font-size: 12px !important;
            }

            .profile-button {
                padding: 6px 12px;
                gap: 8px;
            }

            .profile-button > div:last-child {
                display: none; /* Hide name and role on very small screens */
            }

            .avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .language-selector {
                margin-right: 8px;
            }

            .language-select {
                padding: 6px 8px;
                font-size: 12px;
            }
        }

        /* Hamburger Menu Button */
        .hamburger-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 50;
            background: linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 90%, transparent), color-mix(in srgb, var(--color-secondary) 90%, transparent));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .hamburger-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px color-mix(in srgb, var(--color-secondary) 40%, transparent);
        }

        .hamburger-btn svg {
            width: 24px;
            height: 24px;
            color: white;
        }

        @media (max-width: 768px) {
            .hamburger-btn {
                display: block;
            }
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 30;
            display: none;
            backdrop-filter: blur(2px);
        }

        .mobile-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="floating-circle circle-1"></div>
    <div class="floating-circle circle-2"></div>
    <div class="floating-circle circle-3"></div>

    <!-- Hamburger Menu Button (Mobile Only) -->
    <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">L</div>
            <span class="logo-text">LocaTalk</span>
        </div>

        <!-- Navigation -->
        <nav>
            <!-- Main Section -->
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ org_trans('dashboard') }}
            </a>

            @if(!auth()->user()->isSuperAdmin())
            <!-- Communication Features (Not for Super Admin) -->
            <a href="{{ route('web.messages.modern') }}" class="nav-item {{ request()->routeIs('web.messages*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                {{ org_trans('messages') }}
            </a>

            <a href="{{ route('web.channels') }}" class="nav-item {{ request()->routeIs('web.channels*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                {{ org_trans('channels') }}
            </a>

            <a href="{{ route('web.projects') }}" class="nav-item {{ request()->routeIs('web.projects*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ org_trans('projects') }}
            </a>

            <a href="{{ route('web.tasks') }}" class="nav-item {{ request()->routeIs('web.tasks*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ org_trans('tasks') }}
            </a>

            <a href="{{ route('web.meetings') }}" class="nav-item {{ request()->routeIs('web.meetings') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ org_trans('meetings') }}
            </a>
            @endif

            @if(auth()->user()->canManageUsers())
            <!-- Management Section -->
            <div class="nav-divider"></div>
            <div class="nav-section-title">{{ auth()->user()->isSuperAdmin() ? 'Administration Plateforme' : 'Gestion' }}</div>

            @if(auth()->user()->isSuperAdmin())
            <!-- Super Admin Only: Organizations -->
            <a href="{{ route('web.organizations') }}" class="nav-item {{ request()->routeIs('web.organizations*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ org_trans('organizations') }}
            </a>

            <a href="{{ route('web.subscriptions') }}" class="nav-item {{ request()->routeIs('web.subscriptions*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ org_trans('subscriptions') }}
            </a>
            @endif

            <a href="{{ route('web.users') }}" class="nav-item {{ request()->routeIs('web.users') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{ org_trans('users') }}
            </a>

            @if(!auth()->user()->isSuperAdmin())
            <!-- Regular Users Only: Departments & Analytics -->
            <a href="{{ route('web.departments') }}" class="nav-item {{ request()->routeIs('web.departments*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ org_trans('departments') }}
            </a>

            <a href="{{ route('web.analytics') }}" class="nav-item {{ request()->routeIs('web.analytics*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ org_trans('analytics') }}
            </a>
            @endif

            @if(auth()->user()->role === 'owner' && !auth()->user()->isSuperAdmin())
            <!-- Owner Only Section (Not Super Admin) -->
            <div class="nav-divider"></div>
            <div class="nav-section-title">Configuration</div>

            <a href="{{ route('web.settings') }}" class="nav-item {{ request()->routeIs('web.settings*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ org_trans('settings') }}
            </a>
            @endif
            @endif

            <!-- Bottom Section -->
            <div class="nav-divider"></div>

            <a href="{{ route('profile.view') }}" class="nav-item {{ request()->routeIs('profile.view') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ org_trans('profile') }}
            </a>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                @csrf
                <button type="submit" class="nav-item" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    {{ org_trans('logout') }}
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: white; margin: 0;">
                    {{ $header ?? org_trans('dashboard') }}
                </h1>
                <p style="color: rgba(255, 255, 255, 0.5); margin-top: 4px; font-size: 14px;">
                    {{ org_trans('welcome') }}, {{ auth()->user()->name }}
                </p>
            </div>

            <!-- Language Selector -->
            <div class="language-selector">
                <select id="language-select" class="language-select" onchange="changeLanguage(this.value)">
                    <option value="fr" {{ \App\Helpers\OrganizationHelper::getLanguage() === 'fr' ? 'selected' : '' }}>🇫🇷 Français</option>
                    <option value="es" {{ \App\Helpers\OrganizationHelper::getLanguage() === 'es' ? 'selected' : '' }}>🇪🇸 Español</option>
                    <option value="en" {{ \App\Helpers\OrganizationHelper::getLanguage() === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                </select>
            </div>

            <div class="profile-dropdown">
                <div class="profile-button">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="color: white; font-weight: 600; font-size: 14px;">
                            {{ auth()->user()->name }}
                        </div>
                        <span class="role-badge role-{{ auth()->user()->role }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        {{ $slot }}
    </main>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            // Toggle sidebar on hamburger click
            hamburgerBtn.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            });

            // Close sidebar on overlay click
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });

            // Close sidebar when clicking on a nav item (mobile only)
            const navItems = sidebar.querySelectorAll('.nav-item');
            navItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-open');
                        overlay.classList.remove('active');
                    }
                });
            });
        });

        // Language switching function
        function changeLanguage(language) {
            fetch('/language/change', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    language: language
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to apply the new language
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error changing language:', error);
            });
        }
    </script>

    <!-- Scripts -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

    @stack('scripts')
</body>
</html>
