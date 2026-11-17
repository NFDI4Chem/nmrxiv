import { createApp } from 'vue';
import App from './App.vue';
import { installFileProtocolIframeGuard } from './fileProtocol.js';
import './style.css';

installFileProtocolIframeGuard();

createApp(App).mount('#app');
