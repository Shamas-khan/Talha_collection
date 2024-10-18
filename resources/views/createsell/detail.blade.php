@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Sell</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{ $customerName->customer_name }}</h2>
                            <h2 class="ml-3">[{{ $customerName->invoice_sell_id }}]</h2>

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
                                        <th>Product</th>
                                        <th>Order Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>production_total_cost</th>
                                        <th>production_piece_cost</th>
                                       
                                      
                                      
                                      
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
                 
                  var userId = "{{ request()->segment(count(request()->segments())) }}";
                  console.log(userId)
                
                 
                  
                  $("#ttable").DataTable({
                    dom: "Bfrtip",
                    responsive: true,
                                  processing: true,
                                  serverSide: true,
                                  ordering: false,
                                  pageLength: 50,
                      ajax: {
                        url: "{{ url('/sellcreate/detail') }}/" + userId, 
                          type: 'POST',
                          headers: {
                              'X-CSRF-TOKEN': csrfToken
                          },
                          error: function(xhr, error, thrown) {
                              console.log('Error:', xhr.responseText);
                          }
                      },
                      columns: [
                          { data: "f_name" },
                          { data: "order_product_qty" },
                          { data: "unit_price" },
                          { data: "total_price" },
                          { data: "production_total_cost" },
                          { data: "production_piece_cost" },
                         
                         
                      ]
                  });
              });
              
              
              
              
        </script>
          </div>

          <?php $file = 'dummy.js'; ?>
@endsection
