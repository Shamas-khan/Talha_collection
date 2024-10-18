@extends('layout.layout')
@section('content')

<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left justify-content-center">
            @if ($supplier)
           

            
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
              
              <h3 id="suppliername" class="m-0" data-id="{{$supplier->supplier_id}}">{{$supplier->name}} Payment</h3>
           
              
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
                        <th>Paid</th>
                        <th>Narration</th>
                       
                        
                        
                  </tr>
              </thead>
              <tbody>
                @if($payment)
                    <tr>
                        <td>{{ $payment->created_at }}</td>
                        <td>{{ $payment->paid_amount }}</td>
                        <td>{{ $payment->narration }}</td>
                        
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