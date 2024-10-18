$(document).ready(function() {

    
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
        url: "/stockdetail",
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
        { data: "product_name" },
        { data: "quantity" },
       
        
    ]
});

$('#ttable_wrapper, #ttable').css({
    'overflow-y': 'hidden',
    'padding-bottom': '24px'
});
})