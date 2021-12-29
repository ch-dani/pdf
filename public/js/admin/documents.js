$(document).ready(function() {
    let options_documents = {
        'paging': true,
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'pageLength': 10,
        columnDefs: [{
            "orderable": false,
            "targets": [0]
        }],
        'order': [[1, "desc"]]
    };

    $('#Documents').DataTable(options_documents)

    $('#Documents').on('click', '.DeleteDocument', function () {
        $button = $(this);

        Swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/user/delete_document',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        document_id: $button.data('id'),
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#Documents').DataTable().destroy();
                            $button.closest('tr').remove();
                            $('#Documents').DataTable(options_documents);
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    $('.dataTables_filter label').prepend('<a href="#" class="btn btn-danger import_export_button" id="DeleteDocuments">Delete checked</a>');

    $('body').on('click', '#DeleteDocuments', function () {
        Swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {

                let ids = [];

                $('.CheckboxDeleteDocuments:checked').each(function () {
                    ids.push($(this).data('id'));
                });

                if (!ids.length)
                    swal('Error', 'No documents selected', 'error');
                else
                    $.ajax({
                        type: 'POST',
                        url: '/admin-cp/user/delete_document',
                        data: {
                            _token: $('input[name="_token"]').val(),
                            document_id: ids,
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                $('#Documents').DataTable().destroy();

                                ids.forEach(function(item, i, arr) {
                                    $('#Documents tr[data-id="'+item+'"]').remove();
                                });

                                $('#Documents').DataTable(options_documents);
                                $('.dataTables_filter label').prepend('<a href="#" class="btn btn-danger import_export_button" id="DeleteDocuments">Delete checked</a>');
                                swal('Success', data.message, 'success');
                            } else {
                                swal('Error', data.message, 'error');
                            }
                        }
                    });
            }
        })

        return false;
    });

    let checked_all = false;

    $('.SelectAllDeleteDocuments').on('click', function () {
        let checked = true;

        if (checked_all) {
            checked_all = false;
            checked = false;
        } else
            checked_all = true;

        $('.CheckboxDeleteDocuments').prop('checked', checked);
        $('.SelectAllDeleteDocuments').prop('checked', checked);
    });
});
