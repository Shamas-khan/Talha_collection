@extends('layout.layout')
@section('content')
 <!-- page content -->



 <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
        <h3>{{ $vendor->name }}</h3>


  <span>Total Amount: {{ $vendor->total_amount ?? 0}} </span> <br>
  <span>Paid Amount: {{ $vendor->paid_amount ?? 0}} </span><br>
  <span>Remaining Amount: {{ $vendor->remaining_amount ?? 0}} </span>
  





        </div>

      
      </div>

      <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
            <h2><a id="goBackBtn" class="btn text-white bg-primary p-2" >
          <i class="fas fa-list"></i> Back
           </a>
        </h2>
            <h2><button  type="button" class="btn text-white bg-primary p-2" data-toggle="modal" data-target=".bs-example-modal-sm">Add Payment</button>
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
                      <th>Vendor</th>
                      <th>Paid Amount</th>
                      <th>Narration</th>
                      <th>Date</th>
                      
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

<script>
$(document).ready(function() {
          var csrfToken = $('meta[name="csrf-token"]').attr('content');
          $("#ttable").DataTable({
            dom: "Bfrtip",
                                  responsive: true,
                                  processing: true,
                                  serverSide: true,
                                  ordering: false,
                                  pageLength: 50,
              ajax: {
                url: "{{ route('vendor.paymentlist', ['id' => $vendor->vendor_id]) }}",
                  type: 'POST',
                  headers: {
                      'X-CSRF-TOKEN': csrfToken
                  },
                  error: function(xhr, error, thrown) {
                      alert('Error: ' + xhr.responseText);
                  }
              },
              columns: [
                  { data: "vendor_name" },
                  { data: "paid_amount" },
                  { data: "narration" },
                  { data: "created_at" },
                ]
          });
});
</script>


<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel2">Received Quantity</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="productionform" action="{{route('vendor.paymentstore')}}"  method="post" class="w-100 align-items-center flex-wrap d-flex form-horizontal form-label-left">
                    @csrf
                    <!-- Hidden input to store issue_material_id -->
                    
                    <input type="hidden" name="vendor_id" value="{{$vendor->vendor_id}}" >
                    <div class="col-12">
                        <label for="quantity" class="form-label">Narration</label>
                        <input type="text" name="narration" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="quantity" class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" min="1" required>
                    </div>

                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



 <?php $file="dummy.js"?>
 <!-- /page content -->
@endsection