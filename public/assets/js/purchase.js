
var csrfToken = $('meta[name="csrf-token"]').attr('content');






function ajaxReq(_id,sid){
    $.ajax({
        url:'/purchase/getUnits',
        method:"post",
        data:{id:_id},
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success:function(unit){
            $("#unit-"+sid).val(unit.unit);
            $("#unit_id-"+sid).val(unit.unit_id);
        }
    })
}
$(document).on('change', '.rawMaterialId', function() {
    var _id = $(this).val();
    var si_d = $(this).attr('id');
    var sid = $(this).attr('title');
    ajaxReq(_id, sid);
});

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

    $('#GrandT').val(total.toFixed(3));
}

function getValues(row) {
    var rv = $("#quantity-" + row).val();
    var v = $("#unit_price-" + row).val();
    var vint = parseFloat(rv);
    var v_ = parseFloat(v);
    var total_ = v_ * vint;
    $("#total-" + row).val(total_.toFixed(3));
    calculateTotal();
}

$(document).on('keyup', '.get-rate-bill', function() {
    var row = $(this).attr('title');
    var row_value = $(this).val();
    getValues(row);
    console.log('key-')
});



$(document).ready(function() {
    // Function to validate the form
    function validateForm() {
        let isValid = true;

        
        if ($('select[name="supplier_id"]').val() === null) {
            var select2Element = $('select[name="supplier_id"]').next('.select2-container').find('.select2-selection');
            select2Element.addClass('is-invalid');
            isValid = false;
        } 
        else { 
            var select2Element = $('select[name="supplier_id"]').next('.select2-container').find('.select2-selection');
            select2Element.removeClass('is-invalid');
        }



        $('select[name="raw_material_id[]"]').each(function() {
            // Get the Select2 selection element inside the Select2 container
            var select2Element = $(this).next('.select2-container').find('.select2-selection');
        
            if ($(this).val() === null) {
                // Add 'is-invalid' class to the Select2 selection element
                select2Element.addClass('is-invalid');
                isValid = false;
            } else {
                // Remove 'is-invalid' class from the Select2 selection element
                select2Element.removeClass('is-invalid');
            }
        });

        $('input[name="sheet[]"]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('input[name="unit_name[]"]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('input[name="qty[]"]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('input[name="unit_price[]"]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('input[name="total[]"]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Check static fields
        if ($('input[name="transport_charges"]').val() === '') {
            $('input[name="transport_charges"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="transport_charges"]').removeClass('is-invalid');
        }
        if ($('input[name="date"]').val() === '') {
            $('input[name="date"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="date"]').removeClass('is-invalid');
        }


        if ($('input[name="narration"]').val() === '') {
            $('input[name="narration"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="narration"]').removeClass('is-invalid');
        }
        
       

        if ($('input[name="gandtotal"]').val() === '') {
            $('input[name="gandtotal"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="gandtotal"]').removeClass('is-invalid');
        }

        if ($('input[name="paidamount"]').val() === '') {
            $('input[name="paidamount"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="paidamount"]').removeClass('is-invalid');
        }

        return isValid;
    }

    // Handle form submission
    $('#demo-form2').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            
        }
    });
 
});



  