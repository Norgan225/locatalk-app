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

        #daily-frame {
            width: 100%;
            height: 100%;
            border: none;
        }

        .meeting-info {
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

        .meeting-info h2 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .meeting-info p {
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
            opacity: 0.8;
        }
    </style>

    <div class="video-room-container">
        <div class="meeting-info">
            <h2>{{ $meeting->title }}</h2>
            <p>{{ $meeting->participants->count() }} participant(s)</p>
        </div>

        <div class="video-controls">
            <a href="{{ route('web.meetings') }}" class="control-btn danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.5rem;">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
                Quitter la réunion
            </a>
        </div>

        <iframe
            id="daily-frame"
            allow="camera; microphone; fullscreen; display-capture; autoplay"
            src="{{ $meeting->meeting_link }}"
        ></iframe>
    </div>

    <script>
        // Optional: Daily.js SDK integration for more control
        // You can install @daily-co/daily-js via npm if needed
    </script>
</x-app-layout>
