var A_TOOL = false;


$(document).ready(function () {
    var UUID = guid();

    var basename = false;
    var pages_per_sheet = '2x1';
    var page_ordering = 'horizontal';
    var original_size = 0;

    var sizes = {
        '2x1': 2,
        '2x2': 4,
        '4x2': 8,
        '4x4': 16,
        '8x4': 32
    };

    var toolNUpPdf = {
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

            });
        },
        fileSelected: function (e, file_obj = false) {

            if (file_obj) {
                var that = toolNUpPdf;
                this.files = [file_obj];
                file = (this.files[0]),
                    file_ext = file.name.split(".").pop().toLowerCase();
            } else {
                var that = toolNUpPdf,
                    file = (this.files[0]),
                    file_ext = file.name.split(".").pop().toLowerCase();
            }

            $(document).trigger("pdftool_file_selected", [file]);
            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            $('.file-name-pdf').text('Selected: ' + file.name);

            if (file_ext != 'pdf') {
                swal("Error", "Please select PDF file.", "error");
            } else {
                toolNUpPdf.getBlob(file).then((data) => {
                    //var pdfjsLib = window['pdfjs-dist/build/pdf'];
                    pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

                    var loadingTask = pdfjsLib.getDocument({data: data});
                    $(document).trigger("pdf_loading_task", [loadingTask]);
                    loadingTask.promise.catch(function (e) {
                        console.log("wow doge", e);
                    });
                    that.uploadFile(this.files[0]);
                });

                $('.app-welcome').hide(200);
                setTimeout(function () {
                    $('.n-up-pdf').show(200);
                }, 200);

                $(document).on('click', '#n-up-pdf', function () {
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

                        if (!toolNUpPdf.upload_in_progress) {
                            clearInterval(intervalID);

                            $.ajax({
                                type: 'POST',
                                url: '/tool/n-up-pdf',
                                data: {
                                    _token: $('input[name="_token"]').val(),
                                    UUID: UUID,
                                    basename: basename,
                                    pages_per_sheet: pages_per_sheet,
                                    page_ordering: page_ordering,
                                    original_size: original_size,
                                    more_options: $('.more-options-box').is(':visible')
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

                    return false;
                });
            }
        },
        uploadFile: function (file) {
            var formData = new FormData();
            formData.append('file', file);
            formData.append("UUID", UUID);
            formData.append("path", 'n-up-pdf');
            formData.append("_token", $('input[name="_token"]').val());

            toolNUpPdf.upload_in_progress = true;
            
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


                    toolNUpPdf.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');
                }
            });
        }
    };

    A_TOOL = toolNUpPdf;

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

    toolNUpPdf.init($(".upload-file-tool"));

    $('input[name="nup"]').on('change', function () {
        let size = $('input[name="nup"]:checked').data('size');
        let ordering = $('input[name="nup"]:checked').data('ordering');

        $('input[name="pages_per_sheet"][data-size="' + size + '"]').prop('checked', true);
        $('input[name="ordering"][data-ordering="' + ordering + '"]').prop('checked', true);

        pages_per_sheet = size;
        page_ordering = ordering;
    });

    $('input[name="pages_per_sheet"]').on('change', function () {
        pages_per_sheet = $('input[name="pages_per_sheet"]:checked').data('size');
    });

    $('input[name="ordering"]').on('change', function () {
        page_ordering = $('input[name="ordering"]:checked').data('ordering');
    });

    $('input[name="original_size"]').on('change', function () {
        if ($(this).is(':checked'))
            original_size = 1;
        else
            original_size = 0;
    });

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
