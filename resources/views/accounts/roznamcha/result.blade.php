@extends('layout.layout')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title m-0">
                           
                          
                            <h3 class="d-inline" style="float: left;
    width: 117px;
    margin-right: 15px;
    text-align: end;
    font-size: 22px;
    line-height: 1;">{{ $date }}</h3>
                            <h3 class="m-0 d-inline" style="float: left">روزنامچہ</h3>
                            <div class="clearfix"></div>
                            
                        </div>
                      
                        <div class="x_content">
                            
                            <div class="row mb-5">
                               
                                <div class="col-6">
                                    
                                    <h3 style="text-align: center">خرچہ 
                                        {{ number_format($paymentvouchertotalAmount,2)}}</h3>
                                   
                           
                                    <div class="card-box table-responsive">
                                        <table id="ttable" class="table display table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Narration</th>
                                                    <th>Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($paymentvoucher as $voucher)
                                                    <tr>
                                                        <td>{{ $voucher->person_type }}</td>
                                                        <td>{{ $voucher->person_name }}</td>
                                                        <td>{{ number_format($voucher->amount,2) }}</td>
                                                        <td>{{ $voucher->narration }}</td>
                                                        <td>{{ $voucher->account_name }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No record found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="col-6 ">
                                    <h3 style="text-align: center">آمدن {{number_format($reciptvouchertotalAmount,2)}}</h3>
                                    <div class="card-box table-responsive">
                                        <table id="ttable" class="table display table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Narration</th>
                                                    <th>Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reciptvoucher as $voucher)
                                                    <tr>
                                                        <td>{{ $voucher->person_type }}</td>
                                                        <td>{{ $voucher->person_name }}</td>
                                                        <td>{{ number_format($voucher->amount,2) }}</td>
                                                        <td>{{ $voucher->narration }}</td>
                                                        <td>{{ $voucher->account_name }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No record found</td>
                                                    </tr>
                                                @endforelse
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
    </div>



    <?php $file = 'dummy.js'; ?>
    <!-- /page content -->
@endsection
