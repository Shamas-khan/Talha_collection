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
                            {{-- <h2><a href="{{ route('sell.create') }}" class="btn text-white bg-primary p-2" "="">
                        <i class="fas fa-plus"></i> Add New Order
                       </a>
                      </h2> --}}
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
                                        <th>Invoice No</th>
                                        
                                        <th>Sell Date</th>
                                      <th>Customer Name</th>
                                      <th>Making Cost Rs</th>
                                      <th>Total Sell Rs</th>
                                      
                                      <th>Builty  No</th>
                                      <th>Transport </th>
                                      <th>Currency </th>
                                      <th>Detail </th>
                                     
                                      
                                      
                                      
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
                          url: "{{ route('sell.createlist') }}",
                          type: 'POST',
                          headers: {
                              'X-CSRF-TOKEN': csrfToken
                          },
                          error: function(xhr, error, thrown) {
                              console.log('Error:', xhr.responseText);
                          }
                      },
                      columns: [
                          { data: "invoice_sell_id" },
                         
                          { data: "sell_date" },
                          { data: "customer_name" },
                          { data: "grand_production_cost" },
                          { data: "total_amount" },
                          { data: "builty_nbr" },
                          { data: "transport" },
                          { data: "currency_name" },
                          { 
                              data: null,
                              render: function(data, type, row) {
                                  var url = "{{ route('sell.createlistdetail', ['id' => ':id']) }}";
                                  var sell_id = data.sell_id;
                                  url = url.replace(':id', data.sell_id);
          
                                  return `
                                      <div class="dropdown">
                                          <button class="btn btn-warning btn-sm actionpadding dropdown-toggle" style="padding: 0rem .5rem !important;" type="button" data-toggle="dropdown">
                                              Action
                                              <span class="caret"></span>
                                          </button>
                                          <ul class="dropdown-menu">
                                              <li class="dropdown-item"><a href="${url}">Detail</a></li>
                                              <li class="dropdown-item"><a href="/sell/${sell_id}/prints">Print</a></li>
                                              <li class="dropdown-item"><a data-sell-id="${sell_id}" href="#" class="add-builty" data-toggle="modal" data-target="#exampleModal">Add Builty</a></li>
                                              <li class="dropdown-item"><a data-sell-id="${sell_id}"  href="/sell/${row.sell_id}/prints">Print</a></li>
                                          </ul>
                                      </div>
                                  `;
                              }
                          },
                      ]
                  });
          
                  // Event listener for 'Add Builty' button
                  $('#ttable').on('click', '.add-builty', function() {
                      var sellId = $(this).data('sell-id');
                      $('#recordId').val(sellId);
                  });
              });
          </script>
          
             
          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="updateForm" action="{{route('sell.builty')}}" method="post">
                          @csrf
                            <div class="form-group">
                                <label for="builtyNumber">Builty Number</label>
                                <input type="text" class="form-control" id="builtyNumber" name="builty_number" required>
                            </div>
                            <div class="form-group">
                                <label for="transport">Transport</label>
                                <input type="number" class="form-control" id="transport" name="transport" required>
                            </div>
                            <input type="hidden" id="recordId" name="sell_id">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" id="saveChanges"></input>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        

          <?php $file = 'dummy.js'; ?>
@endsection
