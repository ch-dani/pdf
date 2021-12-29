var ALLOW_FILE_EXT = "text/html";
var A_TOOL = false;
$(document).ready(function () {
    var UUID = guid();

    var toolHtmlToPdf = {
        el: false,
        upload_in_progress: false,
        init: function (element) {
            this.el = element;
            this.bind();
        },
        bind: function () {
            this.el.on("change", this.fileSelected);
        },
        UrlToPdf: function () {
            let $shadow = $('<div id="shadow_box"></div>');
            $shadow.hide();
            $("#apply-popup").before($shadow);
            $shadow.show('fast');

            $shadow.on('click', function () {
                $("#apply-popup").removeClass("active");
                $(this).remove();
                return false;
            });

            $("#apply-popup").addClass("active");
            $(".creating_document").show();
            $(".create_file_box").hide();
            $(".apply_changes_1").html("Wait...");

            $.ajax({
                type: 'POST',
                url: '/tool/html-to-pdf',
                data: {
                    _token: $('input[name="_token"]').val(),
                    urls: $('#urls').val(),
                    UUID: UUID,
                    pageSize: $('select[name="pageSize"] option:checked').val(),
                    pageMargin: $('input[name="pageMargin"]').val(),
                    pageMarginUnits: $('select[name="pageMarginUnits"] option:checked').val(),
                    pageOrientation: $('select[name="pageOrientation"] option:checked').val(),
                    viewportWidth: $('input[name="viewportWidth"]').val(),
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $("#apply-popup .modal-header").removeClass("hidden");

                        $(".creating_document").hide();
                        $(".create_file_box").show();
                        $(".result-top-line .download_file_name").html(data.filename);
                        $(".download-result-link").attr({
                            "href": data.file,
                            "download": data.filename
                        });
                        $("#save-dropbox").attr({
                            'data-url': data.file,
                            'data-file_name': data.filename
                        });
                        $("#save-gdrive").attr({
                            'data-src': data.file,
                            'data-filename': data.filename
                        });

//                        $(document).on("click", "#rename_file", function (e) {
//                            e.preventDefault();
//                            var new_file_name = false;
//                            if (new_file_name = prompt("Rename to", data.filename)) {
//                                $(".download-result-link").attr("download", new_file_name);
//                                $(".download-result-link .download_file_name").html(new_file_name);
//                            }
//                        });

                        $(document).trigger("after_save_file", [{
                            file_name: data.filename,
                            url: data.file
                        }]);
                    } else {
                        $("#apply-popup").removeClass("active");
                        swal('Error', data.message, 'error');
                    }
                }
            });
        },
        FileToPdf: function () {
            let $shadow = $('<div id="shadow_box"></div>');
            $shadow.hide();
            $("#apply-popup").before($shadow);
            $shadow.show('fast');

            $shadow.on('click', function () {
                $("#apply-popup").removeClass("active");
                $(this).remove();
                return false;
            });

            $("#apply-popup").addClass("active");
            $(".creating_document").show();
            $(".create_file_box").hide();
            $(".apply_changes_1").html("Wait...");

            var intervalID = setInterval(function () {

                if (!toolHtmlToPdf.upload_in_progress) {
                    clearInterval(intervalID);

                    $.ajax({
                        type: 'POST',
                        url: '/tool/html-to-pdf',
                        data: {
                            _token: $('input[name="_token"]').val(),
                            UUID: UUID,
                            pageSize: $('select[name="pageSize"] option:checked').val(),
                            pageMargin: $('input[name="pageMargin"]').val(),
                            pageMarginUnits: $('select[name="pageMarginUnits"] option:checked').val(),
                            pageOrientation: $('select[name="pageOrientation"] option:checked').val(),
                            viewportWidth: $('input[name="viewportWidth"]').val(),
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                $("#apply-popup .modal-header").removeClass("hidden");

                                $(".creating_document").hide();
                                $(".create_file_box").show();
                                $(".result-top-line .download_file_name").html(data.filename);
                                $(".download-result-link").attr({
                                    "href": data.file,
                                    "download": data.filename
                                });
                                $("#save-dropbox").attr({
                                    'data-url': data.file,
                                    'data-file_name': data.filename
                                });
                                $("#save-gdrive").attr({
                                    'data-src': data.file,
                                    'data-filename': data.filename
                                });

                                $(document).on("click", "#rename_file", function (e) {
                                    e.preventDefault();
                                    var new_file_name = false;
                                    if (new_file_name = prompt("Rename to", data.filename)) {
                                        $(".download-result-link").attr("download", new_file_name);
                                        $(".download-result-link .download_file_name").html(new_file_name);
                                    }
                                });

                                $(document).trigger("after_save_file", [{
                                    file_name: data.filename,
                                    url: data.file
                                }]);
                            } else {
                                $("#apply-popup").removeClass("active");
                                swal('Error', data.message, 'error');
                            }
                        }
                    });
                }
            }, 250);
        },
        CodeToPdf: function () {
            let $shadow = $('<div id="shadow_box"></div>');
            $shadow.hide();
            $("#apply-popup").before($shadow);
            $shadow.show('fast');

            $shadow.on('click', function () {
                $("#apply-popup").removeClass("active");
                $(this).remove();
                return false;
            });

            $("#apply-popup").addClass("active");
            $(".creating_document").show();
            $(".create_file_box").hide();
            $(".apply_changes_1").html("Wait...");

            $.ajax({
                type: 'POST',
                url: '/tool/html-to-pdf',
                data: {
                    _token: $('input[name="_token"]').val(),
                    code: $('#code').val(),
                    UUID: UUID,
                    pageSize: $('select[name="pageSize"] option:checked').val(),
                    pageMargin: $('input[name="pageMargin"]').val(),
                    pageMarginUnits: $('select[name="pageMarginUnits"] option:checked').val(),
                    pageOrientation: $('select[name="pageOrientation"] option:checked').val(),
                    viewportWidth: $('input[name="viewportWidth"]').val(),
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $("#apply-popup .modal-header").removeClass("hidden");

                        $(".creating_document").hide();
                        $(".create_file_box").show();
                        $(".result-top-line .download_file_name").html(data.filename);
                        $(".download-result-link").attr({
                            "href": data.file,
                            "download": data.filename
                        });
                        $("#save-dropbox").attr({
                            'data-url': data.file,
                            'data-file_name': data.filename
                        });
                        $("#save-gdrive").attr({
                            'data-src': data.file,
                            'data-filename': data.filename
                        });

                        $(document).on("click", "#rename_file", function (e) {
                            e.preventDefault();
                            var new_file_name = false;
                            if (new_file_name = prompt("Rename to", data.filename)) {
                                $(".download-result-link").attr("download", new_file_name);
                                $(".download-result-link .download_file_name").html(new_file_name);
                            }
                        });

                        $(document).trigger("after_save_file", [{
                            file_name: data.filename,
                            url: data.file
                        }]);
                    } else {
                        $("#apply-popup").removeClass("active");
                        swal('Error', data.message, 'error');
                    }
                }
            });
        },
        fileSelected: function (e, file_obj=false) {
			if(file_obj){
				var that = toolHtmlToPdf;
				this.files = [file_obj];
				file = (this.files[0]);
			}else{
		        var that = toolHtmlToPdf,
		            file = (this.files[0]);
            }

	        file_ext = file.name.split(".").pop().toLowerCase();

            basename = file.name;

            let accept_ext = ['html'];

            if (accept_ext.indexOf(file_ext) == -1) {
                swal("Error", "Please select HTML file.", "error");
            } else {
                that.uploadFile(file);

                $('#html-file').prepend('<span class="message">Selected: ' + basename + '</span>');

                $('.app-welcome').hide(200);
                setTimeout(function () {
                    $('#html-file').show(200);
                    $('.fixed-bottom-panel').show(200);
                }, 200);

                $(".upload-file-tool").val('');
            }
        },
        uploadFile: function (file) {
            var formData = new FormData();
            formData.append('file', file);
            currentUploads.changeStatus(file.name, true);
            formData.append("UUID", UUID);
            formData.append("path", 'html-to-pdf');
            formData.append("_token", $('input[name="_token"]').val());

            toolHtmlToPdf.upload_in_progress = true;
            $(".current_file_status").html("File is uploading");

            return $.ajax({
                url: `/tool/upload`,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;

                            currentUploads.changeStatus(file.name, (percentComplete * 100).toFixed(0));

                            if ($(`.current_uploads .current_uploaded`).length == 0 && typeof window.is_editor === 'undefined') {
                                $(`.current_uploads`).append(`<div class='uploading_progress_outer'><div class='file_loading current_uploaded'>file(s) are uploading: - <span class='percent'>0</span>%</div> <div class='progress_inner'></div></div>`);
                            }

                            if (percentComplete >= 1) {
                                console.log("file upload complete");
                                $(".uploading_progress_outer").remove();
                            }

                            console.log(currentUploads.getTotalPercent() + '%')
                            $(`.current_uploads .progress_inner`).css("width", ((percentComplete * 100).toFixed(0)) + "%");
                            $(`.current_uploads .current_uploaded .percent`).html((percentComplete * 100).toFixed(0));
                        }
                    }, false);
                    xhr.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                        }
                    }, false);
                    return xhr;
                },
                success: function (data) {
                    if (data.status != 'success') {
                        swal('Error', data.message, 'error');
                    }
                    $(".current_file_status").html("Your task is processing");

                    currentUploads.changeStatus(file.name, false);
                    toolHtmlToPdf.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');
                    currentUploads.changeStatus(file.name, false);
                    toolHtmlToPdf.upload_in_progress = false;
                }
            });
        }
    };
    
    A_TOOL = toolHtmlToPdf;
    

    var currentUploads = {

        getTotalPercent: function () {
            var perc = 0;
            var count = 0;
            $.each(this.progress, function (i, v) {
                if (v != false) {
                    if (isNaN(v)) {
                        v = 1;
                    }
                    count++;
                    perc += parseInt(v);
                }
            });
            var ret = (perc / count).toFixed(0);
            return isNaN(ret) ? 0 : ret;
        },
        operations: {},
        progress: {},
        changeUploadProgress: function (key, status) {
            this.progress[key] = status;
        },
        changeStatus: function (key, status) {
            this.operations[key] = status;
        },
        isIncomplete: function () {
            if (!this.operations) {
                return false;
            }
            var flag = false;
            $.each(this.operations, function (i, v) {
                if (v == true) {
                    flag = true;
                }
            });
            return flag;
        }
    };

    $('#HTMLToPDF').on('click', function () {
        toolHtmlToPdf.UrlToPdf();
        return false;
    });

    $('#HTMLCodeToPDF').on('click', function () {
        toolHtmlToPdf.CodeToPdf();
        return false;
    });

    $('#HTMLFileToPDF').on('click', function () {
        toolHtmlToPdf.FileToPdf();
        return false;
    });

    toolHtmlToPdf.init($(".upload-file-tool"));

    $(".html-pdf-options-btn").click(function (e) {
        e.preventDefault();

        $(".html-pdf-options-btn").toggleClass("active");

        if ($(".html-pdf-options-btn").hasClass("active"))
            $(".html-pdf-options-btn").text("Fewer options");
        else
            $(".html-pdf-options-btn").text("More options");

        $(".html-to-pdf-more-options").fadeToggle(200);
    });

    $('.pageSize').on('change', function () {
        $('.pageSize option[value="' + $(this).find('option:checked').val() + '"]').prop('selected', true);
    });

    $('.viewportWidth').on('change', function () {
        $('.viewportWidth').val($(this).val());
    });

    $('.pageOrientation').on('change', function () {
        $('.pageOrientation option[value="' + $(this).find('option:checked').val() + '"]').prop('selected', true);
    });

    $('.pageMargin').on('change', function () {
        $('.pageMargin').val($(this).val());
    });

    $('.pageMarginUnits').on('change', function () {
        $('.pageMarginUnits option[value="' + $(this).find('option:checked').val() + '"]').prop('selected', true);
    });

    $('.hideNotices').on('change', function () {
        if ($(this).is(':checked'))
            $('.hideNotices').prop('checked', true);
        else
            $('.hideNotices').prop('checked', false);
    });

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
