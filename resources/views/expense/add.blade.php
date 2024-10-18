@extends('layout.layout')
@section('content')


<div class="right_col" role="main" style="min-height: 723px;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h2><a href="{{route('expense.index')}}" class="btn text-white bg-primary p-2 m-2">
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
              <h2>Add Expense</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br>
              <form id="demo-form2" action="{{route('expense.store')}}" method="post"  class=" form-horizontal form-label-left" novalidate="">
                   @csrf <div class="float-left col-12 col-sm-6 col-md-4 col-lg-2">
                          <label for="unit-price" class="form-label">Date</label>
                          <input type="date" name="date" class="form-control" id="unit-price" >
                        @error('date') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      </div>
                     
                      <div class="float-left col-3">
                          <label for="supplier" class="form-label">Expense Category</label>
                          <select  class="form-control" name="expense_category_id" >
                            <option value="default" selected disabled>Select Expense</option>
                            @if($expensecategory) @foreach ($expensecategory as $d )
                                <option value="{{$d->expense_category_id}}">{{$d->name}}</option>
                            @endforeach
                            @endif
                        </select>
                      @error('expense_category_id') <span class="text-red-500 text-danger">Expense category is required</span> @enderror
                      </div>
                      
                      <div class="float-left col-4">
                        <label for="Narration" class="form-label">Narration</label>
                        <input type="text" name="reason" class="form-control" id="Narration" >
                      @error('reason') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      </div>
                      
                      <div class="float-left col-12 col-sm-6 col-md-4 col-lg-3">
                          <label for="quantity" class="form-label">Amount</label>
                          <input type="number" name="amount" class="form-control" id="quantity" >
                          @error('amount') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      
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
    
      


            <?php $file = 'dummy.js'; ?>
@endsection
