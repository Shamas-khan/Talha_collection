@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">



            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Add Machine </h2>
                            <ul class="nav navbar-right panel_toolbox justify-content-end">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>


                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br>
                            <form id="demo-form2" action="{{route('machine.store')}}" method="post" 
                                class="w-100 align-items-center   form-horizontal form-label-left"
                                novalidate="">
@csrf
                                <div class="col-3 float-left">
                                    <label  class="form-label">Area Code</label>
                                    <input type="text" name="area_code" class="form-control" >
                                    @error('area_code') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
                                </div>
                                <div class="col-3 float-left">
                                    <label  class="form-label">Head Code</label>
                                    <input type="text" name="head_code" class="form-control" >
                                    @error('head_code') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
                                </div>
                                <div class="col-3 float-left">
                                    <label  class="form-label">Size</label>
                                    <input type="text" name="size" class="form-control" >
                                    @error('size') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
                                </div>



                                <div class="mt-4 d-inline-block text-left">
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
                            <h2>List Machine</h2>
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
                                                    <th>Area Code</th>
                                                    <th>Head Code</th>
                                                    <th>Size</th>
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
   

    <?php $file = 'machne.js'; ?>
@endsection
