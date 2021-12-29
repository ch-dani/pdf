
$(document).ready(function() {

	$('input[name=thumbnail]').on("change", function(){
		var fl = $(this);
		file = $(this)[0].files[0]
		x = new Promise((resolve, reject) => {
		    const reader = new FileReader();
		    reader.readAsDataURL(file);
		    reader.onload = () =>
		        resolve(reader.result);
		    reader.onerror = error =>
		        reject(error);
		});
		 
		x.then(function(data){
			fl.closest(".row").find(".thumbnail_preview").attr("src", data);
		})
	});

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
                    url: '/admin-cp/article/delete',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        article_id: $button.data('id'),
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            if ($button.hasClass('btn-block')) {
                                window.location.href = '/admin-cp/articles';
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
            filebrowserUploadUrl: "/admin-cp/upload_photo",
            filebrowserUploadMethod: "form"
        });
    });

    var editors_summary = {};

    $(".ckeditor_summary").each(function () {
        editors_summary[$(this).data("id")] = CKEDITOR.replace($(this).attr("id"), {
            filebrowserUploadUrl: "/admin-cp/upload_photo",
            filebrowserUploadMethod: "form"
        });
    });

    $('#AddArticleForm').on('submit', function(e) {
    	e.preventDefault();
        let data_str = '';

        var formData = new FormData($('#AddArticleForm')[0]);
        



//        $(".ckeditor_content").each(function () {
//            data_str += '&content[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
//        });

//        $(".ckeditor_summary").each(function () {
//            data_str += '&summary[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
//        });

        $(".ckeditor_content").each(function () {
			var i = $(this).data("id");
        	formData.append(`content[${i}]`, htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;"));
        });

        $(".ckeditor_summary").each(function () {
			var i = $(this).data("id");
        	formData.append(`summary[${i}]`, htmlentities.decode(editors_summary[$(this).data("id")].getData()).replace(/&/g, "#38;"));
        });

		if($('input[name=thumbnail]').length>0){
			formData.append('thumbnail', $('input[name=thumbnail]')[0].files[0]); 
    	}


        $.ajax({
            type: 'POST',
            url: '/admin-cp/article/add',
            data: formData,

					cache: false,
					contentType: false,
					processData: false,
            
            success: function(data) {
                if (data.status == 'success') {
                    window.location.href = "/admin-cp/article/edit/"+data.article_id;
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
        //summary
        var formData = new FormData($('#EditArticleForm')[0]);
        

        $(".ckeditor_content").each(function () {
			var i = $(this).data("id");
        	formData.append(`content[${i}]`, htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;"));
//            data_str += '&content[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
        });

        $(".ckeditor_summary").each(function () {
			var i = $(this).data("id");
        	formData.append(`summary[${i}]`, htmlentities.decode(editors_summary[$(this).data("id")].getData()).replace(/&/g, "#38;"));
//            data_str += '&summary[' + $(this).data("id") + ']=' + htmlentities.decode(editors[$(this).data("id")].getData()).replace(/&/g, "#38;");
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
				if($('input[name=thumbnail]').length>0){
					formData.append('thumbnail', $('input[name=thumbnail]')[0].files[0]); 
            	}
            	var url = "";
            	if(typeof page_type != 'undefined' && page_type=='blog_categories'){
            		url = "/admin-cp/article/categories/update";
            	}else{
            		url = "/admin-cp/article/update"
            	}
            
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData, //$('#EditArticleForm').serialize() + data_str,
					cache: false,
					contentType: false,
					processData: false,
                    success: function(data) {
                        if (data.status == 'success') {
                        	if($("input[name='article_id']").val()=='new'){
	                        	window.history.pushState('', '', "/admin-cp/article/categories/edit/"+data.article_id);
                        	}
                        	$("input[name='article_id']").val(data.article_id);
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
