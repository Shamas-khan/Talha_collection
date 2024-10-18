@extends('layout.layout')
@section('content')
      <!-- page content -->
      <div class="right_col" role="main">
        <!-- top tiles -->
        <div class="row"  >
          <h2 class="count_top col-4"> Trail Balance</h2><br>
          <div class="tile_count" style=" width: 100%;">
          <div class="col-md-2 col-sm-4  tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Customers</span><br>
            <span class="count_top"> <i class="green">Remaining Balance </i> </span>
            <div class="count"><i class="green"></i>{{$customertotalRemainingAmount ?? 0}}</div>
           
          </div>
          <div class="col-md-2 col-sm-4  tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Suppliers</span><br>
            <span class="count_top"> <i class="green">Remaining Balance </i> </span>
            <div class="count"><i class="green"></i>{{$suppliertotalRemainingAmount ?? 0}}</div>
           
          </div>
          <div class="col-md-2 col-sm-4  tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Embroidery Vendors </span><br>
            <span class="count_top"> <i class="green">Remaining Balance </i> </span>
            <div class="count"><i class="green"></i>{{$karai_vendor ?? 0}}</div>
           
          </div>
          <div class="col-md-2 col-sm-4  tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Karigar Vendors</span><br>
            <span class="count_top"> <i class="green">Remaining Balance </i> </span>
            <div class="count"><i class="green"></i>{{$vendor ?? 0}}</div>
           
          </div>
          <div class="col-md-2 col-sm-4  tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Direct Parties</span><br>
            <span class="count_top"> <i class="green">Remaining Balance </i> </span>
            <div class="count"><i class="green"></i>{{$parties ?? 0}}</div>
           
          </div>
          <div class="col-md-2 col-sm-4  tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> Employees</span><br>
            <span class="count_top"> <i class="green">Remaining Salaries </i> </span>
            <div class="count"><i class="green"></i>{{$employee ?? 0}}</div>
           
          </div>
        @if ($bank && $bank->count() > 0)
    <h2 class="count_top col-4">Banks</h2><br>
    @foreach ($bank as $d)
        <div class="col-md-2 col-sm-4 tile_stats_count">
            <span class="count_top"><i class="fa fa-user"></i> {{$d->bank_name}}</span><br>
            <span class="count_top"><i class="green">Running Balance</i></span>
            <div class="count"><i class="green"></i> {{ number_format($d->running_balance) }}</div>
        </div>
    @endforeach
@endif
      
         
          
        </div>
      </div>
        <!-- /top tiles -->

       
        
        <?php $file="dummy.js";?>
        @endsection