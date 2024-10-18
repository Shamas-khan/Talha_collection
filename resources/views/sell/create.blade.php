@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h2><a href="{{ route('sell.index') }}" class="btn text-white bg-primary p-2 m-2">
                            <i class="fas fa-list"></i> List
                        </a>
                    </h2>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <!-- form input mask -->
                <div class="col-md-12 col-sm-12  ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Add Sell</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br>
                            <form id="demo-form2" action="{{ route('issue.newsellstore') }}" method="post"
                                class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left">
                                @csrf
                                <input type="hidden" name="sell_order_id" value="{{ $sell_order->sell_order_id }}">
                                <div id="input-fields-container" class="align-items-end w-100 flex-wrap d-flex">

                                    <div class="mb-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label class="form-label">Customer</label>
                                        <input type="hidden" name="customer_id" value="{{ $sell_order->customer_id }}">
                                        <input type="text" readonly
                                            class="form-control"value="{{ $sell_order->customer_name }}">
                                    </div>
                                    <div class="mb-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label for="date" class="form-label">Order Date</label>
                                        <input type="date" name="order_date" readonly class="form-control" id="date"
                                            value="{{ $sell_order->order_date }}">
                                    </div>
                                    <div class="mb-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label for="cdate" class="form-label">Order Completion Date</label>
                                        <input type="date" name="order_completion_date" readonly class="form-control"
                                            id="cdate" value="{{ $sell_order->order_completion_date }}">
                                    </div>
                                    <div class="mb-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label for="cdate" class="form-label">Sell Date</label>
                                        <input type="date" name="sell_date" class="form-control">
                                        @error('sell_date')
                                            <span class="text-red-500 text-danger">field is required</span>
                                        @enderror
                                    </div>
                                    @php
                                        $counter = 0;
                                    @endphp
                                    @foreach ($sell_order_detail as $detail)
                                        <div class="w-100">
                                            <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                                                <label class="form-label">Product</label>
                                                <input type="hidden" name="finish_product_id[]"
                                                    value="{{ $detail->finish_product_id }}">
                                                <input type="text" readonly class="form-control"
                                                    value="{{ $detail->product_name }}">
                                            </div>
                                            <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" readonly name="order_quantity[]"
                                                    class="form-control get-rate-bill " id="quantity-{{ $loop->index }}"
                                                    title="{{ $loop->index }}" value="{{ $detail->order_quantity }}">
                                            </div>
                                            <div class="col-10 col-sm-6 col-md-4 col-lg-1 ">
                                                <label class="form-label">Dozen</label>
                                                <input type="number" readonly name="order_qty_dozen[]"
                                                    class="form-control " id="quantity-{{ $loop->index }}"
                                                    title="{{ $loop->index }}" value="{{ $detail->order_qty_dozen }}">
                                            </div>
                                            <div class="col-10 col-sm-6 col-md-4 col-lg-1 ">
                                                <label class="form-label">Stock</label>
                                                <input type="text" readonly name="sale_stock[]"
                                                    class="form-control " 
                                                     value="{{ $detail->sale_stock }}">
                                            </div>

                                            <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                                                <label class="form-label">Unit Price</label>
                                                <input type="number" name="unit_price[]" class="form-control get-rate-bill"
                                                    id="unit_price-{{ $loop->index }}" title="{{ $loop->index }}">
                                            </div>

                                            <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                                                <label class="form-label">Total Price</label>
                                                <input type="number" readonly name="total_amount[]"
                                                    class="form-control total-bill-amount" id="total-{{ $loop->index }}">
                                            </div>
                                            
                                            <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                                                <label class="form-label">Production </label>
                                                <input type="number" readonly name="total_cost[]" class="form-control "
                                                    value="{{ $detail->total_cost }}">
                                            </div>
                                        </div>
                                        @php $counter++ @endphp
                                    @endforeach

                                    <div class="mt-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label class="form-label">Transport</label>
                                        <input type="number" name="transport" class="form-control">
                                        @error('transport')
                                            <span class="text-red-500 text-danger">field is required</span>
                                        @enderror
                                    </div>
                                    <div class="mt-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label class="form-label">Grand Total</label>
                                        <input type="number" readonly name="grand_total" id="GrandT"
                                            class="form-control">
                                    </div>



                                    <div class="mt-3 col-10 col-sm-6 col-md-4 col-lg-2">
                                        <label for="Product" class="form-label">Currency</label>
                                        <select class="form-control" name="currency_id">
                                            <option value="default" selected disabled>Select</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->currency_id }}"
                                                    @if ($currency->symbol === 'PKR') selected @endif>
                                                    {{ $currency->symbol }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency_id')
                                            <span class="text-red-500 text-danger">field is required</span>
                                        @enderror
                                    </div>












                                </div>
                                
                                
                                <div class="mb-3 mt-3 col-12 col-md-10 text-left">
                                    <button type="submit" class="btn btn-primary btn-custom">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- /form input mask -->
            </div>
        </div>
    </div>


    <?php $file = 'selllist.js'; ?>
@endsection
