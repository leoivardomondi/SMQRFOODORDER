import axios from 'axios';

let isOrderPlaced = false;

export const setOrderPlaced = (status) => {
    isOrderPlaced = status;
};

export const triggerCartAbandonmentAlert = (store) => {
    if (isOrderPlaced) return;

    const authInfo = store.getters.authInfo || {};
    const carts = store.getters['frontendCart/lists'] || [];
    const settings = store.getters['frontendSetting/lists'] || {};
    const globalState = store.getters['globalState/lists'] || {};

    let name = authInfo.name || '';
    let phone = authInfo.phone || '';
    let email = authInfo.email || '';

    // Check session draft backup if authInfo is empty
    if (!name || !phone) {
        try {
            const rawDraft = sessionStorage.getItem('pendingCheckoutDraft');
            if (rawDraft) {
                const draft = JSON.parse(rawDraft);
                name = name || draft.form?.customer_name;
                phone = phone || draft.form?.customer_phone;
                email = email || draft.form?.customer_email;
            }
        } catch (e) {}
    }

    const branchId = globalState.branch_id || localStorage.getItem('selected_branch_id') || settings.site_default_branch || 0;

    if (!name || !phone || !carts || carts.length === 0) return;

    let subtotal = 0;
    carts.forEach(item => {
        subtotal += parseFloat(item.total || 0);
    });

    const payload = {
        customer_name: name,
        customer_phone: phone,
        customer_email: email,
        branch_id: parseInt(branchId),
        cart_items: carts,
        total: subtotal
    };

    if (navigator.sendBeacon) {
        const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
        navigator.sendBeacon('/api/frontend/cart-abandonment-alert', blob);
    } else {
        axios.post('/api/frontend/cart-abandonment-alert', payload, {
            headers: { 'x-api-key': settings.apiKey || '' }
        }).catch(() => {});
    }
};

export const initCartAbandonmentTracker = (store) => {
    window.addEventListener('beforeunload', () => {
        triggerCartAbandonmentAlert(store);
    });
};
