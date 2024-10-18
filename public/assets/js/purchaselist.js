
var csrfToken = $('meta[name="csrf-token"]').attr('content');
$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    ajax: {
        url: "/purchaseListing",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        // dataSrc: function(json) {
        //     console.log('Data received from server:', json);
        //     if (json.data) {
        //         return json.data;
        //     } else {
        //         console.error('Invalid server response:', json);
        //         alert('Error: Invalid server response');
        //         return [];
        //     }
        // },
        error: function(xhr, error, thrown) {
            console.error('AJAX Error:', xhr.responseText);
            alert('Error: ' + xhr.responseText);
        }
    },
    columns: [
        { data: "purchase_date" },
        { data: "invoice" },
        { data: "supplier_name" },
        { data: "transportation_amount" },
        { data: "grand_total" },
      
       
        { 
            data: null,
            render: function(data, type, row) {
                // return '<button class="btn btn-info btn-sm btn-edit">Edit</button>';
                return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
                
                <li class="dropdown-item"><a data-purchase="${data.purchase_material_id}"  href="/supplier/${data.supplier_id}/purchasedetail/${data.purchase_material_id}">Detail</a></li>
                <li class="dropdown-item"><a href="/purchase/return/${data.purchase_material_id}" >Return</a></li>

                </ul></div>`;

            }
        }
    ]
});
