var csrfToken = $('meta[name="csrf-token"]').attr('content');


$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    ajax: {
        url: "/karahivendorlist",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        error: function(xhr, error, thrown) {
            alert('Error: ' + xhr.responseText);
        }
    },
    columns: [
        { data: "created_at" },
        { data: "name" },
        { data: "company" },
        { data: "contact" },
        { data: "address" },
        { data: "op_balance" },
        { data: "total_amount" },
        { data: "paid_amount" },
        { data: "remaining_amount" },
       
        
        
        
        { 
            data: null,
            render: function(data, type, row) {
                return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
                <li  class="dropdown-item"><a  href="karahivendor/recieve/${data.karai_vendor_id}"> Received Detail</a></li>
                <li class="dropdown-item"><a href="karahivendor/issue/${data.karai_vendor_id}">Issue Detail</a></li>
                <li class="dropdown-item"><a href="karahivendor/${data.karai_vendor_id}/payment">Payment Detail</a></li>
                <li class="dropdown-item"><a href="/karahivedor/${data.karai_vendor_id}/ledger" >Ledger</a></li>
                <li class="dropdown-item"><a href="/karahivendor/${data.karai_vendor_id}/edit" >Edit </a></li>
                </ul></div>`;
            }
        }
        
    ],
   
});

$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    function ajaxReq(_id) {
        $.ajax({
            url: '/getAvailableQtyOfRawMaterial',
            method: "post",
            data: { id: _id },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(res) {
                $("#issue-av-qty").text("Available Quantity: " + res);
            }
        });
    }

    function validateForm() {
        var vendorSelected = $('#vendor_id').val();
        var availableQty = parseFloat($("#issue-av-qty").text().replace('Available Quantity: ', ''));
        var enteredQty = $('#qty').val().trim();
        
        // Updated regex to allow decimal numbers like 0.5, 1.5, etc.
        var validQtyRegex = /^(0|[1-9]\d*)(\.\d+)?$/;
        var quantityValid = validQtyRegex.test(enteredQty) && parseFloat(enteredQty) > 0 && parseFloat(enteredQty) <= availableQty;
        console.log(enteredQty);
        if (quantityValid) {
            $('#submit-btn').removeClass('disabled').prop('disabled', false);
            $('#errr').text('');
        } else {
            $('#errr').addClass('text-danger').text('Enter a valid quantity (e.g., 0.5, 1.5) and ensure it does not exceed the available quantity.');
            $('#submit-btn').addClass('disabled').prop('disabled', true);
        }
    
        if (vendorSelected && quantityValid) {
            $('#submit-btn').removeClass('disabled').prop('disabled', false);
            $('#errr').text('');
        } else {
            $('#errr').addClass('text-danger').text('All fields are required and ensure the quantity is valid and does not exceed available quantity.');
            $('#submit-btn').addClass('disabled').prop('disabled', true);
        }
    }
    
    $(document).on('change', '.rawMaterialId', function() {
        var _id = $(this).val();
        $("#qty").val('');
        $('#errr').text('');
        $("#issue-av-qty").text("Loading Quantity.... ");
        ajaxReq(_id);
    });

    $(document).on("input", '.qunaityfields', function() {
        validateForm();
    });

    $('#demo-form2').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize();
        
        // Change button to loading state
        $('#submit-btn').prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="sr-only">Loading...</span>
        `);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    $('#demo-form2')[0].reset(); 
                    $('#errr').removeClass('text-danger').text(response.message);
                    $("#issue-av-qty").text('');
                    $('#myModal').modal('hide'); // Hide the modal
                    $('#submit-btn').addClass('disabled').prop('disabled', true);
                    toastr.success(response.message); 
                    
                    var printUrl = "/reckarahi/issue/" + response.id + "/print";
                
                    // Set iframe src dynamically
                    $('#printFrame').attr('src', printUrl).show();
                
                    document.getElementById('printFrame').onload = function() {
                        this.contentWindow.focus();
                        this.contentWindow.print();
                
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    };
                } else {
                    $('#errr').addClass('text-danger').text(response.message);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';

                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '<br>';
                });

                $('#errr').addClass('text-danger').html(errorMessage);
                toastr.error('An error occurred while processing your request. ' + errorMessage);
            },
            complete: function() {
                // Revert button to original state
                $('#submit-btn').prop('disabled', false).html('Submit');
            }
        });
    });
});



