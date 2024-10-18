
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
        url: "/expensecatListing",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        error: function(xhr, error, thrown) {
            alert('Error: ' + xhr.responseText);
        }
    },
    columns: [
        { data: "name" },
        { 
            data: null,
            render: function(data, type, row) {
                return '<button class="btn btn-info btn-sm btn-edit">Edit</button>';
            }
        }
        
    ]
});
