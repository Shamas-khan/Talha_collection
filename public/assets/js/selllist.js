var csrfToken = $('meta[name="csrf-token"]').attr('content');

// Initialize DataTable
$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    
    ajax: {
        url: "/sellListing",
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
        { data: "sell_order_id" },
        { data: "customer_name" },
        { data: "order_date" },
        { data: "order_completion_date" },
        {
            data: "status"
        },
        {
            data: null,
            render: function(data, type, row) {
                // Check if the status is "Processing"
                if (row.status === 'Processing') {
                    return '<button class="btn btn-info btn-sm btn-edit" disabled>Create sell</button>';
                }
                else if(row.status === 'Completed'){
                    return '<button class="btn text-white  btn-sm btn-edit bg-success" >Done</button>';
                } else {
                    return `<a href="/sell/create/${row.sell_id}" class="btn btn-info btn-sm btn-edit">Create sell</a>`;
                }
            }
        },
        
        { 
            data: null,
            render: function(data, type, row) {
                return `<a href="/sell/detail/${row.sell_id}" class="btn btn-info btn-sm btn-edit">Detail</a>`;
            }
         },
        { 
            data: null,
            render: function(data, type, row) {
                return '<button class="btn btn-info btn-sm btn-edit">Edit</button>';
            }
        }
    ]
});
$(document).ready(function() {
    function calculateTotal() {
        var total = 0;
        $('.total-bill-amount').each(function() {
            var rate = $(this).val();
            if (rate == "") {
                rate = 0;
            }
            var bill_amount = parseFloat(rate);
            total += bill_amount;
        });
        $('#GrandT').val(total);
    }

    function getValues(row) {
        var rv = $("#quantity-" + row).val();
        var v = $("#unit_price-" + row).val();
        var vint = parseFloat(rv) || 0; // handle empty or invalid values
        var v_ = parseFloat(v) || 0; // handle empty or invalid values
        var total_ = v_ * vint;
        $("#total-" + row).val(total_);
        calculateTotal();
    }

    $(document).on('keyup', '.get-rate-bill', function() {
        var row = $(this).attr('title');
        getValues(row);
    });
});


$(document).ready(function() {
    // Function to validate the form
    function validateForm() {
        let isValid = true;

        // Check the karai_vendor_id field
        if ($('select[name="customer_id"]').val() === null) {
            $('select[name="customer_id"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('select[name="customer_id"]').removeClass('is-invalid');
        }
        // Check the karai_vendor_id field
        if ($('input[name="order_date"]').val() === '') {
            $('input[name="order_date"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="order_date"]').removeClass('is-invalid');
        }
        // Check the karai_vendor_id field
        if ($('input[name="order_completion_date"]').val() === '') {
            $('input[name="order_completion_date"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="order_completion_date"]').removeClass('is-invalid');
        }

        // Check dynamic fields
        $('select[name="finish_product_id[]"]').each(function() {
            if ($(this).val() === null) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('input[name="order_quantity[]"]').each(function() {
            if ($(this).val() === '') {
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
