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

    $('#Articles').DataTable(options);

    $('body').on('click', '.DeleteArticle', function() {
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
                    url: '/admin/article/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        article_id: $button.data('id'),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            if ($button.hasClass('btn-block')) {
                                window.location.href = '/admin/articles';
                            } else {
                                $('#Articles').DataTable().destroy();
                                $button.closest('tr').remove();
                                $('#Articles').DataTable(options);
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

    /* Add article */

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
            filebrowserUploadUrl: "/admin/upload_photo",
            filebrowserUploadMethod: "form"
        });
    });

    var editors_summary = {};

    $(".ckeditor_summary").each(function () {
        editors_summary[$(this).data("id")] = CKEDITOR.replace($(this).attr("id"), {
            filebrowserUploadUrl: "/admin/upload_photo",
            filebrowserUploadMethod: "form"
        });
    });

    $('#AddArticleForm').on('submit', function() {
        let data_str = '';

        $(".ckeditor_content").each(function () {
            data_str += '&content[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        $(".ckeditor_summary").each(function () {
            data_str += '&summary[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        $.ajax({
            type: 'POST',
            url: '/admin/article/add',
            data: $(this).serialize() + data_str,
            success: function(data) {
                if (data.status == 'success') {
                    window.location.href = "/admin/article/edit/"+data.article_id;
                } else {
                    swal('Error', data.message, 'error');
                }
            }
        });

        return false;
    });

    /* Edit article */

    $('#EditArticleForm').on('submit', function() {
        let data_str = '';

        $(".ckeditor_content").each(function () {
            data_str += '&content[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        $(".ckeditor_summary").each(function () {
            data_str += '&summary[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        Swal({
            title: 'Save the article?',
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
                    url: '/admin/article/update',
                    data: $('#EditArticleForm').serialize() + data_str,
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