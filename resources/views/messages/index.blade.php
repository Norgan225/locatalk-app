<x-app-layout>
    <!-- Header avec bouton vers interface moderne -->
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="color: white; font-size: 24px; font-weight: 700; margin-bottom: 8px;">💬 Messages</h1>
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 14px;">Interface de messagerie classique</p>
        </div>
        <a href="{{ route('web.messages.modern') }}"
           style="background: linear-gradient(135deg, #fbbb2a, #df5526);
                  color: white;
                  padding: 12px 24px;
                  border-radius: 12px;
                  text-decoration: none;
                  font-weight: 600;
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  transition: transform 0.2s ease;
                  box-shadow: 0 4px 12px rgba(251, 187, 42, 0.3);"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            ✨ Nouvelle Interface Moderne
        </a>
    </div>

    <style>
        .messages-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 0;
            height: calc(100vh - 220px);
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }

        .conversations-sidebar {
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 12px;
        }

        .search-box {
            width: 100%;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 16px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }

        .conversation-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .conversation-item.active {
            background: linear-gradient(90deg, rgba(223, 85, 38, 0.15), rgba(251, 187, 42, 0.15));
            border-left: 3px solid #fbbb2a;
        }

        .conversation-avatar {
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
            margin-bottom: 8px;
        }

        .conversation-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .conversation-name {
            font-weight: 600;
            color: white;
            margin-bottom: 4px;
        }

        .conversation-preview {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.4);
        }

        .unread-badge {
            position: absolute;
            top: 16px;
            right: 20px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Chat Area */
        .chat-area {
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-user-name {
            font-size: 18px;
            font-weight: 700;
            color: white;
        }

        .chat-user-status {
            font-size: 13px;
            color: #34d399;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .message {
            display: flex;
            gap: 12px;
            max-width: 70%;
        }

        .message.sent {
            margin-left: auto;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 36px;
            height: 36px;
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

        .message-content {
            flex: 1;
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .message-sender {
            font-weight: 600;
            color: white;
            font-size: 14px;
        }

        .message-time {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.4);
        }

        .message-bubble {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px 16px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            line-height: 1.5;
        }

        .message.sent .message-bubble {
            background: linear-gradient(135deg, rgba(223, 85, 38, 0.3), rgba(251, 187, 42, 0.3));
        }

        /* Message Input */
        .chat-input-container {
            padding: 20px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chat-input-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .chat-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
            resize: none;
            min-height: 44px;
            max-height: 120px;
        }

        .chat-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-send {
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(223, 85, 38, 0.4);
        }

        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: rgba(255, 255, 255, 0.4);
            text-align: center;
            padding: 40px;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>

    <h1 style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 24px;">Messages</h1>

    <div class="messages-container">
        <!-- Conversations Sidebar -->
        <div class="conversations-sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Conversations</h2>
                <input type="text" class="search-box" placeholder="Rechercher...">
            </div>

            <div class="conversations-list">
                <div class="conversation-item active">
                    <div style="display: flex; gap: 12px;">
                        <div class="conversation-avatar">ML</div>
                        <div style="flex: 1;">
                            <div class="conversation-info">
                                <div>
                                    <div class="conversation-name">Marie Leclerc</div>
                                    <div class="conversation-preview">Super, on se voit demain !</div>
                                </div>
                                <div class="conversation-time">14:32</div>
                            </div>
                        </div>
                    </div>
                    <div class="unread-badge">3</div>
                </div>

                <div class="conversation-item">
                    <div style="display: flex; gap: 12px;">
                        <div class="conversation-avatar" style="background: linear-gradient(135deg, #60a5fa, #a78bfa);">JD</div>
                        <div style="flex: 1;">
                            <div class="conversation-info">
                                <div>
                                    <div class="conversation-name">Jean Dupont</div>
                                    <div class="conversation-preview">Le projet avance bien !</div>
                                </div>
                                <div class="conversation-time">Hier</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="conversation-item">
                    <div style="display: flex; gap: 12px;">
                        <div class="conversation-avatar" style="background: linear-gradient(135deg, #34d399, #10b981);">PB</div>
                        <div style="flex: 1;">
                            <div class="conversation-info">
                                <div>
                                    <div class="conversation-name">Pierre Bernard</div>
                                    <div class="conversation-preview">Merci pour ton aide !</div>
                                </div>
                                <div class="conversation-time">Lun</div>
                            </div>
                        </div>
                    </div>
                    <div class="unread-badge">1</div>
                </div>

                <div class="conversation-item">
                    <div style="display: flex; gap: 12px;">
                        <div class="conversation-avatar" style="background: linear-gradient(135deg, #f87171, #ef4444);">SD</div>
                        <div style="flex: 1;">
                            <div class="conversation-info">
                                <div>
                                    <div class="conversation-name">Sophie Durand</div>
                                    <div class="conversation-preview">Ok parfait 👍</div>
                                </div>
                                <div class="conversation-time">Dim</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                <div class="chat-user-info">
                    <div class="conversation-avatar">ML</div>
                    <div>
                        <div class="chat-user-name">Marie Leclerc</div>
                        <div class="chat-user-status">● En ligne</div>
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 10px; cursor: pointer;">
                        <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </button>
                    <button style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 10px; cursor: pointer;">
                        <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="chat-messages">
                <div class="message">
                    <div class="message-avatar">ML</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">Marie Leclerc</span>
                            <span class="message-time">14:20</span>
                        </div>
                        <div class="message-bubble">
                            Salut ! J'ai terminé la révision du design. Tu peux y jeter un œil ?
                        </div>
                    </div>
                </div>

                <div class="message sent">
                    <div class="message-avatar">Moi</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-time">14:25</span>
                            <span class="message-sender">Vous</span>
                        </div>
                        <div class="message-bubble">
                            Oui bien sûr ! Je regarde ça maintenant 👀
                        </div>
                    </div>
                </div>

                <div class="message">
                    <div class="message-avatar">ML</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">Marie Leclerc</span>
                            <span class="message-time">14:30</span>
                        </div>
                        <div class="message-bubble">
                            Génial ! J'attends ton feedback. J'ai fait quelques ajustements sur les couleurs.
                        </div>
                    </div>
                </div>

                <div class="message sent">
                    <div class="message-avatar">Moi</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-time">14:32</span>
                            <span class="message-sender">Vous</span>
                        </div>
                        <div class="message-bubble">
                            Super, on se voit demain pour en discuter plus en détail ! 🚀
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-input-container">
                <div class="chat-input-wrapper">
                    <button style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 12px; cursor: pointer;">
                        <svg style="width: 20px; height: 20px; color: rgba(255, 255, 255, 0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </button>
                    <textarea class="chat-input" placeholder="Écrivez votre message..." rows="1"></textarea>
                    <button class="btn-send">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
