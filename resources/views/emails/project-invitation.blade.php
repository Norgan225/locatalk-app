@extends('emails.layout')

@section('title', 'Invitation au projet')

@section('content')
    <h2>🚀 Vous avez été ajouté à un projet</h2>

    <p>Bonjour {{ $member->name }},</p>

    <p>Bonne nouvelle ! Vous avez été ajouté au projet <strong>{{ $project->name }}</strong> en tant que <strong>{{ $role === 'manager' ? 'Manager' : 'Membre' }}</strong>.</p>

    <div class="divider"></div>

    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">📋 Informations du projet</h3>

    <div class="info-box">
        <p><strong>Nom du projet :</strong> {{ $project->name }}</p>
        <p><strong>Votre rôle :</strong> {{ $role === 'manager' ? '👨‍💼 Manager' : '👤 Membre' }}</p>
        @if($project->start_date)
            <p><strong>Date de début :</strong> {{ $project->start_date->format('d/m/Y') }}</p>
        @endif
        @if($project->end_date)
            <p><strong>Date de fin prévue :</strong> {{ $project->end_date->format('d/m/Y') }}</p>
        @endif
        <p><strong>Statut :</strong> {{ ucfirst(str_replace('_', ' ', $project->status)) }}</p>
        <p><strong>Progression :</strong> {{ $project->progress }}%</p>
    </div>

    @if($project->description)
        <div style="margin: 20px 0;">
            <h4 style="color: #333; font-size: 16px; margin-bottom: 10px;">Description du projet :</h4>
            <p style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; font-size: 15px;">
                {{ $project->description }}
            </p>
        </div>
    @endif

    @if($role === 'manager')
        <div class="alert" style="background-color: #d1ecf1; border-color: #bee5eb;">
            <p style="color: #0c5460;">
                <strong>👨‍💼 Responsabilités de Manager :</strong> En tant que manager, vous pouvez gérer les membres de l'équipe, créer et assigner des tâches, et suivre l'avancement du projet.
            </p>
        </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/projects/{{ $project->id }}" class="btn">
            📂 Accéder au projet
        </a>
    </div>

    <div class="divider"></div>

    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">👥 Équipe du projet</h3>

    <p style="font-size: 14px; color: #555;">
        Vous rejoignez une équipe de <strong>{{ $project->users()->count() }} personne(s)</strong>.
        Vous pouvez consulter la liste complète des membres dans l'onglet "Équipe" du projet.
    </p>

    <p style="font-size: 14px; color: #777; margin-top: 20px;">
        <strong>💡 Prochaines étapes :</strong><br>
        • Consultez les tâches qui vous sont assignées<br>
        • Familiarisez-vous avec les objectifs du projet<br>
        • Contactez les autres membres de l'équipe
    </p>
@endsection
