@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>{{ $karahiVendor->name }}</h3>

    
                <span>Total Quantity: {{ $karavi_available_qty->total_qty }}</span>
                <span>Total Price: {{ $karavi_available_qty->total_amount }}</span>

              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                  <h2><a id="goBackBtn" class="btn text-white bg-primary p-2" >
                <i class="fas fa-list"></i> Back
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
                            <th>Machine</th>
                            <th>Sheets</th>
                            <th>Raw Material</th>
                            <th>Quantity</th>
                            <th>Design Unit </th>
                            <th>Design Total</th>
                            <th>Fabric Qty</th>
                            <th>Fabric Cost</th>
                            
                            
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

<script>
$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var userId = "{{ request()->segment(count(request()->segments())) }}";
   
    
    var receive_karahi_material_id = userId; // Assign the correct value if needed
    var karai_vendor_id = "{{ $karahiVendor->karai_vendor_id }}"; // Ensure this variable is available and correct
    
    var ajaxUrl = `/karahivendor/recieve/${karai_vendor_id}/detail/${receive_karahi_material_id}`;
    
    
    $("#ttable").DataTable({
      dom: "Bfrtip",
                                  responsive: true,
                                  processing: true,
                                  serverSide: true,
                                  ordering: false,
                                  pageLength: 50,
        ajax: {
            url: ajaxUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            error: function(xhr, error, thrown) {
                console.log('Error:', xhr.responseText);
            }
        },
        columns: [
            { data: "karai_machine_head_code" },
            { data: "sheets" },
            { data: "raw_material_name" },
            { data: "quantity" },
            { data: "unit_price" },
            { data: "total" },
            { data: "used_material_qty" },
            { data: "used_material_cost" },
        ]
    });
});




</script>




<?php $file="dummy.js"?>
        <!-- /page content -->
        @endsection 