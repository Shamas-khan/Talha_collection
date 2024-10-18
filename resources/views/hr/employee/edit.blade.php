@extends('layout.layout')
@section('content')
      <!-- page content -->
      <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h2><a href="{{route('employee.index')}}" class="btn text-white bg-primary p-2 m-2" >
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
                    <h2>Add Employee</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2" action="{{ route('employee.update', $employee->employee_id) }}" method="post" class="w-100 flex-wrap d-flex form-horizontal form-label-left">
    @csrf
    @method('PUT') <!-- Update ke liye PUT method ka use karen -->
    
    <div class="mb-3 col-12 col-md-4">
        <label for="names" class="form-label">Name</label>
        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $employee->name) }}">
        @error('name') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-3 col-12 col-md-4">
        <label for="cnames" class="form-label">Father Name</label>
        <input type="text" name="fname" class="form-control {{ $errors->has('fname') ? 'is-invalid' : '' }}" value="{{ old('fname', $employee->fname) }}">
        @error('fname') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-3 col-12 col-md-4">
        <label for="phone" class="form-label">Contact</label>
        <input type="number" name="contact" class="form-control {{ $errors->has('contact') ? 'is-invalid' : '' }}" value="{{ old('contact', $employee->contact) }}">
        @error('contact') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-3 col-12 col-md-7">
        <label for="addresss" class="form-label"> Address</label>
        <input type="text" name="address" class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" value="{{ old('address', $employee->address) }}">
        @error('address') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-3 col-12 col-md-5">
        <label for="phone" class="form-label"> Basic Salary</label>
        <input type="text" name="basicsalary" id="basicsalary" class="form-control {{ $errors->has('basicsalary') ? 'is-invalid' : '' }}" value="{{ old('basicsalary', number_format($employee->basicsalary)) }}">
        @error('basicsalary') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-3 col-12 col-md-10 text-left">
        <button type="submit" class="btn btn-primary btn-custom">Update</button>
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