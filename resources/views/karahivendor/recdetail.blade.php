@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
              <h3 data-vendor-id="{{ $karahiVendor->karai_vendor_id }}">{{ $karahiVendor->name }}</h3>
@if($karavi_available_qty->isNotEmpty())
    @foreach($karavi_available_qty as $qty)
        <span>Total Quantity: {{ $qty->total_qty }}</span>
    @endforeach
@else
    <span>No quantity available</span>
@endif

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
                            <th>Invoice No</th>
                            <th>Date</th>
                            {{-- <th>Karahi Vendor</th> --}}
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Transport Charges</th>
                            <th>Remaining Amount</th>
                            <th>Detail </th>
                            
                            
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
                $("#ttable").DataTable({
                  dom: "Bfrtip",
                                  responsive: true,
                                  processing: true,
                                  serverSide: true,
                                  ordering: false,
                                  pageLength: 50,
                    ajax: {
                      url: "{{ route('karahivendor.rec', ['id' => $karahiVendor->karai_vendor_id]) }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        error: function(xhr, error, thrown) {
                            console.log('Error: ' + xhr.responseText);
                        }
                    },
                    columns: [
    { data: "invoice_no" },
    { data: "receive_date" },
    // { data: "karai_vendor_name" },
    { data: "grand_total" },
    { data: "paid_amount" },
    { data: "transport_amount" },
    { data: "remaining_amount" },
    { 
        data: null,
        render: function(data, type, row) {
            let url = '{{ route("karahivendor.recd", ["id" => ":id", "receive_karahi_material_id" => ":receive_id"]) }}';
            url = url.replace(':id', data.karai_vendor_id).replace(':receive_id', data.receive_karahi_material_id);
            return `<a href="${url}" class="btn btn-secondary btn-sm btn-detail">Detail</a>`;
        }
    }
]

                });
      });
</script>




<?php $file="dummy.js"?>
        <!-- /page content -->
@endsection