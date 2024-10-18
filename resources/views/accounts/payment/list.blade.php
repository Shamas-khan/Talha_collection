@extends('layout.layout')
@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>List Payment Voucher</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><a href="{{ route('payment.create') }}" class="btn text-white bg-primary p-2" ">
                              <i class="fas fa-plus"></i> Add Payment
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
                                    <div class="card-box table-responsive table-container">
                           
                                        <table id="ttable" class="table display table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                   
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Narration</th>
                                                    <th>Bank</th>
                                                    <th>Action</th>
                                                    
                                                  
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTable will populate rows here -->
                                            </tbody>
                                        </table>
                                    
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
                        </div>
                      </div>
                      <script>
                        $(document).ready(function() {
                                  var csrfToken = $('meta[name="csrf-token"]').attr('content');
                                  $("#ttable").DataTable({
                                    dom: "Bfrtip",
                                                responsive: true,
                                                processing: true,
                                                serverSide: true,
                                                ordering: false,
                                                pageLength: 500,
                                      ajax: {
                                        url: "{{ route('payment.list') }}",
                                          type: 'POST',
                                          headers: {
                                              'X-CSRF-TOKEN': csrfToken
                                          },
                                         
                                          error: function(xhr, error, thrown) {
                                              console.log('Error: ' + xhr.responseText);
                                          }
                                      },
                                      columns: [
                                          { data: "created_at" },
                                          { data: "person_type" },
                                          { data: "person_name" },
                                          { data: "amount" },
                                          { data: "narration" },
                                          { data: "bank_name" },
                                          { 
            data: null,
            render: function(data, type, row) { 
                return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
              
                <li class="dropdown-item"><a   href="payment/${data.paymentvoucher_id}/edit" >Edit </a></li>
                 <li class="dropdown-item"><a class="delete-button"  data-id="${data.paymentvoucher_id}"  href="javascript:void(0)" >Delete </a></li>`

              
            }
        }
                                          
                                          
                                        ]
                                  });
                        });
                        
$(document).ready(function() {
    // Add CSRF token to every AJAX request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.delete-button', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        // Show the confirmation modal
        $('#confirmDeleteModal').modal('show');

        // Handle the delete confirmation
        $('#confirmDeleteButton').off('click').on('click', function() {
            $.ajax({
                url: `/payment/${id}/delete`, // Adjust the URL if needed
                type: 'post',
                success: function(response) {
                    toastr.success(`Deleted `, 'Success');
                
                    $('#confirmDeleteModal').modal('hide');
                    window.location.reload(); // Page ko refresh karne ke liye
                },
                error: function(xhr) {
                    // Extract error message from the response
                    let errorMessage = 'An error occurred while deleting the item.';

                    // Check if response contains a message
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMessage = xhr.responseText; // Fallback to response text
                    }

                    // Show error notification
                    toastr.error(errorMessage, 'Error');

                    $('#confirmDeleteModal').modal('hide');
                }
            });
        });
    });
});

                </script>
                    </div>
                  </div>
                  
                </div>
                <?php $file = 'dummy.js'; ?>
                <!-- /page content -->
@endsection
