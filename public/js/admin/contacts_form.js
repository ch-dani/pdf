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

    $('#Contacts').DataTable(options)

    $('#Contacts').on('click', '.DeleteContact', function () {
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
                    url: '/admin-cp/contact/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        contact_id: $button.data('id'),
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#Contacts').DataTable().destroy();
                            $button.closest('tr').remove();
                            $('#Contacts').DataTable(options);
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

});
