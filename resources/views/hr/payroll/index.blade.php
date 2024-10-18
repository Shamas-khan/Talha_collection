@extends('layout.layout')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main" >
        <div class="">
            <div class="page-title">
                <div class="title_left">
                   
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            
                            <h3>Monthly Salary</h3>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-12">
                        <form id="attendance-form" action="{{ route('payroll.generate') }}" method="POST" class="w-100 align-items-center   form-horizontal form-label-left">
                            @csrf 

                            <div class="mb-3 col-3">
                                <label for="attendance_date" class="form-label">Month and Year</label>
                                <input type="month" name="attendance_month" id="attendance_month"
                                    class="form-control @error('attendance_month') is-invalid @enderror"
                                    value="{{ old('attendance_month') }}" >
                                @error('attendance_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-3">
                            <button type="submit" class="btn btn-primary">Generate Payroll</button>
                        </div>        
                        </form>
                    </div>
                    </div>
                    </div>



                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>List Salary</h2>
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
                                                    <th>Month </th>
                                                    <th>Basic Salary </th>
                                                    <th>Gross Salary </th>
                                                    <th>Net Salary </th>
                                                    
                                                    
                                                    
                                                    
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTables will populate data here -->
                                            </tbody>
                                        </table>
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
                                url: "{{ route('salary.list') }}",
                                  type: 'POST',
                                  headers: {
                                      'X-CSRF-TOKEN': csrfToken
                                  },
                                  error: function(xhr, error, thrown) {
                                      console.log('Error: ' + xhr.responseText);
                                  }
                              },
                              columns: [
                                {
            data: "salary_month",
            render: function (data, type, row) {
                if (data) {
                    var date = new Date(data); // Convert to Date object
                    var year = date.getFullYear(); // Get year
                    var month = ('0' + (date.getMonth() + 1)).slice(-2); // Get month and add leading zero
                    return year + '-' + month; // Return formatted string
                }
                return data; // If data is null or undefined, return as is
            }
        },
                                  { data: "total_basic_salary" },
                                  { data: "total_gross_salary" },
                                  { data: "total_net_salary" },
                                  
                                  {
                                    data: null,
                                    render: function(data, type, row) {
                                      return `<a class="btn btn-warning btn-sm actionpadding" href="/salary/detail/${data.salary_month}">Detail</a>`;
                                       
                                    }
                                  }
                                  
                                ]
                          });
                });
        </script>

            


        </div>
    </div>

    
    
    <?php $file = 'dummy.js'; ?>
    <!-- /page content -->
@endsection
