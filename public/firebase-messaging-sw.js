importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyADNjUDQW5Wq1RvR-2y3Eu552KRxz56ttk",
        authDomain: "bwibo-smqr.firebaseapp.com",
        projectId: "bwibo-smqr",
        storageBucket: "bwibo-smqr.firebasestorage.app",
        messagingSenderId: "839227996849",
        appId: "1:839227996849:web:309e15f4be4560bb394090",
        measurementId: "G-N1XXPSD5W3",
 };
firebase.initializeApp(config);
const messaging = firebase.messaging();
let backgroundOrderCount = 0;

messaging.onBackgroundMessage(async (payload) => {
    const notificationTitle = payload.notification?.title || payload.data?.title || 'New Order';
    const isNewOrder = ['new-order-found', 'new-table-order-found'].includes(payload.data?.topicName);
    if (isNewOrder) backgroundOrderCount += 1;

    const notificationOptions = {
        body: payload.notification?.body || payload.data?.body || '',
        icon: payload.notification?.image || '/images/default/firebase-logo.png',
        badge: '/images/default/firebase-logo.png',
        tag: isNewOrder ? 'bwibo-new-order' : (payload.data?.topicName || 'bwibo-notification'),
        renotify: true,
        requireInteraction: isNewOrder,
        vibrate: isNewOrder ? [500, 180, 500, 180, 800] : [250],
        data: {
            url: payload.data?.url || (
                isNewOrder && payload.data?.orderId
                    ? `/admin/online-orders/show/${payload.data.orderId}`
                    : '/admin/online-orders'
            ),
            topicName: payload.data?.topicName,
            orderId: payload.data?.orderId
        }
    };

    if (isNewOrder && self.navigator?.setAppBadge) {
        try { await self.navigator.setAppBadge(backgroundOrderCount); } catch (_) {}
    }

    const clients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
    clients.forEach(client => client.postMessage({
        type: 'NEW_ORDER_NOTIFICATION',
        count: backgroundOrderCount
    }));

    await self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    backgroundOrderCount = 0;
    if (self.navigator?.clearAppBadge) self.navigator.clearAppBadge().catch(() => {});

    const targetUrl = new URL(event.notification.data?.url || '/admin/online-orders', self.location.origin).href;
    event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
        clients.forEach(client => client.postMessage({ type: 'CLEAR_ORDER_NOTIFICATIONS' }));
        const existing = clients.find(client => client.url.startsWith(self.location.origin));
        if (existing) return existing.focus().then(() => existing.navigate(targetUrl));
        return self.clients.openWindow(targetUrl);
    }));
});
