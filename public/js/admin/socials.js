$(document).ready(function() {
    $('#SocialsForm').on('submit', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/setting/socials',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    swal('Success', '', 'success');
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });
});
