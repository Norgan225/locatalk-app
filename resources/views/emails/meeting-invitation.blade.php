@extends('emails.layout')

@section('title', 'Invitation à une réunion')

@section('content')
    <h2>📅 Nouvelle invitation à une réunion</h2>

    <p>Bonjour {{ $participant->name }},</p>

    <p>Vous avez été invité(e) à participer à une réunion organisée par <strong>{{ $meeting->organizer->name }}</strong>.</p>

    <div class="divider"></div>

    <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">📋 Détails de la réunion</h3>

    <div class="info-box">
        <p><strong>Titre :</strong> {{ $meeting->title }}</p>
        <p><strong>Organisateur :</strong> {{ $meeting->organizer->name }}</p>
        <p><strong>Date et heure :</strong> {{ $meeting->scheduled_at->format('d/m/Y à H:i') }}</p>
        <p><strong>Durée :</strong> {{ $meeting->duration }} minutes</p>
        @if($meeting->meeting_link)
            <p><strong>Lien de la réunion :</strong> <a href="{{ $meeting->meeting_link }}" style="color: #667eea;">Rejoindre</a></p>
        @endif
    </div>

    @if($meeting->description)
        <div style="margin: 20px 0;">
            <h4 style="color: #333; font-size: 16px; margin-bottom: 10px;">Description :</h4>
            <p style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; font-size: 15px;">
                {{ $meeting->description }}
            </p>
        </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/meetings/{{ $meeting->id }}/accept" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); margin-right: 10px;">
            ✅ Accepter l'invitation
        </a>
        <a href="{{ config('app.url') }}/meetings/{{ $meeting->id }}/decline" class="btn" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
            ❌ Décliner
        </a>
    </div>

    <div class="divider"></div>

    <div class="alert" style="background-color: #d1ecf1; border-color: #bee5eb;">
        <p style="color: #0c5460;">
            <strong>💡 Astuce :</strong> Ajoutez cette réunion à votre calendrier pour ne pas l'oublier !
        </p>
    </div>

    <p style="font-size: 14px; color: #777; margin-top: 20px;">
        Vous recevrez un rappel avant le début de la réunion.
    </p>
@endsection
