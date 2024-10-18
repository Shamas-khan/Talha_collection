@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">

 

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Add Design </h2>
                            <ul class="nav navbar-right panel_toolbox justify-content-end">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>


                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br>
                            <form id="demo-form2" action="{{route('design.store')}}" method="post" 
                                class="w-100 align-items-center   form-horizontal form-label-left"
                                novalidate="" enctype="multipart/form-data">
                                    @csrf
                                <div class="col-3 float-left">
                                    <label  class="form-label">Design Code</label>
                                    <input type="text" name="design_code" class="form-control" >
                                    @error('design_code') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
                                </div>
                                <div class="col-4 float-left">
                                    <label  class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" >
                                    @error('name') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                                    
                                    <input type="hidden" name="type" value="0" >
                                </div>
                                <div class="col-3 float-left">
                                    <label  class="form-label">Size</label>
                                    <select class="form-control "  name="unit_id" >
                                        <option value="default" selected disabled>Select</option>
                                        @if($Unit) 
                                            @foreach ($Unit as $d)
                                                <option value="{{ $d->unit_id }}">{{ $d->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('unit_id') 
                                        <span class="text-red-500 text-danger">field is required</span> 
                                    @enderror
                                </div>
                                {{-- <div class="col-3 float-left">
                                    <label  class="form-label">Cost</label>
                                    <input type="text" name="cost" class="form-control" >
                                    @error('cost') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                                    
                                </div> --}}
                                <div class="col-3 float-left">
                                <input type="file" name="img" class="-control mt-3" >
                                @error('img') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
                                </div>



                                <div class="col-3 float-left mt-3">
                                    <button type="submit"class="btn btn-primary unit-btn">Submit</button>

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
                            <h2>List Design</h2>
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
                                                    <th>Name</th>
                                                    <th>Desin Code</th>
                                                    <th>Size</th>
                                                    {{-- <th>Cost</th> --}}
                                                    <th>Image</th>
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

            </div>


        </div>
        
    </div>
   

    <?php $file = 'design.js'; ?>
@endsection
