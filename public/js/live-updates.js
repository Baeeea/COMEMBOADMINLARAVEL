// Real-time database update handler
class LiveUpdateManager {
    constructor() {
        this.eventSource = null;
        this.retryCount = 0;
        this.maxRetries = 5;
        this.isConnected = false;
        this.init();
    }

    init() {
        this.connect();
        this.setupVisibilityHandler();
    }

    connect() {
        if (this.eventSource) {
            this.eventSource.close();
        }

        this.eventSource = new EventSource('/live-updates/stream');
        
        this.eventSource.onopen = () => {
            console.log('Live updates connected');
            this.isConnected = true;
            this.retryCount = 0;
            this.updateConnectionStatus(true);
        };

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('Live update received:', data);
        };

        this.eventSource.addEventListener('database_update', (event) => {
            const data = JSON.parse(event.data);
            console.log('Database update detected:', data);
            this.handleDatabaseUpdate(data);
        });

        this.eventSource.addEventListener('keepalive', (event) => {
            console.log('Keep-alive received');
        });

        this.eventSource.onerror = (error) => {
            console.error('Live updates connection error:', error);
            this.isConnected = false;
            this.updateConnectionStatus(false);
            this.handleError();
        };
    }

    handleDatabaseUpdate(data) {
        // Show notification
        this.showUpdateNotification(data.message);
        
        // Auto-reload page after a short delay
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    showUpdateNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        `;
        
        notification.innerHTML = `
            <i class="bi bi-arrow-clockwise me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    updateConnectionStatus(connected) {
        let statusElement = document.getElementById('live-status');
        
        if (!statusElement) {
            statusElement = document.createElement('div');
            statusElement.id = 'live-status';
            statusElement.className = 'position-fixed';
            statusElement.style.cssText = `
                bottom: 20px;
                right: 20px;
                z-index: 9998;
                padding: 8px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                transition: all 0.3s ease;
            `;
            document.body.appendChild(statusElement);
        }

        if (connected) {
            statusElement.className = 'position-fixed bg-success text-white';
            statusElement.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Live Updates';
        } else {
            statusElement.className = 'position-fixed bg-danger text-white';
            statusElement.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Disconnected';
        }
    }

    handleError() {
        if (this.retryCount < this.maxRetries) {
            this.retryCount++;
            setTimeout(() => {
                console.log(`Retrying connection (${this.retryCount}/${this.maxRetries})`);
                this.connect();
            }, 3000 * this.retryCount);
        } else {
            console.error('Max retries reached. Live updates disabled.');
            this.updateConnectionStatus(false);
        }
    }

    setupVisibilityHandler() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Page is hidden, close connection
                if (this.eventSource) {
                    this.eventSource.close();
                    this.isConnected = false;
                }
            } else {
                // Page is visible, reconnect
                if (!this.isConnected) {
                    this.connect();
                }
            }
        });
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        this.isConnected = false;
        this.updateConnectionStatus(false);
    }

    // Manual trigger for testing
    triggerUpdate() {
        fetch('/live-updates/trigger', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Update triggered:', data);
        })
        .catch(error => {
            console.error('Error triggering update:', error);
        });
    }
}

// Initialize live updates when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.liveUpdateManager = new LiveUpdateManager();
});

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (window.liveUpdateManager) {
        window.liveUpdateManager.disconnect();
    }
});
