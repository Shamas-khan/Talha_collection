@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Production Issue</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><a href="{{ route('issue.create') }}" class="btn text-white bg-primary p-2" "="">
                        <i class="fas fa-plus"></i> Add Production Issue
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
                                <table id="ttable" class="table display table-bordered table-striped table-hover">
                                  <thead>
                                     <tr><th>#</th> <th>Date</th>
                                            <th>Vendor Name</th>
                                            <th>Ref Customer</th>
                                          <th>Product</th>
                                          <th>Total Qty</th>
                                          <th>Ready Qty</th>
                                          <th>Remaining Qty</th>
                                          <th>Production </th>
                                          <th>Action </th>
                                          
                                        
                                     </tr>
                               </thead>
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


          <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel2">Received Quantity</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <form id="productionform" action="{{ route('issue.recived') }}" method="post" class="w-100 align-items-center flex-wrap d-flex form-horizontal form-label-left">
                            @csrf
                            <!-- Hidden input to store issue_material_id -->
                            <input type="hidden" name="issue_material_id" id="issue_material_id" >
                            <input type="hidden" name="finish_product_id" id="finish_product_id" >
                            <div class="col-12">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control">
                                @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="modal-footer mt-4">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Are you sure?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Do you really want to delete this item? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>




 
            <?php $file = 'issuelist.js'; ?>
@endsection
