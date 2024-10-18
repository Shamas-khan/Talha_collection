@extends('layout.layout')
@section('content')

<div class="right_col" role="main" style="min-height: 724px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>List Finish Product</h3>
          

         
        </div>

      
      </div>

      <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              
             
              <h2><a href="{{route('finishproduct.create')}}" class="btn text-white bg-primary p-2 m-2">
                <i class="fas fa-plus"></i> Add Product
              </a>
              </h2>
              <h2><a href="{{route('old.view')}}" class="btn text-white bg-primary p-2 m-2">
                <i class="fas fa-plus"></i> Add Old stock 
              </a>
              </h2>
              
             
              <h2 class="">
                <button type="button" class="btn text-white bg-primary p-2 m-2" data-toggle="modal"
                    data-target=".bs-example-modal-md">Re-process Product</button>
               </h2>
              
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
               
               
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
                  <table id="ttable" class="table display table-bordered table-striped table-hover">
                      <thead>
                         <tr>
                                <th>Name</th>
                              <th>Detail</th>
                              <th>Detail</th>
                         </tr>
                   </thead>
            </table>
                        </div>
            </div>
        </div>
      </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Issue Material</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="demo-form2" class="w-100 form-horizontal form-label-left">
                    @csrf

                    <div class="col-7  float-left">
                        <label class="form-label">Product</label>
                        <select class="form-control rawMaterialId select22" name="raw_material_id" id="raw_material_id">
                            <option value="default" selected disabled>Select</option>
                            @if($fp) 
                            @foreach ($fp as $d)
                              <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="col-5 float-left">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control qunaityfields" id="qty">
                        <span class="text-center quaniitybyserver text-success" id="issue-av-qty"></span>
                    </div>
                </div>
                <div class="modal-footer w-100 mt-4">
                    <p id="errr" class="m-0 text-danger"></p>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="submit-btn" type="button" class="btn btn-primary ">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var availableQuantity = 0;

        // Function to update available quantity display
        function updateAvailableQuantityDisplay() {
            if (availableQuantity > 0) {
                $('#issue-av-qty').text('Available Quantity: ' + availableQuantity);
            } else {
                $('#issue-av-qty').text('');
            }
        }

        $('#raw_material_id').change(function() {
            var productId = $(this).val();
            $('#issue-av-qty').text('Loading.....');
            if (productId !== 'default') {
                $.ajax({
                    url: '{{ route("get-product-quantity") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: { finish_product_id: productId },
                    success: function(response) {
                        if (response.success === true) {
                            availableQuantity = response.quantity;
                        } else {
                            availableQuantity = 0;
                        }
                        updateAvailableQuantityDisplay(); // Update display
                    },
                    error: function() {
                        availableQuantity = 0;
                        updateAvailableQuantityDisplay(); // Update display
                    }
                });
            } else {
                availableQuantity = 0;
                updateAvailableQuantityDisplay(); // Update display
            }
        });

        $('#submit-btn').click(function() {
            var productId = $('#raw_material_id').val();
            var quantity = parseInt($('#qty').val());

            if (productId === 'default') {
                toastr.error('Please select a product');
                return false;
            }

            if (isNaN(quantity) || quantity <= 0) {
                toastr.error('Quantity must be greater than zero');
                return false;
            }

            if (quantity > availableQuantity) {
                toastr.error('Quantity cannot be greater than available quantity');
                return false;
            }

            $('#submit-btn').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="sr-only">Loading...</span>
            `);

            var formData = {
                raw_material_id: productId,
                quantity: quantity,
                _token: csrfToken
            };

            $.ajax({
                url: '{{ route("reprcess.product") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#demo-form2')[0].reset();
                        availableQuantity = 0; // Reset available quantity after success
                        $('#issue-av-qty').text(''); // Clear the available quantity display
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    var errorMessage = response.responseJSON && response.responseJSON.message ? response.responseJSON.message : 'An error occurred while submitting the form';
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $('#submit-btn').prop('disabled', false).html('Submit');
                    $('#raw_material_id').val('default').trigger('change'); // Reset dropdown
                }
            });
        });
    });
</script>


 

    <?php $file = 'finishproduct.js'; ?>
@endsection