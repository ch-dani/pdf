var A_TOOL = false;
var ALLOW_FILE_EXT = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
var number = 0;
FILE_NUM = 0;

$(document).ready(function () {
    var UUID = guid();
    var numPages = 0;
    var toolJpgToPdf = {
        el: false,
        alredy_init: false,
        upload_in_progress: false,
        init: function (element) {
            this.el = element;
            this.bind();
        },
        bind: function () {
            this.el.on("change", this.fileSelected);
        },
        fileSelected: function (e, file_obj = false) {
            if (file_obj) {
                var that = toolJpgToPdf;
                files = this.files = [file_obj];
                file = (this.files[0]),
                    file_ext = file.name.split(".").pop().toLowerCase();
            } else {
                var that = toolJpgToPdf,
                    files = this.files;
                var file = (this.files[0]);
            }

            file_ext = file.name.split(".").pop().toLowerCase();
            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            that.uploadFile(files);

            setTimeout(function () {
                $("html, body").animate({scrollTop: $('#pages-pdf').offset().top - 150}, "medium");
            }, 500);

            // Show remove icons
            $(document).on('click', '#showRemoveIcons', function (e) {
                e.preventDefault();

                $('.remove_icon_doc').show();
                setTimeout(function () {
                    $("html, body").animate({scrollTop: $('#pages-pdf').offset().top - 150}, "medium");
                }, 500);
            });

            // Remove uploaded file
            $(document).on('click', '.remove-uploaded-file', function (e) {
                e.preventDefault();

                let $this = $(this);

                $.ajax({
                    type: 'POST',
                    url: '/tool/remove-uploaded-file',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        UUID: UUID,
                        file_number_postfix: $this.data('number'),
                        operation_type: 'exceltopdf',
                    },
                    success: function (data) {
                        console.log(data);
                        $this.closest('.image-canvas-li').remove();
                    },
                });
            });

            // // Save to Dropbox
            // $('#saveToDropbox').click(function (e) {
            //     e.preventDefault();
            //
            //     let files = [];
            //
            //     $('.image-canvas-li').each(function () {
            //         files.push($(this).data('number'));
            //     });
            //
            //     if (files.length === 0) {
            //         swal('No files uploaded', 'Upload at least one file', 'warning');
            //         return false;
            //     }
            //
            //     $.ajax({
            //         type: 'POST',
            //         url: '/word-to-pdf',
            //         data: {
            //             _token: $('input[name="_token"]').val(),
            //             UUID: UUID,
            //             pageFormat: 'auto',
            //             pageMargin: 0,
            //             pageOrientation: 'auto',
            //             files: files,
            //         },
            //         success: function (data) {
            //             if (data.status == 'success') {
            //                 Dropbox.save(location.protocol + '//' + location.hostname + data.url);
            //             } else {
            //                 swal('Error', data.message, 'error');
            //             }
            //         }
            //     });
            // });

            let allowedExtensions = ['doc', 'docx'];

            if (!allowedExtensions.includes(file_ext)) {
                swal("Error", "Please select DOC or DOCX file.", "error");
            } else {
                Array.from(files).forEach(file => {
                    that.getBase64(file).then(function (base64) {
                        expansion = file.name.substring(file.name.lastIndexOf('.'));
                        let filename = basename + '.' + file.name.split(".").pop().toLowerCase();
                        let $tmp_canvas = $(
                            '<div class="convert_doc right_doc image-canvas-li" data-number="' + number + '">' +
                            '<div class="convert_doc_content">' +
                            '<div class="download_convert_doc">' +
                            '<img src="https://toppng.com/uploads/preview/pdf-icon-11549528510ilxx4eex38.png" alt="">' +
                            '</div>' +
                            '</div>' +
                            '<div class="download_icon_doc remove_icon_doc">' +
                            '<a class="remove-uploaded-file" href="#" data-number="' + number + '"><img src="freeconvert/img/close_icon.svg"></a>' +
                            '</div>' +
                            '<div class="download_icon_doc">' +
                            '<a class="save-image-page" href="#" data-number="' + number + '"><img src="freeconvert/img/download_arrow.svg"></a>' +
                            '</div>' +
                            '<div class="name_doc">' +
                            '<h6>' + filename + '</h6>' +
                            '</div>' +
                            '</div>'
                        );

                        number += 1;

                        $('#pages-pdf').append($tmp_canvas);
                        $('main').addClass('file_uploaded').removeClass('file_not_loaded');
                    });
                });

                if (!toolJpgToPdf.alredy_init) {
                    $(document).on('click', ".image-delete-page", function () {
                        $(this).closest(".image-canvas-li").remove();
                    });

                    $('#pageOrientation').on('change', function () {
                        if ($('#pageFormat option:checked').val() == 'auto') return true;

                        if ($('#pageOrientation option:checked').val() == 'landscape') {
                            $('.image-page-caption').width('141.354px');
                            $('.image-canvas-wrap').width('141.354px');
                            $('.image-canvas-wrap').height('100px');
                        } else {
                            $('.image-page-caption').width('100px');
                            $('.image-canvas-wrap').width('100px');
                            $('.image-canvas-wrap').height('141.354px');
                        }
                    });

                    $('#pageMargin').on('change', function () {
                        $('.image-canvas-wrap img').css('border-width', (parseFloat($('#pageMargin option:checked').val()) * 12.0919) + 'px')
                    });

                    $('#pageFormat').on('change', function () {
                        if ($('#pageFormat option:checked').val() == 'auto') {
                            ;
                            $('.image-page-caption').width('100px');
                            $('.image-canvas-wrap').width('100px');
                            $('.image-canvas-wrap').height('100px');
                        } else {
                            $('#pageOrientation').change();
                            $('#pageMargin').change();
                        }
                    });

                    $(document).on('click', '.save-pdf, .save-image-page', function (e) {
                        e.preventDefault();
                        PDFTOOLS.startTask();

                        let $button = $(this);

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
                            if (!toolJpgToPdf.upload_in_progress) {
                                clearInterval(intervalID);
                                let files = [];
                                let allFiles = false;

                                if ($button.hasClass('save-pdf')) allFiles = true;

                                if ($button.hasClass('save-image-page')) {
                                    files.push($button.data('number'));
                                } else {
                                    $('.image-canvas-li').each(function () {
                                        files.push($(this).data('number'));
                                    });
                                }

                                if (files.length === 0) {
                                    swal('No files uploaded', 'Upload at least one file', 'warning');
                                    return false;
                                }

                                // Convert word to PDF file
                                if ($button.attr('href') === '#' || $button.hasClass('save-pdf')) {
                                    $.ajax({
                                        type: 'POST',
                                        url: '/word-to-pdf',
                                        data: {
                                            _token: $('input[name="_token"]').val(),
                                            UUID: UUID,
                                            pageFormat: 'auto',
                                            pageMargin: 0,
                                            pageOrientation: 'auto',
                                            files: files,
                                            all_files: allFiles,
                                        },
                                        success: function (data) {
                                            debugger
                                            if (data.status == 'success') {
                                                data.success = true;
                                                PDFTOOLS.taskComplete(data);

                                                $("#save-gdrive").attr({
                                                    'data-src': data.url,
                                                    'data-filename': data.new_file_name
                                                });

                                                if (!$button.hasClass('save-pdf')) {
                                                    $button.attr('href', location.protocol + '//' + location.hostname + data.url);
                                                    $button.data('name', data.new_file_name);

                                                    const link = document.createElement('a');
                                                    link.href = location.protocol + '//' + location.hostname + data.url;
                                                    link.setAttribute('download', data.new_file_name);
                                                    document.body.appendChild(link);
                                                    // link.click();
                                                } else {
                                                    const link = document.createElement('a');
                                                    link.href = location.protocol + '//' + location.hostname + data.url;
                                                    link.setAttribute('download', data.new_file_name);
                                                    document.body.appendChild(link);
                                                    // link.click();
                                                }
                                            } else {
                                                swal('Error', data.message, 'error');
                                            }
                                        }
                                    });
                                } else {
                                    const link = document.createElement('a');
                                    link.href = $button.attr('href');
                                    link.setAttribute('download', $button.data('name'));
                                    document.body.appendChild(link);
                                    // link.click();
                                }
                            }
                        }, 250);
                    });
                }

                toolJpgToPdf.alredy_init = 1;
            }
        },
        getBase64: function (file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        getBlob: function (file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsBinaryString(file);
                reader.onload = () =>
                    resolve(reader.result);
                reader.onerror = error =>
                    reject(error);
            });
        },
        uploadFile: function (files) {
            var formData = new FormData();
            $.each(files, function (i, file) {
                formData.append('files[]', file);
                currentUploads.changeStatus(file.name, true);
            });
            formData.append("UUID", UUID);
            formData.append("path", 'doc-to-pdf');
            formData.append("_token", $('input[name="_token"]').val());
            if (files.length == 1) {
                formData.append("file_num", FILE_NUM);
                FILE_NUM++;
            } else {
                FILE_NUM = files.length;
            }

            toolJpgToPdf.upload_in_progress = true;

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

                            $.each(files, function (i, file) {
                                formData.append('files[]', file);
                                currentUploads.changeStatus(file.name, (percentComplete * 100).toFixed(0));
                            });

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

                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });
                    toolJpgToPdf.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload files..", 'error');
                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });
                    toolJpgToPdf.upload_in_progress = false;
                }
            });
        }
    };

    A_TOOL = toolJpgToPdf;

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

    toolJpgToPdf.init($(".upload-file-tool"));

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});


