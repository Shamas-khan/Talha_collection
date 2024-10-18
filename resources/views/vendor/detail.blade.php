@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>{{ $vendor->name }}</h3>


  <span>Total Amount: {{ $vendor->total_amount ?? 0 }}</span> <br>
  <span>Paid Amount: {{ $vendor->paid_amount ?? 0 }} </span><br>
  <span>Remaining Amount: {{ $vendor->remaining_amount ?? 0 }} </span>
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
                            <th>Finished Product</th>
                            <th>Total Qty</th>
                            <th>Received Qty</th>
                            <th>Remaining Qty</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Remaining Amount</th>
                           
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
                            url: "{{ route('vendor.detail', ['id' => $vendor->vendor_id]) }}",
                              type: 'POST',
                              headers: {
                                  'X-CSRF-TOKEN': csrfToken
                              },
                              error: function(xhr, error, thrown) {
                                  console.log('Error: ' + xhr.responseText);
                              }
                          },
                          columns: [
                                        { data: "product_name" },
                                        { data: "total_quantity" },
                                        { data: "received_quantity" },
                                        { data: "remaining_quantity" },
                                        { data: "total_amount" },
                                        { data: "paid_amount" },
                                        { data: "remaining_amount" },
                                        
                                    ]
      
                      });
            });
      </script>

        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection