@extends('layout.layout')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main" >
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>روزنامچہ</h3>
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
                                <div class="col-12">
                                    <form id="attendance-form" action="{{ route('roznamcha.generate') }}" method="GET" class="w-100 align-items-center form-horizontal form-label-left">
                                        <div class="mb-3 col-3">
                                            <label for="attendance_date" class="form-label">Date</label>
                                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}">
                                            @error('date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-3">
                                            <button type="submit" class="btn btn-primary">View</button>
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
