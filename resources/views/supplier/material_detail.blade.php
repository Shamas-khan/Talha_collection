@extends('layout.layout')
@section('content')

<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
           
        </div>

      
      </div>

      <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              @if ($supplier)
              <h3 id="suppliername" class="m-0" data-purchase="{{ $purchase_detail_id }}" data-id="{{ $supplier->supplier_id }}">
                  {{ $supplier->name }} Purchase Detail
              </h3>
          @endif
              
              
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
             
                      <table id="material_detail" class="table display table-bordered table-striped table-hover">
                        <thead>
                       <tr>
                        <th>Raw Material</th>
                        <th>Unit </th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                        
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
  
  <!-- /page content -->


<?php $file="supplier.js"?>


@endsection