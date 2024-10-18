var csrfToken = $('meta[name="csrf-token"]').attr('content');

$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    ajax: {
        url: "/vendorListing",
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
            data: 'vendor_id',
            
        },
        { data: "name" },
        { data: "cnic" },
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
                <li  class="dropdown-item"><a  href="/vendor/${data.vendor_id}"> Vendor Detail</a></li>
                <li class="dropdown-item"><a href="/vendor/${data.vendor_id}/payment">Payment Detail</a></li>
                <li class="dropdown-item"><a href="/vendor/${data.vendor_id}/ledger" >Ledger</a></li>
                <li class="dropdown-item"><a href="/vendors/${data.vendor_id}/edit" >Edit Vendor</a></li>
                </ul></div>`;
            }
        }
    ],
    
   
});


