@extends('layout.layout')
@section('content')

<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left  justify-content-center">
            @if ($supplier)
            
           

            
            <h6>Total Amount: {{  number_format($supplier->total_amount, 2) ?? 0 }}</h6> 
            <h6>Paid Amount: {{  number_format($supplier->paid_amount, 2) ?? 0 }} </h6>
            <h6>Remaining Amount: {{  number_format($supplier->remaining_amount, 2) ?? 0 }} </h6> 
           

            
        </div>

      
      </div>
      

      <div class="clearfix"></div>
      

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              
              <h3 id="suppliername" class="m-0" data-id="{{$supplier->supplier_id}}">{{$supplier->name}} Purchase</h3>
              @endif
             
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
             
                      <table id="supplier_pd" class="table display table-bordered table-striped table-hover">
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
  <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel2">Received Quantity</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="productionform" action="{{route('supplier.paymentstore')}}"  method="post" class="w-100 align-items-center flex-wrap d-flex form-horizontal form-label-left">
                    @csrf
                    <!-- Hidden input to store issue_material_id -->
                    
                    <input type="hidden" name="supplier_id" value="{{$supplier->supplier_id}}" >
                    <div class="col-12">
                        <label for="quantity" class="form-label">Narration</label>
                        <input type="text" name="narration" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label for="quantity" class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" min="1" required>
                    </div>

                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
  <!-- /page content -->


<?php $file="supplier.js"?>
@endsection