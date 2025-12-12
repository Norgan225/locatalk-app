@extends('emails.layout')

@section('title', 'Bienvenue sur LocaTalk')

@section('content')
    <h2>Bienvenue {{ $user->name }} ! 👋</h2>

    <p>Nous sommes ravis de vous accueillir sur <strong>LocaTalk</strong>, la plateforme de communication et de collaboration de votre organisation <strong>{{ $organization->name }}</strong>.</p>

    <div class="divider"></div>

    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">📋 Informations de votre compte</h3>

    <div class="info-box">
        <p><strong>Email :</strong> {{ $user->email }}</p>
        <p><strong>Organisation :</strong> {{ $organization->name }}</p>
        <p><strong>Rôle :</strong> {{ ucfirst($user->role) }}</p>
        @if($user->department)
            <p><strong>Département :</strong> {{ $user->department->name }}</p>
        @endif
    </div>

    @if($temporaryPassword)
        <div class="alert">
            <p><strong>⚠️ Mot de passe temporaire :</strong></p>
            <p style="font-size: 18px; font-weight: 600; color: #856404; margin-top: 10px;">{{ $temporaryPassword }}</p>
            <p style="margin-top: 10px;">Pour des raisons de sécurité, veuillez changer ce mot de passe lors de votre première connexion.</p>
        </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/login" class="btn">
            Se connecter à LocaTalk
        </a>
    </div>

    <div class="divider"></div>

    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">🚀 Fonctionnalités disponibles</h3>

    <p style="margin-bottom: 10px;"><strong>✉️ Messagerie instantanée</strong><br>
    Communiquez en temps réel avec vos collègues via messages directs ou canaux.</p>

    <p style="margin-bottom: 10px;"><strong>📊 Gestion de projets</strong><br>
    Créez et suivez vos projets, assignez des tâches et suivez leur avancement.</p>

    <p style="margin-bottom: 10px;"><strong>📅 Réunions et appels</strong><br>
    Planifiez des réunions, lancez des appels audio/vidéo avec votre équipe.</p>

    <p style="margin-bottom: 10px;"><strong>🔔 Notifications</strong><br>
    Restez informé de toutes les activités importantes de votre organisation.</p>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #777;">
        <strong>Besoin d'aide ?</strong><br>
        Notre équipe est là pour vous accompagner. N'hésitez pas à consulter notre documentation ou à contacter votre administrateur.
    </p>
@endsection
