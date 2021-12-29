var A_TOOL = false;
var ALLOW_FILE_EXT = "application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
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

            let allowedExtensions = ['xls', 'xlsx'];

            if (!allowedExtensions.includes(file_ext)) {
                swal("Error", "Please select XLS or XLSX file.", "error");
            } else {
                Array.from(files).forEach(file => {
                    that.getBase64(file).then(function (base64) {
                        expansion = file.name.substring(file.name.lastIndexOf('.'));
                        let filename = basename + '.' + file.name.split(".").pop().toLowerCase();
                        let $tmp_canvas = $(
                            '<div class="convert_doc right_doc image-canvas-li" data-number="' + number + '">' +
                            '<div class="convert_doc_content">' +
                            '<div class="download_convert_doc">' +
                            '<img src="https://www.iconfinder.com/data/icons/file-formats-7/502/Untitled-26-512.png" alt="">' +
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

                                // Convert Excel to PDF file
                                $.ajax({
                                    type: 'POST',
                                    url: '/excel-to-pdf',
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
            formData.append("path", 'excel-to-pdf');
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


//$(".hint-block").remove()
//detectTables(1)

//var pt_to_mm =  0.3527777777778;

// window.show_anyway = true;
// window.skip_extract = true;
//
// var ExcelPDF = {
//     name: "ExcelPDF",
//     need_preview: true,
//     tool_section: $(".excel_pdf"),
//     preview_section: false,
//     csfr: false,
//     pages_list: false,
//     pages_ranges_blocs: false,
//     page_preview_width: false,
//     preview_block: false,
//     selectable_pages: false,
//     pages_ranges: false,
//     toolurl: "/tool/pdf-to-excel",
//     random_colors: [],
//     data: { tables: {} },
//     fill_range: false,
//     show_only_first_page: true,
//     override_scale: 1.5,
//     need_scrool: false,
//     init: function(){
//         this.bind();
//     },
//     first_page_rendered: false,
//     bind: function(){
//         $(".download-result-link").css("background-image", "url('/img/ext/xls.svg')");
//         $(".page-between").addClass("hidden");
//         $(".page-side-bar").hide();
//         $(".page-side-bar").addClass("hidden");
//         $(".delete-page").addClass("hidden");
//
//         $(document).on("click", "#save_pdf", $.proxy(this.save, this));
//
//         $(document).on("click", "#save_csv", $.proxy(this.save, this));
//
//         $(document).on("textlayerrendered", function(page){
//             if(!ExcelPDF.first_page_rendered){
//                 ExcelPDF.first_page_rendered = true;
//                 $(".before_upload").addClass("hidden");
//                 $(".after_upload").removeClass("hidden");
//             }
//             detectTables($(page.target));
//         });
//
//
//
// //		$(document).on("pdftool_file_selected", function(ev, file){
//
// //			pdfUploader.getBlob(file).then((data) => {
// //				//var pdfjsLib = window['pdfjs-dist/build/pdf'];
// //				pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
//
// //				var loadingTask = pdfjsLib.getDocument({data: data});
// //				$(document).trigger("pdf_loading_task", [loadingTask]);
// //				loadingTask.promise.catch(function(e){
// //					console.log("wow doge", e);
// //				});
// //			});
// //
// //
// //		});
//
//
//     },
//     save: function(e){
//         e.preventDefault();
//         var $this = this;
//         var type = 'xls';
//         if($(e.target).hasClass("csv")){
//             type = "csv";
//         }
//
//
//
//
//         PDFTOOLS.startTask()
//         var intervalID = setInterval( function() {
//             console.log("upload progress is "+spe.upload_in_progress);
//             if(!spe.upload_in_progress){
//                 clearInterval(intervalID);
//
//                 $this.ajax({uuid: UUID,
//
//                     numPages: spe.pdfDocument.pdfInfo.numPages,
//
//                     UUID: UUID,  type: type, tables: PDFTOOLS.data.tables,
//                     file_name: $this.file.name,
//                     basename: $this.file.name.split(".")[0]
//                 }).then($this.taskComplete);
//
//             }
//         } , 250);
//
//
//
//         return false;
//     },
// };
//
//
// if(ExcelPDF.tool_section.length>0){
//     ExcelPDF = $.extend(PDFTOOLS, ExcelPDF);
//     ExcelPDF.main();
// }
//
//
// function Column(t) {
//     this.texts = Array.isArray(t) ? t : [t];
//     this.updateDimensions = function() {
//         this.top = Math.min(...this.texts.map(function(t) { return t.top }))
//         this.left = Math.min(...this.texts.map(function(t) { return t.left }))
//         this.right = Math.max(...this.texts.map(function(t) { return t.right }))
//         this.bottom = Math.max(...this.texts.map(function(t) { return t.bottom }))
//     }
//
//     this.updateDimensions()
//
//     this.add = function(t) {
//         this.texts.push(t)
//         this.updateDimensions()
//     }
//
//     this.toString = function() {
//         var texts = this.texts.map(function(t){ return t.text }).join(' ')
//         return [this.top, this.left, this.bottom, this.right, texts].toString()
//     }
//
//     this.mergeWith = function(other) {
//         var mergedTexts = this.texts.concat(other.texts)
//         return new Column(mergedTexts);
//     }
//     return this;
// }
//
//
//
// function MultiLineBlock(l) {
//     this.lines = [l]
//     this.columns = []
//
//     this.addLine = function(l) {
//         this.lines.push(l)
//     }
//
//     this.top = function() {
//         return Math.min(...this.columns.map(function(c) { return c.top }))
//     }
//
//     this.left = function() {
//         return Math.min(...this.columns.map(function(c) { return c.left }))
//     }
//     this.right = function(){
//         return Math.max(...this.columns.map(function(c) { return c.right }))
//     }
//     this.bottom = function() {
//         return Math.max(...this.columns.map(function(c) { return c.bottom }))
//     }
//
//     this.getBlockSize = ()=>{
//         $.each(this.lines, (i, v)=>{
//             this.lines[i].right = Math.max(...this.lines[i].texts.map(function(t) { return t.left+t.width }));
//             this.lines[i].left = Math.min(...this.lines[i].texts.map(function(t) { return t.left }));
//             this.lines[i].top = Math.min(...this.lines[i].texts.map(function(t) { return t.top }));
//             this.lines[i].bottom = Math.max(...this.lines[i].texts.map(function(t) { return t.bottom }));
//         });
//
//         this.left = Math.min(...this.lines.map(function(t) { return t.left }));
//         this.right = Math.max(...this.lines.map(function(t) { return t.right; }));
//         this.top = Math.min(...this.lines.map(function(t) { return t.top; }));
//         this.bottom = Math.max(...this.lines.map(function(t) { return t.bottom; }));;
//         return {left: this.left, right: this.right, top: this.top, bottom: this.bottom};
//     }
//
//     this.toString = function() {
//         return '[\n' + this.lines.join('\n') + '\n(' + this.top() +', '+ this.left() +')(' + this.bottom() +', '+ this.right() +')]'
//     }
//     this.findColumn = function(t) {
//         return this.columns.find(function(c){
//             var noOverlap = t.right < c.left || t.left > c.right
//             if(!noOverlap) {
//                 return c
//             }
//         })
//     }
//
//     this.addColumn = function(c) {
//         this.columns.push(c)
//     }
//     var self = this
//     this.decomposeColumns = function() {
//         var it = 0;
//         this.lines.forEach(function(line) {
//             line.texts.forEach(function(t) {
//                 var col = self.findColumn(t)
//                 if(!col) {
//                     var new_c = new Column(t);
//                     self.addColumn(new_c)
//                 } else {
//                     col.add(t)
//                 }
//             });
//             it++;
//         })
//         this.columns = this.columns.filter(function(c){
//             var existsAnotherThatIncludesC = self.columns.find(function(other){
//                 var same = other.left == c.left && other.right == c.right
//                 var otherIncludesC = other.left <= c.left && other.right >= c.right
//                 return !same && otherIncludesC
//             })
//             return !existsAnotherThatIncludesC
//         });
//
//         this.columns.sort(function(a, b){
//             return a.left - b.left
//         })
//
//         var mergedColumns = [];
//         var threshold = 5;
//
//         for(var i = 0; i < this.columns.length; i++) {
//             var c = this.columns[i];
//             var nextColumn = this.columns[i + 1];
//             if(nextColumn) {
//             }
//             if(nextColumn && (nextColumn.left - c.right < threshold)) {
//                 mergedColumns.push(c.mergeWith(nextColumn));
//                 i += 1
//             } else {
//                 mergedColumns.push(c);
//             }
//         }
//
//         if(mergedColumns.length != this.columns.length) {
//             this.columns = mergedColumns;
//         }
//     }
//
//     return this;
// }
//
//
//
// function Line(t) {
//     this.texts = [t]
//
//     this.left = Math.min(...this.texts.map(function(t) {  return t.left }));
//     this.right =  Math.max(...this.texts.map(function(t) { return t.left+t.width }));
//     this.top = Math.min(...this.texts.map(function(t) {  return t.top }))
//     this.bottom = Math.max(...this.texts.map(function(t) { return t.top+t.height; }))
//
//
//     this.getMaxBottom = ()=>{
//         return Math.max(...this.texts.map(function(t){
//             return t.bottom;
//         }));
//     }
//
//     this.getMinTop = ()=>{
//         return Math.min(...this.texts.map(function(t){
//             return t.top;
//         }));
//     }
//
//     this.add = function(t) { this.texts.push(t) }
//     this.toString = function() { return this.texts.map(function(t) { return t.text }).join(',') }
//     this.toStringEx = function() { return '[top:'+ this.top +', bottom:'+ this.bottom +', texts:[' + this.texts.map(function(t) { return t.text }).join(',') + ']]' }
//     this.multiline = function() { return this.texts.length > 1 }
//     this.sort = ()=>{
//         this.texts = this.texts.sort(function(a, b){
//             return a.left - b.left
//         })
//     }
//
//     return this;
// }
//
//
// function detectTables($textLayer) {
//     var $pageWrap = $textLayer.closest(".page");
//     var pageNum = $pageWrap.data("page-number");
//     var texts = [];
//     texts = $textLayer.find('div:not(.eba_div)').map(function(i, elem){
//         var $this = $(elem)
//         var position = $this.position()
//         var height = $this.height()
//         var width = $this.width()
//
//         return new Text({
//             text: $this.text(),
//             node: $this,
//             top: position.top,
//             bottom: position.top + height,
//             left: position.left,
//             right: position.left + width,
//             height: height,
//             width: width,
//         })
//     }).toArray();
//
//     console.log("test is ", texts);
//
//     if(texts.length==0 || texts.length==1){
//         $(".fixed-bottom-panel").addClass("hidden");
//         swal("Error", "We're sorry, image-only scanned documents are not supported.", "error");
//         return false;
//     }
//
//
//     texts = texts.filter(function(t){
//         return t.text != '';
//     });
//
//     texts = texts.sort(function(a, b) {
//         return a.top - b.top
//     })
//
//     if(texts.length == 0) return;
//
//     var lines = []
//     var perci = 0;
//     texts.forEach(function(t) {
//         var last = lines[lines.length - 1]
//         t.top = parseInt(t.top.toFixed(0));
//         //TODO костыль для некоторых блоков. если в них текст налазит или еще чего.
//         t.bottom = parseInt(t.bottom.toFixed(0))-3;
//         if(last){
//             last.bottom = parseInt(last.getMaxBottom().toFixed(1));
//             last.top = parseInt(last.getMinTop().toFixed(1));
//             //console.log(t.text, " last.top ", last.top, "t.top", t.top, " last.bottom", last.bottom);
//         }
//         if(last && ((last.top <= t.top+perci && t.top <= last.bottom) || (last.top <= t.bottom+perci && t.bottom <= last.bottom+perci)   ) ) {
//             last.add(t)
//         } else {
//             nl = new Line(t);
//             lines.push(nl)
//         }
//     });
//
//     //TODO mb uncomment
// //    lines.forEach(function(l){
// //        l.sort()
// //    })
//
//
//
//     function round3(x) {
//         return Math.ceil(x/3)*3;
//     }
//
//     function mode(array) {
//         if (array.length == 0)
//             return null;
//         var modeMap = {};
//         var maxEl = array[0], maxCount = 1;
//         for (var i = 0; i < array.length; i++) {
//             var el = array[i];
//             if (modeMap[el] == null)
//                 modeMap[el] = 1;
//             else
//                 modeMap[el]++;
//             if (modeMap[el] > maxCount) {
//                 maxEl = el;
//                 maxCount = modeMap[el];
//             }
//         }
//         return maxEl;
//     }
//
//
//     function distanceOf(i) {
//         var l = lines[i]
//         var prev = lines[i-1]
//         if(l && prev) {
//             return Math.round(l.top - prev.bottom)
//         }
//     }
//
//     var dist = []
//     lines.forEach(function(l, i){
//         var d = distanceOf(i)
//         // console.log(d, "on line ",i, l)
//         if(d) dist.push(d)
//     })
//
//     dist = dist.sort(function(a, b){
//         return a - b
//     });
//
//     var variance = 20;
//     if(dist.length > 0) {
//         var std = math.std(...dist),
//             mean = math.mean(...dist);
//         variance = mean + std / 2;
//     }
//
//     var blocks = []
//     var multiMode = false;
//
//     function areFollowingSingleLines(start, upTo) {
//         if(start + upTo >= lines.length) return false
//         var i = 0
//         while(i < upTo) {
//             var l = lines[start + i]
//             if(l.multiline()) return false
//             i++
//         }
//         return true
//     }
//
//
//     for(var i=0; i< lines.length; i++){
//         var line = lines[i]
//         var d = distanceOf(i)
//         var seemsBlockBreak = d && d > variance;
//         //console.log("line is", d, "is variance", variance);
//
//         if(!line.multiline() ){
//             continue;
//         }
//
//         if(seemsBlockBreak || blocks.length==0){
//             block = new MultiLineBlock(line);
//             blocks.push(block)
//         }else{
//             var mlb = blocks[blocks.length - 1]
//             mlb.addLine(line)
//         }
//         //TODO хуерга с сейджи
//         if(false){
//             if(line.multiline()) {
//                 if(seemsBlockBreak) {
//                     multiMode = false
//                 }
//                 if(!multiMode) {
//                     block = new MultiLineBlock(line);
//                     blocks.push(block)
//                     multiMode = true
//                 } else {
//                     var mlb = blocks[blocks.length - 1]
//                     mlb.addLine(line)
//                 }
//             } else {
//                 var fewSingleBlocks = areFollowingSingleLines(i, 2)
//
//                 if(fewSingleBlocks && !seemsBlockBreak) {
//                     i++
//                 } else {
//                     multiMode = false
//                 }
//             }
//         }
//     }
//
//     var table_iterator = 0;
//     blocks.forEach(function(b) {
//         b.getBlockSize();
//         b.decomposeColumns()
//
//         var bHeight = b.bottom-b.top;
//         var bWidth = b.right-b.left; //-left_is;
//
//         var padding = 7
//         var scale = window.current_fw_scale;
//
//         var $hint = $('<div class="hint-block"></div>').css({
//             top: b.top - padding, left: b.left - padding,
//             width: bWidth + 2 * padding, height: bHeight + 2 * padding
//         });
//
//
//         if(typeof ExcelPDF.data.tables[pageNum] == 'undefined'){
//             ExcelPDF.data.tables[pageNum] = {};
//         }
//         ExcelPDF.data.tables[pageNum][table_iterator] = {
//             top: parseInt(px2mm(b.top-padding)/pt_to_mm),
//             left: parseInt(px2mm(b.left)/pt_to_mm),
//             width: parseInt(px2mm(bWidth + 2 * padding)/pt_to_mm),
//             height: parseInt(px2mm(bHeight + 2 * padding)/pt_to_mm)
//         };
//
//
//         var $hintInner = $('<div class="hint-block-inner"></div>')
//
//         $hint.append($hintInner)
//         $pageWrap.append($hint)
//
//         $textLayer.css({ opacity: 1 })
//         $pageWrap.find('canvas').css({ opacity: '0.3' })
//
//         var bPos = $hint.position()
//         var height = $hint.height()
//
//         var table = {
//             rows: [], columns: [], pageNum: pageNum
//         }
//         if(true){
//             b.columns.forEach(function(c, i) {
//                 // text in tables should be visible
//                 c.texts.forEach(function(t) {
//                     //t.node.css({ color: 'red' });
//                     // t.node.remove()
//                 })
//
//                 var top = padding, left = c.left - bPos.left, width = (c.right - c.left);
//                 var $column = $('<div class="hint-block-column"></div>').css({
//                     top: top, left: left,
//                     width: width, height: height,
//                     position: "absolute",
//                 });
//
//                 $hintInner.append($column)
//
//                 var column = {
//                     left: c.left/scale,
//                     top: b.top/scale,
//                     width: width/scale,
//                     height: bHeight/scale,
//                 }
//                 table.columns.push(column)
//             })
//         }
//
//
//         // draw rows
//         if(true){
//             var cels = { };
//
//             var ri = -1;
//             b.lines.forEach(function(l){
//                 ri++;
//                 var top = l.top - bPos.top
//                 var width = $hint.width()
//                 var height = (l.bottom - l.top)
//
//                 var cel_n = -1;
//                 if(typeof cels[ri]=='undefined'){ cels[ri] = {}; }
//
//
//                 l.texts.forEach(function(t) {
//                     cel_n++;
//                     //console.log(t);
//                     cels[ri][cel_n] = t.text;
//                     //t.node.css({ color: 'red' });
//                     // t.node.remove()
//                 });
//
//                 var $row = $('<div  class="hint-block-row"></div>').css({
//                     top: top,
//                     left: 0,
//                     width: width,
//                     height: height,
//                     position: "absolute",
//
//                 })
//                 $hintInner.append($row)
//
//                 var row = {
//                     x :  px2mm(b.left-padding)/pt_to_mm, //new Big(b.left.div(scale).round().toFixed(),
//                     y : px2mm(l.top-padding)/pt_to_mm, //new Big(l.top).div(scale).round().toFixed(),
//                     w: px2mm(bWidth+padding)/pt_to_mm, //new Big(bWidth).div(scale).round().toFixed(),
//                     h: px2mm(height+padding)/pt_to_mm //new Big(height).div(scale).round().toFixed()
//                 }
//                 table.rows.push(row)
//             })
//         }
//
//         table_iterator++;
//         ExcelPDF.data.cels = cels;
//         ExcelPDF.data.table= table;
//
//         //console.log("table is ", table);
//     })
//
//
// }
//
//
// function Text(data) {
//     $.extend(this, data)
//     return this;
// }
//
//
//
// function px2mm(px, scale=false){
//     if(scale){
//
//     }else{
//         scale = typeof spe.pdfViewer.currentScale=='undefined'?1:spe.pdfViewer.currentScale;
//     }
//     return px / pixel_ratio / scale;
// }



