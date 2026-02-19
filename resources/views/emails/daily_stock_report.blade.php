<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1e293b; margin-top: 0; }
        .alert-box { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-red { background-color: #fee2e2; border-left: 5px solid #ef4444; color: #991b1b; }
        .alert-yellow { background-color: #fef9c3; border-left: 5px solid #eab308; color: #854d0e; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px 0; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #94a3b8; }
        .btn { display: inline-block; background: #2563eb; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Morning Inventory Report</h2>
        <p>Here is the status of <strong>{{ $labName }}</strong> for {{ date('Y-m-d') }}.</p>

        @if(count($lowStock) > 0)
        <div class="alert-box alert-red">
            <strong>🔴 CRITICAL: {{ count($lowStock) }} Items Low on Stock</strong>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Current</th>
                        <th>Min Limit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStock as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td style="color: #dc2626; font-weight: bold;">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td>{{ $item->min_threshold }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if(count($expiring) > 0)
        <div class="alert-box alert-yellow">
            <strong>🟡 WARNING: {{ count($expiring) }} Batches Expiring Soon (30 Days)</strong>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Lot #</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiring as $batch)
                    <tr>
                        <td>{{ $batch->item->name }}</td>
                        <td>{{ $batch->lot_number }}</td>
                        <td>{{ $batch->expiry_date->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ url('/dashboard') }}" class="btn">Login to Dashboard</a>
        </div>

        <div class="footer">
            Automated email sent by Nexus Lab System.
        </div>
    </div>
</body>
</html>
