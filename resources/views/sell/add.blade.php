@extends('layout.layout')
@section('content')
<div class="right_col" role="main" style="min-height: 724px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h2><a href="{{route('sell.index')}}" class="btn text-white bg-primary p-2 m-2">
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
              <form id="demo-form2" action="{{ route('sell.store') }}" method="post" class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left">
                @csrf
                <div id="input-fields-container" class="align-items-end w-100 flex-wrap d-flex">
                    <div class="mb-3 col-4">
                        <label class="form-label">Customer</label>
                        <select class="form-control" name="customer_id">
                            <option value="default" selected disabled>Select</option>
                            @if($customer)
                                @foreach ($customer as $d)
                                    <option value="{{ $d->customer_id }}">{{ $d->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('customer_id') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>
                    <div class="mb-3 col-4">
                        <label for="date" class="form-label">Order Date</label>
                        <input type="date" name="order_date" class="form-control" id="date" aria-describedby="quantityHelp">
                        @error('order_date') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>
                    <div class="mb-3 col-4">
                        <label for="cdate" class="form-label">Order Completion Date</label>
                        <input type="date" name="order_completion_date" class="form-control" id="cdate" aria-describedby="quantityHelp">
                        @error('order_completion_date') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>
                    <div class="col-5">
                      <label for="Product" class="form-label">Product</label>
                      <select class="form-control" name="finish_product_id[]">
                          <option value="default" selected disabled>Select</option>
                          @if($fproduct)
                              @foreach ($fproduct as $d)
                                  <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
                              @endforeach
                          @endif
                      </select>
                      @error('finish_product_id') 
                          <span class="text-red-500 text-danger">Field is required</span> 
                      @enderror
                  </div>
                  
                    <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="order_quantity[]" class="form-control" id="quantity" aria-describedby="quantityHelp">
                        @error('order_quantity.0') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>

                    <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                        <label  class="form-label">Sell From</label>
                        <select class="form-control" name="sale_stock[]">
                            <option value="default" selected disabled>Select</option>
                            <option value="old" >Old Stock</option>
                            <option value="new" >New Stock</option>
                     
                        </select>
                        @error('sale_stock') 
                            <span class="text-red-500 text-danger">Field is required</span> 
                        @enderror
                    </div>
                    <div class="mr-2">
                        <p class="plus-custom mcustom"><i class="fa fa-plus"></i></p>
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
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        let counter = 1;
        function addNewFields() {
            let newElement = document.createElement('div');
            newElement.classList.add('align-items-end', 'w-100', 'flex-wrap', 'd-flex');
            let innerContent = `
                <div class="col-5">
                    <label for="Product" class="form-label">Product</label>
                    <select class="form-control" name="finish_product_id[]">
                        <option value="default" selected disabled>Select</option>
                        @if($fproduct)
                            @foreach ($fproduct as $d)
                                <option value="{{ $d->finish_product_id }}">{{ $d->product_name }}</option>
                            @endforeach
                        @endif
                    </select>
                   
                </div>
                <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="order_quantity[]" class="form-control" id="quantity" aria-describedby="quantityHelp">
                    @error('order_quantity.${counter}') <span class="text-red-500 text-danger">Field is required</span> @enderror
                </div>
                <div class="col-10 col-sm-6 col-md-4 col-lg-2">
                        <label  class="form-label">Sell From</label>
                        <select class="form-control" name="sale_stock[]">
                            <option value="default" selected disabled>Select</option>
                            <option value="old" >Old Stock</option>
                            <option value="new" >New Stock</option>
                     
                        </select>
                        @error('sale_stock.${counter}') 
                            <span class="text-red-500 text-danger">Field is required</span> 
                        @enderror
                    </div>
                <div class="mr-2">
                    <p class="plus-custom mcustom"><i class="fa fa-plus"></i></p>
                </div>
                <div>
                    <p class="minus-custom mcustom"><i class="fa fa-minus"></i></p>
                </div>`;
            counter++;
            newElement.innerHTML = innerContent;

            let container = document.getElementById('input-fields-container');
            container.appendChild(newElement);
            newElement.querySelector('.plus-custom').addEventListener('click', () => {
                addNewFields();
                newElement.querySelector('.plus-custom').parentElement.style.display = 'none';
            });

            newElement.querySelector('.minus-custom').addEventListener('click', () => {
                container.removeChild(newElement);
                const buttons = document.querySelectorAll('.plus-custom');
                const lastButton = buttons[buttons.length - 1];
                lastButton.parentElement.style.display = 'block';
            });
        }

        document.querySelectorAll('.plus-custom').forEach((button) => {
            button.addEventListener('click', () => {
                addNewFields();
                button.parentElement.style.display = 'none';
            });
        });
    });
</script>

<?php $file = 'selllist.js'; ?>
@endsection