@extends('layout.layout')
@section('content')
      <!-- page content -->
      <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h2><a href="{{route('parties.index')}}" class="btn text-white bg-primary p-2 m-2" >
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
                    <h2>Add Direct Party</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2"  action="{{route('parties.store')}}" method="post" class="w-100   flex-wrap d-flex form-horizontal form-label-left" novalidate="">
                      @csrf
                      
                      <div class="mb-3 col-12 col-md-4">
                        <label for="names" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}"  >
                        @error('name') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                       
                      </div>
                      
                      <div class="mb-3 col-12 col-md-4">
                        <label for="phone" class="form-label">Contact</label>
                        <input type="number" name="phone_number" class="form-control {{ $errors->has('phone_number') ? 'is-invalid' : '' }}" value="{{ old('phone_number') }}">
                        @error('phone_number') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      </div>
                      
                      <div class="mb-3 col-12 col-md-4">
                        <label for="phone" class="form-label">Opening Balance</label>
                        <input type="text" name="op_balance" id="op_balance" class="form-control {{ $errors->has('op_balance') ? 'is-invalid' : '' }}" value="{{ old('op_balance') }}">
                        @error('op_balance') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      </div>
                       <div class="mb-3 col-12 col-md-10 text-left">
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
        <!-- /page content -->
        <?php $file="dummy.js"?>
        @endsection