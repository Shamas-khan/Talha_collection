$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('#ttable').DataTable({
        dom: "Bfrtip",
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 50,
        ajax: {
            url: "machines/list",
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
            { data: 'area_code', name: 'area_code' },
            { data: 'head_code', name: 'head_code' },
            { data: 'size', name: 'size' }
        ]
    });
    $('#ttable_wrapper, #ttable').css({
        'overflow-y': 'hidden',
        'padding-bottom': '24px'
    });
});