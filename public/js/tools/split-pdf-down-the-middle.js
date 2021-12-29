window.show_anyway = true;
window.skip_extract = true;

var A_TOOL = false;

$(document).ready(function () {
    var UUID = guid();

    var basename = false;
    var selected = [];
    var numPages = false;
    var width = false;
    var height = false;
    var scale = 1;

    var toolSplitInHalf = {
        el: false,
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
                var that = toolSplitInHalf;
                this.files = [file_obj];
                file = (this.files[0]);
            } else {
                var that = toolSplitInHalf,
                    file = (this.files[0]);
            }

            file_ext = file.name.split(".").pop().toLowerCase();
            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            if (file_ext != 'pdf') {
                swal("Error", "Please select a PDF file.", "error");
            } else {
                that.getBlob(file).then((data) => {
                    var pdfjsLib = window['pdfjs-dist/build/pdf'];
                    pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

                    var loadingTask = pdfjsLib.getDocument({data: data});

                    $(document).trigger("pdf_loading_task", [loadingTask]);

                    loadingTask.promise.then(function (pdf) {
                        numPages = pdf.pdfInfo.numPages;
                        that.uploadFile(file);

                        for (currentPage = numPages; currentPage >= 1; currentPage--) {
                            selected.push(currentPage);

                            let $tmp_canvas = $('<div class="tool-page-block">' +
                                '<canvas id="canvas-page-' + currentPage + '"></canvas>' +
                                '</div>');
                            $('#pages-pdf .wr').append($tmp_canvas);

                            pdf.getPage(currentPage).then(function (page) {
                                var scale = 2;
                                var viewport = page.getViewport({scale: scale});

                                var canvas = document.getElementById('canvas-page-' + (page.pageIndex + 1) + '');
                                var context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                var renderContext = {
                                    canvasContext: context,
                                    viewport: viewport
                                };
                                var renderTask = page.render(renderContext);
                                renderTask.promise.then(function () {
                                    console.log('Page rendered');
                                });
                            });
                        }

                        $('.app-welcome').hide(200);
                        setTimeout(function () {
                            $("#resizable").resizable({
                                create: function (event, ui) {
                                    $('.ui-resizable-s').css('cssText', 'z-index: 90; display: block !important; width: 100%; left: 0; border: 0px; background: #0000;');
                                    $('#resizable').height('4.135in');
                                }
                            });
                            $('#pages-pdf').show(200);
                            $('.fixed-bottom-panel').show(200);
                        }, 200);

                    }, function (reason) {
                        // PDF loading error
                        console.error(reason);
                    });

                    $(document).on('change', 'input[name="split"]', function () {
                        if ($('input[name="split"]:checked').val() == 'vertically') {
                            $('#resizable').addClass('vertically');
                            $('#resizable').css('cssText', 'height: ' + height + 'in; width: ' + (width / 2) + 'in; max-width: ' + width + 'in; max-height: ' + height + 'in; left: calc(50% - ' + (width / 2) + 'in);');
                            $('.ui-resizable-e').css('cssText', 'z-index: 90; height: 100%; border: 0px; background: rgba(0, 0, 0, 0); display: block !important; top: 5px;');
                            $('.ui-resizable-s').hide();
                        } else {
                            $('#resizable').removeClass('vertically');
                            $('#resizable').css('cssText', 'height: ' + (height / 2) + 'in; width: ' + width + 'in; max-height: ' + height + 'in; max-width: ' + width + 'in;  left: calc(50% - ' + (width / 2) + 'in);');
                            $('.ui-resizable-s').css('cssText', 'z-index: 90; display: block !important; width: 100%; left: 0; border: 0px; background: #0000;');
                            $('.ui-resizable-e').hide();
                        }
                    });

                    $(document).on('click', '#split-in-half', function () {
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

                            if (!toolSplitInHalf.upload_in_progress) {
                                clearInterval(intervalID);

                                $.ajax({
                                    type: 'POST',
                                    url: '/tool/split-in-half',
                                    data: {
                                        _token: $('input[name="_token"]').val(),
                                        UUID: UUID,
                                        basename: basename,
                                        numPages: numPages,
                                        split: $('input[name="split"]:checked').val(),
                                        width: $("#resizable")[0].getBoundingClientRect().width * scale,
                                        height: $("#resizable")[0].getBoundingClientRect().height * scale,
                                        pattern: $('#pattern').val(),
                                        arabic: $('#arabic').is(':checked'),
                                        booklet: $('#booklet').is(':checked')
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

//                                            $(document).on("click", "#rename_file", function (e) {
//                                                e.preventDefault();
//                                                var new_file_name = false;
//                                                if (new_file_name = prompt("Rename to", data.filename)) {
//                                                    $(".download-result-link").attr("download", new_file_name);
//                                                    $(".download-result-link .download_file_name").html(new_file_name);
//                                                }
//                                            });

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
                });
            }
        },
        resize: function () {
            $('#resizable').css({
                'height': (height / 2) + 'in',
                'max-height': height + 'in',
                'width': width + 'in',
                'left': 'calc(50% - ' + (width / 2) + 'in)'
            });

            $('#pages-pdf.split .wr').css({
                'width': width + 'in',
                'height': height + 'in'
            });

            $('#pages-pdf.split .tool-page-block').css({
                'width': width + 'in',
                'height': height + 'in'
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
        uploadFile: function (file) {
            var formData = new FormData();
            formData.append('file', file);
            currentUploads.changeStatus(file.name, true);
            formData.append("UUID", UUID);
            formData.append("path", 'split-in-half');
            formData.append("_token", $('input[name="_token"]').val());

            toolSplitInHalf.upload_in_progress = true;

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
                    } else {
                        $.ajax({
                            url: `/tool/get-doc-size`,
                            type: "POST",
                            data: {
                                _token: $('input[name="_token"]').val(),
                                UUID: UUID
                            },
                            success: function (response) {
                                if (response.status == 'success') {
                                    width = response.width;
                                    height = response.height;

                                    if (width > 12 || height > 17) {
                                        scale = 2;
                                        width = width / scale;
                                        height = height / scale;
                                    }

                                    toolSplitInHalf.resize();
                                }
                            }
                        });
                    }

					$(".current_file_status").html("Your task is processing");

                    currentUploads.changeStatus(file.name, false);
                    toolSplitInHalf.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');
                    currentUploads.changeStatus(file.name, false);
                    toolSplitInHalf.upload_in_progress = false;
                }
            });
        }
    };

    A_TOOL = toolSplitInHalf;

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

    toolSplitInHalf.init($(".upload-file-tool"));

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
