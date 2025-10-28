importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');

// Isi sesuai firebaseConfig dari step 1
firebase.initializeApp({
    apiKey: "AIzaSyBnB-TOvyAcjHB2m6tc_5jqy3qHsMOqGLM",
    authDomain: "momatic-29f49.firebaseapp.com",
    projectId: "momatic-29f49",
    messagingSenderId: "108472346001",
    appId: "1:108472346001:web:8703302d04f20bcb0e6630"
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
