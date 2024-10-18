@extends('layout.layout')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3 class="m-0 d-inline" style="float: left"> Profit And Loss</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title m-0">
                            <h5 class="d-inline" style=" margin-right: 176px;">
                                {{ $sdate }} To {{ $edate }}
                            </h5>
                            <h6 class="d-inline text-success ml-2 mr-2">
                                Total Making <span class="text-dark">{{ number_format($result['total_production_cost'], 2) }}</span>
                            </h6>
                            <h6 class="d-inline text-success ml-2 mr-2">
                                Total Sell <span class="text-dark">{{ number_format($result['total_amount'], 2) }}</span>
                            </h6>
                            <h6 class="d-inline text-success ml-2 mr-2">
                                Total Profit/loss <span class="text-dark">{{ number_format($result['total_profit'], 2) }}</span>
                            </h6>
                            <div class="clearfix"></div>
                        </div>
                      
                        <div class="x_content">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-box table-responsive">
                                        <table id="ttable" class="table display table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Invoice No</th>
                                                    <th>Date</th>
                                                    <th>Currency</th>
                                                    <th>Customer</th>
                                                    <th>Making Cost</th>
                                                    <th>Sell Amount</th>
                                                    <th>Profit/Loss</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $serial = ($result['sell']->currentPage() - 1) * $result['sell']->perPage() + 1; @endphp
                                                @foreach($result['sell'] as $record)
                                                    <tr>
                                                        <td>{{ $serial++ }}</td>
                                                        <td>INO-{{ $record->sell_id }}</td>
                                                        <td>{{ $record->sell_date }}</td>
                                                        <td>{{ $record->currency_symbol }}</td>
                                                        <td>{{ $record->customer_name }}</td>
                                                        <td>{{ number_format($record->grand_production_cost, 2) }}</td>
                                                        <td>{{ number_format($record->total_amount, 2) }}</td>
                                                        <td style="color: {{ $record->profit < 0 ? 'red' : 'black' }};">
                                                            {{ number_format($record->profit, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <!-- Bootstrap Pagination Links -->
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $result['sell']->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
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
</div> <?php $file = 'dummy.js'; ?>
@endsection
