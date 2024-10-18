
@extends('layout.layout')
@section('content')


<div class="right_col" role="main" style="min-height: 723px;">
    <div class="">
      
      

      <div class="row">
        <!-- form input mask -->
        <div class="col-md-12 col-sm-12  ">
          <div class="x_panel">
            <div class="x_title">
              <h2>Forecasting</h2>
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
                        <label  class="form-label">Product</label>
                        <select class="form-control"  name="finish_product_id" id="fpro" >
                          <option value="default" selected disabled>Select</option>
                          @if($fproduct) 
                            @foreach ($fproduct as $d)
                              <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
                            @endforeach
                          @endif
                        </select>
                    </div>

                   


                  
                

                <div class="mt-2">
                  <button id="forecasting" type="button" class="btn mb-0  btn-info">Calculate</button>
                 
                </div>
                
                <div id="show" class="table-responsive d-none">
                  <table class="table">
                  
                  <thead>
                    <tr>
                      <th scope="col">Production Quantity</th>
                      
                    </tr>
                  </thead>
                  <tbody id="radw-detail-issue">
                  </tbody>
                </table>
                <h1 id="raw-detail-issue"></h1>
                </div>
                
                

              
              </form>
            </div>
          </div>
        </div>
        <!-- /form input mask -->
      </div>
    </div>
  </div>

    






<?php $file = 'forecasting.js'; ?>
@endsection
