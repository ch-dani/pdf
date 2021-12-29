$(document).ready(function () {
    let options = {
        'paging': true,
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'pageLength': 15,
        'order': [[0, "desc"]]
    };

    $('#Users').DataTable(options)

    $('#Users').on('click', '.DeleteUser', function () {
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
                    url: '/admin-cp/user/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        user_id: $button.data('id'),
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#Users').DataTable().destroy();
                            $button.closest('tr').remove();
                            $('#Users').DataTable(options);
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    /* User edit */
    let options_documents = {
        'paging': true,
        'lengthChange': false,
        'searching': false,
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

    $('#DeleteDocuments').on('click', function () {
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

    $('#EditUser').on('click', function () {
        $button = $(this);

        Swal({
            title: 'Save changes?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/admin-cp/user/update',
                    data: $('#EditUserForm').serialize(),
                    success: function (data) {
                        if (data.status == 'success') {
                            swal('Success', '', 'success');
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    /* Add User */

    $('#AddUserForm').on('submit', function () {

        $.ajax({
            type: 'POST',
            url: '/admin-cp/user/add',
            data: $(this).serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    window.location.href = "/admin-cp/user/show/" + data.user_id;
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

});
