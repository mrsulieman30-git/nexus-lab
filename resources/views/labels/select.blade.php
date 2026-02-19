<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Batch - {{ $item->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10 flex flex-col items-center">

    <div class="max-w-2xl w-full bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white p-4">
            <h1 class="text-xl font-bold">Select Batch for Labeling</h1>
            <p class="text-blue-100 text-sm">Item: {{ $item->name }} ({{ $item->sku }})</p>
        </div>

        <div class="p-6">
            <p class="text-gray-600 mb-4">Multiple batches detected. Which specific stock are you labeling?</p>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-500 border-b">
                        <th class="p-2">Lot Number</th>
                        <th class="p-2">Expiry Date</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-mono font-bold text-gray-800">
                            {{ $batch->lot_number ?? 'Unknown' }}
                        </td>
                        <td class="p-3 {{ $batch->expiry_date < now() ? 'text-red-600 font-bold' : 'text-green-600' }}">
                            {{ $batch->expiry_date ? $batch->expiry_date->format('d M Y') : 'N/A' }}
                            @if($batch->expiry_date < now()) (Expired) @endif
                        </td>
                        <td class="p-3">{{ $batch->quantity }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('items.label', ['item' => $item->id, 'batch_id' => $batch->id]) }}"
                               class="bg-gray-900 text-white px-4 py-2 rounded text-sm hover:bg-black transition">
                                Print Label
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 p-4 text-center">
            <a href="{{ route('items.label', ['item' => $item->id, 'batch_id' => 'none']) }}" class="text-gray-500 text-xs underline">
                Skip and print generic label (No Lot/Exp)
            </a>
        </div>
    </div>

</body>
</html>
