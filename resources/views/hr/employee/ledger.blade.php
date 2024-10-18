@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
               
              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title d-flex" style="align-items: center; gap: 34px;">
                   
                      <h3>{{$employees->name}} Ledger</h3>
                     
                      <h6>Remainng Balance: {{$employees->remaining_amount}}</h6>
              
                   
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
                          url: "{{ route('employee.ledgerlist', ['id' => $employees->employee_id]) }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            error: function(xhr, error, thrown) {
                                alert('Error: ' + xhr.responseText);
                            }
                        },
                        columns: [
                            { data: "created_at" },
                            { data: "status" },
                            { data: "narration" },
                            { data: "debit" },
                            { data: "credit" },
                           
                            { data: "running_balance" },
                            
                            
                          ]
                    });
          });
  </script>
        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection