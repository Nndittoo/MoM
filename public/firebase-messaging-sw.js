importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');


firebase.initializeApp({
    apiKey: "{{ config('services.firebase.api_key') }}",
    authDomain: "{{ config('services.firebase.auth_domain') }}",
    projectId: "{{ config('services.firebase.project_id') }}",
    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
    appId: "{{ config('services.firebase.app_id') }}"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('Received background message ', payload);

    const notificationTitle = payload.notification.title;

    // Tentukan icon berdasarkan tipe notifikasi
    let icon = '/logo.png';
    let badge = '/logo.png';

    if (payload.data.type === 'task_overdue' || payload.data.type === 'task_urgent') {
        badge = '/alert-badge.png'; // Opsional: gunakan badge khusus untuk task
    }

    const notificationOptions = {
        body: payload.notification.body,
        icon: icon,
        badge: badge,
        data: payload.data,
        requireInteraction: payload.data.type === 'task_overdue', // Require interaction untuk overdue tasks
        vibrate: [200, 100, 200], // Pattern vibrasi
        tag: payload.data.type + '_' + payload.data.related_id, // Prevent duplicate notifications
        renotify: true
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const data = event.notification.data;
    let url = '/';

    // Redirect berdasarkan tipe notifikasi
    if (data.type === 'task_urgent' || data.type === 'task_overdue') {
        url = data.redirect_url || '/admin/tasks';
    } else if (data.type === 'mom_created' || data.type === 'mom_pending') {
        url = data.redirect_url || `/admin/moms/${data.related_id || data.mom_id}`;
    } else if (data.type === 'mom_approved' || data.type === 'mom_rejected') {
        url = `/moms/${data.mom_id}`;
    } else if (data.redirect_url) {
        url = data.redirect_url;
    }

    event.waitUntil(
        clients.openWindow(url)
    );
});
