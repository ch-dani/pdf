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

    $('#Administrators').DataTable(options)

    $('#Administrators').on('click', '.DeleteAdministrator', function () {
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
                    url: '/admin-cp/administrator/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        user_id: $button.data('id'),
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#Administrators').DataTable().destroy();
                            $button.closest('tr').remove();
                            $('#Administrators').DataTable(options);
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    /* Administrator edit */

    $('#EditAdministrator').on('click', function () {
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
                    url: '/admin-cp/administrator/update',
                    data: $('#EditAdministratorForm').serialize(),
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

    /* Add Administrator */

    $('#AddAdministratorForm').on('submit', function () {

        $.ajax({
            type: 'POST',
            url: '/admin-cp/administrator/add',
            data: $(this).serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    window.location.href = "/admin-cp/administrator/edit/" + data.user_id;
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

});
