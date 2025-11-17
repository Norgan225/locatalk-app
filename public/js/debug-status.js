// ====================================
// 🔍 Script de Debug pour les Statuts
// ====================================

class StatusDebugger {
    constructor() {
        this.logs = [];
        this.maxLogs = 100;
    }

    init() {
        console.log('%c🔍 STATUS DEBUGGER ACTIVÉ', 'color: #10b981; font-weight: bold; font-size: 14px;');
        this.createDebugUI();
        this.monitorAPIRequests();
        this.monitorDOMChanges();
        this.monitorEvents();
    }

    createDebugUI() {
        const debugPanel = document.createElement('div');
        debugPanel.id = 'status-debug-panel';
        debugPanel.innerHTML = `
            <div style="position: fixed; bottom: 10px; right: 10px; width: 400px; max-height: 400px;
                        background: rgba(0,0,0,0.9); color: #fff; border-radius: 8px;
                        padding: 15px; font-family: monospace; font-size: 11px;
                        z-index: 999999; overflow-y: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;
                            border-bottom: 1px solid #333; padding-bottom: 8px;">
                    <span style="font-weight: bold; color: #10b981;">🔍 STATUS DEBUG</span>
                    <div>
                        <button onclick="statusDebugger.refresh()" style="background: #10b981; border: none;
                                color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                            🔄 Refresh
                        </button>
                        <button onclick="statusDebugger.clear()" style="background: #ef4444; border: none;
                                color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                            🗑️ Clear
                        </button>
                        <button onclick="document.getElementById('status-debug-panel').remove()"
                                style="background: #6b7280; border: none; color: white; padding: 4px 8px;
                                border-radius: 4px; cursor: pointer;">✕</button>
                    </div>
                </div>
                <div id="debug-logs" style="max-height: 300px; overflow-y: auto;"></div>
            </div>
        `;
        document.body.appendChild(debugPanel);
    }

    log(message, type = 'info', data = null) {
        const timestamp = new Date().toLocaleTimeString();
        const colors = {
            info: '#3b82f6',
            success: '#10b981',
            warning: '#f59e0b',
            error: '#ef4444',
            api: '#8b5cf6'
        };

        const logEntry = {
            timestamp,
            message,
            type,
            data,
            color: colors[type] || colors.info
        };

        this.logs.unshift(logEntry);
        if (this.logs.length > this.maxLogs) this.logs.pop();

        this.renderLogs();
    }

    renderLogs() {
        const container = document.getElementById('debug-logs');
        if (!container) return;

        container.innerHTML = this.logs.map(log => `
            <div style="margin-bottom: 8px; padding: 6px; background: rgba(255,255,255,0.05);
                        border-left: 3px solid ${log.color}; border-radius: 3px;">
                <div style="color: #9ca3af; font-size: 9px;">${log.timestamp}</div>
                <div style="color: ${log.color}; font-weight: bold; margin: 2px 0;">${log.message}</div>
                ${log.data ? `<pre style="color: #d1d5db; font-size: 10px; margin: 4px 0 0 0;
                              white-space: pre-wrap; word-break: break-all;">${JSON.stringify(log.data, null, 2)}</pre>` : ''}
            </div>
        `).join('');
    }

    monitorAPIRequests() {
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const url = args[0];

            if (url.includes('/api/messaging/') || url.includes('/api/status/')) {
                this.log(`📡 API Request: ${url}`, 'api');
            }

            const response = await originalFetch(...args);

            if (url.includes('/api/messaging/conversations') ||
                url.includes('/api/messaging/users') ||
                url.includes('/api/status/online')) {

                const clonedResponse = response.clone();
                try {
                    const data = await clonedResponse.json();

                    if (data.conversations) {
                        const statuses = data.conversations.map(c => ({
                            user_id: c.user_id,
                            name: c.user_name,
                            status: c.user_status
                        }));
                        this.log(`✅ Conversations loaded`, 'success', statuses);
                    }

                    if (data.users) {
                        const statuses = data.users.map(u => ({
                            user_id: u.id,
                            name: u.name,
                            status: u.status
                        }));
                        this.log(`✅ Users loaded`, 'success', statuses);
                    }

                    if (data.online_users) {
                        this.log(`✅ Online users`, 'success', data.online_users);
                    }
                } catch (e) {
                    // Not JSON response
                }
            }

            return response;
        };
    }

    monitorDOMChanges() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.classList && node.classList.contains('status-badge')) {
                        const status = Array.from(node.classList)
                            .find(c => c.startsWith('status-'))
                            ?.replace('status-', '') || 'unknown';

                        const conversationItem = node.closest('.conversation-item');
                        const userId = conversationItem?.dataset?.userId;

                        this.log(`🎨 Badge added: User ${userId} → ${status}`, 'info');
                    }
                });

                if (mutation.type === 'attributes' &&
                    mutation.target.classList.contains('status-badge')) {
                    const status = Array.from(mutation.target.classList)
                        .find(c => c.startsWith('status-'))
                        ?.replace('status-', '') || 'unknown';

                    const conversationItem = mutation.target.closest('.conversation-item');
                    const userId = conversationItem?.dataset?.userId;

                    this.log(`🔄 Badge updated: User ${userId} → ${status}`, 'warning');
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });
    }

    monitorEvents() {
        window.addEventListener('user-status-changed', (event) => {
            this.log('📢 Event: user-status-changed', 'success', event.detail);
        });
    }

    async refresh() {
        this.log('🔄 Manual refresh triggered', 'info');

        // Vérifier les statuts actuels dans le DOM
        const badges = document.querySelectorAll('.conversation-item .status-badge');
        const currentStatuses = Array.from(badges).map(badge => {
            const conversationItem = badge.closest('.conversation-item');
            const userId = conversationItem?.dataset?.userId;
            const status = Array.from(badge.classList)
                .find(c => c.startsWith('status-'))
                ?.replace('status-', '') || 'unknown';

            return { userId, status };
        });

        this.log('📋 Current statuses in DOM', 'info', currentStatuses);

        // Forcer un refresh des conversations
        if (window.messagingApp) {
            await window.messagingApp.refreshConversationStatuses();
            this.log('✅ Forced conversation refresh', 'success');
        }
    }

    clear() {
        this.logs = [];
        this.renderLogs();
        this.log('🗑️ Logs cleared', 'info');
    }
}

// Auto-init
window.statusDebugger = new StatusDebugger();
window.statusDebugger.init();
