var A_TOOL = false;
var file_names = [];
$(document).ready(function () {
    var UUID = guid();

    var basename = false
    var numFiles = 0;
    var uploadIteration = -1;
    var selected = [];

    var toolMergePdf = {
        el: false,
        upload_in_progress: false,
        init: function (element) {
            this.el = element;
            this.bind();
        },
        bind: function () {
            $(document).on('click', '.convert_docs_wrapper .tool-page-block', function (e) {
                e.preventDefault();

                $(this).toggleClass('selected');
            });

            $(document).on('click', '.split_selected_pages', function (e) {
                e.preventDefault();

                $('.tool-page-block.selected').each(function( index ) {
                    let uploadIteration = parseInt($(this).data('uploadIteration'));
                    $('.tool-page-block[data-upload-iteration="'+uploadIteration+'"]').removeClass('hidden').removeClass('selected').removeClass('tool-page-main').addClass('tool-page-slave');
                });
            });
            
            $(document).on('click', '.remove_selected_pages', function (e) {
                e.preventDefault();

                $('.tool-page-block.selected').each(function( index ) {
                    $(this).find('.delete-page').click();
                });
            });

            $('#merge-pdf').on('click', '.delete-page', function (e) {
                e.preventDefault();

                // delete selected[selected.indexOf(parseInt($(this).data('page')))];

                let $button = $(this);
                let page = parseInt($button.data('page'));
                let uploadIteration = parseInt($button.data('uploadIteration'));

                if($button.closest('.tool-page-block').hasClass('tool-page-main')){
                    delete selected[uploadIteration];
                    $('.tool-page-block[data-upload-iteration="'+uploadIteration+'"]').remove();
                }else{
                    delete selected[uploadIteration][page - 1];
                    $(this).closest(".tool-page-block").remove();
                }
            });

        	$(document).on("change", "input[name='onlySpecificRanges']", function(){
        		var el = $(this),
        			doc_items = $(".pdf-file-item-content");
        		if(el.is(":checked")){
        			doc_items.find(".ranges").removeClass("hidden");
        		}else{
        			doc_items.find(".ranges").addClass("hidden");
        		}
        	});
        
            this.el.on("change", this.fileSelected);

            $(document).on('click', '.remove-file', function () {
                $(this).closest(".pdf-file-item-wrap").remove();

                $(".pdf-file-item-wrap").each(function () {
                    var currentIndex = $(this).index() + 1;
                    $(this).find(".order").text(currentIndex);
                });
            });

            $(document).on('click', '.sort-btn', function () {
                let sortDescending = $(this).data('sort-order') == 'desc' ? true : false;
                toolMergePdf.sortUnorderedList(sortDescending);

                return false;
            });

            $(document).on('click', '.merge-pdf-save', function () {
                $('html').addClass('loading');

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

                    if (!toolMergePdf.upload_in_progress) {
                        clearInterval(intervalID);

                        let docs = [];
                        // let basename = false;
                        let ranges = {};

                        $('#merge-pdf li').each(function (index, value) {
                            if (!basename){
                                basename = $(this).data('basename').substring(0, $(this).data('basename').lastIndexOf("."));
                            }
                            docs.push($(this).data('num'));
                            if($("[name='onlySpecificRanges']").is(":checked")){
                            	var rr = $(value).find(".doc_ranges").val() || 'all';
                            }else{
                            	var rr = "all";
                            }
                            nn = $(this).data("num");
                            ranges[nn] = (rr);
                        });

						console.log("ranges: ", ranges);


                        $.ajax({
                            type: 'POST',
                            url: '/tool/merge-pdf',
                            data: {
                            	formFields: $("[name='formFields']:checked").val(),
                            	outline: $("[name='outline']:checked").val(),
                            	firstInputCoverTitle: $("input[name='firstInputCoverTitle']").is(":checked")?1:0,
                            	normalizePageSizes: $("input[name='normalizePageSizes']").is(":checked")?1:0,
                            	tableOfContents: $("[name='tableOfContents']:checked").val(),
                            	ranges: ranges,
                            	filenameFooter: $("input[name='filenameFooter']").is(":checked") ?1 : 0,
                            	blankPageIfOdd: $("input[name='blankPageIfOdd']").is(":checked") ? 1: 0,
                                _token: $('input[name="_token"]').val(),
                                UUID: UUID,
                                docs: docs,
                                basename: basename,
                                file_names: file_names,
                                page: selected,
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

//                                    $(document).on("click", "#rename_file", function (e) {
//                                        e.preventDefault();
//                                        var new_file_name = false;
//                                        if (new_file_name = prompt("Rename to", data.filename)) {
//                                            $(".download-result-link").attr("download", new_file_name);
//                                            $(".download-result-link .download_file_name").html(new_file_name);
//                                        }
//                                    });

                                    $('.download_pdf').attr('href', data.file).attr('download', data.filename);
                                    $('main').removeClass('step2').addClass('step3');
                                    $('html').removeClass('loading');

                                    $(document).trigger("after_save_file", [{
                                        file_name: data.filename,
                                        url: data.file
                                    }]);

                                    PDFTOOLS.taskComplete({
                                        success: true,
                                        url: data.file,
                                        new_file_name: data.filename
                                    });
                                } else {
                                    $('html').removeClass('loading');
                                    $("#apply-popup").removeClass("active");
                                    swal('Error', data.message, 'error');
                                }
                            }
                        });
                    }
                }, 250);
            });
        },
        fileSelected: function (e, file_obj = false) {
            $('html').addClass('loading');
            if (file_obj) {
                var that = toolMergePdf,
                    file = (file_obj),
                    file_ext = file_obj.name.split(".").pop().toLowerCase();
                this.files = [file_obj];
                files = [];
                files.push(file_obj);
	            file_names.push(file_obj.name);
            } else {
                var that = toolMergePdf,
                    files = this.files;
            }

            let accept_ext = ['pdf', 'jpg', 'png', 'jpeg', 'tiff', 'tif', 'gif', 'bmp'];
            let accept = true;

            let old_numFiles = numFiles;

            basename = this.files[0].name.substring(0, this.files[0].name.lastIndexOf("."));

            pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

            $.each(files, function (i, item) {
	            file_names.push(item.name);

                if (accept_ext.indexOf(this.name.split(".").pop().toLowerCase()) == -1)
                    accept = false;

                numFiles++;

                that.getBlob(item).then((data) => {
                    //var pdfjsLib = window['pdfjs-dist/build/pdf'];

                    var loadingTask = pdfjsLib.getDocument({data: data});
                    $(document).trigger("pdf_loading_task", [loadingTask]);
                    loadingTask.promise.catch(function (e) {
                        console.log("wow doge", e);
                    });
                });
            });

            if (!accept) {
                $('html').removeClass('loading');
                numFiles = old_numFiles;
                swal("Error", "Please select PDF or image files.", "error");
            } else {
                that.uploadFile(files);
                uploadIteration++;

                /*
                $.each(files, function (i, item) {
                    $('#merge-pdf').append('<li  class="pdf-file-item-wrap ui-sortable-handle" data-num="' + $('#merge-pdf .pdf-file-item-wrap').length + '" data-basename="' + this.name + '">' +
                        '<div style="flex-wrap: wrap;" class="pdf-file-item-content">' +
                        '<span class="filename"><i class="far fa-file"></i>' + this.name + '</span>' +
                        '<span class="order">' + ($('#merge-pdf .pdf-file-item-wrap').length + 1) + '</span>' +
                        '<span class="remove-file"><i class="fas fa-times"></i></span>' +
                        
                        `<div style="width: 100%;" class='ranges hidden'><input autocomplete="off"  class='doc_ranges' placeholder="Example: 1-3,4,6-10" type='text'></div>`+
                        '</div>' +
                        '</li>');
                });
                */
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

                                if(currentPage == 1){
                                    var filename = basename + file_ext;
                                }else{
                                    var filename = basename + '-' + currentPage + file_ext;
                                }

                                let $tmp_canvas = $(
                                    `<div class="convert_doc right_doc tool-page-block ${currentPage == 1 ? 'tool-page-main' : 'tool-page-slave hidden'}" data-upload-iteration="` + uploadIteration + `" data-page="` + currentPage + `">` +
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

                                $('#merge-pdf').append($tmp_canvas);

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
                        }, function (reason) {
                            // PDF loading error
                            console.error(reason);
                        });
                    });
                });

                /*
                $('.app-welcome').hide(200);
                setTimeout(function () {
                    $('.merge-pdf').show(200);
                    $('.fixed-bottom-panel').show(200);
                }, 200);
                */
                $('main').addClass('file_uploaded').removeClass('file_not_loaded').removeClass('step1').removeClass('step3').addClass('step2');

                $(".upload-file-tool").val('');

                $('html').removeClass('loading');
            }

            $('#upload-merge').on("change", this.fileSelected);
        },
        sortUnorderedList: function (sortDescending) {
            var vals = [];

            $('#merge-pdf li').each(function (i, elem) {
                vals.push($(this).data('basename'));
            });

            vals.sort(function (a, b) {
                return a.toLowerCase().localeCompare(b.toLowerCase());
            });

            if (sortDescending)
                vals.reverse();

            $.each(vals, function (index, value) {
                $('#merge-pdf li[data-basename="' + value + '"]').appendTo($('#merge-pdf'));
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

            let num_files = numFiles;

            $.each(files, function (i, file) {
                formData.append('files[]', file);
                currentUploads.changeStatus(file.name, true);
                num_files--;
            });

            if (num_files == 0) {
                setTimeout(function () {
                    $("html, body").animate({scrollTop: $('.merge-pdf').offset().top - 150}, "medium");
                }, 500)
            }

            formData.append("UUID", UUID);
            formData.append("numFiles", num_files);
            formData.append("path", 'merge-pdf');
            formData.append("_token", $('input[name="_token"]').val());

            toolMergePdf.upload_in_progress = true;
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
                        $('#merge-pdf li[data-basename="' + file.name + '"]').remove();
                    }
                    $(".current_file_status").html("Your task is processing");

                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });
                    toolMergePdf.upload_in_progress = false;
                },
                error: function (data) {
                    swal('Error', "Cant upload file..", 'error');
                    $.each(files, function (i, file) {
                        currentUploads.changeStatus(file.name, false);
                    });
                    toolMergePdf.upload_in_progress = false;
                }
            });
        }
    };


    A_TOOL = toolMergePdf;

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

    toolMergePdf.init($(".upload-file-tool"));

    /*
    $(".pdf-files-list-sortable").sortable({
        stop: function () {
            $(".pdf-file-item-wrap").each(function () {
                var currentIndex = $(this).index() + 1;
                $(this).find(".order").text(currentIndex);
            });
        }
    });

    $(".pdf-files-list-sortable").disableSelection();
    */

    $(".more-options-btn").click(function (e) {
        e.preventDefault();
        $(".fixed-task-form-hidden").slideToggle(200);
    });

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
});
