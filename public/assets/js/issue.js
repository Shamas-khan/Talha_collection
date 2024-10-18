$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    function calculateTotal() {
        var orderQty = parseFloat($("#order_qty").val());
        var unitPrice = parseFloat($("#unit_price").val());
        

        if (!isNaN(orderQty) && !isNaN(unitPrice)) {
            var total = orderQty * unitPrice;
            
            $("#total").val(total); // Display the total with 2 decimal places
        } else {
            $("#total").val(''); // Clear the total if inputs are not valid numbers
        }
    }
    
    $("#order_qty, #unit_price").on("input", calculateTotal);
    
    $("#calculate").on("click", function() {
        var orderqty = $("#order_qty").val();
        var vendor = $("#vendor_id").val();
        var product = $("#product_id").val();
        var unit_price = $("#unit_price").val();
        var customer = $("#customer_id").val();
       

        if (orderqty === '' || vendor === '' || product === '' || unit_price === '' || customer === '' || customer === null) {
            alert("All Fields are Required");
            return;
        }
    

        // Show loading indicator
        $("#load").removeClass('d-none').text('Loading...');
        $("#raw-detail-issue").html('');

        $.ajax({
            url: "/issue/getfinishproductqty",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                product: product,
                vendor: vendor,
                orderqty: orderqty,
                unit_price: unit_price,
            }),
            success: function(response) {
                // console.error(response);
                $("#load").addClass('d-none');
                // Update the HTML content
                $("#raw-detail-issue").html(response);
                // Show the result container
                $("#show").removeClass('d-none');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert("An error occurred: " + xhr.responseText);
                // Hide loading indicator in case of error
                $("#load").addClass('d-none');
            }
        });
    });
});
