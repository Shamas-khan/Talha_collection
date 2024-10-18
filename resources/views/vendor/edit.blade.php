@extends('layout.layout')
@section('content')

<div class="right_col" role="main" style="min-height: 724px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h2><a href="{{route('vendors.index')}}" class="btn text-white bg-primary p-2 m-2">
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
              <h2>edit Vendor</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br>
              <form action="{{ route('vendors.update', $vendor->vendor_id) }}" method="POST">
    @csrf
    @method('PUT') <!-- PUT method to update the resource -->

    <div class="form-group mb-3 col-12 col-md-4">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $vendor->name) }}" required>
    </div>

    <div class="form-group mb-3 col-12 col-md-4">
        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" class="form-control" value="{{ old('contact', $vendor->contact) }}" required>
    </div>

    <div class="form-group mb-3 col-12 col-md-4">
        <label for="cnic">CNIC:</label>
        <input type="text" name="cnic" id="cnic" class="form-control" value="{{ old('cnic', $vendor->cnic) }}" required>
    </div>

    <div class="form-group mb-3 col-12 col-md-4">
        <label for="address">Address:</label>
        <input name="address" id="address" class="form-control" value="{{ old('address', $vendor->address) }}" required>
        </input>
    </div>

    <div class="form-group mb-3 col-12 col-md-4">
        <label for="op_balance">Opening Balance:</label>
        <input type="text" name="op_balance"  class="form-control seperate" 
        value="{{ old('op_balance', abs($vendor->op_balance)) }}" >
    </div>

    <div class="form-group mb-3 col-12 col-md-4">
        <label for="transaction_type">Transaction Type:</label>
        <select name="transaction_type" id="transaction_type" class="form-control" required>
            <option value="debit" {{ old('transaction_type', $vendor->op_balance < 0 ? 'debit' : '') == 'debit' ? 'selected' : '' }}>Debit</option>
            <option value="credit" {{ old('transaction_type', $vendor->op_balance >= 0 ? 'credit' : '') == 'credit' ? 'selected' : '' }}>Credit</option>
        </select>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Update Vendor</button>
    </div>
</form>

            </div>
          </div>
        </div>
        <!-- /form input mask -->
      </div>
    </div>
  </div>
        <?php $file="dummy.js"?>
        @endsection