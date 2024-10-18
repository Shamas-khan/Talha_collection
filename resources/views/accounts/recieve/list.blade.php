@extends('layout.layout')
@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Recipt Voucher</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><a href="{{ route('recieve.create') }}" class="btn text-white bg-primary p-2" ">
                              <i class="fas fa-plus"></i> recieve
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
                                    <div class="card-box table-responsive table-container">
                           
                                        <table id="ttable" class="table display table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                   
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Narration</th>
                                                    <th>Bank</th>
                                                    
                                                  
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTable will populate rows here -->
                                            </tbody>
                                        </table>
                                    
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
                                        url: "{{ route('recieve.list') }}",
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
                                          { data: "person_type" },
                                          { data: "person_name" },
                                          { data: "amount" },
                                          { data: "narration" },
                                          { data: "bank_name" },
                                          
                                          
                                        ]
                                  });
                        });
                </script>
                    </div>
                  </div>
                </div>
                <?php $file = 'dummy.js'; ?>
                <!-- /page content -->
@endsection
