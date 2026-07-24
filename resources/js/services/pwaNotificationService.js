const BADGE_KEY = 'bwibo-new-order-count';
let initialised = false;

const readCount = () => Math.max(0, Number.parseInt(localStorage.getItem(BADGE_KEY) || '0', 10) || 0);

const applyBadge = async (count) => {
    if (!('setAppBadge' in navigator)) return;

    try {
        if (count > 0) await navigator.setAppBadge(count);
        else if ('clearAppBadge' in navigator) await navigator.clearAppBadge();
    } catch (_) {
        // Badging is best-effort and is not available on every PWA platform.
    }
};

const saveCount = (count) => {
    const safeCount = Math.max(0, count);
    localStorage.setItem(BADGE_KEY, String(safeCount));
    applyBadge(safeCount);
    window.dispatchEvent(new CustomEvent('pwa-order-count', { detail: safeCount }));
    return safeCount;
};

const playAlert = (audioUrl) => {
    if (!audioUrl) return;
    const audio = new Audio(audioUrl);
    audio.volume = 1;
    audio.play().catch(() => {});
};

export default {
    initialise() {
        applyBadge(readCount());
        if (initialised) return;
        initialised = true;
        navigator.serviceWorker?.addEventListener('message', (event) => {
            if (event.data?.type === 'NEW_ORDER_NOTIFICATION') {
                saveCount(Math.max(readCount() + 1, Number(event.data.count) || 0));
            } else if (event.data?.type === 'CLEAR_ORDER_NOTIFICATIONS') {
                saveCount(0);
            }
        });
    },
    newOrder(audioUrl) {
        const count = saveCount(readCount() + 1);
        navigator.vibrate?.([500, 180, 500, 180, 800]);
        playAlert(audioUrl);
        return count;
    },
    clear() {
        return saveCount(0);
    },
    count() {
        return readCount();
    }
};
