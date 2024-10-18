@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">



            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Add Unit </h2>
                            <ul class="nav navbar-right panel_toolbox justify-content-end">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>


                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br>
                            <form id="demo-form2" action="{{route('units.store')}}" method="post" 
                                class="w-100 align-items-center   form-horizontal form-label-left"
                                novalidate="">
@csrf
                                <div class="mb-3 col-12 col-sm-5">
                                    <label  class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" >
                                    @error('name') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
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
                            <h2>List Unit</h2>
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
                                       
                                        <th>Edit</th>
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


    <?php $file = 'unit.js'; ?>
@endsection
