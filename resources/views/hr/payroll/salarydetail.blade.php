@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
               
                @php
                    use Carbon\Carbon;
                    $formattedDate = Carbon::parse($date)->format('Y-m');
                @endphp



              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h3>Salary Detail Of {{ $formattedDate }} </h3>
                   
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive table-container">
                   
                            <table id="ttable" class="table display table-bordered table-striped table-hover">
                              <thead>
                             <tr>
                            
                              <th>Name</th>
                              <th>F-Name</th>
                              <th>B-Salary</th>
                              <th>Work Days</th>
                              <th>Late</th>
                              <th>Early</th>
                              <th>Absent</th>
                              <th>Leaves</th>
                              <th>Deduction</th>
                              <th>Allowances</th>
                              <th>Gross Salary</th>
                              <th>Net Salary</th>
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
                            url: "{{ route('salary.detaillist', ['date' => $date]) }}",
                              type: 'POST',
                              headers: {
                                  'X-CSRF-TOKEN': csrfToken
                              },
                              error: function(xhr, error, thrown) {
                                  console.log('Error: ' + xhr.responseText);
                              }
                          },
                          columns: [
                              { data: "e_name" },
                              { data: "f_name" },
                              { data: "basic_salary" },
                              { data: "days_worked" },
                              { data: "late_comming" },
                              { data: "early_going" },
                              { data: "days_absents" },

                              { data: "days_leaves" },
                              { data: "deductions" },
                              { data: "allowances" },
                              { data: "gross_salary" },
                              { data: "net_salary" },
                              
                              
                            ]
                      });
            });
    </script>
        </div>
        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection