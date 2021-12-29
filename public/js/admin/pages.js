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

    $('#Pages').DataTable(options);

    $('body').on('click', '.DeletePage', function() {
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
                    url: '/admin-cp/page/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        page_id: $button.data('id'),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            if ($button.hasClass('btn-block')) {
                                window.location.href = '/admin-cp/pages';
                            } else {
                                $('#Pages').DataTable().destroy();
                                $button.closest('tr').remove();
                                $('#Pages').DataTable(options);
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


    $('#Import').on('click', function() {
        $('#UploadImport').click();
    });

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
        fData.append('type', 'pages');

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

    /* Add page */

    if ($('textarea#content').length)
        CKEDITOR.replace('content')

    $('#AddPageForm').on('submit', function() {

        var data = {
            content: CKEDITOR.instances.content.getData(),
            status: $('#status').val(),
            tool: $('#tool').val(),
            title: $('#title').val(),
            link: $('#link').val(),
            seo_title: $('#seo_title').val(),
            seo_keywords: $('#seo_keywords').val(),
            seo_description: $('#seo_description').val(),
            _token: $('input[name="_token"]').val()
        };

        console.log(data);

        $.ajax({
            type: 'POST',
            url: '/admin-cp/page/add',
            data: data,
            success: function(data) {
                if (data.status == 'success') {
                    window.location.href = "/admin-cp/page/edit/"+data.page_id;
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

    /* Edit page */

    $('#EditPageForm').on('submit', function() {
        Swal({
            title: 'Save the page?',
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
                    url: '/admin-cp/page/update',
                    data: $('#EditPageForm').serialize(),
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
