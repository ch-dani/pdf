$(document).ready(function() {
    let options_documents = {
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false,
        'pageLength': 5,
        'order': [[0, "desc"]]
    };

    $('#Documents').DataTable(options_documents)
});