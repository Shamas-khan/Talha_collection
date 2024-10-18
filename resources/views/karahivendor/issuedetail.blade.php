@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
              <h3>{{ $karahiVendor->name }}</h3>

    
              <span>Total Quantity: {{ $karavi_available_qty->total_qty ?? 0 }}</span>
              <span>Total Price: {{ $karavi_available_qty->total_amount ?? 0 }}</span>
    

    


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
                            <th>Raw Material</th>
                            <th>Issue QTY</th>
                            <th>Cost Amount</th>
                            <th>Date</th>
                            <th>Print</th>
                            
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
                      url: "{{ route('karahivendor.issue', ['id' => $karahiVendor->karai_vendor_id]) }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        error: function(xhr, error, thrown) {
                            alert('Error: ' + xhr.responseText);
                        }
                    },
                    columns: [
                        { data: "raw_material_name" },
                        { data: "issue_qty" },
                        { data: "amount_issue" },
                        { data: "created_at" },
                        { 
                            data: null,
                            render: function(data, type, row) {
                                return `<a class="btn btn-secondary btn-sm" href="/reckarahi/issue/${data.issue_karahi_material_id}/print">Print</a>`;
                            }
                        }
                      ]
                });
      });
</script>




<?php $file="dummy.js"?>
        <!-- /page content -->
@endsection