/*
var ALLOW_FILE_EXT = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
var A_TOOL = false;

$(document).ready(function () {
    var UUID = guid();

    var basename = false;

    var toolDocToPdf = {
        el: false,
        upload_in_progress: false,
        init: function (element) {
            this.el = element;
            this.bind();
        },
        getBlob: function (file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsBinaryString(file);
                reader.onload = () =>
                    resolve(reader.result);
                reader.onerror = error =>
                    reject(error);
            });
        },
        bind: function () {
            this.el.on("change", this.fileSelected);

            $(document).on("pdftool_file_selected", function (ev, file) {

                toolDocToPdf.getBlob(file).then((data) => {
                    //var pdfjsLib = window['pdfjs-dist/build/pdf'];
                    pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

                    var loadingTask = pdfjsLib.getDocument({data: data});
                    $(document).trigger("pdf_loading_task", [loadingTask]);
                    loadingTask.promise.catch(function (e) {
                        console.log("wow doge", e);
                    });
                });

            });

        },
        fileSelected: function (e, file_obj = false) {

            if (file_obj) {
                var that = toolDocToPdf;
                this.files = [file_obj];
                file = (this.files[0]);
            } else {
                var that = toolDocToPdf,
                    file = (this.files[0]);
            }
            file_ext = file.name.split(".").pop().toLowerCase();


            $(document).trigger("pdftool_file_selected", [file]);

            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            if (file_ext != 'doc' && file_ext != 'docx' && file_ext != 'odt') {
                swal("Error", "Please select a DOC, DOCX or ODT file.", "error");
            } else {
                that.uploadFile(file);

                $('.app-welcome').hide(200);
                setTimeout(function () {
                    $('#extract-pdf').show(200);
                }, 200);

                $(document).on('click', '.convert-to-pdf', function () {
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

                        if (!toolDocToPdf.upload_in_progress) {
                            clearInterval(intervalID);

                            $.ajax({
                                type: 'POST',
                                url: '/tool/doc-to-pdf',
                                data: {
                                    _token: $('input[name="_token"]').val(),
                                    UUID: UUID,
                                    basename: basename,
                                    file_ext: file_ext
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

//                                        $(document).on("click", "#rename_file", function (e) {
//                                            e.preventDefault();
//                                            var new_file_name = false;
//                                            if (new_file_name = prompt("Rename to", data.filename)) {
//                                                $(".download-result-link").attr("download", new_file_name);
//                                                $(".download-result-link .download_file_name").html(new_file_name);
//                                            }
//                                        });

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
                });
            }
        },
        uploadFile: function (file) {
            var formData = new FormData();
            formData.append('file', file);
            currentUploads.changeStatus(file.name, true);
            formData.append("UUID", UUID);
            formData.append("path", 'doc-to-pdf');
            formData.append("_token", $('input[name="_token"]').val());

            toolDocToPdf.upload_in_progress = true;
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
                    toolDocToPdf.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');

                    currentUploads.changeStatus(file.name, false);
                    toolDocToPdf.upload_in_progress = false;
                }
            });
        }
    };

    A_TOOL = toolDocToPdf;


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

    toolDocToPdf.init($(".upload-file-tool"));

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
*/
