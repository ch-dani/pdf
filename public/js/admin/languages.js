$(document).ready(function() {
    let options = {
        'paging'        : true,
        'lengthChange'  : false,
        'searching'     : true,
        'ordering'      : true,
        'info'          : true,
        'autoWidth'     : false,
        'pageLength'    : 15,
        'order'         : [[ 0, "desc" ]]
    };

    $('#Languages').DataTable(options);

    $('body').on('click', '.DeleteLanguage', function() {
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
                    url: '/admin-cp/language/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        language_id: $button.data('id'),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            if ($button.hasClass('btn-block')) {
                                window.location.href = '/admin-cp/languages';
                            } else {
                                $('#Languages').DataTable().destroy();
                                $button.closest('tr').remove();
                                $('#Languages').DataTable(options);
                            }
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            }
        })

        return false;
    });

    /* Add language */

    $('#flag').on('change', function() {
        $('#flag-img').attr('src', $(this).val());
    });

    $('#AddLanguageForm').on('submit', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/language/add',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.href = '/admin-cp/languages';
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

    /* Edit language */

    $('#EditLanguageForm').on('submit', function() {
        Swal({
            title: 'Save the language?',
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
                    url: '/admin-cp/language/update',
                    data: $('#EditLanguageForm').serialize(),
                    success: function(data) {
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
});
