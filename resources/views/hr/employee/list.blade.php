@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>List Employees</h3>
              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><a href="{{route('employee.create')}}" class="btn text-white bg-primary p-2" ">
                      <i class="fas fa-plus"></i> Add Employee
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
                            <th>Name</th>
                            <th>F-Name</th>
                           
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Salary </th>
                            <th>Remaining</th>
                            
                            
                            
                            <th>Action</th>
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
                            url: "{{ route('employee.list') }}",
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
                              { data: "name" },
                              { data: "fname" },
                              { data: "contact" },
                              { data: "address" },
                              { data: "basicsalary" },
                              { data: "remaining_amount" },
                              { 
            data: null,
            render: function(data, type, row) {
                return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
            
                
                  <li class="dropdown-item"><a   href="/employee/${data.employee_id}/ledger" > Ledger</a></li>
                <li class="dropdown-item"><a   href="/employee/${data.employee_id}/edit" >Edit </a></li>
                </ul></div>`;
            }
        }
                               
                            ]
                      });
            });
    </script>
        </div>
        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection