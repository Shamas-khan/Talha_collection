@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>List Parties</h3>
              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><a href="{{route('parties.create')}}" class="btn text-white bg-primary p-2" ">
                      <i class="fas fa-plus"></i> Add Direct Party
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
                            <th>Contact</th>
                            
                            <th>Opening</th>
                            <th>Total</th>
                            <th>Paid</th>
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
                        url: "/partieslisting",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        error: function(xhr, error, thrown) {
                            alert('Error: ' + xhr.responseText);
                        }
                    },
                    columns: [
                       
                    {
            data: 'created_at',
            render: function (data, type, row) {
                if (data) {
                    var date = new Date(data);
                    
                    var year = date.getFullYear();
                    var month = String(date.getMonth() + 1).padStart(2, '0'); 
                    var day = String(date.getDate()).padStart(2, '0');
                    
                  
                    return `${year}-${month}-${day}`;
                }
                return '';
            }
        },
                        { data: "name" },
                        { data: "phone_number" },
                       
                       
                        { data: "opening_balance"},
                        { data: "total_amount" },
                        { data: "paid_amount" },
                        { data: "remaining_amount" },
                    
                        
                    
                        
                    { 
                            data: null,
                            render: function(data, type, row) {
                                return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
                           <li class="dropdown-item"><a href="/party/${data.parties_id}/ledger" > Ledger</a></li>
                            <li class="dropdown-item"><a   href="/customer/${data.parties_id}" >Edit </a></li>
                                </ul></div>`;
                            }
                        }
                        
                        
                    ]
                });


            })
        </script>
        <?php $file="dummy.js"?>
        <!-- /page content -->
@endsection