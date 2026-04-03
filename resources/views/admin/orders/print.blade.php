<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
        }

        .text-center {
            text-center: center;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .header h2 {
            margin-bottom: 5px;
        }

        .info p {
            margin: 2px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }

        td {
            padding: 5px 0;
            vertical-align: top;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
        }

        @media print {
            body {
                width: 80mm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Struk</button>
        <a href="{{ route('admin.orders.index') }}"
            style="padding: 10px 20px; text-decoration: none; background: #eee; color: #000; margin-left: 10px;">Kembali</a>
    </div>

    <div class="header text-center">
        <h2>69 CAFE</h2>
        <p>Jl. Merpati No. 123, Kota Kota</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="line"></div>

    <div class="info">
        <p>ID Pesanan: #{{ $order->id }}</p>
        <p>No. Meja/Tipe: {{ $order->table_number ?? 'Bawa Pulang' }}</p>
        <p>Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p>Pelanggan: {{ $order->user->name }}</p>
        <p>Metode: {{ $order->payment_method }}</p>
    </div>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <tr class="total">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="footer text-center">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Selamat menikmati hidangan kami.</p>
    </div>

    <script>
        // Auto print when page loads if not in preview
        window.onload = function () {
            // Optional: window.print();
        }
    </script>
</body>

</html>