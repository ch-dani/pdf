var A_TOOL = false;
FILE_NUM = 0;
var file_names = [];
$(document).ready(function () {
    var UUID = guid();

    var selected = [];
    var numPages = false;
    var basename = false;
    var numFiles = 0;
    var uploadIteration = -1;
    var currentPageGlobal = 0;

    converterTool = toolDeletePages = {
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
                var that = toolDeletePages,
                    file = (file_obj),
                    file_ext = file_obj.name.split(".").pop().toLowerCase();
                this.files = [file_obj];
                files = [];
                files.push(file_obj);
                file_names.push(file_obj.name);
            } else {
                var that = toolDeletePages,
                    files = this.files;
            }
            console.log(files);

            let accept_ext = ['pdf'];
            let accept = true;
            let old_numFiles = numFiles;

            $.each(files, function (i, item) {
                if (accept_ext.indexOf(this.name.split(".").pop().toLowerCase()) == -1)
                    accept = false;

                numFiles++;
            });

            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            // if (file_ext != 'pdf') {
            if (!accept) {
                numFiles = old_numFiles;
                swal("Error", "Please select PDF file.", "error");
            } else {
                that.uploadFile(files);
                uploadIteration++;

                Array.from(files).forEach(file => {
                    that.getBlob(file).then((data) => {
                        var file_ext = file.name.substring(file.name.lastIndexOf('.'));
                        var pdfjsLib = window['pdfjs-dist/build/pdf'];
                        pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

                        var loadingTask = pdfjsLib.getDocument({data: data});
                        $(document).trigger("pdf_loading_task", [loadingTask]);

                        loadingTask.promise.then(function (pdf) {
                            numPages = pdf.pdfInfo.numPages;
                            // that.uploadFile(file);

                            for (currentPage = 1; currentPage <= numPages; currentPage++) {
                                currentPageGlobal += currentPage;
                                // selected.push(currentPage);
                                console.log('uploadIteration', uploadIteration);
                                if (!(uploadIteration in selected)) {
                                    selected.push([]);
                                }
                                selected[uploadIteration].push(currentPage);

                                /*
								let $tmp_canvas = $('<div class="tool-page-block">' +
									'<span class="page-num">' + currentPage + '</span>' +
									'<canvas id="canvas-page-' + currentPage + '"></canvas>' +
									'<div class="button-wrapper">' +
									'<div class="save-image-page delete-page" data-page="' + currentPage + '"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete page</div>' +
									'</div>' +
									'</div>');
									*/

                                if (currentPage == 1) {
                                    var filename = basename + '.' + file_ext;
                                } else {
                                    var filename = basename + '-' + currentPage + '.' + file_ext;
                                }

                                let $tmp_canvas = $(
                                    '<div class="convert_doc right_doc tool-page-block">' +
                                    '<div class="convert_doc_content">' +
                                    '<div class="download_convert_doc">' +
                                    '<canvas id="canvas-page-' + uploadIteration + '-' + currentPage + '"></canvas>' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="download_icon_doc">' +
                                    '<a class="delete-page" href="#" data-upload-iteration="' + uploadIteration + '" data-page="' + currentPage + '"><img src="freeconvert/img/close_icon.svg"></a>' +
                                    '</div>' +
                                    '<div class="name_doc">' +
                                    '<h6>' + filename + '</h6>' +
                                    '</div>' +
                                    '</div>'
                                );

                                $('#pages-pdf').append($tmp_canvas);

                                pdf.getPage(currentPage).then(function (page) {
                                    var scale = 1.5;
                                    var viewport = page.getViewport({scale: scale});

                                    var canvas = document.getElementById('canvas-page-' + uploadIteration + '-' + (page.pageIndex + 1) + '');
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

                            /*
							$('.app-welcome').hide(200);
							setTimeout(function () {
								$('#pages-pdf').show(200);
								$('.fixed-bottom-panel').show(200);
							}, 200);
							*/
                            $('main').addClass('file_uploaded').removeClass('file_not_loaded');
                        }, function (reason) {
                            // PDF loading error
                            console.error(reason);
                        });
                    });
                });

                if (!toolDeletePages.alredy_init) {
                    $('#pages-pdf').on('click', '.delete-page', function (e) {
                        e.preventDefault();

                        let $button = $(this);

                        // console.log($(this));
                        // console.log(selected);
                        // delete selected[selected.indexOf(parseInt($(this).data('page')))];
                        delete selected[parseInt($button.data('uploadIteration'))][parseInt($button.data('page')) - 1];

                        $(this).closest(".tool-page-block").remove();
                    });

                    $(document).on('click', '.save-pdf', function () {
                        console.log('click save-pdf');

                        PDFTOOLS.startTask();

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

                            if (!toolDeletePages.upload_in_progress) {
                                clearInterval(intervalID);

                                $.ajax({
                                    type: 'POST',
                                    url: '/tool/delete-pages',
                                    data: {
                                        _token: $('input[name="_token"]').val(),
                                        page: selected,
                                        UUID: UUID,
                                        basename: basename,
                                        numPages: numPages,
                                        pattern: $('input[name="outputFilenamePattern"]').val()
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

                                            /*
                                            let link = document.createElement('a');
                                            link.setAttribute('href', data.file);
                                            link.setAttribute('download', data.filename);
                                            document.body.appendChild(link);
                                            link.click();
                                            link.remove();

                                            $(document).trigger("after_save_file", [{
                                                file_name: data.filename,
                                                url: data.file
                                            }]);
                                            */
                                            PDFTOOLS.taskComplete({
                                                success: true,
                                                url: data.file,
                                                new_file_name: data.filename
                                            });
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

                toolDeletePages.alredy_init = true;
            }
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
            formData.append("path", 'delete-pages');
            formData.append("_token", $('input[name="_token"]').val());
            if (files.length == 1) {
                formData.append("file_num", FILE_NUM);
                FILE_NUM++;
            } else {
                FILE_NUM = files.length;
            }
            toolDeletePages.upload_in_progress = true;

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
                    toolDeletePages.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');
                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });
                    toolDeletePages.upload_in_progress = false;
                }
            });
        }
    };

    A_TOOL = toolDeletePages;

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

    toolDeletePages.init($(".upload-file-tool"));

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
