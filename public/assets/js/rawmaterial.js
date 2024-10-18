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
        url: "/rawmaterialListing",
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
        { data: "unit_name" }, // Adjusted to match the correct field from the server
        { data: "available_quantity" }, // Adjusted to match the correct field from the server
        { 
            data: null,
            render: function(data, type, row) {
                return '<button class="btn btn-info btn-sm btn-edit">Edit</button>';
            }
        }
    ]
});
