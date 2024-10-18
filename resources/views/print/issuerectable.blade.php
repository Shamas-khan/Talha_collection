<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    </style>
    <script>
        function printReceipt() {
            window.print();
        }
    
        window.onload = function() {
            printReceipt();
        };
    
        window.onafterprint = function() {
            window.location.href = "{{ route('issue.index') }}";
        };
    </script>
</head>
<body >
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
<h2>کٹائی مال ریسیو پرچی</h2>        <p style="text-align: center;margin: 5px 0 0 0;" >S#: {{ $query->issue_material_id }}</p>
        <div style="display: flex;justify-content: space-around;margin-top: -6px;">
          
            <div>
                <p><span style="font-weight: bold;">{{ now()->format('Y-m-d') }}</span> تاریخ</p>
                <p><span style="font-weight: bold;">Order Qty:</span> {{ $query->total_quantity }}</p>
            </div>
            <div>
                <p>{{ $query->vendor_name }}<span style="font-weight: bold;">  نام     </span> </p>
                <p><span style="font-weight: bold;">Product Name:</span> {{ $query->product_name }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>REc Qty</th>
                    <th>Product</th>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td>{{$query->received_quantity }}</td> 
                    <td>{{$query->product_name }}</td>
                </tr>
              
            </tbody>
        </table>
    </div>
   
</body>
</html>
