$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $("#forecasting").on("click", function() {
        var fpro = $("#fpro").val();
        
        if (!fpro ) {
            alert("field is required");
            return;
        }
        $("#raw-detail-issue").addClass('text-success').text("loading");
        $("#show").removeClass('d-none');
        // alert("Order: " + order + ", Vendor: " + vendor + ", Product: " + product);

        $.ajax({
            url: "getforcasting",
            method:"post",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {fpro: fpro},

            success: function(response) {
                $("#raw-detail-issue").html(response);
                $("#show").removeClass('d-none');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert("An error occurred: " + xhr.responseText);
            }
        });
    });
});
