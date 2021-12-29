var A_TOOL = false;
$(document).ready(function () {
    var UUID = guid();

    var selected = [];
    var numPages = 0;
    var dpi = 150;
    var format = 'pdf';
    var basename = false;
    var expansion = 'pdf';

    converterTool = toolPdfToJpg = {
        el: false,
        upload_in_progress: false,
        multiple_upload: true,
        current_letter: 0,
        letter: "A",
        letters: [],
        init: function (element) {
            this.el = element;
            this.bind();
        },
        bind: function () {
            this.el.on("change", this.fileSelected);

            $(document).on('click', '.save-images-array', function () {
                PDFTOOLS.startTask();

                $(".download-result-link").css("background-image", "url('/img/ext/jpeg.svg')");

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

                if($('input[name="format"]').length){
                    format = $('input[name="format"]').val();
                }

                var intervalID = setInterval(function () {

                    if (!toolPdfToJpg.upload_in_progress) {
                        clearInterval(intervalID);
                        $.ajax({
                            type: 'POST',
                            url: '/pdf-burst',
                            data: {
                                _token: $('input[name="_token"]').val(),
                                page: selected,
                                uuid: UUID,
                                // dpi: dpi,
                                // format: format,
                                // numPages: numPages,
                                // basename: basename,
                                // outputFilenamePattern: $('input[name="outputFilenamePattern"]').val(),
                                operation_id: operation_id,
                                type: 'everyPage',
                                letters: toolPdfToJpg.letters,
                                file_name: basename + '.pdf',
                                name_patern: $('input[name="outputFilenamePattern"]').val(),
                                pdf_password: false

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

                                    /*let link = document.createElement('a');
									link.setAttribute('href', data.file);
									link.setAttribute('download', data.filename);
									document.body.appendChild(link);
									link.click();
									link.remove();

									$(document).trigger("after_save_file", [{
										file_name: data.filename,
										url: data.file
									}]);*/
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

            $('#pages-pdf').on('click', '.save-image-page', function (e) {
                e.preventDefault();

                let $button = $(this);

                PDFTOOLS.startTask();

                if($('input[name="format"]').length){
                    format = $('input[name="format"]').val();
                }

                $.ajax({
                    type: 'POST',
                    url: '/pdf-burst',
                    data: {
                        _token: $('[name="csrf-token"]').attr('content'),
                        page: $(this).data('page'),
                        format: format,
                        uuid: UUID,
                        basename: basename,
                        operation_id: operation_id,
                        // operation_id: $(document).find('input[name=operation_id]').val()
                        ranges: $(this).data('page'),
                        operation_id: operation_id,
                        type: 'byPage',
                        single_page_extractor: true,
                        letter: $button.data('uploadIteration'),
                        letters: toolPdfToJpg.letters,
                        file_name: basename + '.pdf',
                        name_patern: $('input[name="outputFilenamePattern"]').val(),
                        pdf_password: false
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            let link = document.createElement('a');
                            link.setAttribute('href', data.file);
                            link.setAttribute('download', data.filename);
                            document.body.appendChild(link);
                            // link.click();
                            PDFTOOLS.taskComplete({
                                success: true,
                                url: data.file,
                                new_file_name: data.filename
                            });
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    }
                });
            });

            $('#pages-pdf').on('click', '.tool-page-block', function (e) {
                if ($(e.target).hasClass('save-image-page'))
                    return false;

                let page = parseInt($(this).find('.save-image-page').data('page'));

                if ($(this).hasClass('selected')) {
                    selected = selected.slice(selected.indexOf(page), 1);
                    $(this).removeClass('selected');
                } else {
                    selected.push(page);
                    $(this).addClass('selected');
                }
            });

            $('input[name="resolution"]').on('change', function () {
                dpi = parseInt($(this).val());
            });

            $('input[name="format"]').on('change', function () {
                format = $(this).val();
            });
        },
        fileSelected: function (e, file_obj = false) {


            if(file_obj){
                var that = toolPdfToJpg;
                this.files = [file_obj];
                file = (this.files[0]);
            }else{

                var that = toolPdfToJpg,
                    file = (this.files[0]);
            }
            file_ext = file.name.split(".").pop().toLowerCase();
            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            if($('input[name="format"]').length){
                switch($('input[name="format"]').val()){
                    case 'png16m':
                        expansion = 'png';
                        break;
                }
            }

            if (file_ext != 'pdf') {
                swal("Error", "Please select PDF file.", "error");
            } else {
                that.getBlob(file).then((data) => {
                    //var pdfjsLib = window['pdfjs-dist/build/pdf'];
                    pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

                    var loadingTask = pdfjsLib.getDocument({data: data});
                    $(document).trigger("pdf_loading_task", [loadingTask]);

                    loadingTask.promise.then(function (pdf) {
                        console.log(pdf);
                        numPages = pdf.pdfInfo.numPages;
                        that.uploadFile(file);

                        for (currentPage = 1; currentPage <= numPages; currentPage++) {
                            /*let $tmp_canvas = $('<div class="tool-page-block">' +
                                '<span class="page-num">' + currentPage + '</span>' +
                                '<canvas id="canvas-page-' + currentPage + '"></canvas>' +
                                '<div class="button-wrapper">' +
                                '<div class="save-image-page" data-page="' + currentPage + '"><i class="fas fa-arrow-down"></i>Save as JPG</div>' +
                                '</div>' +
                                '</div>');*/
                            if(currentPage == 1){
                                var filename = basename + '.' + expansion;
                            }else{
                                var filename = basename + '-' + currentPage + '.' + expansion;
                            }

                            let $tmp_canvas = $(
                                '<div class="convert_doc right_doc">' +
                                '<div class="convert_doc_content">' +
                                '<div class="download_convert_doc">' +
                                '<canvas id="canvas-page-' + toolPdfToJpg.letter + '-' + currentPage + '"></canvas>' +
                                '</div>' +
                                '</div>' +
                                '<div class="download_icon_doc">' +
                                '<a class="save-image-page" href="#" data-upload-iteration="' + toolPdfToJpg.letter + '" data-page="' + currentPage + '">' +
                                '<img src="freeconvert/img/download_arrow.svg">' +
                                '</a>' +
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

                                var canvas = document.getElementById('canvas-page-' + toolPdfToJpg.letter + '-' + (page.pageIndex + 1) + '');
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
        uploadFile: function (file) {
            toolPdfToJpg.current_letter++;
            toolPdfToJpg.letter = String.fromCharCode(64 + toolPdfToJpg.current_letter);
            toolPdfToJpg.letters.push(toolPdfToJpg.letter);

            var formData = new FormData();
            formData.append('file', file);
            formData.append('operation_type', 'splitpdf');
            formData.append('skip_extract', 1);
            formData.append('remove_all_texts', 0);
            formData.append('multiple_upload', toolPdfToJpg.letter);
            formData.append('type', 'PDF');
            formData.append('operation_id', operation_id);
            formData.append('pdf_password', false);

            currentUploads.changeStatus(file.name, true);
            formData.append("UUID", UUID);
            formData.append("_token", $('input[name="_token"]').val());

            if($('input[name="path"]').length){
                formData.append("path", $('input[name="path"]').val());
            }

            toolPdfToJpg.upload_in_progress = true;

            $(".current_file_status").html("File is uploading");

            return $.ajax({
                url: `/pdf/uploadPDF`,
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
                    if (!data.success) {
                        swal('Error', data.message, 'error');
                        return false;
                    }
                    $(".current_file_status").html("Your task is processing");
                    // $(document).find('input[name=operation_id]').val(data.operation_id);

                    currentUploads.changeStatus(file.name, false);
                    toolPdfToJpg.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');

                    currentUploads.changeStatus(file.name, false);
                    toolPdfToJpg.upload_in_progress = false;
                }
            });
        }
    };


    A_TOOL = toolPdfToJpg;

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

    toolPdfToJpg.init($(".upload-file-tool"));

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
