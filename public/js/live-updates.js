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
        };        this.eventSource.addEventListener('database_update', (event) => {
            const data = JSON.parse(event.data);
            console.log('Database update detected:', data);
            this.handleDatabaseUpdate(data);
        });

        this.eventSource.addEventListener('table_update', (event) => {
            const data = JSON.parse(event.data);
            console.log('Table update detected:', data);
            this.handleTableUpdate(data);
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
    }    handleDatabaseUpdate(data) {
        // Show notification with more details
        const message = data.details && data.details.message 
            ? data.details.message 
            : 'Database updated - refreshing content...';
        
        this.showUpdateNotification(message, data.details);
        
        // Auto-reload page after a short delay (reduced from 1000ms to 500ms for faster response)
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    handleTableUpdate(data) {
        // Handle specific table updates without full page reload for some cases
        const message = `${data.message} - updating...`;
        this.showUpdateNotification(message, { table: data.table });
        
        // For resident table updates, try to update specific elements first
        if (data.table === 'residents' && this.canUpdateInPlace()) {
            this.updateResidentElements();
            // Still reload after a delay to ensure consistency
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // For other tables or if in-place update isn't possible, reload faster
            setTimeout(() => {
                window.location.reload();
            }, 300);
        }
    }

    canUpdateInPlace() {
        // Check if we're on a resident page where we can update elements in place
        return window.location.pathname.includes('/residents/') || 
               document.querySelector('#residents-table') !== null ||
               document.querySelector('.resident-status') !== null;
    }

    updateResidentElements() {
        // Try to update resident status elements without full reload
        const statusElements = document.querySelectorAll('.resident-status, [class*="text-success"], [class*="text-danger"]');
        statusElements.forEach(element => {
            if (element.textContent.includes('Verified') || element.textContent.includes('Not Verified')) {
                element.style.opacity = '0.6';
                element.innerHTML += ' <i class="bi bi-arrow-clockwise text-primary"></i>';
            }
        });
    }    showUpdateNotification(message, details = {}) {
        // Create notification element with enhanced styling
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            max-width: 400px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
            border-left: 4px solid #17a2b8;
            animation: slideInRight 0.3s ease-out;
        `;
        
        let detailsHtml = '';
        if (details.table) {
            detailsHtml = `<small class="d-block mt-1 text-muted">Table: ${details.table}</small>`;
        }
        if (details.updated_fields && details.updated_fields.length > 0) {
            detailsHtml += `<small class="d-block text-muted">Updated: ${details.updated_fields.join(', ')}</small>`;
        }
        
        notification.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="bi bi-arrow-clockwise me-2 text-info" style="font-size: 1.1em; margin-top: 2px;"></i>
                <div class="flex-grow-1">
                    <strong>${message}</strong>
                    ${detailsHtml}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 4 seconds (reduced from 5)
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }
        }, 4000);
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
