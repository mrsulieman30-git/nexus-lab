<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label - {{ $item->name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <style>
        /* 1. RESET & CENTERING LOGIC */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        body {
            display: flex; /* Flexbox is the key to centering */
            justify-content: center; /* Center Horizontally */
            align-items: center; /* Center Vertically */
        }

        /* 2. THE LABEL DESIGN (Fixed Size) */
        /* Dimensions: 320px x 200px
           Approx: 3.3 inch x 2 inch (Common medium label size)
        */
        .label-container {
            width: 320px;
            height: 200px;
            border: 2px solid #000; /* Visible border */
            padding: 8px;
            box-sizing: border-box; /* Includes padding in width */

            display: flex;
            justify-content: space-between;
            align-items: center;

            /* Ensure background prints correctly */
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* 3. INTERNAL LAYOUT */
        .info-col {
            width: 65%;
            padding-right: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .qr-col {
            width: 35%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Typography */
        .lab-name {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            color: #444;
            margin-bottom: 4px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
            font-family: sans-serif;
        }
        .item-name {
            font-size: 14px; /* Adjusted slightly for long names */
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-family: sans-serif;
            word-wrap: break-word;
        }
        .meta-row {
            font-size: 11px;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
            font-family: monospace;
        }
        .meta-label { font-weight: bold; color: #555; }
        .meta-val { font-weight: bold; color: #000; }

        .lot-box {
            border: 1px solid #000;
            padding: 2px 4px;
            margin-top: 4px;
            font-size: 10px;
            font-family: monospace;
            display: flex;
            justify-content: space-between;
            background: #eee;
        }

        /* QR Styling */
        canvas#qr {
            width: 85px !important;
            height: 85px !important;
        }
        .sku-tag {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 11px;
            margin-top: 2px;
            background: #000;
            color: #fff;
            padding: 1px 4px;
        }

        /* 4. PRINTER SPECIFIC OVERRIDES */
        @media print {
            /* Remove browser header/footer/url */
            @page {
                margin: 0;
                size: auto;
            }

            /* Ensure the body still fills the page for centering */
            html, body {
                height: 100vh;
                width: 100%;
            }

            /* Remove extra margins that browsers might add */
            .label-container {
                border: 2px solid #000; /* Keep border for A4/A5 readability */
            }
        }
    </style>
</head>
<body onload="setTimeout(() => window.print(), 500)">

    <div class="label-container">
        <div class="info-col">
            <div class="lab-name">{{ Auth::user()->laboratory->name ?? 'NEXUS LAB' }}</div>

            <div class="item-name">{{ Str::limit($item->name, 40) }}</div>

            <div class="meta-row">
                <span class="meta-label">EXP:</span>
                <span class="meta-val" style="color: {{ isset($batch) && $batch->expiry_date < now() ? 'red' : 'black' }}">
                    {{ isset($batch) && $batch->expiry_date ? $batch->expiry_date->format('d M Y') : 'N/A' }}
                </span>
            </div>

            <div class="meta-row">
                <span class="meta-label">Loc:</span>
                <span class="meta-val">{{ $item->storage_location ?? 'General' }}</span>
            </div>

            <div class="lot-box">
                <span class="meta-label">LOT:</span>
                <span class="meta-val">{{ isset($batch) ? Str::limit($batch->lot_number, 12) : 'GENERIC' }}</span>
            </div>
        </div>

        <div class="qr-col">
            <canvas id="qr"></canvas>
            <div class="sku-tag">{{ $item->sku }}</div>
        </div>
    </div>

    <script>
        // Generate QR Code automatically
        (function() {
            var qr = new QRious({
                element: document.getElementById('qr'),
                value: '{{ $item->sku }}',
                size: 200,
                level: 'H'
            });
        })();
    </script>
</body>
</html>
