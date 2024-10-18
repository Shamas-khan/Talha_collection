$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('#ttable').DataTable({
        dom: "Bfrtip",
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: false,
        pageLength: 50,
        ajax: {
            url: "design/list",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            error: function(xhr, error, thrown) {
                console.log('Error: ' + xhr.responseText);
            },
            dataSrc: function(data) {
                if (data && data.data) {
                    if (data.data.length === 0) {
                        console.log(data);
                        return [];
                    } else {
                        console.log(data);
                        return data.data;
                    }
                } else {
                    console.log(data);
                    return [];
                }
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'design_code', name: 'design_code' },
            { data: 'unit_name', name: 'unit_name' },
            // { data: 'cost', name: 'cost' },
            { 
                data: 'img', 
                name: 'img',
                render: function(data, type, row) {
                    if(data) {
                        return '<a class="image-popup" href="' + data + '"><img src="' + data + '" width="50" height="50"/></a>';
                    }
                    return '';
                }
            }
        ],
        drawCallback: function() {
            $('.image-popup').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        }
    });
    $('#ttable_wrapper, #ttable').css({
        'overflow-y': 'hidden',
        'padding-bottom': '24px'
    });
});
