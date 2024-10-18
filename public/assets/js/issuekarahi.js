function calculateTotal() {
    var total = 0;
    // var bill_labour=parseInt($("#bill-labour").val());
    // var bill_expense=parseInt($("#bill-expense").val());
  
    $('.total-bill-amount').each(function() {
      var rate=$(this).val();
      if(rate==""){
        rate=0;
      }
      var bill_amount=parseFloat(rate);
      total += bill_amount;
    });
    // total+=bill_labour;
    // total+=bill_expense
    $('#GrandT').val(total);
}
function getValues(row){
  var rv=$("#quantity-"+row).val();
  var v=$("#unit_price-"+row).val();
  var vint = parseFloat(rv);
  var v_ = parseFloat(v);
  var total_=v_*vint;
  $("#total-"+row).val(total_);
  calculateTotal();
  }
    $(document).on('keyup', '.get-rate-bill', function() {
    var row=$(this).attr('title');
    var row_value=$(this).val();
    getValues(row);
   });



$(document).ready(function() {
    // Function to validate the form
    function validateForm() {
        let isValid = true;

       

      
        var selectElement = $('select[name="karai_vendor_id"]');
        var select2Selection = selectElement.next('.select2-container').find('.select2-selection');
        
        if (selectElement.val() === 'default' || selectElement.val() === null || selectElement.val() === '') {
            select2Selection.addClass('is-invalid');
            selectElement.siblings('.text-danger').show(); // Show error message
            isValid = false;
        } else {
            select2Selection.removeClass('is-invalid');
            selectElement.siblings('.text-danger').hide(); // Hide error message
        }

       $('select[name="karai_machine_id[]"]').each(function() {
            var selectElement = $(this);
            var select2Container = selectElement.next('.select2-container');

            // Check if the value is null or an empty string
            if (selectElement.val() === null || selectElement.val() === '') {
                select2Container.find('.select2-selection').addClass('is-invalid');
                selectElement.siblings('.text-danger').show(); // Show error message
                isValid = false;
            } else {
                select2Container.find('.select2-selection').removeClass('is-invalid');
                selectElement.siblings('.text-danger').hide(); // Hide error message
            }
       });

       $('select[name="raw_material_id[]"]').each(function() {
        var selectElement = $(this);
        var select2Container = selectElement.next('.select2-container');
    
        // Check if the value is null or an empty string
        if (selectElement.val() === null || selectElement.val() === '') {
            select2Container.find('.select2-selection').addClass('is-invalid');
            selectElement.siblings('.text-danger').show(); // Show error message
            isValid = false;
        } else {
            select2Container.find('.select2-selection').removeClass('is-invalid');
            selectElement.siblings('.text-danger').hide(); // Hide error message
        }
    });

        $('input[name="sheet[]"]').each(function() {
          var value = $(this).val();
      
          // Check if the value is empty or zero
          if (value === '' || value === '0') {
              $(this).addClass('is-invalid');
              isValid = false;
          } else {
              $(this).removeClass('is-invalid');
          }
      });

      

      $('input[name="qty[]"]').each(function() {
        var value = $(this).val();
        if (value === '' || value === '0') {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $('input[name="unit_price[]"]').each(function() {
      var value = $(this).val();
      if (value === '' || value === '0') {
          $(this).addClass('is-invalid');
          isValid = false;
      } else {
          $(this).removeClass('is-invalid');
      }
  });

        // $('input[name="total[]"]').each(function() {
        //     if ($(this).val() === '') {
        //         $(this).addClass('is-invalid');
        //         isValid = false;
        //     } else {
        //         $(this).removeClass('is-invalid');
        //     }
        // });
        $('input[name="total[]"]').each(function() {
          var value = $(this).val();
          if (value === '' || value === '0') {
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
        if ($('input[name="invoice_no"]').val() === '') {
            $('input[name="invoice_no"]').addClass('is-invalid');
            isValid = false;
        } else {
            $('input[name="invoice_no"]').removeClass('is-invalid');
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