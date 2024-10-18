var csrfToken = $('meta[name="csrf-token"]').attr('content');

// Initialize DataTable
$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    pageLength: 50,
    ordering: false,
    ajax: {
        url: "/expenseListing",
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
        { data: "date" },
        { data: "expense_category_name" },
        { data: "reason" },
        { data: "amount" },
       
       
        
        { 
            data: null,
            render: function(data, type, row) {
                return '<button class="btn btn-info btn-sm btn-edit">Edit</button>';
            }
        }
    ]
});
