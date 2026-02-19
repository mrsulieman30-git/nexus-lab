<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 100px; }
        .section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/' . $lab->logo_path) }}" class="logo" alt="Logo">
        <h1>{{ $lab->name }}</h1>
        <p>{{ $lab->address }}</p>
        <h2>Financial Report - {{ ucfirst($period) }}</h2>
        <p>From {{ $start_date->format('Y-m-d') }} to {{ $end_date->format('Y-m-d') }}</p>
    </div>

    <div class="section">
        <h3>Summary</h3>
        <p>Total Valuation: ${{ number_format($valuation, 2) }}</p>
        <p>Incomes: ${{ number_format($incomes, 2) }}</p>
        <p>Outcomes: ${{ number_format($outcomes, 2) }}</p>
    </div>

    <div class="section">
        <h3>Transactions</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                    <td>{{ $transaction->item->name }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->quantity }}</td>
                    <td>${{ number_format($transaction->quantity * $transaction->cost_at_time, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
