@extends('layout.layout')
@section('content')

<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
            @if ($supplier)
            <h3 id="suppliername" data-id="{{$supplier->vendor_id}}">{{$supplier->name}} issue</h3>
           

            
            <span>Total Amount: {{  number_format($supplier->total_amount, 2) ?? 0 }}</span> <br>
            <span>Paid Amount: {{  number_format($supplier->paid_amount, 2) ?? 0 }} </span><br>
            <span>Remaining Amount: {{  number_format($supplier->remaining_amount, 2) ?? 0 }} </span> 
            @endif

            
        </div>

      
      </div>
      

      <div class="clearfix"></div>
      

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2><a href="{{route('suppliers.index')}}" class="btn text-white bg-primary p-2" >
                <i class="fas fa-list"></i> Back
            </a>
              </h2>

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
                        <th>Ref Customer</th>
                        <th>Product</th>
                        <th>Total</th>
                        
                        
                        <th>Detail</th>
                        
                        
                  </tr>
              </thead>
              <tbody>
                @if($purchase_material)
                    <tr>
                        <td>{{ $purchase_material->created_at }}</td>
                        <td>{{ $purchase_material->customer_name }}</td>
                        <td>{{ $purchase_material->product_name }}</td>
                        <td>{{ $purchase_material->total_amount }}</td>
                       
                        
                       
                        <td><a class="btn btn-secondary btn-sm"
                             href="/getdetail/{{ $purchase_material->issue_material_id }}">Detail</a></td>
                    </tr>
                @else
                    <tr>
                        <td colspan="6">No Data Available</td>
                    </tr>
                @endif
            </tbody>
          </table>
                      
            </div>
          </div>
        </div>
      </div>
     
        <script>


            $(document).ready(function() {
                $('#ttable').DataTable({
                        paging: false, 
                        searching: false,
                        responsive: true,
                        processing: true,
                        ordering: false,
                });
            });
</script>
     
          </div>
        </div>

      </div>
    </div>
  </div>
  
  <!-- /page content -->


<?php $file="dummy.js"?>
@endsection