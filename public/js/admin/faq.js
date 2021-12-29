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

    $('#Faq').DataTable(options);

    $('body').on('click', '.DeleteFaq', function() {
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
                    url: '/admin-cp/faq/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        faq_id: $button.data('id'),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            if ($button.hasClass('btn-block')) {
                                window.location.href = '/admin-cp/faq';
                            } else {
                                $('#Faq').DataTable().destroy();
                                $button.closest('tr').remove();
                                $('#Faq').DataTable(options);
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

    /* Export Import */


    let upload_import = false;

    $('#UploadImport').change(function(e) {
        if (upload_import == true) {
            swal('Error', 'Wait for the file to load', 'error');
            return false;
        }

        var files = e.target.files;

        var fData = new FormData;
        fData.append('file', $(this).prop('files')[0]);
        fData.append('_token', $('input[name="_token"]').val());
        fData.append('type', 'faq');

        if (typeof $(this).prop('files')[0] != 'undefined')
            $.ajax({
                url: '/admin-cp/import',
                data: fData,
                processData: false,
                contentType: false,
                type: 'POST',
                beforeSend: function() {
                    upload_file = true;
                },
                success: function (data) {
                    if (data.status == 'success') {
                        swal('Success', '', 'success');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        swal('Error', data.message, 'error');
                    }
                },
                complete: function() {
                    upload_import = false;
                }
            });
    });

    /* Add faq */

    $('.AddStep').on('click', function() {
        let id = $('.step_1').length + 1;

        $clone = $('.clone_step').first().clone();
        $clone.removeClass('clone_step');
        $clone.data('id', id);
        $clone.find('label span').text(id);
        $clone.find('input').val('');

        $(".tab-pane").each(function () {
            $clone.find('input').removeClass('step_1');
            $clone.find('input').addClass('step_' + $(this).data('id'));
            $clone.find('input').attr('name', 'step[' + $(this).data('id') + '][' + id + ']');
            $clone.find('input').attr('id', 'step[' + $(this).data('id') + '][' + id + ']');
            $clone.find('label').attr('for', 'step[' + $(this).data('id') + '][' + id + ']');

            $(this).find('.AddStep').before($clone.clone());
        });

    });

    let upload_file = false;

    $('#UploadIcon').change(function(e) {
        if (upload_file == true) {
            swal('Error', 'Wait for the file to load', 'error');
            return false;
        }

        var files = e.target.files;

        var fData = new FormData;
        fData.append('file', $(this).prop('files')[0]);
        fData.append('_token', $('input[name="_token"]').val());

        if (typeof $(this).prop('files')[0] != 'undefined')
            $.ajax({
                url: '/admin-cp/upload',
                data: fData,
                processData: false,
                contentType: false,
                type: 'POST',
                beforeSend: function() {
                    upload_file = true;
                },
                success: function (data) {
                    if (data.status == 'success' && data.upload.length != 0) {
                        $('.icons_box').append('<img src="' + data.upload + '" /><input type="hidden" value="' + data.upload + '" name="icons[]">');
                        $('#UploadIcon').val('');
                    } else {
                        swal('Error', data.message, 'error');
                    }
                },
                complete: function() {
                    upload_file = false;
                }
            });
    });

    $('.icons_box').on('click', 'img', function() {
        $img = $(this);

        Swal({
            title: 'Remove icon?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $('input[name="icons[]"][value="' + $img.attr('src') + '"]').remove();
                $img.remove();
            }
        })
    });

    $('#AddFaqForm').on('submit', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/faq/add',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.href = "/admin-cp/faq/edit/"+data.faq_id;
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

    /* Edit faq */

    $('#EditFaqForm').on('submit', function() {
        Swal({
            title: 'Save the FAQ?',
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
                    url: '/admin-cp/faq/update',
                    data: $('#EditFaqForm').serialize(),
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
