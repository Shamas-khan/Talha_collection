@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>List Vendors</h3>
              </div>

             
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><a href="{{route('vendors.create')}}" class="btn text-white bg-primary p-2" ">
                      <i class="fas fa-plus"></i> Add Vendor
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
                              <div class="table-container">
                            <table id="ttable" class="table display table-bordered table-striped table-hover">
                              <thead>
                             <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>cnic</th>
                            <th>Contact</th>
                            <th>Address</th>
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
        </div>
        <?php $file="vendor.js"?>
        <!-- /page content -->
@endsection