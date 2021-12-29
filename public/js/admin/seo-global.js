$(document).ready(function() {
    $('#SeoGlobalForm').on('submit', function() {
        $.ajax({
            type: 'POST',
            url: '/admin-cp/setting/seo-global',
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
