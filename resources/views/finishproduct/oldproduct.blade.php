@extends('layout.layout')
@section('content')
<div class="right_col" role="main" style="min-height: 723px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h2><a href="{{route('finishproduct.index')}}" class="btn text-white bg-primary p-2 m-2">
            <i class="fas fa-list"></i> Add Old product
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
              <h2>Old Finish Product</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br>
              <form id="demo-form2" 
                 action="{{route('oldproduct.store')}}" method="post"
               class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left" novalidate="">
               @csrf
                 
                  <div id="input-fields-container" class="align-items-end w-100 ">
                    
                  
                    <div class="col-6 mb-3">
                      <label for="raw-material" class="form-label">product Name</label>
                      <select class="form-control rawMaterialId custom_s" title="0" name="finish_product_id">
                        <option value="default" selected disabled>Select</option>
                        @if($fp) 
                          @foreach ($fp as $d)
                            <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
                          @endforeach
                        @endif
                      </select>
                      @error('finish_product_id') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                    </div>
                    
                    
                    

                      <div class=" col-3" style="float: left">
                          <label for="quantity" class="form-label">Quantity</label>
                          <input type="number" name="quantity" class="form-control">
                          @error('quantity') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                       
                      </div>
                      <div class=" col-3" style="float: left">
                          <label for="quantity" class="form-label">Per Piece Making Cost</label>
                          <input type="number" name="unit_price" class="form-control">
                          @error('unit_price') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                       
                      </div>
                      
                      
                      
                  </div>
                  
                
                  <div class="mb-3 mt-3 col-12 col-md-10 text-left">
                      <button type="submit" class="btn btn-primary btn-custom">Submit</button>
                  </div>
              </form>
              
            </div>
          </div>
        </div>
      
        
      </div>
    </div>
  </div>

    <?php $file = 'finishproduct.js'; ?>
@endsection