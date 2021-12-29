$(document).ready(function() {
    $('#Avatar').on('click', function () {
        $('#UploadAvatar').click();
    })

    let upload_file = false;

    $('#UploadAvatar').change(function(e) {
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
                        $('#Avatar').attr('src', data.upload);
                        $('input[name="avatar"]').val(data.upload);
                    } else {
                        swal('Error', data.message, 'error');
                    }
                },
                complete: function() {
                    upload_file = false;
                }
            });
    });

    $('#ProfileForm').on('submit', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/profile',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    swal('Success', '', 'success');
                    $('input[name="password"]').val('');
                    $('input[name="password_confirmation"]').val('');
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

});
