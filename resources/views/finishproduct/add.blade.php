@extends('layout.layout')
@section('content')
<div class="right_col" role="main" style="min-height: 723px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h2><a href="{{route('finishproduct.index')}}" class="btn text-white bg-primary p-2 m-2">
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
              <h2>Add Finish Product</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br>
              <form id="demo-form2" 
                 action="{{route('finishproduct.store')}}" method="post"
               class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left" novalidate="">
               @csrf
                  <div class="col-6 mb-3">
                      <label  class="form-label ">Product</label>
                      <input name="product_name" type="name" class="form-control col-12" >
                      @error('product_name') <span class="text-red-500 text-danger">Field is required</span> @enderror
                  </div>
                  <div id="input-fields-container" class="align-items-end w-100 flex-wrap d-flex">
                    
                  
                    <div class="col-4">
                      <label for="raw-material" class="form-label">Raw Material</label>
                      <select class="form-control rawMaterialId custom_s" title="0" name="raw_material_id[]">
                        <option value="default" selected disabled>Select</option>
                        @if($RawMaterial) 
                          @foreach ($RawMaterial as $d)
                            <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                          @endforeach
                        @endif
                      </select>
                      @error('raw_material_id') <span class="text-red-500 text-danger">Field is required</span> @enderror
                    </div>
                    
                    
                    

                      <div class=" col-2">
                          <label for="quantity" class="form-label">Quantity</label>
                          <input type="number" name="quantity[]" class="form-control" id="quantity" aria-describedby="quantityHelp">
                          @error('quantity.0') <span class="text-red-500 text-danger">Field is required</span> @enderror
                       
                      </div>
                      
                      
                      <div class=" mr-2">
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
      
        <script>
        

            document.addEventListener('DOMContentLoaded', () => {
              let counter = 1;
                function addNewFields() {
                    let newElement = document.createElement('div');
                    newElement.classList.add( 'align-items-end', 'w-100', 'flex-wrap', 'd-flex');
                    let innerContent = `
                        <div class=" col-4">
                          
                              <label for="raw_material" class="form-label">Raw Material</label>
                              <select class="form-control rawMaterialId select22" title="${counter}" name="raw_material_id[]" id="raw_material_id-${counter}">
                                <option value="default" selected disabled>Select</option>
                                @if($RawMaterial) 
                                    @foreach ($RawMaterial as $d)
                                        <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class=" col-2">
                             <label for="quantity" class="form-label">Quantity</label>
                                  <input type="number" name="quantity[]" class="form-control" id="quantity" aria-describedby="quantityHelp">
                        </div>
                        
                        
                        <div class=" mr-2">
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
                    $(".select22").each((_i, e) => {
        var $e = $(e);
        $e.select2({
        theme: 'bootstrap4',
          width: '100%' ,
          dropdownParent: $e.parent()
        });
      })
                }
        
                document.querySelectorAll('.plus-custom').forEach((button) => {
                    button.addEventListener('click', () => {
                        addNewFields();
                        button.parentElement.style.display = 'none';
                    });
                });
            });
        </script>
      </div>
    </div>
  </div>

    <?php $file = 'finishproduct.js'; ?>
@endsection