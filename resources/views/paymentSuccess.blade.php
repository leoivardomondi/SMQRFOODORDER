<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company['company_name'] }} - Payment Successful</title>
    <link rel="icon" href="{{ $faviconLogo->faviconLogo }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/lab/lab.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
    <style>
        :root {
            --payment-primary: {{ $theme['theme_primary_color'] ?? '#008BBA' }};
            --payment-button-text: {{ $theme['theme_button_text_color'] ?? '#ffffff' }};
        }

        body.payment-success-page {
            margin: 0;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Light Theme Styles */
        body.payment-success-page.light-mode {
            background-color: #f8fafc;
            color: #334155;
        }
        body.payment-success-page.light-mode .payment-card-bg {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        body.payment-success-page.light-mode .payment-heading-text {
            color: #0f172a;
        }
        body.payment-success-page.light-mode .payment-subtext {
            color: #64748b;
        }
        body.payment-success-page.light-mode .payment-tx-label {
            background-color: #f1f5f9;
            color: #475569;
        }
        body.payment-success-page.light-mode .payment-tx-val {
            background-color: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-top: 0;
        }
        body.payment-success-page.light-mode .theme-btn {
            background-color: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        /* Dark Theme Styles */
        body.payment-success-page.dark-mode {
            background-color: #080b11;
            color: #cbd5e1;
        }
        body.payment-success-page.dark-mode .payment-card-bg {
            background-color: #111827;
            border: 1px solid #1e293b;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        body.payment-success-page.dark-mode .payment-heading-text {
            color: #f8fafc;
        }
        body.payment-success-page.dark-mode .payment-subtext {
            color: #94a3b8;
        }
        body.payment-success-page.dark-mode .payment-tx-label {
            background-color: #1e293b;
            color: #94a3b8;
        }
        body.payment-success-page.dark-mode .payment-tx-val {
            background-color: #0f172a;
            color: #f8fafc;
            border: 1px solid #1e293b;
            border-top: 0;
        }
        body.payment-success-page.dark-mode .theme-btn {
            background-color: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }

        .whatsapp-paid-order-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-bottom: 14px;
            padding: 14px 20px;
            border-radius: 9999px;
            background: #25D366;
            color: #ffffff;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);
            border: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .whatsapp-paid-order-btn:hover,
        .whatsapp-paid-order-btn:focus {
            background: #1ebe5d;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
            color: #ffffff;
        }

        .my-orders-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 9999px;
            background: var(--payment-primary);
            color: var(--payment-button-text);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .my-orders-icon-btn:hover,
        .my-orders-icon-btn:focus {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.28);
            color: var(--payment-button-text);
        }

        .share-status {
            min-height: 20px;
            margin: -4px 0 12px;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body class="payment-success-page dark-mode" id="page-body">

<div class="fixed top-4 right-4 z-50">
    <button id="theme-toggle-btn" type="button" class="theme-btn px-3.5 py-2 rounded-full text-xs font-bold flex items-center gap-2 transition cursor-pointer">
        <span id="theme-icon">🌙</span>
        <span id="theme-text">Dark</span>
    </button>
</div>

<div class="py-12 px-4 w-full flex items-center justify-center">
    <div class="payment-card-bg p-8 sm:p-10 rounded-3xl w-full max-w-md flex flex-col items-center justify-center text-center">
        <a href="{{ route('home') }}" class="w-32 mb-6">
            <img class="w-full h-auto object-contain" src="{{ $logo->logo }}" alt="logo">
        </a>

        <img class="w-24 h-24 mb-4 object-contain" src="{{ asset('images/default/payment-success.gif') }}" alt="success">

        <h3 class="text-2xl font-bold mb-6 payment-heading-text">
            <span class="block text-xl font-medium text-amber-500 mb-1">{{ __('all.label.congratulations') }}</span>
            {{ __('all.message.payment_successful') }}
        </h3>

        <div class="w-full mb-6">
            <dl class="text-center w-full">
                <dt class="payment-tx-label uppercase py-2.5 px-4 rounded-t-xl text-xs font-bold tracking-wider">{{ __('all.label.transaction_id') }}</dt>
                <dd class="payment-tx-val uppercase py-3 px-4 rounded-b-xl text-sm font-bold tracking-widest">{{ $order?->transaction?->transaction_no }}</dd>
            </dl>
        </div>

        @if($whatsappUrl)
            <p class="payment-subtext text-sm font-medium mb-3">Send your order details to WhatsApp to get direct updates on your order.</p>
            <button id="whatsapp-route" type="button" class="whatsapp-paid-order-btn">
                <span aria-hidden="true" class="text-lg">&#128172;</span> Send to WhatsApp for Direct Updates
            </button>
            <p id="share-status" class="share-status payment-subtext" role="status" aria-live="polite"></p>
        @endif

        <div class="mt-3 flex flex-col items-center gap-1.5">
            <a id="my-orders-route" href="{{ url('/#/my-orders') }}" title="My Orders" aria-label="My Orders" class="my-orders-icon-btn">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </a>
            <span class="text-[11px] font-semibold uppercase tracking-wider payment-subtext">My Orders</span>
        </div>
    </div>
</div>

<script>
    const pageBody = document.getElementById('page-body');
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');

    function applyTheme(mode) {
        if (mode === 'light') {
            pageBody.classList.remove('dark-mode');
            pageBody.classList.add('light-mode');
            if (themeIcon) themeIcon.textContent = '☀️';
            if (themeText) themeText.textContent = 'Light';
        } else {
            pageBody.classList.remove('light-mode');
            pageBody.classList.add('dark-mode');
            if (themeIcon) themeIcon.textContent = '🌙';
            if (themeText) themeText.textContent = 'Dark';
        }
    }

    const savedTheme = localStorage.getItem('payment_theme_mode') || 'dark';
    applyTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isDark = pageBody.classList.contains('dark-mode');
            const nextTheme = isDark ? 'light' : 'dark';
            localStorage.setItem('payment_theme_mode', nextTheme);
            applyTheme(nextTheme);
        });
    }

    // The order is confirmed, so a retained checkout from the payment attempt
    // must not appear as a new basket when the customer returns to the app.
    sessionStorage.removeItem('pendingCheckoutDraft');
    try {
        const persistedState = JSON.parse(localStorage.getItem('vuex'));
        if (persistedState && persistedState.frontendCart) {
            persistedState.frontendCart.lists = [];
            persistedState.frontendCart.subtotal = 0;
            persistedState.frontendCart.coupon = {};
            persistedState.frontendCart.timeSlot = {};
            localStorage.setItem('vuex', JSON.stringify(persistedState));
        }
    } catch (error) {
        // A malformed/stale persisted state should not block the receipt page.
    }

    const shareButton = document.getElementById('whatsapp-route');
    const shareStatus = document.getElementById('share-status');
    const receiptLines = @json($receiptLines ?? []);
    const textWhatsappUrl = @json($whatsappUrl);
    const receiptStoreUrl = @json($receiptStoreUrl ?? '');
    const receiptLogoUrl = @json($logo->logo);
    const brandName = @json($company['company_name'] ?? 'Restaurant');
    const brandPrimary = @json($theme['theme_primary_color'] ?? '#0f766e');
    const brandHeading = @json($theme['theme_heading_color'] ?? '#1f1f39');

    function wrapReceiptLine(context, text, maxWidth) {
        if (!text) return [''];
        const words = String(text).split(/\s+/);
        const lines = [];
        let current = '';
        words.forEach((word) => {
            const candidate = current ? `${current} ${word}` : word;
            if (context.measureText(candidate).width <= maxWidth || !current) {
                current = candidate;
            } else {
                lines.push(current);
                current = word;
            }
        });
        if (current) lines.push(current);
        return lines;
    }

    function loadReceiptLogo() {
        return new Promise((resolve) => {
            const logo = new Image();
            logo.crossOrigin = 'anonymous';
            logo.onload = () => resolve(logo);
            logo.onerror = () => resolve(null);
            logo.src = receiptLogoUrl;
        });
    }

    function roundedRect(context, x, y, width, height, radius) {
        context.beginPath();
        context.moveTo(x + radius, y);
        context.lineTo(x + width - radius, y);
        context.quadraticCurveTo(x + width, y, x + width, y + radius);
        context.lineTo(x + width, y + height - radius);
        context.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        context.lineTo(x + radius, y + height);
        context.quadraticCurveTo(x, y + height, x, y + height - radius);
        context.lineTo(x, y + radius);
        context.quadraticCurveTo(x, y, x + radius, y);
        context.closePath();
    }

    async function createReceiptBlob() {
        const width = 720;
        const padding = 48;
        const lineHeight = 50;
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        context.font = '500 31px Rubik, Arial, sans-serif';

        // URLs remain clickable in the WhatsApp message and do not need to be
        // printed as tiny, unreadable text inside the receipt image.
        const printableReceiptLines = receiptLines.filter((line) => {
            const value = String(line);
            return !/^https?:\/\//i.test(value)
                && !/^[*-]+$/.test(value)
                && value !== 'View receipt and open order in Admin:'
                && !value.startsWith('PAID ORDER -')
                && value !== 'Payment status: PAID';
        });
        const wrappedLines = printableReceiptLines.reduce((allLines, line) => {
            return allLines.concat(wrapReceiptLine(context, line, width - (padding * 2)));
        }, []);
        const headerHeight = 350;
        const footerHeight = 140;
        canvas.width = width;
        canvas.height = headerHeight + footerHeight + (wrappedLines.length * lineHeight);

        context.fillStyle = '#eee9dc';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = '#ffffff';
        context.fillRect(14, 14, canvas.width - 28, canvas.height - 28);
        context.strokeStyle = brandPrimary;
        context.lineWidth = 3;
        context.strokeRect(14, 14, canvas.width - 28, canvas.height - 28);
        context.fillStyle = brandPrimary;
        context.fillRect(14, 14, canvas.width - 28, 14);

        const logo = await loadReceiptLogo();
        if (logo) {
            const scale = Math.min(128 / logo.width, 128 / logo.height);
            const logoWidth = logo.width * scale;
            const logoHeight = logo.height * scale;
            context.drawImage(logo, (width - logoWidth) / 2, 45, logoWidth, logoHeight);
        } else {
            context.fillStyle = '#090909';
            context.beginPath();
            context.arc(width / 2, 105, 60, 0, Math.PI * 2);
            context.fill();
            context.strokeStyle = brandPrimary;
            context.lineWidth = 4;
            context.stroke();
            context.fillStyle = '#ffffff';
            context.textAlign = 'center';
            context.font = '700 27px Rubik, Arial, sans-serif';
            context.fillText(brandName, width / 2, 114);
        }

        context.textAlign = 'center';
        context.fillStyle = brandHeading;
        context.font = '800 38px Rubik, Arial, sans-serif';
        context.fillText('PAYMENT RECEIPT', width / 2, 213);
        context.fillStyle = '#187a3d';
        roundedRect(context, width / 2 - 132, 235, 264, 58, 29);
        context.fill();
        context.fillStyle = '#ffffff';
        context.font = '800 31px Rubik, Arial, sans-serif';
        context.fillText('PAID', width / 2, 274);
        context.strokeStyle = brandPrimary;
        context.lineWidth = 2;
        context.setLineDash([10, 9]);
        context.beginPath();
        context.moveTo(padding, 324);
        context.lineTo(width - padding, 324);
        context.stroke();
        context.setLineDash([]);

        context.textAlign = 'left';
        context.font = '500 31px Rubik, Arial, sans-serif';
        let y = headerHeight;
        wrappedLines.forEach((line) => {
            const isHeading = line === 'ORDER DETAILS' || line === 'CUSTOMER';
            const isImportant = line.startsWith('Order: #') || line.startsWith('TOTAL PAID:');
            if (isHeading) {
                context.fillStyle = '#f3ead0';
                context.fillRect(32, y - 34, width - 64, 48);
                context.fillStyle = '#725d20';
                context.font = '800 31px Rubik, Arial, sans-serif';
                context.fillText(line, padding, y);
            } else {
                context.fillStyle = '#171717';
                context.font = isImportant
                    ? '800 34px Rubik, Arial, sans-serif'
                    : '500 31px Rubik, Arial, sans-serif';
                context.fillText(line, padding, y);
            }
            y += lineHeight;
        });

        context.strokeStyle = '#b79a4b';
        context.setLineDash([10, 9]);
        context.beginPath();
        context.moveTo(padding, canvas.height - 105);
        context.lineTo(width - padding, canvas.height - 105);
        context.stroke();
        context.setLineDash([]);
        context.fillStyle = '#171717';
        context.textAlign = 'center';
        context.font = '700 27px Rubik, Arial, sans-serif';
        context.fillText('Thank you for your order', width / 2, canvas.height - 64);
        context.fillStyle = '#725d20';
        context.font = '500 22px Rubik, Arial, sans-serif';
        context.fillText('Securely generated after payment confirmation', width / 2, canvas.height - 31);

        return new Promise((resolve, reject) => {
            canvas.toBlob((blob) => blob ? resolve(blob) : reject(new Error('Unable to create receipt image.')), 'image/png');
        });
    }

    if (shareButton) {
        shareButton.addEventListener('click', async () => {
            shareButton.disabled = true;
            shareStatus.textContent = 'Preparing and securely saving your receipt preview…';
            try {
                const blob = await createReceiptBlob();
                const formData = new FormData();
        formData.append('receipt', blob, 'paid-order-{{ $order->order_serial_no }}.png');
                const response = await fetch(receiptStoreUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    throw new Error('Receipt image could not be saved.');
                }
                shareStatus.textContent = 'Receipt saved. Opening WhatsApp…';
                window.location.href = textWhatsappUrl;
            } catch (error) {
                shareStatus.textContent = 'The receipt preview could not be saved. Please tap the button to try again.';
            } finally {
                shareButton.disabled = false;
            }
        });
    }

</script>
</body>
</html>
