@extends('layout.layout')
@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Banks</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><a href="{{ route('banks.create') }}" class="btn text-white bg-primary p-2" ">
                              <i class="fas fa-plus"></i> Add Bank
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
                                                   
                                                    <th>Bank Code</th>
                                                    <th>Bank Name</th>
                                                    <th>Account Number</th>
                                                    <th>Opening </th>
                                                    <th> Running</th>
                                                
                                                    <th>Action</th>
                                                    
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

                              $('#ttable').DataTable({
                                  dom: "Bfrtip",
                                  responsive: true,
                                  processing: true,
                                  serverSide: true,
                                  ordering: false,
                                  pageLength: 50,
                                  ajax: {
                                      url: "{{ route('bank.list') }}",
                                      type: 'POST',
                                      headers: {
                                          'X-CSRF-TOKEN': csrfToken
                                      }
                                  },
                                  columns: [

                                      {
                                          data: 'branch_code',
                                          name: 'branch_code'
                                      },
                                      {
                                          data: 'bank_name',
                                          name: 'bank_name'
                                      },
                                      {
                                          data: 'account_number',
                                          name: 'account_number'
                                      },
                                      {
                                          data: 'opening_balance',
                                          name: 'opening_balance'
                                      },
                                      {
                                          data: 'running_balance',
                                          name: 'running_balance'
                                      },

                                      {
                                          data: null,
                                          render: function(data, type, row) {
                                              return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> 
                                                <ul class="dropdown-menu">
                                                    <li class="dropdown-item">
                                                     <a   href="" >Edit</a>
                                                    </li>
                                                    <li class="dropdown-item">
                                                     <a   href="/banks/${data.bank_id}/ledger" >Ledger</a>
                                                    </li>
                                                </ul>
                                                </div>`;
                                          }
                                      }

                                  ],

                              });
                          });
                      </script>
                    </div>
                  </div>
                </div>
                <?php $file = 'dummy.js'; ?>
                <!-- /page content -->
@endsection
