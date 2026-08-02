let installPrompt = null;
const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;

const inheritStorefrontTheme = (element) => {
    const storefront = document.querySelector('.frontend-theme');
    if (!storefront) return;
    const theme = window.getComputedStyle(storefront);
    [
        '--store-primary', '--store-primary-hover', '--store-button-text',
        '--store-page-bg', '--store-surface', '--store-heading',
        '--store-body-text', '--store-border'
    ].forEach((property) => element.style.setProperty(property, theme.getPropertyValue(property)));
};

const showIosInstallGuide = () => {
    document.querySelector('#ios-pwa-guide')?.remove();
    const guide = document.createElement('div');
    guide.id = 'ios-pwa-guide';
    guide.setAttribute('role', 'dialog');
    guide.setAttribute('aria-modal', 'true');
    inheritStorefrontTheme(guide);
    guide.innerHTML = `
        <div class="ios-pwa-card">
            <button class="ios-pwa-close" type="button" aria-label="Close">&times;</button>
            <div class="ios-pwa-icon">&#8679;</div>
            <h2>Install Bwibo</h2>
            <p>In Safari, tap the <strong>Share</strong> button at the bottom of the screen.</p>
            <p>Scroll down and tap <strong>Add to Home Screen</strong>, then tap <strong>Add</strong>.</p>
            <small>Older iPhones can install the app this way. Push notifications require iOS 16.4 or newer.</small>
        </div>`;
    document.body.appendChild(guide);
    guide.querySelector('.ios-pwa-close').addEventListener('click', () => guide.remove());
    guide.addEventListener('click', (event) => { if (event.target === guide) guide.remove(); });
};

const handleInstall = async (event) => {
    if (isIos && !isStandalone) {
        event?.preventDefault();
        event?.stopImmediatePropagation();
        showIosInstallGuide();
        return;
    }
    if (!installPrompt) return;
    await installPrompt.prompt();
    installPrompt = null;
};

const addIosNotificationButton = () => {
    if (!isIos || !isStandalone || !('Notification' in window) || Notification.permission !== 'default') return;
    const button = document.createElement('button');
    button.id = 'enable-ios-notifications';
    button.type = 'button';
    button.textContent = 'Enable order notifications';
    inheritStorefrontTheme(button);
    button.addEventListener('click', async () => {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            button.textContent = 'Notifications enabled';
            setTimeout(() => window.location.reload(), 500);
        }
    });
    document.body.appendChild(button);
};

const styles = document.createElement('style');
styles.textContent = `
    #ios-pwa-guide{position:fixed;inset:0;z-index:99999;display:flex;align-items:flex-end;justify-content:center;padding:18px;background:rgba(0,0,0,.72)}
    .ios-pwa-card{position:relative;width:min(100%,380px);padding:28px 22px 24px;border:1px solid var(--store-border,#cdaa5d);border-radius:20px;background:var(--store-surface,#111);color:var(--store-body-text,#ddd);text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.5)}
    .ios-pwa-card h2{margin:4px 0 14px;color:var(--store-heading,#fff);font-size:22px}.ios-pwa-card p{margin:10px 0;line-height:1.5}.ios-pwa-card strong,.ios-pwa-icon{color:var(--store-primary-hover,#d4af60)}.ios-pwa-card small{display:block;margin-top:16px;color:var(--store-body-text,#ddd);line-height:1.4}
    .ios-pwa-close{position:absolute;right:14px;top:10px;padding:5px;border:0;background:transparent;color:var(--store-heading,#fff);font-size:28px}.ios-pwa-icon{font-size:34px}
    #enable-ios-notifications{position:fixed;right:14px;bottom:82px;z-index:9998;padding:12px 16px;border:1px solid var(--store-border,#e0c176);border-radius:999px;background:var(--store-primary,#cdaa5d);color:var(--store-button-text,#080808);font-weight:700;box-shadow:0 8px 24px rgba(0,0,0,.35)}
`;
document.head.appendChild(styles);
window.addEventListener("beforeinstallprompt", (event) => {
    event.preventDefault();
    installPrompt = event;
});
document.addEventListener("click", (event) => {
    if (event.target.closest("#installPWA, #installPWAsm")) {
        handleInstall(event);
    }
}, true);
window.addEventListener('load', addIosNotificationButton);
