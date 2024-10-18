var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Make sure CSRF token is included


    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Make sure CSRF token is included

        $("#ttable").DataTable({
            dom: "Bfrtip",
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            pageLength: 50,

            // Ajax configuration for server-side processing
            ajax: {
                url: "/finishproductlisting",  // Update this route as per your requirement
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken  // Add CSRF token in headers
                },
                dataSrc: function(json) {
                    console.log('Data received from server:', json);  // Debug the server response
                    if (json.data) {
                        return json.data;
                    } else {
                        console.error('Invalid server response:', json);
                        alert('Error: Invalid server response');
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('Error details:', xhr, error, thrown);
                    alert('Error: ' + (xhr.responseText || 'An unexpected error occurred'));
                }
            },

            // Define columns with proper data keys
            columns: [
                { data: "product_name" },  // Column for product name
                { 
                    data: "finish_product_id",  // Use finish_product_id to build the detail URL
                    render: function(data, type, row) {
                        return `<a href="/finishproduct/${data}/detail" class="btn btn-info btn-sm btn-edit">Detail</a>`;
                    }
                },
                { 
                    data: "finish_product_id",  // Use finish_product_id to build the edit URL
                    render: function(data, type, row) {
                        return `<a href="/finishproduct/${data}/edit" class="btn btn-info btn-sm btn-edit">Edit</a>`;
                    }
                }
            ]
        });
    });

$(document).ready(function() {
    // Function to validate the form
    function validateForm() {
        let isValid = true;

        // Check the karai_vendor_id field
        if ($('input[name="product_name"]').val() === '') {
            $('input[name="product_name"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="product_name"]').removeClass('is-invalid');
        }

        // Check dynamic fields
        $('select[name="raw_material_id[]"]').each(function() {
            if ($(this).val() === null) {
                $(this).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                isValid = false;
            } else {
                $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }
        });
        
        $('input[name="quantity[]"]').each(function() {
            if ($(this).val() === '' || parseFloat($(this).val()) <= 0) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        return isValid;
    }

    // Handle form submission
    $('#demo-form2').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            
        }
    });
});


