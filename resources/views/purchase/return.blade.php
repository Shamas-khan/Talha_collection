@extends('layout.layout')
@section('content')
      <!-- page content -->

      <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
          <div class="page-title">
            <div class="title_left">
              <h2><a href="{{route('purchase.index')}}" class="btn text-white bg-primary p-2 m-2">
                <i class="fas fa-list"></i> List
              </a>
              </h2> 
            </div>
          </div>
          <div class="clearfix"></div>

          <div class="row">
            <!-- form input mask -->
            <div class="col-md-12 col-sm-12  ">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Add Purchase</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <br>
                 
                  <form id="demo-form2" action="{{ route('return.store') }}" method="post" class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left" novalidate="">
    @csrf
    <!-- Supplier Dropdown -->
    <div class="mb-3 col-12 col-sm-6 col-md-4 col-lg-4">
        <label for="supplier" class="form-label">Supplier</label>
        <select class="form-control" name="supplier_id" readonly>
            <option value="{{ $purchase->supplier_id }}">{{ $purchase->supplier_name }}</option>
        </select>
        @error('supplier_id') 
            <span class="text-red-500 text-danger">field is required</span> 
        @enderror
    </div>
    <input type="hidden" name='purchase_id' value="{{ $purchase->purchase_material_id }}" readonly>

    <!-- Date Field -->
    <div class="mb-3 col-4">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" value="{{ old('date', $purchase->purchase_date) }}" >
        @error('date') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
    </div>

    <!-- Dynamic Fields for Raw Material Details -->
    <div id="input-fields-container" class="align-items-end w-100 flex-wrap">
        @foreach($details as $index => $detail)
        <div class="row w-100">
            <!-- Raw Material Dropdown -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <label for="raw-material" class="form-label">Raw Material</label>
                <input type="text" class="form-control" value="{{ $detail->raw_material_name }}" readonly>
                <input type="hidden" name="raw_material_id[]" value="{{ $detail->raw_material_id }}">
            </div>

            <!-- Unit -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <label class="form-label">Unit</label>
                <input type="text" name="unit_name[]" value="{{ $detail->unit_name }}" class="form-control" readonly>
                <input type="hidden" name="unit_id[]" value="{{ $detail->unit_id }}">
            </div>

            <!-- Quantity -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <label class="form-label">Purchasq QTY</label>
                <input type="number" readonly name="qty[]" class="form-control" value="{{ $detail->quantity }}">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <label class="form-label">Return QTY</label>
                <input type="number" name="return_qty[]" class="form-control" required>
                @error('return_qty.*') 
                    <span class="text-red-500 text-danger">field is required</span> 
                @enderror
            </div>

            <!-- Unit Price -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <label for="unit-price" class="form-label">Unit Price</label>
                <input type="number" name="unit_price[]" readonly class="form-control" value="{{ $detail->unit_price }}">
            </div>

            <!-- Total -->
            <div class="float-left col-10 col-sm-6 col-md-4 col-lg-2">
                <label for="total" class="form-label">Total</label>
                <input type="number" name="total[]" readonly class="form-control total-bill-amount" value="{{ $detail->total_amount }}">
            </div>
        </div>
        @endforeach
    </div>

    <!-- Transport Charges -->
    <div class="mt-3 col-12 col-sm-6 col-md-4 col-lg-2">
        <label for="transport" class="form-label">Transport Charges</label>
        <input type="number" name="transport_charges" readonly class="form-control" value="{{ $purchase->transportation_amount }}">
    </div>

    <!-- Grand Total -->
    <div class="mt-3 col-12 col-sm-6 col-md-4 col-lg-2">
        <label for="Grand" class="form-label">Grand Total</label>
        <input type="number" name="grand_total" readonly class="form-control" value="{{ $purchase->grand_total }}">
    </div>
    
    <div class="mt-3 col-12 d-flex">
        <!-- Submit Button -->
        <div class="mb-3 p-0 col-2 text-left">
            <button type="submit" class="btn btn-primary btn-custom">Submit</button>
        </div>
        <div class="mb-3 p-0 col-2 text-left">
            <button type="reset" class="btn btn-danger btn-custom">Reset</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    function calculateTotal() {
        var grandTotal = 0;
        
        // Iterate through each row to calculate the total
        $('#input-fields-container .row').each(function() {
            var returnQty = parseFloat($(this).find('input[name="return_qty[]"]').val()) || 0; // Return QTY field
            var unitPrice = parseFloat($(this).find('input[name="unit_price[]"]').val()) || 0; // Unit Price field

            // Calculate total for this row
            var total = returnQty > 0 ? (returnQty * unitPrice) : 0;
            $(this).find('input[name="total[]"]').val(total.toFixed(2)); // Set the total for the row
            grandTotal += total; // Add to grand total
        });

        // Update the grand total field
        $('input[name="grand_total"]').val(grandTotal.toFixed(2)); // Set grand total with 2 decimal points
    }

    // Calculate total when return quantity changes
    $(document).on('input', 'input[name="return_qty[]"]', function() {
        calculateTotal();
    });
});
</script>

                  
                </div>
              </div>
            </div>
           
        
            <!-- /form input mask -->
          </div>
        </div>
      </div>
        <!-- /page content -->
        <?php $file="dummy.js"?>
@endsection