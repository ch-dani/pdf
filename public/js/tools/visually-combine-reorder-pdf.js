window.skip_extract = true;
window.show_anyway = true;
var A_TOOL = false;
var UUID = false;
$(document).ready(function () {
    UUID = guid();
    var numFiles = 0;
    var NameFiles = [];
    var htmls = {};
    
    var files = [];

    var combinePdf = {
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
                var that = combinePdf;
                files.push(file_obj);
            } else {
                var that = combinePdf,
                    files = this.files;
            }


            if ($('.s-combine-reorder').css('display') == 'none')
                setTimeout(function () {
                    $("html, body").animate({scrollTop: $('.s-combine-reorder').offset().top - 150}, "medium");
                }, 500)

            var pdfjsLib = window['pdfjs-dist/build/pdf'];
            pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

            Array.from(files).forEach(file => {
                that.getBlob(file).then((data) => {

                    var loadingTask = pdfjsLib.getDocument({data: data});

                    $(document).trigger("pdf_loading_task", [loadingTask]);

                    loadingTask.promise.then(function (pdf) {
                        console.log(pdf);
                        that.uploadFile(files);

                        let numPages = pdf.pdfInfo.numPages;
                        let pdf_id = guid();
                        let filename = file.name.substring(0, file.name.lastIndexOf('.'))

                        NameFiles.push(file.name);
                        $('#FileList').append('<li><a class="open-right-panel" href="#" data-id="' + pdf_id + '">' + file.name + '</a></li>');
                        $('#changeFileSelectorBtn .dropdown-menu').append('<li><a class="open-right-panel" href="#" data-id="' + pdf_id + '">' + file.name + '</a></li>');
                        $('.clear-opts').append('<li><a href="#" class="keep_only" data-id="' + pdf_id + '">Keep only ' + file.name + '</a></li>');

                        for (currentPage = 1; currentPage <= numPages; currentPage++) {
                            let $tmp_canvas = $('<li class="image-canvas-li" data-page="' + currentPage + '" data-doc="' + pdf_id + '" data-basename="' + file.name + '">' +
                                '    <div class="image-canvas-list-item">' +
                                '        <div class="image-page-caption">' + currentPage + ' of ' + filename + '</div>' +
                                '        <div class="image-canvas-wrap">' +
                                '            <canvas id="' + pdf_id + '-' + currentPage + '" data-rotation="0"></canvas>' +
                                '        </div>' +
                                '        <button class="image-delete-page"><i class="fas fa-times"></i></button>' +
                                '        <div class="combine-reorder-rotate">' +
                                '            <div class="btn-group">' +
                                '                <button class="combine-reorder-tools-btn" data-rotation="-90"><i class="fas fa-undo"></i></button>' +
                                '                <button class="combine-reorder-tools-btn" data-rotation="90"><i class="fas fa-redo"></i></button>' +
                                '            </div>' +
                                '        </div>' +
                                '    </div>' +
                                '</li>');

                            $('.s-combine-reorder #sortable').append($tmp_canvas);

                            if (!$('#sortable-' + pdf_id).length) {
                                $('.combine-reorder-selector-wrap-list').append('<ul style="display: none;" class="image-canvas-list" id="sortable-' + pdf_id + '"></ul>');

                                $('#sortable-' + pdf_id).sortable({
                                    connectWith: '#sortable',
                                    tolerance: "pointer",
                                    stop: function (event, ui) {
                                        if (ui.position.left > 300) {
                                            ui.item.after(ui.item.find("li"))

                                            let clone = $(ui.item).clone();

                                            var canvas = $(clone).find('canvas').get(0);
                                            var ctx = canvas.getContext("2d");
                                            var img = $(ui.item).find('canvas').get(0);
                                            ctx.drawImage(img, 0, 0);

                                            $('#sortable .image-canvas-li:eq(' + ui.item.index() + ')').before(clone[0]);
                                            return false;
                                        } else
                                            return false;
                                    },
                                    start: function (e, info) {
                                        //info.item.siblings(".selected").not(".ui-sortable-placeholder").appendTo(info.item);
                                    },
                                });
                                $('#sortable-' + pdf_id).disableSelection();
                            }

                            $tmp_canvas = $('<li class="image-canvas-li" data-page="' + currentPage + '" data-doc="' + pdf_id + '" data-basename="' + file.name + '">' +
                                '    <div class="image-canvas-list-item">' +
                                '        <div class="image-page-caption">' + currentPage + ' of ' + filename + '</div>' +
                                '        <div class="image-canvas-wrap">' +
                                '            <canvas id="' + pdf_id + '-' + currentPage + '-panel"  data-rotation="0"></canvas>' +
                                '        </div>' +
                                '        <button class="image-delete-page"><i class="fas fa-times"></i></button>' +
                                '        <div class="combine-reorder-rotate">' +
                                '            <div class="btn-group">' +
                                '                <button class="combine-reorder-tools-btn" data-rotation="-90"><i class="fas fa-undo"></i></button>' +
                                '                <button class="combine-reorder-tools-btn" data-rotation="90"><i class="fas fa-redo"></i></button>' +
                                '            </div>' +
                                '        </div>' +
                                '    </div>' +
                                '</li>');

                            $('#sortable-' + pdf_id).append($tmp_canvas);

                            pdf.getPage(currentPage).then(function (page) {
                                var scale = 1.5;
                                var viewport = page.getViewport({scale: scale});
                                var rotate = page.pageInfo.rotate;

                                var canvas = document.getElementById(pdf_id + '-' + (page.pageIndex + 1));
                                var context = canvas.getContext('2d');

                                if (rotate == 90 || rotate == 270) {
                                    canvas.height = viewport.width;
                                    canvas.width = viewport.height;

                                    $('#' + pdf_id + '-' + (page.pageIndex + 1)).closest('.image-canvas-list-item').addClass('rotate-page');
                                } else {
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;
                                }

                                var renderContext = {
                                    canvasContext: context,
                                    viewport: viewport
                                };
                                var renderTask = page.render(renderContext);
                                renderTask.promise.then(function () {
                                    console.log('Page rendered');
                                });

                                var canvas = document.getElementById(pdf_id + '-' + (page.pageIndex + 1) + '-panel');
                                var context = canvas.getContext('2d');

                                if (rotate == 90 || rotate == 270) {
                                    canvas.height = viewport.width;
                                    canvas.width = viewport.height;

                                    $('#' + pdf_id + '-' + (page.pageIndex + 1) + '-panel').closest('.image-canvas-list-item').addClass('rotate-page');
                                } else {
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;
                                }

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

                        if ($('.s-combine-reorder').css('display') == 'none') {
                            $('.app-welcome').hide(200);
                            setTimeout(function () {
                                $('.s-combine-reorder').show(200);
                                $('.fixed-bottom-panel').show(200);
                            }, 200)
                        }
                    }, function (reason) {
                        // PDF loading error
                        console.error(reason);
                    });
                });
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
            formData.append("path", 'combine-pdf');
            formData.append("_token", $('input[name="_token"]').val());

            combinePdf.upload_in_progress = true;

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
                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });

                    if (data.status != 'success') {
                        swal('Error', data.message, 'error');
                    }
                    
                    $(".current_file_status").html("Your task is processing");
                    

                    combinePdf.upload_in_progress = false;
                },
                error: function (data) {
                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });

                    combinePdf.upload_in_progress = false;

                    swal('Error', "Cant upload files..", 'error');
                }
            });
        }
    };


    A_TOOL = combinePdf;

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

    combinePdf.init($(".upload-file-tool"));

    $(".s-combine-reorder #sortable").sortable({
        tolerance: "pointer"
    });
    $(".s-combine-reorder #sortable").disableSelection();

    $('#preview_zoom').on('change', function () {
        let width = parseInt($(this).val()) + 40;
        let height = parseInt($(this).val()) * 1.41;

        $('.image-canvas-li').css('width', width + 'px');
        $('.image-canvas-wrap').css('height', height + 'px');

        $('.rotate-page .image-canvas-wrap').css('width', '100%');
        $('.rotate-page .image-canvas-wrap').css('height', (width * 0.7) + 'px');
    })

    $('#preview_zoom').on('input', function () {
        $(this).trigger('change');
    });

    $('#ReverseList').on('click', function () {
        let $ul = $('.s-combine-reorder #sortable');
        $ul.html($ul.find('li').get().reverse());

        $(".combine-reorder-tools .dropdown-menu").hide();
        return false;
    });

    $(document).on('click', '.keep_only', function () {
        let id = $(this).data('id');

        $('.s-combine-reorder .image-canvas-li').each(function () {
            if ($(this).data('doc') != id)
                $(this).remove();
        });

        $(".combine-reorder-tools .dropdown-menu").hide();
        $('.open-right-panel:first').click();
        return false;
    });

    $('#addBlankPageBtn').on('click', function () {
        let $tmp_canvas = $('<li class="image-canvas-li" data-page="1">' +
            '    <div class="image-canvas-list-item">' +
            '        <div class="image-page-caption">New blank page</div>' +
            '        <div class="image-canvas-wrap">' +
            '            <canvas style=" background: #fff; " data-rotation="0"></canvas>' +
            '        </div>' +
            '        <button class="image-delete-page"><i class="fas fa-times"></i></button>' +
            '        <div class="combine-reorder-rotate">' +
            '            <div class="btn-group">' +
            '                <button class="combine-reorder-tools-btn" data-rotation="-90"><i class="fas fa-undo"></i></button>' +
            '                <button class="combine-reorder-tools-btn" data-rotation="90"><i class="fas fa-redo"></i></button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '</li>');

        $('.s-combine-reorder #sortable').prepend($tmp_canvas);
    });

    $(".combine-reorder-selector-header .close").on("click", function () {
        $(".combine-reorder-selector-wrap").hide();
        $(".body-wrap").css({
            "margin-left": "0",
            "overflow-x": "auto"
        });
    });

    $(document).on("click", ".open-right-panel", function () {
        $('.combine-reorder-selector-wrap-list ul').hide();
        $('.combine-reorder-selector-wrap-list #sortable-' + $(this).data('id')).show();
        $('.combine-reorder-selector-header .filename').text($(this).text());

        $(".combine-reorder-selector-wrap").show().css("width", "402px");
        $(".body-wrap").css({
            "margin-left": "402px",
            "overflow-x": "hidden"
        });

        $(".dropdown-menu").hide();
        return false;
    });

    $(".combine-reorder-tools .dropdown-toggle, .combine-reorder-selector-header .dropdown-toggle").on("click", function () {
        let dropMenu = $(this).parent().find(".dropdown-menu");

        if (!dropMenu.is(":visible"))
            $(".combine-reorder-tools .dropdown-menu").hide();

        dropMenu.fadeToggle(200);
    });

    $(document).on('click', ".image-delete-page", function () {
        $(this).closest(".image-canvas-li").remove();
    });

    $(document).on("mouseup", function (event) {
        var target = event.target;
        var dropBtn = $(".combine-reorder-tools .btn-group, .combine-reorder-selector-header .btn-group");

        if (!dropBtn.is(target) && dropBtn.has(target).length === 0)
            $(".combine-reorder-tools .dropdown-menu, .combine-reorder-selector-header .dropdown-menu").hide();
    });

    $('#SelectAll').on('click', function () {
        $('.s-combine-reorder .image-canvas-li').each(function () {
            if (!$(this).hasClass('selected'))
                $(this).addClass('selected');
        });

        $(".combine-reorder-tools .dropdown-menu").hide();
        return false;
    });

    $('#SelectAllPanel').on('click', function () {
        $('.combine-reorder-selector-wrap-list .image-canvas-li:visible').each(function () {
            if (!$(this).hasClass('selected'))
                $(this).addClass('selected');
        });

        return false;
    });

    $('#SelectNonePanel').on('click', function () {
        $('.combine-reorder-selector-wrap-list .image-canvas-li:visible').each(function () {
            if ($(this).hasClass('selected'))
                $(this).removeClass('selected');
        });

        return false;
    });

    $('#DeselectAll').on('click', function () {
        $('.s-combine-reorder .image-canvas-li').each(function () {
            if ($(this).hasClass('selected'))
                $(this).removeClass('selected');
        });

        $(".combine-reorder-tools .dropdown-menu").hide();
        return false;
    });

    $('#InvertSelection').on('click', function () {
        $('.s-combine-reorder .image-canvas-li').each(function () {
            if ($(this).hasClass('selected'))
                $(this).removeClass('selected');
            else
                $(this).addClass('selected');
        });

        $(".combine-reorder-tools .dropdown-menu").hide();
        return false;
    });

    $('#RemoveSelected').on('click', function () {
        $('.s-combine-reorder .image-canvas-li').each(function () {
            if ($(this).hasClass('selected'))
                $(this).remove();
        });

        $(".combine-reorder-tools .dropdown-menu").hide();
        return false;
    });

    $('#ClearAll').on('click', function () {
        $('.s-combine-reorder .image-canvas-li').each(function () {
            $(this).remove();
        });

        $(".combine-reorder-tools .dropdown-menu").hide();
        $('.open-right-panel:first').click();
        return false;
    });

    $(".image-canvas-list").on("click", ".combine-reorder-tools-btn", function () {

        let canvasImg = $(this).closest(".image-canvas-list-item").find("canvas"),
            positionOfCanvas = canvasImg.data("rotation"),
            rotateDirection = $(this).data("rotation");

        console.log(canvasImg)

        if (rotateDirection === 90) {
            positionOfCanvas += rotateDirection;
            if (positionOfCanvas === 90) {
                canvasImg.removeClass("rotated-270 rotated-180").addClass("rotated-90").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 90);
            } else if (positionOfCanvas === 180) {
                canvasImg.removeClass("rotated-270 rotated-90").addClass("rotated-180").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 180);
            } else if (positionOfCanvas === 270) {
                canvasImg.removeClass("rotated-180 rotated-90").addClass("rotated-270").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 270);
            } else {
                positionOfCanvas = 0;
                canvasImg.removeClass("rotated-270 rotated-180 rotated-90").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 0);
            }
        } else if (rotateDirection === -90) {
            if (positionOfCanvas === 0) {
                positionOfCanvas = 360;
            }
            positionOfCanvas += rotateDirection;
            if (positionOfCanvas === 90) {
                canvasImg.removeClass("rotated-270 rotated-180").addClass("rotated-90").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 90);
            } else if (positionOfCanvas === 180) {
                canvasImg.removeClass("rotated-270 rotated-90").addClass("rotated-180").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 180);
            } else if (positionOfCanvas === 270) {
                canvasImg.removeClass("rotated-180 rotated-90").addClass("rotated-270").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 270);
            } else {
                positionOfCanvas = 0;
                canvasImg.removeClass("rotated-270 rotated-180 rotated-90").data("rotation", positionOfCanvas);
                canvasImg.data('rotation', 0);
            }
        }

    });

    $(document).on('click', '.image-canvas-li', function (e) {
        if ($(e.target).hasClass('combine-reorder-tools-btn') || $(e.target).hasClass('fas'))
            return false;

        if ($(this).hasClass('selected'))
            $(this).removeClass('selected');
        else
            $(this).addClass('selected');
    });

    $(document).on('click', '.save-pdf', function () {
        if (!$('.s-combine-reorder .image-canvas-li').length)
            swal('Error', 'No pages. Please, add at least one page.', 'error');

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

            if (!combinePdf.upload_in_progress) {
                clearInterval(intervalID);

                let files = {};

//                if (!$('.s-combine-reorder .image-canvas-li.selected').length){
//                    $('.s-combine-reorder .image-canvas-li').each(function () {
//                        if (typeof files[$(this).data('basename')] == 'undefined')
//                            files[$(this).data('basename')] = [];

//                        files[$(this).data('basename')].push([
//                            $(this).find('canvas').data('rotation'),
//                            $(this).data('page')
//                        ]);
//                    });
//                }else{
//                    $('.s-combine-reorder .image-canvas-li.selected').each(function () {
//                        if (typeof files[$(this).data('basename')] == 'undefined')
//                            files[$(this).data('basename')] = [];

//                        files[$(this).data('basename')].push([
//                            $(this).find('canvas').data('rotation'),
//                            $(this).data('page')
//                        ]);
//                    });
//                }


				files = {};


				$(".combine-reorder-lists .image-canvas-list li").each(function(i, el){
					el = $(el);
					files[i] = {
						"file": el.data("basename"),
						"page": el.data("page"),
						"rotation": el.find('canvas').data('rotation')
					};
				});



                $.ajax({
                    type: 'POST',
                    url: '/tool/combine-pdf',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        files: JSON.stringify(files),
                        UUID: UUID
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

//                            $(document).on("click", "#rename_file", function (e) {
//                                e.preventDefault();
//                                var new_file_name = false;
//                                if (new_file_name = prompt("Rename to", data.filename)) {
//                                    $(".download-result-link").attr("download", new_file_name);
//                                    $(".download-result-link .download_file_name").html(new_file_name);
//                                }
//                            });

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

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
