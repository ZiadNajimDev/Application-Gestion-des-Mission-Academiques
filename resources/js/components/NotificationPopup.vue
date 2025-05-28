<template>
    <div class="notification-container">
        <transition-group name="notification" tag="div">
            <div v-for="notification in notifications" 
                 :key="notification.id" 
                 class="notification-popup"
                 :class="notification.type">
                <div class="notification-icon">
                    <i :class="'fas ' + notification.icon"></i>
                </div>
                <div class="notification-content">
                    <h4>{{ notification.title }}</h4>
                    <p>{{ notification.message }}</p>
                </div>
                <button class="notification-close" @click="removeNotification(notification.id)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </transition-group>
    </div>
</template>

<script>
export default {
    data() {
        return {
            notifications: [],
            notificationId: 0
        }
    },
    mounted() {
        console.log('NotificationPopup mounted');
        console.log('User ID:', window.userId);
        
        if (!window.Echo) {
            console.error('Laravel Echo is not initialized');
            return;
        }

        try {
            // Listen for new notifications
            window.Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    console.log('Received notification:', notification);
                    this.addNotification(notification);
                });

            console.log('Successfully subscribed to notifications channel');
        } catch (error) {
            console.error('Error setting up notification listener:', error);
        }
    },
    methods: {
        addNotification(notification) {
            console.log('Adding notification:', notification);
            const id = this.notificationId++;
            this.notifications.push({
                id,
                ...notification
            });

            // Remove notification after 10 seconds
            setTimeout(() => {
                this.removeNotification(id);
            }, 10000);
        },
        removeNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications.splice(index, 1);
            }
        }
    }
}
</script>

<style scoped>
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notification-popup {
    background: white;
    border-radius: 8px;
    padding: 15px;
    min-width: 300px;
    max-width: 400px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: flex-start;
    gap: 12px;
    position: relative;
}

.notification-icon {
    font-size: 20px;
    width: 24px;
    text-align: center;
}

.notification-content {
    flex: 1;
}

.notification-content h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
}

.notification-content p {
    margin: 0;
    font-size: 14px;
    color: #666;
}

.notification-close {
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
}

.notification-close:hover {
    color: #666;
}

/* Type-specific styles */
.notification-popup.success {
    border-left: 4px solid #28a745;
}

.notification-popup.success .notification-icon {
    color: #28a745;
}

.notification-popup.warning {
    border-left: 4px solid #ffc107;
}

.notification-popup.warning .notification-icon {
    color: #ffc107;
}

.notification-popup.danger {
    border-left: 4px solid #dc3545;
}

.notification-popup.danger .notification-icon {
    color: #dc3545;
}

.notification-popup.info {
    border-left: 4px solid #17a2b8;
}

.notification-popup.info .notification-icon {
    color: #17a2b8;
}

/* Animation */
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}

.notification-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.notification-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style> 