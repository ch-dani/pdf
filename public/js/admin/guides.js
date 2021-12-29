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

    $('#Guides').DataTable(options);

    $('body').on('click', '.DeleteGuide', function() {
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
                    url: '/admin-cp/guide/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        guide_id: $button.data('id'),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            if ($button.hasClass('btn-block')) {
                                window.location.href = '/admin-cp/guides';
                            } else {
                                $('#Guides').DataTable().destroy();
                                $button.closest('tr').remove();
                                $('#Guides').DataTable(options);
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

    let upload_import = false;



    /* Add guide */

    (function(window){
    	window.htmlentities = {
    		encode : function(str) {
    			var buf = [];

    			for (var i=str.length-1;i>=0;i--) {
    				buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
    			}

    			return buf.join('');
    		},
    		decode : function(str) {
    			return str.replace(/&#(\d+);/g, function(match, dec) {
    				return String.fromCharCode(dec);
    			});
    		}
    	};
    })(window);

    CKEDITOR.editorConfig = function( config ) {
        //config.extraPlugins = 'linkbutton, timestamp';
        config.allowedContent = true;
        config.protectedSource.push(/<(i)[^>]*>.*<\/i>/ig);
    };

    var editors = {};

    $(".ckeditor_content").each(function () {
        editors[$(this).data("id")] = CKEDITOR.replace($(this).attr("id"), {
            filebrowserUploadUrl: "/admin-cp/upload_photo",
            filebrowserUploadMethod: "form",
            extraAllowedContent: {
                'h4' : {styles:'*',attributes:'*',classes:'*'},
                'p' : {styles:'*',attributes:'*',classes:'*'}
            }
        });
    });

    $('#AddGuideForm').on('submit', function() {
        let data_str = '';

        $(".ckeditor_content").each(function () {
            data_str += '&content[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        $.ajax({
            type: 'POST',
            url: '/admin-cp/guide/add',
            data: $(this).serialize() + data_str,
            success: function(data) {
                if (data.status == 'success') {
                    window.location.href = "/admin-cp/guide/edit/"+data.guide_id;
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

    /* Edit guide */

    $('#EditGuideForm').on('submit', function() {
        let data_str = '';

        $(".ckeditor_content").each(function () {
            data_str += '&content[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        Swal({
            title: 'Save the guide?',
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
                    url: '/admin-cp/guide/update',
                    data: $('#EditGuideForm').serialize() + data_str,
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
