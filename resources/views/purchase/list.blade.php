@extends('layout.layout')
@section('content')
    <!-- page content -->
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Purchase</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><a href="{{ route('purchase.create') }}" class="btn text-white bg-primary p-2" "="">
                            <i class="fas fa-plus"></i> Add Purchase
                        </a>
                          </h2>
                            <h2><a href="{{ route('rubber.purchase') }}" class="btn text-white bg-primary p-2" "="">
                            <i class="fas fa-plus"></i> Rubber Purchase
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
                   
                                  <table id="ttable" class="table display table-bordered table-striped table-hover">
                                    <thead>
                                   <tr>
                                  <th>Date</th>
                                  <th>Invoice NO</th>
                                  <th>Supplier</th>
                                  <th>Transpotation</th>
                                  <th>Total Amount</th>
                                 
                                  <th>Detail</th>
                                 
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

                <!-- /page content -->
                <?php $file = 'purchaselist.js'; ?>
@endsection
