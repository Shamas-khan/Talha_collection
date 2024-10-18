var csrfToken = $('meta[name="csrf-token"]').attr('content');

// Initialize DataTable
$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 500,
    ajax: {
        url: "/issueListing",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        dataSrc: function(json) {
            console.log('Data received from server:', json);
            if (json.data) {
                return json.data;
            } else {
                console.error('Invalid server response:', json);
                alert('Error: Invalid server response');
                return [];
            }
        },
        error: function(xhr, error, thrown) {
            alert('Error: ' + xhr.responseText);
        }
       
    },
    columns: [
         { data: "issue_material_id" },
         { data: "created_at" },
        { data: "vendor_name" },
        { data: "customer_name" },
        { data: "product_name" }, 
        { data: "total_quantity" }, 
        { data: "received_quantity" }, 
        { data: "remaining_quantity" }, 
       
        {
            data: null,
            render: function(data, type, row) {
                return `<button id="add_production" type="button" data-fpro="${data.finish_product_id}" data-issue_material_id="${data.issue_material_id}" class="btn btn-info btn-sm btn-edit" data-toggle="modal" data-target=".bs-example-modal-sm">Recieve </button>`;
            }
        },
       
     
        
        
        
       
        
        { 
            data: null,
            render: function(data, type, row) { 
                return `<div class="dropdown">
                            <button class="btn btn-warning btn-sm actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-item"><a href="/getdetail/${data.issue_material_id}">Detail</a></li>
                                <li class="dropdown-item"><a href="/print/${data.issue_material_id}/table">Rec Print</a></li>
                                <li class="dropdown-item"><a href="/issueprint/${data.issue_material_id}">Issue Print</a></li>
                                <li class="dropdown-item"><a href="#" class="delete-button" data-id="${data.issue_material_id}">Delete</a></li>
                            </ul>
                        </div>`;
            }
        }
    ]
});
$(document).ready(function() {
$(document).on('click', '#add_production', function() {
    var issue_material_id = $(this).data('issue_material_id');
    $('#issue_material_id').val(issue_material_id);
});
$(document).on('click', '#add_production', function() {
    var finish_product_id = $(this).data('fpro');
    
    $('#finish_product_id').val(finish_product_id);
});
});


$(document).ready(function() {
    // Fetching the cid value from head data attribute
    var cid = $('#head').data('cid');
    
    // Constructing the URL for AJAX request
    var url = "/getdetail/" + cid + "/detail";
   // Optional: Alert to check the constructed URL
    
    // Initializing DataTable with server-side processing
    var table = $("#detail").DataTable({
        dom: "Bfrtip",
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: false,
        pageLength: 50,
        ajax: {
            url: url, // URL for the AJAX request
            type: 'POST', // HTTP method (POST in this case)
            headers: {
                'X-CSRF-TOKEN': csrfToken // CSRF token for security
            },
            // Function to process data received from server
            dataSrc: function(json) {
                console.log('Data received from server:', json);
                if (json.data) {
                    return json.data; // Returning data array
                } else {
                    console.error('Invalid server response:', json);
                    alert('Error: Invalid server response');
                    return []; // Returning empty array on error
                }
            },
            // Function to handle AJAX errors
            error: function(xhr, error, thrown) {
                alert('Error: ' + xhr.responseText);
            }
        },
        columns: [
            { data: "raw_material_name"},
            { data: "Required_qty" },
            { data: "issue_qty" },
            { data: "Remaining_qty" },
            { data: "issue_qty_in_gaz" },
            { data: "created_at" },
            
        ]
    });
    $('#detail_wrapper, #detail').css({
        'overflow-y': 'hidden',
        'padding-bottom': '24px'
    });

    // End of DataTable initialization

});


$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    function ajaxReq(_id, sid) {
        $.ajax({
            url: '/getAvailableQtyOfRawMaterial',
            method: "post",
            data: { id: _id },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(res) {
                $("#issue-av-qty-" + sid).text("Available Quantity: " + res);
                $("#issue-av-qty-" + sid).data('available-qty', res);
            },
            error: function(xhr, status, error) {
                $("#issue-av-qty-" + sid).text("Not Available");
                $("#issue-av-qty-" + sid).data('available-qty', 0); // Optional, set available quantity to 0 or some default value
            }
        });
    }

    $(document).on('change', '.rawMaterialId', function() {
        var _id = $(this).val();
        var sid = $(this).attr('title');
        $("#qty-" + sid).val('');
        $("#issue-av-qty-" + sid).text("Loading Quantity....");
        ajaxReq(_id, sid);
    });

    function validateForm() {
        let isValid = true;

        $('select[name="raw_material_id[]"]').each(function() {
            if ($(this).val() === null) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('input[name="quantity[]"]').each(function() {
            var sid = $(this).attr('id').split('-')[1]; // Extract sid from the input id
            var availableQty = $("#issue-av-qty-" + sid).data('available-qty');
            var inputQty = $(this).val();

            if (inputQty === '' || parseFloat(inputQty) > parseFloat(availableQty)) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        return isValid;
    }

    $('#issueeezero').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            $('#errr').addClass('text-danger').text('ensure quantity is equal to or less than available quantity.');
        }
         else {
            $('#errr').removeClass('text-danger').text('');
        }
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
                url: `/issue/${id}/delete`, // Adjust the URL if needed
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

