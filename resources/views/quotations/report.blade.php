<!DOCTYPE html>
<html>

<head>
    <title>Quotation {{ $quotation->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        p {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <h1>Quotation for {{ $quotation->customer_name }}</h1>
    <p><strong>Status:</strong> {{ ucfirst($quotation->status) }}</p>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit_price }}</td>
                    <td>{{ $item->subtotal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Total Price:</strong> {{ $quotation->total_price }}</p>
</body>

</html>
