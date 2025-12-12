<x-app-layout>
        <!-- Toast Notification -->
        <div id="toast" style="position:fixed;top:32px;right:32px;z-index:9999;min-width:220px;max-width:400px;padding:18px 32px;background:linear-gradient(135deg,#df5526,#fbbb2a);color:white;font-weight:600;border-radius:12px;box-shadow:0 4px 24px rgba(251,187,42,0.18);display:none;align-items:center;gap:12px;font-size:16px;transition:all 0.4s;">
            <span id="toast-message"></span>
            <button onclick="hideToast()" style="background:none;border:none;color:white;font-size:18px;cursor:pointer;margin-left:16px;">&times;</button>
        </div>
    <style>
        .channels-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .btn-create {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(251, 187, 42, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(251, 187, 42, 0.4);
        }

        .channel-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tab-btn {
            padding: 12px 24px;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .tab-btn:hover {
            color: rgba(255, 255, 255, 0.9);
        }

        .tab-btn.active {
            color: #fbbb2a;
            border-bottom-color: #fbbb2a;
        }

        .channels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .channel-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .channel-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(251, 187, 42, 0.3);
            transform: translateY(-2px);
        }

        .channel-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .channel-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .channel-icon-public {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2), rgba(16, 185, 129, 0.2));
        }

        .channel-icon-private {
            background: linear-gradient(135deg, rgba(251, 187, 42, 0.2), rgba(223, 85, 38, 0.2));
        }

        .channel-icon-department {
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.2), rgba(59, 130, 246, 0.2));
        }

        .channel-info {
            flex: 1;
        }

        .channel-name {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin-bottom: 4px;
        }

        .channel-type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .type-public {
            background: rgba(52, 211, 153, 0.1);
            color: #34d399;
        }

        .type-private {
            background: rgba(251, 187, 42, 0.1);
            color: #fbbb2a;
        }

        .type-department {
            background: rgba(96, 165, 250, 0.1);
            color: #60a5fa;
        }

        .channel-description {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.6;
        }

        .channel-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .members-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .members-avatars {
            display: flex;
            margin-left: -8px;
        }

        .member-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #0f172a;
            margin-left: -8px;
            background: linear-gradient(135deg, #df5526, #fbbb2a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            font-weight: 600;
        }

        .members-count {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
        }

        .channel-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid;
        }

        .action-join {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.1), rgba(16, 185, 129, 0.1));
            border-color: rgba(52, 211, 153, 0.3);
            color: #34d399;
        }

        .action-join:hover {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2), rgba(16, 185, 129, 0.2));
        }

        .action-leave {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            border-color: rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .action-leave:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
        }

        .action-joined {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
        }

        .channel-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge-member {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2), rgba(16, 185, 129, 0.2));
            border: 1px solid rgba(52, 211, 153, 0.4);
            color: #34d399;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .channels-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Header -->
    <div class="channels-header">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">{{ org_trans('channels') }}</h1>
        @if(auth()->check() && auth()->user()->canManageUsers())
        <a href="{{ route('web.channels.create') }}" class="btn-create">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ org_trans('new') }} {{ org_trans('channels') }}
        </a>
        @endif
    </div>

    <!-- Tabs -->
    <div class="channel-tabs">
        <button class="tab-btn active" data-filter="all">{{ org_trans('all') }}</button>
        <button class="tab-btn" data-filter="my">{{ org_trans('my') }} {{ org_trans('channels') }}</button>
        <button class="tab-btn" data-filter="public">{{ org_trans('public') }}</button>
        <button class="tab-btn" data-filter="private">{{ org_trans('private') }}</button>
    </div>

    <!-- Channels Grid -->
    <div class="channels-grid">
        @forelse($channels as $channel)
        <div class="channel-card"
             data-type="{{ $channel->type }}"
             data-member="{{ $channel->is_member ? 'yes' : 'no' }}"
             onclick="window.location.href='{{ route('web.channels.show', $channel->id) }}'">
            <div class="channel-header">
                <div class="channel-icon channel-icon-{{ $channel->type }}">
                    @if($channel->type === 'public')
                        <svg style="width: 24px; height: 24px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    @elseif($channel->type === 'private')
                        <svg style="width: 24px; height: 24px; color: #fbbb2a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @else
                        <svg style="width: 24px; height: 24px; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    @endif
                </div>
                <div class="channel-info">
                    <div class="channel-name">
                        {{ $channel->display_name }}
                    </div>
                    <span class="channel-type type-{{ $channel->type }}">
                        {{ $channel->type_label }}
                    </span>
                </div>
            </div>

            @if($channel->description)
            <p class="channel-description">
                {{ Str::limit($channel->description, 120) }}
            </p>
            @endif

            <div class="channel-meta">
                <div class="members-info">
                    <div class="members-avatars">
                        @php
                            $members = $channel->users->take(3);
                            $remaining = $channel->members_count - 3;
                        @endphp
                        @foreach($members as $index => $member)
                            <div class="member-avatar" style="background: linear-gradient(135deg, {{ ['#ef4444, #dc2626', '#34d399, #10b981', '#60a5fa, #3b82f6', '#a78bfa, #8b5cf6'][$index % 4] }});">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                        @endforeach
                        @if($remaining > 0)
                            <div class="member-avatar" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                +{{ $remaining }}
                            </div>
                        @endif
                    </div>
                    <span class="members-count">{{ $channel->members_count }} {{ $channel->members_count > 1 ? org_trans('members') : org_trans('member') }}</span>
                </div>

                @if($channel->is_member)
                    <button class="channel-action action-leave" onclick="event.stopPropagation(); leaveChannel({{ $channel->id }})">
                        {{ org_trans('leave') }}
                    </button>
                @else
                    <button class="channel-action action-join" onclick="event.stopPropagation(); joinChannel({{ $channel->id }})">
                        {{ org_trans('join') }}
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column: 1 / -1;">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 style="color: rgba(255, 255, 255, 0.7); margin-bottom: 8px;">{{ org_trans('no_channels_available') }}</h3>
            <p>{{ org_trans('create_first_channel') }}</p>
        </div>
        @endforelse
    </div>

    <script>
                // Toast notification system
                function showToast(message, type = 'error', duration = 3500) {
                    const toast = document.getElementById('toast');
                    const toastMessage = document.getElementById('toast-message');

                    if (!toast || !toastMessage) {
                        console.error('Toast elements not found');
                        return;
                    }

                    toastMessage.textContent = message;

                    // Reset styles
                    toast.style.background = '';
                    toast.style.boxShadow = '';

                    // Apply styles based on type
                    if (type === 'success') {
                        toast.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                        toast.style.boxShadow = '0 4px 24px rgba(16, 185, 129, 0.3)';
                    } else if (type === 'error') {
                        toast.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                        toast.style.boxShadow = '0 4px 24px rgba(239, 68, 68, 0.3)';
                    } else {
                        // Default orange style
                        toast.style.background = 'linear-gradient(135deg, #df5526, #fbbb2a)';
                        toast.style.boxShadow = '0 4px 24px rgba(251, 187, 42, 0.3)';
                    }

                    toast.style.display = 'flex';

                    // Auto-hide after duration
                    setTimeout(() => {
                        hideToast();
                    }, duration);
                }

                function showSuccessToast(message, duration = 3500) {
                    console.log('Showing success toast:', message);
                    showToast(message, 'success', duration);
                }

                function showErrorToast(message, duration = 4000) {
                    console.log('Showing error toast:', message);
                    showToast(message, 'error', duration);
                }

                function hideToast() {
                    const toast = document.getElementById('toast');
                    if (toast) {
                        toast.style.display = 'none';
                    }
                }

                function hideToast() {
                    document.getElementById('toast').style.display = 'none';
                }
        // Filtrage des canaux
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active buttons
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                const cards = document.querySelectorAll('.channel-card');

                cards.forEach(card => {
                    let show = false;

                    switch(filter) {
                        case 'all':
                            show = true;
                            break;
                        case 'my':
                            show = card.dataset.member === 'yes';
                            break;
                        case 'public':
                            show = card.dataset.type === 'public';
                            break;
                        case 'private':
                            show = card.dataset.type === 'private';
                            break;
                    }

                    card.style.display = show ? 'block' : 'none';
                });
            });
        });

        // Function to join a channel
        function joinChannel(channelId) {
            console.log('Joining channel:', channelId);
            fetch(`/channels/${channelId}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => {
                console.log('Join response:', response);
                return response.json();
            })
            .then(data => {
                console.log('Join data:', data);
                showSuccessToast(data.message || 'Vous avez rejoint le canal.');
                setTimeout(() => window.location.reload(), 1200);
            })
            .catch(error => {
                console.error('Join error:', error);
                showErrorToast('Erreur lors de la demande.');
            });
        }

        // Function to leave a channel
        function leaveChannel(channelId) {
            if (confirm('{{ org_trans('confirm_leave_channel') }}')) {
                console.log('Leaving channel:', channelId);
                fetch(`/channels/${channelId}/leave`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    console.log('Leave response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Leave data:', data);
                    // Pas de toast pour quitter - c'est moins critique que rejoindre
                    setTimeout(() => window.location.reload(), 800);
                })
                .catch(error => {
                    console.error('Leave error:', error);
                    showErrorToast('Erreur lors de la demande.');
                });
            }
        }
    </script>
    @if(session('success'))
        <script>showSuccessToast(@json(session('success')));</script>
    @endif
    @if(session('error'))
        <script>showToast(@json(session('error')), 4000);</script>
    @endif
</x-app-layout>
