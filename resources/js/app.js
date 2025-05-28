import './bootstrap';
import { createApp } from 'vue';
import NotificationPopup from './components/NotificationPopup.vue';

const app = createApp({});

// Register the notification component
app.component('notification-popup', NotificationPopup);

// Mount the app
app.mount('#app');
