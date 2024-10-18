@extends('layout.layout')
@section('content')

<div class="right_col" role="main" style="min-height: 724px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3> {{$fp->product_name}} Detail</h3>
        </div>

      
      </div>

      <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2><a href="{{route('finishproduct.create')}}" class="btn text-white bg-primary p-2" "="">
                <i class="fas fa-plus"></i> Add Finish Product
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
                                <th> Material Qty</th>
                              
                         </tr>
                   </thead>
            </table>
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
                       url: "{{route('finishproduct.detaillist',['id' => $fp->finish_product_id])}}", 
                         type: 'POST',
                         headers: {
                             'X-CSRF-TOKEN': csrfToken
                         },
                         error: function(xhr, error, thrown) {
                             alert('Error: ' + xhr.responseText);
                         }
                     },
                     columns: [
                       
                     { data: "material_name" },
                     { data: "material_qty" },
                     
                        
                       ]
                 });
           });
    </script>

       </div>
     </div>
   
          </div>
        </div>

      </div>
    </div>
  </div>

    <?php $file = 'dummy.js'; ?>
@endsection