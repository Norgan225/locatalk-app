@extends('emails.layout')

@section('title', 'Nouveau message')

@section('content')
    <h2>💬 Nouveau message reçu</h2>

    <p>Bonjour {{ $recipient->name }},</p>

    <p>Vous avez reçu un nouveau message de <strong>{{ $message->sender->name }}</strong>
        @if($message->channel_id)
            dans le canal <strong>{{ $message->channel->name }}</strong>.
        @else
            en message direct.
        @endif
    </p>

    <div class="divider"></div>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin: 20px 0;">
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px; margin-right: 15px;">
                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #333;">{{ $message->sender->name }}</p>
                <p style="margin: 0; font-size: 13px; color: #777;">{{ $message->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        <p style="margin: 0; color: #333; font-size: 15px; line-height: 1.6;">
            {{ Str::limit($message->content, 200) }}
        </p>

        @if($message->attachments && count(json_decode($message->attachments, true)) > 0)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                <p style="margin: 0; font-size: 14px; color: #555;">
                    📎 <strong>{{ count(json_decode($message->attachments, true)) }} pièce(s) jointe(s)</strong>
                </p>
            </div>
        @endif
    </div>

    <div style="text-align: center; margin: 30px 0;">
        @if($message->channel_id)
            <a href="{{ config('app.url') }}/channels/{{ $message->channel_id }}" class="btn">
                💬 Voir le message dans le canal
            </a>
        @else
            <a href="{{ config('app.url') }}/messages?user={{ $message->sender_id }}" class="btn">
                💬 Répondre au message
            </a>
        @endif
    </div>

    <div class="divider"></div>

    @if($message->channel_id)
        <div class="info-box">
            <p><strong>📢 Canal :</strong> {{ $message->channel->name }}</p>
            <p><strong>Type :</strong> {{ ucfirst($message->channel->type) }}</p>
            @if($message->channel->description)
                <p><strong>Description :</strong> {{ $message->channel->description }}</p>
            @endif
        </div>
    @endif

    <p style="font-size: 13px; color: #999; margin-top: 20px; text-align: center;">
        Pour ne plus recevoir de notifications par email pour les messages, vous pouvez ajuster vos préférences dans les paramètres de votre compte.
    </p>
@endsection
