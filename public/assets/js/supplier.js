$(document).ready(function() {
    $('#goBackBtn').click(function() {
      window.history.back();
    });
  });
  
var csrfToken = $('meta[name="csrf-token"]').attr('content');

$("#ttable").DataTable({
    dom: "Bfrtip",
    responsive: true,
    processing: true,
    serverSide: true,
    ordering: false,
    pageLength: 50,
    ajax: {
        url: "/supplierListing",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        
    },
    columns: [
        {
            data: 'created_at',
            render: function (data, type, row) {
                if (data) {
                    var date = new Date(data);
                    
                    var year = date.getFullYear();
                    var month = String(date.getMonth() + 1).padStart(2, '0'); 
                    var day = String(date.getDate()).padStart(2, '0');
                    
                    // Format as YYYY/MM/DD
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
                return ` <div class="dropdown">
                <button class="btn btn-warning btn-sm  actionpadding dropdown-toggle" type="button" data-toggle="dropdown">Action<span class="caret"></span></button> <ul class="dropdown-menu">
                <li  class="dropdown-item"><a   href="/supplier/${data.supplier_id}/payments"> Payment Detail</a></li>
                <li class="dropdown-item"><a href="/supplier/${data.supplier_id}">Purchase Detail</a></li>
                <li class="dropdown-item"><a href="/supplierledger/${data.supplier_id}" >Ledger </a></li>
                <li class="dropdown-item"><a href="suppliers/${data.supplier_id}/edit" >Edit </a></li>
                </ul></div>`;
            }
        }
        
    ],

   
});



$(document).ready(function() {

    
    
    // Extract supplier ID from the h3 element
    var supplierId = $('h3').data('id');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Initialize DataTable
    $("#supplier_pd").DataTable({
        dom: "Bfrtip",
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: false,
        pageLength: 50,
        ajax: {
            url: `/supplier/${supplierId}/purchasedetail`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            dataSrc: function(json) {
                // console.log('Data received from server:', json);
                if (json.data) {
                    return json.data;
                } else {
                    // console.error('Invalid server response:', json);
                    // alert('Error: Invalid server response');
                    return [];
                }
            },
            error: function(xhr, error, thrown) {
                alert('Error: ' + xhr.responseText);
            }
        },
        columns: [
            { data: "purchase_date" },
            { data: "invoice_no" },
            { data: "supplier_name" },
            { data: "transportation_amount" },
            { data: "grand_total" },
            
            
            { 
                data: null,
                render: function(data, type, row) {
                    return `<a data-purchase="${data.purchase_material_id}" class="btn btn-secondary btn-sm" href="/supplier/${supplierId}/purchasedetail/${data.purchase_material_id}">Detail</a>`;
                }
            },
            
        ]
    });
    $('#supplier_pd_wrapper, #supplier_pd').css({
        'overflow-y': 'hidden',
        'padding-bottom': '24px'
    });
});

$(document).ready(function() {
    
    // Extract supplier ID from the h3 element
    var supplierId = $('h3').data('id');
    var purchase_detail_id = $('h3').data('purchase');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Initialize DataTable
    $("#material_detail").DataTable({
        dom: "Bfrtip",
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: false,
        pageLength: 50,
        ajax: {
            url: `/supplier/${supplierId}/purchasedetail/${purchase_detail_id}/detail`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            dataSrc: function(json) {
                // console.log('Data received from server:', json);
                if (json.data) {
                    return json.data;
                } else {
                    // console.error('Invalid server response:', json);
                    alert('Error: Invalid server response');
                    return [];
                }
            },
            error: function(xhr, error, thrown) {
                alert('Error: ' + xhr.responseText);
            }
        },
        columns: [
            { data: "raw_material_name" },
            { data: "unit_name" },
            { data: "quantity" },
            { data: "unit_price" },
            { data: "total_amount" },
           
            
            
        ]
    });
    $('#material_detail_wrapper, #material_detail').css({
        'overflow-y': 'hidden',
        'padding-bottom': '24px'
    });
});