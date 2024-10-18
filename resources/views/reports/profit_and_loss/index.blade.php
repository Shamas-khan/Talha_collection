@extends('layout.layout')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main" >
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Profit And Loss</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>


                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-12" style="float: left;">
                        <form id="attendance-form" action="{{ route('profit.loss') }}" method="get" class="w-100 align-items-center   form-horizontal form-label-left">
                           
                        <div class="col-12">
                            <div class="mb-3 col-3" style="float: left;" >
                                <label  class="form-label">Start Date</label>
                                <input type="date" name="sdate" style="float: left;" 
                                    class="form-control @error('sdate') is-invalid @enderror"
                                    value="{{ old('sdate') }}" >
                                @error('sdate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-3" style="float: left;">
                                <label  class="form-label">End Date</label>
                                <input type="date" name="edate"  style="float: left;"
                                    class="form-control @error('edate') is-invalid @enderror"
                                    value="{{ old('edate') }}" >
                                @error('edate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">
                            </div>
                        </div>
                        <div class="col-12" style="float: left;">
                            <div class=" col-3">
                                <button type="submit" class="btn btn-primary">View </button>
                            </div> 
                        </div>        
                        </form>
                    </div>
                    </div>
                    </div>



                    </div>
                </div>
            </div>

            

           

            


        </div>
    </div>

    
    
    <?php $file = 'dummy.js'; ?>
    <!-- /page content -->
@endsection
