<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Material Receipt</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            width: 300px; /* adjust to your thermal printer's width */
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
        }

        .shop-info {
            text-align: center;
        }

        .shop-info h1, .shop-info h2 {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .shop-info p {
            margin: 0;
            padding: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .book {
            margin-top: 5px;
        }

        .book h2 {
            margin: 0;
            padding: 0;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 5px;
            border: 1px solid #000;
            text-align: end;
        }

        th {
            background-color: #f2f2f2;
            background-color: #f2f2f2;
            width: 50%;
        }

        .page-break {
            page-break-before: always;
            height: 0; /* Adjust as needed */
            visibility: hidden; /* Hide page-break element */
        }

    </style>
    <script>
        function printReceipt() {
            window.print();
        }

        window.onload = function() {
            printReceipt();
        };

        window.onafterprint = function() {
            window.location.href = "{{ route('issue.index') }}"; // Redirect after printing
        };
    </script>
</head>
<body>
    @foreach ($query->groupBy('vendor_name') as $vendorName => $items)
        <div class="header">
            <div class="shop-info">
                <h1>دھنک ہینڈی کرافٹ</h1>
                <h2>T. اینڈ طلحہ کلیکشن</h2>
                <p>مکّہ مسجد ریشم بازار حیدرآباد</p>
                <div style="display: flex; justify-content: space-around; margin-top: 10px;">
                    <div class="shopphone">
                        <p style="font-weight: bold;">دوکان فون نمبر</p>
                        <p>022 2103834</p>
                    </div>
                    <div class="menphone">
                        <p><span style="font-weight: bold;">T</span> 03213098035</p>
                        <p>03213098035</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="book">
            <h2>ٹکائی مال بنانے کی بک</h2>
            <p style="text-align: center; margin: 5px 0 0 0;">S#: {{ $items->first()->issue_material_id }}</p>
            <div style="display: flex; justify-content: space-around; margin-top: -6px;">
                <div>
                    <p><span style="font-weight: bold;">{{ $items->first()->created_at }}</span> تاریخ</p>
                    <p><span style="font-weight: bold;">Order Qty:</span> {{ $items->first()->product_qty }}</p>
                </div>
                <div>
                    <p>{{ $items->first()->vendor_name }}<span style="font-weight: bold;">  نام </span></p>
                    <p><span style="font-weight: bold;">Product Name:</span> {{ $items->first()->product_name }}</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Qty</th>
                        <th>Raw Material</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr>
                        <td> ({{ number_format($item->issue_qty_in_inches, 2) }})</td>
                        <td>{{ $item->material_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
