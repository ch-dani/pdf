'use strict';

window.show_anyway = true;
window.skip_extract = true;
var PDF_PASSWORD = '';
PDFTOOLS.name = "rotatepdf";

var RotatePDF = {
    canvas_list: {},
    pages_selector: "all",
    pages_rotate: 0,
    pages_param: {},
    original_width: 261,
    init: function () {
        window.show_anyway = true;

        if (!pdfjsLib.getDocument || !pdfjsViewer.PDFViewer) {
            alert('PDF LIB NOT FOUND');
            return false;
        }

        $(document).on("file_selected", this.fileSelected);
        $(document).on("click", "#start_task", this.saveFile);
        $(document).on("click", "#rotate_section button.rotate_page", this.rotatePage);
        $(document).on("click", ".buttons_block_1 button", this.allPageRotate);
        // $(document).on("input", "#change_zoom", this.changeZoom);
    },
    saveFile: function () {
        var $this = RotatePDF;
        $("#apply-popup").addClass("active");
        PDFTOOLS.startTask();

        var intervalID = setInterval(function () {
            console.log("upload progress is " + spe.upload_in_progress);
            if (!spe.upload_in_progress) {
                clearInterval(intervalID);

                $.ajax({
                    method: "POST",
                    url: "/pdf-rotate",
                    data: {pages: RotatePDF.pages_param, UUID: UUID},
                    headers: {
                        'X-CSRF-TOKEN': $("#editor_csrf").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data.success) {
                            $('.module__how-convert').hide();

                            const link = document.createElement('a');
                            link.href = '/' + data.url;
                            link.setAttribute('download', data.new_file_name);
                            document.body.appendChild(link);
                            link.click();

                            PDFTOOLS.taskComplete({
                                success: true,
                                url: '/' + data.url,
                                new_file_name: data.new_file_name
                            });
                        }
                    },
                    error: function (error) {
                        swal.fire('Error', '', 'error');
                    }
                });
            }
        }, 250);

        return false;
    },
    fileSelected: function (prom, file) {
        RotatePDF.file_name = file.name;
        pdfUploader.getBlob(file).then((data) => {
            var params = {filename: file.name, size: file.size, fileData: data};

            RotatePDF.preview(params);
            $(".r_upload_section").hide();
            $("#rotate_section").removeClass("hidden");
            // $("#zoom_section").removeClass("hidden");
            // $(".footer-editor").addClass("active");
        });
    },
//     changeZoom: function (e) {
//         var val = $(this).val();
//         var new_width = RotatePDF.original_width * val / 100;
//         $("#pages_here canvas").css("width", new_width + "px");
//     },
    allPageRotate: function (e) {
        var block = $(this).closest(".buttons_block_1"),
            button = $(this),
            val = button.data("val"),
            type = block.data("type"),
            $this = RotatePDF;

        $("button", block).removeClass("active");
        button.addClass("active");

        switch (type) {
            case 'pages_rotate':
                $this.pages_rotate = val;
                break;
            case 'pages_selector':
                $this.pages_selector = val;
                break;
            default:
                return false;
                break;

        }
        var ps = "";
        switch ($this.pages_selector) {
            default:
            case 'all':
                ps = $("#pages_here canvas");
                break;
            case 'even':
                ps = $("#pages_here canvas:odd");
                break;
            case 'odd':
                ps = $("#pages_here canvas:even");
                break;
        }

        $("#pages_here canvas").data("rotate", 0).css("transform", "rotate(0deg)");
        $.each($this.pages_param, function (i, v) {
            $this.pages_param[i] = 0;
        });

        $.each(ps, function (i, v) {
            $this.pages_param[$(v).data("page-id")] = $this.pages_rotate;
            $(v).data("rotate", $this.pages_rotate).css("transform", `rotate(${$this.pages_rotate}deg)`);
        });
    },
    rotatePage: function (e) {
        var element = $(this).closest("li"),
            canvas = $("canvas", element),
            $this = RotatePDF,
            v = parseInt($(this).data("rotate")),
            current_rotate = parseInt(canvas.data("rotate")),
            new_rotate = current_rotate + v;
        let cr = 0;
        cr = parseInt(canvas.data("current-rotation"));

        if (new_rotate == 360) {
            new_rotate = 0;
        }
        if (new_rotate == -360) {
            new_rotate = 0;
        }

        if (new_rotate < 0) {
            var real_rotate = new_rotate - cr;
        } else {
            var real_rotate = new_rotate + cr;
        }
        if (real_rotate == 360) {
            real_rotate = 0;
        }
        if (real_rotate == -360) {
            real_rotate = 0;
        }

        $this.pages_param[$(canvas).data("page-id")] = real_rotate;

        canvas.data("rotate", new_rotate);
        canvas.css("transform", `rotate(${new_rotate}deg)`);
    },
    preview: function (params) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
        var $this = this,
            CMAP_URL = '/libs/pdfjs-dist/cmaps/',
            CMAP_PACKED = true,
            pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null;

        var params = {
            data: params.fileData,
            password: PDF_PASSWORD,
            cMapUrl: CMAP_URL,
            cMapPacked: CMAP_PACKED,
        };

        var loadingTask = pdfjsLib.getDocument(params)
        //$(document).trigger("pdf_loading_task", [loadingTask]);

        loadingTask.promise.then(function (pdfDoc_) {
            pdfDoc = pdfDoc_;
            blocker.hide();

            for (let i = 1; i != pdfDoc.numPages + 1; i++) {
                pageNum = i;
                $this.pages_param[pageNum] = 0;
                var li_content = `
					<div class='page_number'>${i}</div>
					<canvas data-rotate="0" data-page-id="${i}" id="page_canvas_${i}"></canvas>
					<div class='rotate_buttons'>
						<button class='rotate_page' data-rotate='-90'>
							<i class="fa fa-undo"></i>
						</button>
						<button class='rotate_page' data-rotate='+90'>
							<i class="fa fa-repeat"></i>
						</button>
					</div>
					`;

                $("#pages_here").append(`<li class='r_page_item page_${i}'>${li_content}</li>`);
                var tcanvas = $(`#page_canvas_${i}`)[0];

                $this.canvas_list[pageNum] = tcanvas;

                pdfDoc.getPage(pageNum).then(function (page) {
                    var pn = page.pageIndex + 1,
                        canvas = $this.canvas_list[pn],
                        ctx = canvas.getContext('2d');

                    var unscaled_viewport = page.getViewport({
                        scale: 1
                    });

                    var bm = 261 / unscaled_viewport.width;
                    var viewport = page.getViewport({
                        scale: bm
                    });
                    $(canvas).data("current-rotation", viewport.rotation);
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    var renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    var renderTask = page.render(renderContext);
                });
            }
        });

        return false;
    }
}

if ($("#rotate_section").length > 0) {
    var converterTool;
    converterTool = RotatePDF = $.extend(PDFTOOLS, RotatePDF);
    RotatePDF.init();
}
