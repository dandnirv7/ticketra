import Alpine from 'alpinejs';
import { registerSW } from 'virtual:pwa-register';

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
    registerSW({
        immediate: true,
        onNeedRefresh() {
            console.log('Konten baru tersedia, silakan refresh.');
        },
        onOfflineReady() {
            console.log('Aplikasi siap digunakan secara offline.');
        },
    });
}
