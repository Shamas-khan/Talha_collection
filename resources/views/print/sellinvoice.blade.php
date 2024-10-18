<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: white;
        }
        .container {
            margin: 50px auto;
        }
        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #f7ebeb; 
            margin-bottom: 10px;
        }
        .title {
            font-size: 1.5em;
            color: #f7ebeb;/* Yellowish Gold */
            text-align: center;
            margin-bottom: 10px;
        }
        .contact {
            font-size: 12px;
            text-align: center;
            margin-bottom: 7px;
            margin-top: 7px;
        }
        .details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .details label {
            font-weight: bold;
        }
        .details input {
            width: 150px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .header {
            background-color: #805020;
            padding: 10px 0;
            color: #f7ebeb;
            text-align: center;
            border-radius: 5px 5px 0 0;
            display: flex;
            justify-content: center;
        }
        .header h1 {
            margin: 0;
        }
        .header .title {
            font-size: 1.2em;
        }
        tfoot tr {
        border: none; 
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
            window.location.href = "{{ route('sell.createlist') }}";
        };
    </script> 
</head>
<body >
    <div class="container">
        <div class="header flex-wrap">
            <h1 class="col-1" style="font-size: 70px;">T.</h1>
            <div class="title col-6">
                <p class="m-0" style="letter-spacing: 5px; font-size: 20px; font-weight: 700;">DHANAK HANDI CRAFT</p>
                <p class="m-0" style="letter-spacing: 5px; font-size: 20px; font-weight: 700;">& TALHA COLLECTION</p>
                <p class="m-0" style="font-size: 14px;">Ladies Purse Manufacturer & Wholesaler</p>
            </div>
        </div>

        <div class="contact">
            <i class="fab fa-whatsapp"></i> 0321-3098035, 0310-5509000<br>
            Near Al-Farooq School<br>
            Resham Bazar, Hyderabad
        </div>

        <div class="details">
            <label for="no">Sell ID: <span style="text-decoration: underline; font-weight: 300;">{{ $data[0]->sell_id }}</span></label>
            <label for="date">Sell Date: <span style="text-decoration: underline; font-weight: 300;">{{ $data[0]->sell_date }}</span></label>
        </div>

        <div class="details" style="display: block; width: 100%;">
            <label for="date">Customer Name: <span style="text-decoration: underline; font-weight: 300;">{{ $data[0]->customer_name }}</span></label>
        </div>

        <div class="">
            <table class="table table-bordered" style="border-color: #1d1d1e;">
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%">SNO</th>
                        <th scope="col">QTY</th>
                        <th scope="col">Dozen</th>
                        <th scope="col">Detail</th>
                        <th scope="col" >Rate</th>
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody><?php $total_amount=0;?>
                    @foreach ($data as $key => $item)
                        <tr>
                            <th scope="row">{{ $key + 1 }}</th>
                            <td>{{ $item->order_product_qty }}</td>
                            <td>{{ $item->order_qty_dozen }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>{{ $item->total_price }}</td>
                            <?php $total_amount+=$item->total_price;?>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end" style=" border-width: 0 0px !important;"></td>
                        <td  class="text-end"  style="border-width: 1px 1px;">Total Amount:</td>
                        <td style="border-width: 1px 1px;">{{ $data[0]->currency_symbol }} {{ $total_amount}}</td>
                   
                    </tr>
                    <tr>
                        
                        <td colspan="4" class="text-end" style=" border-width: 0 0px !important;"></td>
                        <td  class="text-end" style="border-width: 1px 1px;">Paid Amount:</td>
                        <td style="border-width: 1px 1px;">{{  $data[0]->currency_symbol }} {{ $data[0]->paid_amount }} </td>
                   
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end" style=" border-width: 0 0px !important;"></td>
                        <td  class="text-end" style="border-width: 1px 1px;">Remaining Amount:</td>
                        <td style="border-width: 1px 1px;">{{ $data[0]->currency_symbol }} {{ $data[0]->remaining_amount }} </td>
                   
                    </tr>
                    {{-- <tr>
                        
                        <td colspan="5" class="text-end">Transport:</td>
                        <td>{{ $data->first()->currency_symbol }} {{ $data->first()->transport }} </td>
                   
                    </tr> --}}
                </tfoot>
            </table>
        </div>
    </div>

    {{-- <script>
       
        setTimeout(function() {
            window.location.href = "{{ route('sell.index') }}";
        }, 1000); 
      
    </script> --}}
</body>
</html>
