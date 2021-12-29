$(function () {
   // Delete subscription
    $(document).on('click', '.DeleteSubscription', function () {
        $button = $(this);
        let subscriptionId = $button.data('id');

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
                    url: '/admin-cp/subscription/delete/' + subscriptionId,
                    data: {
                        _token: $('input[name="_token"]').val(),
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            location.reload();
                        } else {
                            swal('Error', 'Cannot delete. Try later', 'error');
                        }
                    }
                });
            }
        })

        return false;
    });
});
