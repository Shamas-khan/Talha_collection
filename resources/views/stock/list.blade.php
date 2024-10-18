@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Stock</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title" style="
    display: flex;
    justify-content: space-between;
">
                            <h2 style="
    width: 15%;
"><a href="{{ route('oldstock.view') }}" class="btn text-white bg-primary p-2" >
                        <i class="fas fa-plus"></i> Old Stock
                       </a>

                      </h2>
                     

<p style="
    margin: 0;
    display: flex;
    align-items: center;
    width: 85%;
    justify-content: flex-end;
">Current Date and Time: {{ now()->format('h:i A, d-m-Y') }}</p>
                      <div class="clearfix"></div>
                     </div>
                    <div class="x_content">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="card-box table-responsive">
                            <table id="ttable" class="table display table-bordered table-striped table-hover">
                              <thead>
                                 <tr>
                                        <th>Product</th>
                                      <th>Total Qty</th>
                                      
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

          <?php $file = 'stock.js'; ?>
@endsection
