@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left  justify-content-center">
               
                <span>{{$supplier->op_balance}} OP Balance</span>
                <span>{{$supplier->total_amount}}Total Balance</span>
                <span>{{$supplier->remaining_amount}}Remainng Balance</span>
              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    
                    <h3 class="m-0">{{$supplier->name}} Ledger</h3>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                              {{-- <div class="table-container"> --}}
                            <table id="ttable" class="table display table-bordered table-striped table-hover">
                              <thead>
                             <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Narration </th>
                            <th>Debit </th>
                            <th>Credit</th>
                            <th>Running Balance</th>
                            <th>Detail</th>
                            
                        </tr>
                    </thead>
                </table>
                            
                  {{-- </div> --}}
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
                  
                  pageLength: 500,
                  ajax: {
                      url: "{{ route('supplier.ledgerlist', ['id' => $supplier->supplier_id]) }}",
                      type: 'POST',
                      headers: {
                          'X-CSRF-TOKEN': csrfToken
                      },
                      error: function(xhr, error, thrown) {
                          console.log('Error: ' + xhr.responseText);
                      },
                      data: function (d) {
                          return $.extend({}, d, {
                              // Custom data for the server-side script
                          });
                      }
                  },
                  columns: [
                      { data: "created_at" },
                      { data: "status" },
                      { data: "narration" },
                      { data: "debit" },
                      { data: "credit" },
                      { data: "running_balance" },
                      {
                          data: null,
                          render: function(data, type, row) {
                              if (data.purchase_material_id !== null) {
                                  return `<a class="btn btn-secondary btn-sm" href="/supplier/${data.supplier_id}/ledger/purchasedetail/${data.purchase_material_id}">Detail</a>`;
                              } else if (data.paymentvoucher_id !== null) {
                                  return `<a class="btn btn-secondary btn-sm" href="/supplier/${data.supplier_id}/ledger/paymentdetail/${data.paymentvoucher_id}">Detail</a>`; 
                              } else {
                                  return ``;
                              }
                          }
                      }
                  ]
              });
          });
      </script>
        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection