@extends('layout.layout')
@section('content')
      <!-- page content -->
      <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h2><a href="{{route('banks.index')}}" class="btn text-white bg-primary p-2 m-2" >
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
                    <h2>Add Bank</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2"  action="{{route('banks.store')}}" method="post" class="w-100   flex-wrap d-flex form-horizontal form-label-left" novalidate="">
                      @csrf
                      
                      <div class="mb-3 col-12 col-md-6">
                        <label for="names" class="form-label">Branch Code</label>
                        <input type="text" name="branch_code" class="form-control {{ $errors->has('branch_code') ? 'is-invalid' : '' }}" value="{{ old('branch_code') }}"  >
                        @error('branch_code') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                       
                      </div>
                      <div class="mb-3 col-12 col-md-6">
                        <label for="names" class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control {{ $errors->has('bank_name') ? 'is-invalid' : '' }}" value="{{ old('bank_name') }}"  >
                        @error('bank_name') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                       
                      </div>
                      <div class="mb-3 col-12 col-md-6">
                        <label for="cnames" class="form-label">Account Number</label>
                        <input type="text" name="account_number" class="form-control {{ $errors->has('account_number') ? 'is-invalid' : '' }}" value="{{ old('account_number') }}">
                        @error('account_number') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                        
                      </div>
                      <div class="mb-3 col-12 col-md-6">
                        <label for="phone" class="form-label">Opening Balance</label>
                        <input type="text" name="opening_balance" class="form-control {{ $errors->has('opening_balance') ? 'is-invalid' : '' }}" value="{{ old('opening_balance') }}">
                        @error('opening_balance') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
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