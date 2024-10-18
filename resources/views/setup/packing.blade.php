@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">



            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Add Packing </h2>
                            <ul class="nav navbar-right panel_toolbox justify-content-end">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>


                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br>
                            <form id="demo-form2" action="{{route('packing.store')}}" method="post" 
                                class="w-100 align-items-center   form-horizontal form-label-left"
                                novalidate="">
@csrf
<div class="col-12 m-0 p-0 float-left">
<div class="col-3 float-left">
    <label  class="form-label">Product</label>
    <select class="form-control "  name="finish_product_id" >
        <option value="default" selected disabled>Select</option>
        @if($fproduct) 
            @foreach ($fproduct as $d)
                <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
            @endforeach
        @endif
    </select>
    @error('finish_product_id') 
        <span class="text-red-500 text-danger">field is required</span> 
    @enderror
</div>
</div>
<div class="col-3 mt-2 float-left">
    <label  class="form-label">Small Shoper</label>
    <select class="form-control "  name="small_shoper_id" >
        <option value="default" selected disabled>Select</option>
        @if($smallshoper) 
            @foreach ($smallshoper as $d)
                <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
            @endforeach
        @endif
    </select>
    @error('small_shoper_id') 
        <span class="text-red-500 text-danger">field is required</span> 
    @enderror
</div>

<div class="col-2 mt-2 float-left">
    <label  class="form-label">Product Quanity</label>
    <input type="number" name="small_product_qty" class="form-control" >
    @error('small_product_qty')  <span class="text-red-500 text-danger">field is required</span>  @enderror

</div>


<div class="col-3 mt-2 float-left">
    <label  class="form-label">Large Shoper</label>
    <select class="form-control "  name="big_shoper_id" >
        <option value="default" selected disabled>Select</option>
        @if($bigshoper) 
            @foreach ($bigshoper as $d)
                <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
            @endforeach
        @endif
    </select>
    @error('big_shoper_id') 
        <span class="text-red-500 text-danger">field is required</span> 
    @enderror
</div>
                               
                                
<div class="col-2 mt-2 float-left">
    <label  class="form-label">Product Quanity</label>
    <input type="number" name="big_product_qty" class="form-control" >
    @error('big_product_qty')  <span class="text-red-500 text-danger">field is required</span>  @enderror

</div>


                                <div class=" d-inline-block text-left" style="
                                margin-top: 34px;
                            ">
                                    <button type="submit" class="btn btn-primary unit-btn">Submit</button>

                                </div>









                            </form>
                        </div>
                    </div>
                </div>
            </div>






            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>List packing</h2>
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
                                                    <th>Product</th>
                                                    <th>Small Shoper</th>
                                                    <th>Pices / small Shoper</th>
                                                    <th>Big Shoper</th>
                                                    <th>Pices / big Shoper</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTables will populate data here -->
                                            </tbody>
                                        </table>
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
            url: "{{ route('packing.list') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            dataSrc: function (json) {
                console.log(json); // Log the entire response from the server
                if (typeof json === 'string') {
                    json = JSON.parse(json); // If the response is a string, parse it to JSON
                }
                return json.data; // Ensure that DataTables receives the expected data format
            },
            error: function(xhr, error, thrown) {
                console.log('Error: ' + xhr.responseText);
                console.log('Status: ' + error);
                console.log('Thrown: ' + thrown);
            }
        },
        columns: [
            { data: "product_name" },
            { data: "small_shoper_name" },
            { data: "small_product_qty" },
            { data: "big_shoper_name" },
            { data: "big_product_qty" }
        ]
    });
});


              </script>
            </div>


        </div>
        
    </div>
   

    <?php $file = 'dummy.js'; ?>
@endsection
