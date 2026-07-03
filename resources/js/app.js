import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerSW } from 'virtual:pwa-register';

Livewire.start();

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
