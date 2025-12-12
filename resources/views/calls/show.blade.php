<x-app-layout>
    <style>
        .video-room-container {
            width: 100%;
            height: calc(100vh - 140px);
            background: #000;
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
        }

        .video-controls {
            position: absolute;
            top: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1rem;
            z-index: 100;
        }

        .control-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .control-btn.danger {
            background: rgba(239, 68, 68, 0.8);
        }

        .control-btn.danger:hover {
            background: rgba(239, 68, 68, 1);
        }

        #jitsi-frame {
            width: 100%;
            height: 100%;
            border: none;
        }

        .call-info {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            color: white;
            z-index: 100;
        }

        .call-info h2 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .call-info p {
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
            opacity: 0.8;
        }
    </style>

    <div class="video-room-container">
        <div class="call-info">
            <h2>Appel avec {{ $otherUser->name }}</h2>
            <p>{{ ucfirst($call->type) }}</p>
        </div>

        <div class="video-controls">
            <a href="{{ route('web.messages') }}" class="control-btn danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.5rem;">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
                Raccrocher
            </a>
        </div>

        <iframe
            id="jitsi-frame"
            allow="camera; microphone; fullscreen; display-capture; autoplay"
            src="{{ $call->meeting_link }}"
        ></iframe>
    </div>
</x-app-layout>
