@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Old Stock</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                          
                      
                      <div class="clearfix"></div>
                     </div>
                    <div class="x_content">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="card-box table-responsive">
                            <table id="ttable" class="table display table-bordered table-striped table-hover">
                              <thead>
                                 <tr>
                                        <th>Product</th>
                                      <th>Total Qty</th>
                                      <th>Unit Price</th>
                                      
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
          </div>
          <script>
            $(document).ready(function() {

    
var csrfToken = $('meta[name="csrf-token"]').attr('content');
// Initialize DataTable
$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    
    ajax: {
        url: "/oldstockdetail",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        dataSrc: function(json) {
            console.log('Data received from server:', json);
            if (json.data) {
                return json.data;
            } else {
                console.error('Invalid server response:', json);
                alert('Error: Invalid server response');
                return [];
            }
        },
        error: function(xhr, error, thrown) {
            alert('Error: ' + xhr.responseText);
        }
    },
    columns: [
        { data: "product_name" },
        { data: "quantity" },
        { data: "unit_cost_price" },
       
        
    ]
});

$('#ttable_wrapper, #ttable').css({
    'overflow-y': 'hidden',
    'padding-bottom': '24px'
});
})

          </script>
          <?php $file = 'dummy.js'; ?>
@endsection
