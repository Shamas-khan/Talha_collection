@extends('layout.layout')
@section('content')
 <!-- page content -->
 <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>List karahi Vendor</h3>
              </div>

            
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><a href="{{route('karahivendor.create')}}" class="btn text-white bg-primary p-2" ">
                      <i class="fas fa-plus"></i> Add karahi Vendor
                  </a>
                   </h2>
                  <h2>
                                <button type="button" class="btn text-white bg-primary p-2" data-toggle="modal"
                                    data-target=".bs-example-modal-lg">Issue Material</button>
                  </h2>


                  <h2>
    <a href="{{ route('karahivendor.receiving') }}" class="btn text-white bg-primary p-2">
        Received Material
    </a>
</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                     
                     
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row">
                      <div class="col-sm-12">
                          <div class="card-box table-responsive">
                              <div class="table-container">
                                  <table id="ttable" class="table display table-bordered table-striped table-hover">
                                      <thead>
                                          <tr>
                                              <th>Date</th>
                                              <th>Name</th>
                                              <th>Company</th>
                                              <th>Contact</th>
                                              <th>Address</th>
                                              <th>Opening</th>
                                              <th>Total</th>
                                              <th>Paid</th>
                                              <th>Remaining</th>
                                            
                                              <th>Action</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <!-- Data will be inserted here by DataTables -->
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        {{-- Model --}}
<div class="modal fade bs-example-modal-lg"id="myModal"  tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Issue Material</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="demo-form2" action="{{ route('karahivendorissue') }}" method="post" class=" w-100  form-horizontal form-label-left">
                    @csrf
                    <div id="input-fields-container" class="d-flex w-100 flex-wrap ">
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xxl-4  float-left">
                            <label class="form-label">Vendor</label>
                            <select class="form-control select22" name="vendor" id="vendor_id">
                                <option value="default" selected disabled>Select</option>
                                @if($vendor)
                                    @foreach ($vendor as $d)
                                        <option value="{{ $d->karai_vendor_id }}">{{ $d->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('vendor') <span class="text-red-500 text-danger">Field is required</span> @enderror
                        </div>

                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xxl-4  float-left">
                            <label class="form-label">Raw Material</label>
                            <select class="form-control rawMaterialId select22" name="raw_material_id" id="raw_material_id">
                                <option value="default" selected disabled>Select</option>
                                @if($rm)
                                    @foreach ($rm as $d)
                                        <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xxl-4 float-left">
                            <label class="form-label">Quantity</label>
                            <input type="text" name="quantity" class="form-control qunaityfields" id="qty">
                            <span class="text-center quaniitybyserver text-success" id="issue-av-qty"></span>
                        </div>
                    </div>
                    <div class="modal-footer w-100 mt-4">
                        <p id="errr" class="m-0"></p>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="submit-btn" disabled type="submit" class="btn btn-primary disabled">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<iframe id="printFrame" style="display:none;"></iframe>
{{-- End Model --}}




        <?php $file="karahi.js"?> 
        <!-- /page content -->
@endsection