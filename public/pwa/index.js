let installPrompt = null;
const installButton = document.querySelector("#installPWA");
const installButtonSM = document.querySelector("#installPWAsm");
const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;

const showIosInstallGuide = () => {
    document.querySelector('#ios-pwa-guide')?.remove();
    const guide = document.createElement('div');
    guide.id = 'ios-pwa-guide';
    guide.setAttribute('role', 'dialog');
    guide.setAttribute('aria-modal', 'true');
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
    .ios-pwa-card{position:relative;width:min(100%,380px);padding:28px 22px 24px;border:1px solid #cdaa5d;border-radius:20px;background:#111;color:#fff;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.5)}
    .ios-pwa-card h2{margin:4px 0 14px;font-size:22px}.ios-pwa-card p{margin:10px 0;line-height:1.5}.ios-pwa-card strong,.ios-pwa-icon{color:#d4af60}.ios-pwa-card small{display:block;margin-top:16px;color:#ddd;line-height:1.4}
    .ios-pwa-close{position:absolute;right:14px;top:10px;padding:5px;border:0;background:transparent;color:#fff;font-size:28px}.ios-pwa-icon{font-size:34px}
    #enable-ios-notifications{position:fixed;right:14px;bottom:82px;z-index:9998;padding:12px 16px;border:1px solid #e0c176;border-radius:999px;background:#cdaa5d;color:#080808;font-weight:700;box-shadow:0 8px 24px rgba(0,0,0,.35)}
`;
document.head.appendChild(styles);
window.addEventListener("beforeinstallprompt", (event) => {
    event.preventDefault();
    installPrompt = event;
});
installButton?.addEventListener("click", handleInstall, true);
installButtonSM?.addEventListener("click", handleInstall, true);
window.addEventListener('load', addIosNotificationButton);
