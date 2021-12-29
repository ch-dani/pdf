// var EPUB2PDF = {
// 	name: "EPUB2PDF",
// 	need_preview: false,
// 	tool_section: $(".epub_section"),
// 	preview_block: false, //$(".pages_preview_block"),
// 	preview_section: false,
// 	csfr: false,
// 	selectable_pages: true,
// 	toolurl: "/epub-to-pdf",
// 	data: { },
// 	load_blob: true,
// 	file_data: false,
// 	page_preview_width: 1065,
// 	one_canvas_for_all_pages: false,
// 	hide_before_upload: true,
// 	page_preview_items_selector: false,
// 	show_only_first_page: false,
// 	init: function(){
// 		this.bind();
// 	},
// 	bind: function(){
// 		$(document).on("click", "#start_task", $.proxy(this.save, this));
//
// 		$(document).on("pdftool_file_selected", function(ev, file){
// 			pdfUploader.getBlob(file).then((data) => {
// 				//var pdfjsLib = window['pdfjs-dist/build/pdf'];
// 				pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
//
// 				var loadingTask = pdfjsLib.getDocument({data: data});
// 				$(document).trigger("pdf_loading_task", [loadingTask]);
// 				loadingTask.promise.catch(function(e){
// 					console.log("wow doge", e);
// 				});
// 			});
// 		});
// 	},
//
// 	save: function(e){
// 		$this = PDFTOOLS;
// 		PDFTOOLS.startTask()
// 		var intervalID = setInterval( function() {
// 			console.log("upload progress is "+spe.upload_in_progress);
// 			if(!spe.upload_in_progress){
// 				clearInterval(intervalID);
// 				$this.ajax({uuid: UUID, file_name: $this.file.name}).then($this.taskComplete);
// 			}
// 		} , 250);
//
// 		return false;
// 	},
// }
// if(EPUB2PDF.tool_section.length>0){
// 	window.skip_extract = 1;
// 	EPUB2PDF = $.extend(PDFTOOLS, EPUB2PDF);
// 	EPUB2PDF.main();
// }
//


var A_TOOL = false;
var ALLOW_FILE_EXT = "application/epub+zip";
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

            if (file_ext != 'epub') {
                swal("Error", "Please select EPUB file.", "error");
            } else {
                Array.from(files).forEach(file => {
                    that.getBase64(file).then(function (base64) {
                        expansion = file.name.substring(file.name.lastIndexOf('.'));
                        var filename = basename + '.epub';
                        let $tmp_canvas = $(
                            '<div class="convert_doc right_doc image-canvas-li" data-number="' + number + '">' +
                            '<div class="convert_doc_content">' +
                            '<div class="download_convert_doc">' +
                            '<img src="https://b1.pngbarn.com/png/826/150/flatfiles-1-epub-logo-png-clip-art-thumbnail.png" alt="">' +
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
                        $(document).trigger("start_task", []);

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

                                // Convert EPUB to PDF file
                                $.ajax({
                                    type: 'POST',
                                    url: '/epub-to-pdf',
                                    data: {
                                        _token: $('input[name="_token"]').val(),
                                        UUID: UUID,
                                        pageFormat: 'auto',
                                        pageMargin: 0,
                                        pageOrientation: 'auto',
                                        files: files,
                                    },
                                    success: function (data) {
                                        if (data.status == 'success') {
                                            $("#apply-popup .modal-header").removeClass("hidden");

                                            $(".creating_document").hide();
                                            $(".create_file_box").show();
                                            $(".result-top-line .download_file_name").html(data.new_file_name);
                                            $(".download-result-link").attr({
                                                "href": data.url,
                                                "download": data.filename
                                            });
                                            $("#save-dropbox").attr({
                                                'data-url': data.url,
                                                'data-file_name': data.new_file_name
                                            });
                                            $("#save-gdrive").attr({
                                                'data-src': data.url,
                                                'data-filename': data.new_file_name
                                            });

                                            $(document).trigger("after_save_file", [{
                                                file_name: data.new_file_name,
                                                url: data.url
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
            formData.append("path", 'epub-to-pdf');
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
