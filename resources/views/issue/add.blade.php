@extends('layout.layout')
@section('content')
<div class="right_col" role="main" style="min-height: 723px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h2><a href="{{route('issue.index')}}" class="btn text-white bg-primary p-2 m-2">
            <i class="fas fa-list"></i> List
          </a>
          </h2>
        </div>
      </div>
      <div class="clearfix"></div>

      <div class="row">
        <!-- form input mask -->
        <div class="col-md-12 col-sm-12  ">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add Production Issue</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br>
              <form id="demo-form2" action="{{route('issue.store')}}" method="post"
                    class="w-100 align-items-center  flex-wrap d-flex form-horizontal form-label-left">
                 @csrf 
                    <div class="mb-3 col-12 col-sm-3">
                        <label  class="form-label">Vendor</label>
                        <select class="form-control multipleselect custom_s"  name="vendor[]" id="vendor_id" >
                          <option value="default" selected disabled>Select</option>
                          @if($vendor) 
                            @foreach ($vendor as $d)
                              <option value="{{ $d->vendor_id }}">{{ $d->name }}</option>
                            @endforeach
                          @endif
                        </select>
                         @error('vendor') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>


                    <div class="mb-3 col-12 col-sm-3">
                        <label  class="form-label">Product</label>
                        <select class="form-control custom_s"  name="product" id="product_id" >
                          <option value="default" selected disabled>Select</option>
                          @if($product) 
                            @foreach ($product as $d)
                              <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('product') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>


                  
                <div class="mb-3 col-12 col-sm-2">
                  <label  class="form-label">Quantity</label>
                  <input type="number" class="form-control" id="order_qty" name="total_qty">
                  @error('total_qty') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    
                </div>

                

                <div class="mb-3 col-12 col-sm-2">
                  <label  class="form-label">Unit Price</label>
                  <input type="text" class="form-control" name="unit_price" id="unit_price">
                  @error('unit_price') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    
                </div>


                <div class="mt-2">
                  <button id="calculate" type="button" class="btn mb-0  btn-info">Calculate</button>
                 
                </div>


                <div class="mb-3 col-12 col-sm-3">
                  <label  class="form-label">Total </label>
                  <input type="number" class="form-control" id="total"  name="total"  readonly>
                  @error('total') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    
                </div>
                <div class="mb-3 col-12 col-sm-3">
                  <label class="form-label">Customer Reference</label>
                  <select class="form-control custom_s" name="customer_id" id="customer_id">
                      <option value="" disabled selected>Select</option>
                      @if($customer) 
                          @foreach ($customer as $d)
                              <option value="{{ $d->customer_id }}">{{ $d->name }}</option>
                          @endforeach
                      @endif
                  </select>
                  @error('customer_id') <span class="text-red-500 text-danger">Field is required</span> @enderror
              </div>
              
                

                <div id='load' class="col-12 text-success"></div>
                <div id="show" class="table-responsive d-none">
                  <table class="table">
                  
                  <thead>
                    <tr>
                      <th scope="col">Material</th>
                      <th scope="col">Required QTY</th>
                      <th scope="col">Available QTY</th>
                      <th scope="col">Issue QTY</th>
                      <th scope="col">Cost Amount</th>
                    </tr>
                  </thead>
                  <tbody id="raw-detail-issue">
                  </tbody>
                </table>
                </div>
                
                 <div class="mb-3 col-12 col-md-10 text-left">
                  <button type="submit" class="btn btn-primary btn-custom">Submit</button>
                  
                </div>

              
              </form>
            </div>
          </div>
        </div>
        <!-- /form input mask -->
      </div>
    </div>
  </div>

    <?php $file = 'issue.js'; ?>

@endsection