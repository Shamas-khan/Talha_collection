@extends('layout.layout')
@section('content')
      <!-- page content -->

      <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
          <div class="page-title">
            <div class="title_left">
              <h2><a href="{{route('purchase.index')}}" class="btn text-white bg-primary p-2 m-2">
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
                  <h2>Add Purchase</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <br>
                  <form id="demo-form2" action="{{ route('purchase.store') }}" method="post"  class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left" novalidate="">
                    @csrf
                    <div class="mb-3 col-12 col-sm-6 col-md-4 col-lg-4">
                        <label for="supplier" class="form-label">Supplier</label>
                        <select class="form-control custom_s " name="supplier_id">
                            <option  selected disabled>Select Supplier</option>
                            @if($supplier) 
                                @foreach ($supplier as $d)
                                    <option value="{{ $d->supplier_id }}">{{ $d->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('supplier_id') 
                            <span class="text-red-500 text-danger">field is required</span> 
                        @enderror
                    </div>

                    <div class="mb-3 col-4">
                        <label  class="form-label">Date</label>
                        <input type="date" name="date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" value="{{ old('date') }}">
                        @error('date') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                    </div>
                    
                    <div id="input-fields-container" class="align-items-end w-100 flex-wrap">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <label for="raw-material" class="form-label">Raw Material</label>
                            <select class="form-control custom_s rawMaterialId" title="0" name="raw_material_id[]" id="raw_material_id-0">
                                <option value="default" selected disabled>Select</option>
                                @if($raw) 
                                    @foreach ($raw as $d)
                                        <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('raw_material_id') 
                                <span class="text-red-500 text-danger">field is required</span> 
                            @enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit_name[]" readonly class="form-control" id="unit-0">
                            <input type="hidden" name="unit_id[]" class="form-control" id="unit_id-0">
                            @error('unit_id.0') 
                                <span class="text-red-500 text-danger">field is required</span> 
                            @enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" title="0" name="qty[]" class="form-control get-rate-bill" id="quantity-0" >
                            @error('qty.0') 
                                <span class="text-red-500 text-danger"> field is required</span> 
                            @enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <label for="unit-price" class="form-label">Unit Price</label>
                            <input type="number" title="0" name="unit_price[]" class="form-control get-rate-bill" id="unit_price-0" aria-describedby="unitPriceHelp" >
                            @error('unit_price.0') 
                                <span class="text-red-500 text-danger">field is required</span> 
                            @enderror
                        </div>
                        <div class="float-left col-10 col-sm-6 col-md-4 col-lg-2">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" readonly name="total[]" class="form-control total-bill-amount" id="total-0" aria-describedby="totalHelp">
                            @error('total.0') 
                                <span class="text-red-500 text-danger">field is required</span> 
                            @enderror
                        </div>
                        <div class="mr-2 mt-6 float-left ">
                            <p class="plus-custom mcustom"><i class="fa fa-plus"></i></p>
                        </div>
                    </div>
                    
                    <div class="mt-3 col-12 col-sm-6 col-md-4 col-lg-2">
                        <label for="transport" class="form-label">Transport Charges</label>
                        <input type="number" name="transport_charges" class="form-control" id="transport" aria-describedby="totalHelp" >
                        @error('transport_charges') 
                            <span class="text-red-500 text-danger">field is required</span> 
                        @enderror
                    </div>
                    
                    <div class="mt-3 col-12 col-sm-6 col-md-4 col-lg-2">
                        <label for="Grand" class="form-label">Grand Total</label>
                        <input type="number" name="gandtotal" class="form-control" id="GrandT" readonly  >
                        @error('gandtotal') 
                            <span class="text-red-500 text-danger">field is required</span> 
                        @enderror
                    </div>
                    
                    
                   
                    
                    <div class="mb-3 mt-3 col-12 col-md-10 text-left">
                        <button type="submit" class="btn btn-primary btn-custom">Submit</button>
                    </div>
                </form>
                  
                </div>
              </div>
            </div>
            <script>
              document.addEventListener('DOMContentLoaded', () => {
                  let counter = 1;
          
                  function addNewFields() {
                      let newElement = document.createElement('div');
                      newElement.id = 'field-' + counter;
                      newElement.classList.add('align-items-end', 'w-100', 'flex-wrap', 'd-flex');
                      
                      let innerContent = `
                          <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                              <label for="raw-material" class="form-label">Raw Material</label>
                              <select name="raw_material_id[]" title="${counter}" id="raw-material-${counter}" class="form-control rawMaterialId custom_s">
                                  <option value="default" selected disabled>Select</option>
                                   @if($raw) 
                                    @foreach ($raw as $d)
                                        <option value="{{ $d->raw_material_id }}" >{{ $d->name }}</option>
                                    @endforeach
                                @endif
                              </select>
                              @error('raw_material_id.${counter}')
                                  <span class="text-red-500 text-danger">raw material field is required.</span>
                              @enderror
                          </div>
                          <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                              <label for="quantity" class="form-label">Unit</label>
                              <input type="text" readonly name="unit_name[]" class="form-control" id="unit-${counter}">
                              <input type="hidden" name="unit_id[]" class="form-control" id="unit_id-${counter}">
                              @error('unit_id.${counter}')
                                  <span class="text-red-500 text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                              <label for="quantity" class="form-label">Quantity</label>
                              <input type="number" name="qty[]" title="${counter}" class="form-control get-rate-bill" id="quantity-${counter}" >
                              @error('qty.${counter}')
                                  <span class="text-red-500 text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                              <label for="unit-price" class="form-label">Unit Price</label>
                              <input type="number" title="${counter}" name="unit_price[]" class="form-control get-rate-bill" id="unit_price-${counter}" >
                              @error('unit_price.${counter}')
                                  <span class="text-red-500 text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="col-8 col-sm-6 col-md-4 col-lg-2">
                              <label for="total" class="form-label">Total</label>
                              <input type="number" readonly name="total[]" class="form-control total-bill-amount" id="total-${counter}" aria-describedby="totalHelp" > 
                              @error('total.${counter}')
                                  <span class="text-red-500 text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="mr-2">
                              <p class="mcustom plus-custom"><i class="fa fa-plus"></i></p>
                          </div>
                          <div class="">
                              <p class="mcustom minus-custom"><i class="fa fa-minus"></i></p>
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
                          newElement.querySelector('.plus-custom').parentElement.style.display = 'block';
                          const buttons = document.querySelectorAll('.plus-custom');
                          const lastButton = buttons[buttons.length - 1];
                          lastButton.parentElement.style.display = 'block';
                      });
                      $('.custom_s').select2({
      
      theme: 'bootstrap4',
     
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
          
            <!-- /form input mask -->
          </div>
        </div>
      </div>
        <!-- /page content -->
        <?php $file="purchase.js"?>
@endsection