@extends('layout.layout')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3 class="m-0 d-inline" style="float: left"> Summary Report</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title m-0">
                            <h5 class="mb-0" style="text-align:center">
                                {{ $result['start_date'] }} To {{ $result['end_date'] }}
                            </h5>
                           
                            
                           
                            <div class="clearfix"></div>
                        </div>
                      
                        <div class="x_content">
                            <div class="row">
                                <div class="col-12 p-0" >
                        <div class="col-12 d-flex" style="flex-wrap: wrap;text-align: center;width: 50%;margin: 0 auto;">
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2  class="col-6">Sell</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['sell_total'], 2) }}</p>
                            </div>
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2 class="col-6">Making Cost</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['sell_making_total'], 2) }}</p>
                            </div>
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2 class="col-6">Gross Profit</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['sell_profit'], 2) }}</p>
                            </div>
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2 class="col-6">employee Salaries</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['employeesalaary'], 2) }}</p>
                            </div>
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2 class="col-6">Expense</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['expensee'], 2) }}</p>
                            </div>
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2 class="col-6">Total Costing</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['sumofmonthcosting'], 2) }}</p>
                            </div>
                            <div class="col-12 d-flex" style="gap: 16px;align-items: center;justify-content: center; text-align: justify;">
                                    <h2 class="col-6">Net Profit</h2> 
                                    <p class="mb-0 col-6">{{ number_format($result['netprofit'], 2) }}</p>
                            </div>
                        </div>
                                    {{-- <div class="card-box table-responsive">
                                        <table id="ttable" class="table display table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                 
                                                    <th>Sell </th>
                                                    <th>Making Cost</th>
                                                    <th>Profit/Loss</th>
                                                    <th>employee Salaries</th>
                                                    <th>Expense</th>
                                                    <th>Total Costing</th>
                                                    <th>Net Profte</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                    <tr>
                                                        
                                                        
                                                        
                                                <td>{{ number_format($result['sell_total'], 2) }}</td>
                                                <td>{{ number_format($result['sell_making_total'], 2) }}</td>
                                                <td>{{ number_format($result['sell_profit'], 2) }}</td>
                                                <td>{{ number_format($result['employeesalaary'], 2) }}</td>
                                                <td>{{ number_format($result['expensee'], 2) }}</td>
                                                <td>{{ number_format($result['sumofmonthcosting'], 2) }}</td>
                                                <td>{{ number_format($result['netprofit'], 2) }}</td>
                                                        
                                                    </tr>
                                         
                                            </tbody>
                                        </table>
                                       
                                       
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <?php $file = 'dummy.js'; ?>
@endsection
