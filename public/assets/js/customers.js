
var csrfToken = $('meta[name="csrf-token"]').attr('content');

$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    ajax: {
        url: "/customersListing",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        error: function(xhr, error, thrown) {
            alert('Error: ' + xhr.responseText);
        }
    },
    columns: [
        {
            data: 'created_at',
            render: function (data, type, row) {
                if (data) {
                    var date = new Date(data);
                    
                    var year = date.getFullYear();
                    var month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed, so add 1
                    var day = String(date.getDate()).padStart(2, '0');
                    
                  
                    return `${year}-${month}-${day}`;
                }
                return '';
            }
        },
        { data: "name" },
        { data: "company" },
        { data: "contact" },
        { data: "address" },
        { data: "op_balance" },
        { data: "total_amount" },
        { data: "paid_amount" },
        { data: "remaining_amount" },
       
        
       
        
       { 
            data: null,
            render: function(data, type, row) { 
                return ` <div class="dropdown"><button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
               <li class="dropdown-item"><a   href="/customerdetail/${data.customer_id}" >Sell Detail</a></li>
                <li class="dropdown-item"><a   href="/customer/${data.customer_id}/payments">Payment Detail</a></li>
                <li class="dropdown-item"><a   href="/customers/${data.customer_id}/edit" >Edit </a></li>
                <li class="dropdown-item"><a   href="/customerledger/${data.customer_id}" > Ledger</a></li>
                </ul></div>`;
            }
        }
        
        
    ]
});
