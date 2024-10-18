
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
    var _id =$(this).val();
    var si_d =$(this).attr('id');
    var sid =$(this).attr('title');
    ajaxReq(_id,sid);
})

function calculateTotal() {
    var grandTotal = 0;

    $('.total-bill-amount').each(function() {
        var total = parseFloat($(this).val());
        if (isNaN(total)) {
            total = 0;
        }
        grandTotal += total;
    });

    $('#GrandT').val(grandTotal);
}

function getValues(row) {
    var kilogram = parseFloat($("#kilogram-" + row).val());
    var sheet = parseFloat($("#sheet-" + row).val());
    var unitPrice = parseFloat($("#unit_price-" + row).val());

    // console.log("Kilogram:", kilogram);
    // console.log("Sheet:", sheet);
    // console.log("Unit Price:", unitPrice);

     // Validate and calculate based on valid inputs
     if (!isNaN(kilogram) && !isNaN(unitPrice)) {
        var total = kilogram * unitPrice;
        $("#total-" + row).val(total);
    } else if (!isNaN(sheet) && !isNaN(unitPrice)) {
        var total = sheet * unitPrice;
        $("#total-" + row).val(total);
    } else {
        // Handle case where inputs are not valid numbers
        $("#total-" + row).val(0); // or display an error message
    }
    calculateTotal();
}


$(document).on('keyup', '.get-rate-bill', function() {
    var row = $(this).attr('id').split('-')[1];
    getValues(row);
});



$(document).ready(function() {
    // Function to validate the form
    function validateForm() {
        let isValid = true;

        // Check the karai_vendor_id field
        if ($('select[name="supplier_id"]').val() === null) {
            $('select[name="supplier_id"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('select[name="supplier_id"]').removeClass('is-invalid');
        }

        // Check dynamic fields
        $('select[name="raw_material_id[]"]').each(function() {
            if ($(this).val() === null) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
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



  