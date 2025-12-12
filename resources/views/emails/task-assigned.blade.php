@extends('emails.layout')

@section('title', 'Nouvelle tâche assignée')

@section('content')
    <h2>✅ Nouvelle tâche assignée</h2>

    <p>Bonjour {{ $assignee->name }},</p>

    <p>Une nouvelle tâche vous a été assignée dans le projet <strong>{{ $task->project->name }}</strong>.</p>

    <div class="divider"></div>

    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">📋 Détails de la tâche</h3>

    <div class="info-box">
        <p><strong>Titre :</strong> {{ $task->title }}</p>
        <p><strong>Projet :</strong> {{ $task->project->name }}</p>
        <p><strong>Priorité :</strong>
            @if($task->priority === 'high')
                <span class="highlight" style="background-color: #f8d7da; color: #721c24;">🔴 Haute</span>
            @elseif($task->priority === 'medium')
                <span class="highlight" style="background-color: #fff3cd; color: #856404;">🟡 Moyenne</span>
            @else
                <span class="highlight" style="background-color: #d4edda; color: #155724;">🟢 Basse</span>
            @endif
        </p>
        <p><strong>Statut :</strong> {{ ucfirst(str_replace('_', ' ', $task->status)) }}</p>
        @if($task->due_date)
            <p><strong>Date limite :</strong> {{ $task->due_date->format('d/m/Y') }}
                @if($task->due_date->isPast())
                    <span style="color: #dc3545; font-weight: 600;">(En retard)</span>
                @elseif($task->due_date->diffInDays(now()) <= 2)
                    <span style="color: #ffc107; font-weight: 600;">(Urgent)</span>
                @endif
            </p>
        @endif
    </div>

    @if($task->description)
        <div style="margin: 20px 0;">
            <h4 style="color: #333; font-size: 16px; margin-bottom: 10px;">Description :</h4>
            <p style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; font-size: 15px;">
                {{ $task->description }}
            </p>
        </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/tasks/{{ $task->id }}" class="btn">
            📝 Voir la tâche
        </a>
    </div>

    <div class="divider"></div>

    @if($task->priority === 'high')
        <div class="alert" style="background-color: #f8d7da; border-color: #f5c6cb;">
            <p style="color: #721c24;">
                <strong>⚠️ Attention :</strong> Cette tâche a une priorité HAUTE. Veuillez la traiter rapidement.
            </p>
        </div>
    @endif

    <p style="font-size: 14px; color: #777; margin-top: 20px;">
        <strong>💡 Conseil :</strong> N'oubliez pas de mettre à jour le statut de la tâche au fur et à mesure de votre progression.
    </p>
@endsection
