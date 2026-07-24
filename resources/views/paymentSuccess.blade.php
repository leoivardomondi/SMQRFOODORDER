<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company['company_name']  }}</title>
    <link rel="icon" href="{{ $faviconLogo->faviconLogo }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/lab/lab.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
    <style>
        .whatsapp-paid-order-btn {
            display: block;
            width: 100%;
            margin-bottom: 12px;
            padding: 12px 18px;
            border-radius: 9999px;
            background: #25D366;
            color: #ffffff;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(37, 211, 102, 0.24);
            border: 0;
            cursor: pointer;
        }
        .whatsapp-paid-order-btn:focus,
        .whatsapp-paid-order-btn:hover {
            background: #1ebe5d;
            color: #ffffff;
        }
        .share-status {
            min-height: 20px;
            margin: -4px 0 12px;
            color: #6b7280;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="py-14 px-4 w-full max-w-2xl mx-auto flex flex-col items-center justify-center">
    <a href="{{ route('home') }}" class="w-36 mb-8">
        <img class="w-full" src="{{ $logo->logo }}" alt="logo">
    </a>

    <img class="w-full max-w-[120px] mb-3" src="{{ asset('images/default/payment-success.gif') }}" alt="success">

    <h3 class="text-[22px] font-medium leading-[34px] text-center text-[#1AB759] mb-12">
        <span class="block">{{ __('all.label.congratulations') }}</span>
        {{ __('all.message.payment_successful') }}
    </h3>
    <div class="w-full max-w-[360px]">
        <dl class="text-center shadow-xs w-full mb-8">
            <dt class="uppercase py-2.5 rounded-tl-lg rounded-tr-lg text-heading bg-[#F7F7FC]">{{ __('all.label.transaction_id')  }}</dt>
            <dd class="uppercase py-3 rounded-bl-lg rounded-br-lg payment-font-size font-medium leading-10 text-heading bg-white">{{ $order?->transaction?->transaction_no }}</dd>
        </dl>
        @if($whatsappUrl)
            <p class="text-center text-heading mb-4">Send the detailed paid order with a receipt preview to the restaurant.</p>
            <button id="whatsapp-route" type="button" class="whatsapp-paid-order-btn">
                <span aria-hidden="true">&#128172;</span> Send Paid Order via WhatsApp
            </button>
            <p id="share-status" class="share-status" role="status" aria-live="polite"></p>
        @endif
        <a id="my-orders-route" href="{{ url('/my-orders') }}" class="block py-3 w-full rounded-3xl text-center text-base font-medium bg-primary text-white">My Orders</a>
    </div>
</div>

<script>
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
        context.strokeStyle = '#b79a4b';
        context.lineWidth = 3;
        context.strokeRect(14, 14, canvas.width - 28, canvas.height - 28);
        context.fillStyle = '#b79a4b';
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
            context.strokeStyle = '#b79a4b';
            context.lineWidth = 4;
            context.stroke();
            context.fillStyle = '#ffffff';
            context.textAlign = 'center';
            context.font = '700 27px Rubik, Arial, sans-serif';
            context.fillText('BWIBO', width / 2, 114);
        }

        context.textAlign = 'center';
        context.fillStyle = '#171717';
        context.font = '800 38px Rubik, Arial, sans-serif';
        context.fillText('PAYMENT RECEIPT', width / 2, 213);
        context.fillStyle = '#187a3d';
        roundedRect(context, width / 2 - 132, 235, 264, 58, 29);
        context.fill();
        context.fillStyle = '#ffffff';
        context.font = '800 31px Rubik, Arial, sans-serif';
        context.fillText('PAID', width / 2, 274);
        context.strokeStyle = '#b79a4b';
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
                formData.append('receipt', blob, 'bwibo-paid-order-{{ $order->order_serial_no }}.png');
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
