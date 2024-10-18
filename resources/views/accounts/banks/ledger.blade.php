@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>{{$bank->bank_name}} Ledger</h3>
                <span>{{number_format($bank->opening_balance,2)}} OP Balance</span>
                
                <span>{{number_format($bank->running_balance,2)}}Rruning Balance</span>
              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><a href="" class="btn text-white bg-primary p-2" >
                      <i class="fas fa-plus"></i>
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
                            {{-- <th>Detail</th> --}}
                            
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
                          url: "{{ route('bank.ledgerlist', ['id' => $bank->bank_id]) }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            error: function(xhr, error, thrown) {
                                console.log('Error: ' + xhr.responseText); 
                            }
                        },
                        columns: [
                            { data: "created_at" },
                            { data: "status" },
                            { data: "narration" },
                            { data: "debit" },
                            { data: "credit" },
                            { data: "running_balance" },
                            // {
                            //   data: null,
                            //   render: function(data, type, row) {
                            //       if (data.sell_id !== null) {
                            //           return `<a class="btn btn-secondary btn-sm" href="/customer/${data.customer_id}/ledger/sell/${data.sell_id}">Detail</a>`;
                            //       } else if(data.paymentvoucher_id !== null) {
                            //           return `<a class="btn btn-secondary btn-sm" href="/customer/${data.customer_id}/ledger/payment/${data.paymentvoucher_id}">Detail</a>`; 
                            //       }else{
                            //         return ``;
                            //       }
                            //   }
                            // }
                            
                          ]
                    });
          });
  </script>
        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection