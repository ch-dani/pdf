window.show_anyway = false;
window.skip_extract = 1;
PDF_PASSWORD = '';

converterTool = PDF2EPUB = {
    name: "PDF2EPUB",
    need_preview: true,
    tool_section: $(".epub_section"),
    preview_block: false, //$(".pages_preview_block"),
    preview_section: false,
    // pages_list: $("#pages_previews_here_2"),
    pages_list: $("#pages-pdf"),
    csfr: false,
    selectable_pages: true,
    toolurl: "/pdf-to-epub",
    data: {},
    current_letter: 1,
    document_iterator: 0,
    letter: "A",
    load_blob: true,
    file_data: false,
    page_preview_width: 1065,
    one_canvas_for_all_pages: false,
    multiple_upload: "A",
    hide_before_upload: true,
    page_preview_items_selector: false,
    show_only_first_page: false,
    dont_clean_pages_list: true,
    fix_canvas_id: true,
    replace_preview_wraper: true,

    init: function () {
        window.show_anyway = true;
        this.bind();
    },

    bind: function () {
        $(document).on("click", "#start_task", $.proxy(this.save, this));

        $(document).on("click", ".save-image-page", (e) => {
            e.preventDefault();

            var document_id = $(e.currentTarget).data("document-id");
            var page_id = $(e.currentTarget).data("page");

            this.saveOnePage(e, document_id, page_id);
        });

        $(document).on("pdftool_file_selected", function (ev, file) {
            pdfUploader.getBlob(file).then((data) => {
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

    saveOnePage: function (e, di, pi) {
        var $this = this;

        //PDFTOOLS.startTask()
        //startTask
        var intervalID = setInterval(function () {
            console.log("upload progress is " + spe.upload_in_progress);
            if (!spe.upload_in_progress) {
                clearInterval(intervalID);
                start_task_popup = false;
                $this.ajax({
                    uuid: UUID,
                    document: di,
                    page: pi,
                    single_page_extractor: 1,
                    type: $(".output-btn-active").data("val"),
                    lang: $(".lang_select").val(), file_name: $this.file.name
                }).then((data) => {
                    start_task_popup = undefined;

                    // window.open(data.url);
                    let link = document.createElement('a');
                    link.setAttribute('href', data.file);
                    link.setAttribute('download', data.filename);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                });
            }
        }, 250);

        return false;
    },

    save: function (e) {
        // $this = PDFTOOLS;
        var $this = this;

        PDFTOOLS.startTask();
        var intervalID = setInterval(function () {
            console.log("upload progress is " + spe.upload_in_progress);
            if (!spe.upload_in_progress) {
                clearInterval(intervalID);
                $this.ajax({uuid: UUID, file_name: $this.file.name}).then($this.taskComplete);
            }
        }, 250);

        return false;
    },

    fileSelected: function (prom, file) {
        PDF2EPUB.file_name = file.name;
        $this = PDF2EPUB;
        $this.file = file;
        var params = {filename: $this.file.name, size: $this.file.size, fileData: $this.file_data};

        pdfUploader.getBlob(file).then((data) => {
            var params = {filename: file.name, size: file.size, fileData: data};

            PDF2EPUB.preview(params);
            $(".r_upload_section").hide();
            $("#rotate_section").removeClass("hidden");
            $("#zoom_section").removeClass("hidden");
            $(".footer-editor").addClass("active");

            $this.multiple_upload = String.fromCharCode($this.multiple_upload.charCodeAt(0) + 1);
            ;
            $this.letter = $this.multiple_upload;
        });
    },

    renderPageCanvas: function (page, $this) { //TODO рендерим канвас страницы preview -> renderPagesBlocks -> renderPageCanvas
        // console.log($this.canvas_list);
        // console.log('page', page);
        var pn = page.pageIndex + 1;

        var canvas = $(`#page_canvas_${$this.document_id - 1}_${pn}`)[0];

        if (typeof canvas == 'undefined') {
            console.error("Error: canvas is undefined, check selectors in tool setings");
            return false;
        }

        var ctx = canvas.getContext('2d'),
            unscaled_viewport = page.getViewport({scale: 1}),
            bm = $this.page_preview_width / unscaled_viewport.width,
            viewport = page.getViewport({scale: bm});

//		console.log("unscaled_viewport ", unscaled_viewport);
//		console.log("viewport ", viewport);

        $this.bm = bm;

        canvas.setAttribute("pt-width", unscaled_viewport.width * pt_to_mm);
        canvas.setAttribute("pt-height", unscaled_viewport.height * pt_to_mm);

        canvas.height = viewport.height;
        canvas.width = viewport.width;

        $(document).trigger("after_set_canvas_size", [pn, viewport.width, viewport.height, canvas]);

        $(canvas).attr("rotation", viewport.rotation);

        if ($this.one_canvas_for_all_pages && pn != 1) {
            var renderContext = {
                canvasContext: ctx,
                background: 'rgba(0,0,0,0)',
                viewport: viewport
            };
        } else {
            var renderContext = {
                canvasContext: ctx,
                //background: 'rgba(0,0,0,0)',
                viewport: viewport
            };
        }

        if ($this.one_canvas_for_all_pages && pn != 1) {
            return false;
        }

        $(document).trigger("before_page_render", [viewport, unscaled_viewport, pn, page, canvas]);

        var renderTask = page.render(renderContext);
    },

    getPagePreviewTemplate: function (params) {
        let uploadIteration = '';
        let currentPage = '';
        let filename = '';

        let tmp_canvas = $(
            '<div class="convert_doc right_doc">' +
            '<div class="convert_doc_content">' +
            '<div class="download_convert_doc">' +
            '<canvas  id="page_canvas_' + PDFTOOLS.document_id + '_' + params.page_num + '" xxx="canvas-page-' + uploadIteration + '-' + currentPage + '"></canvas>' +
            '</div>' +
            '</div>' +
            '<div class="download_icon_doc">' +
            '<a class="save-image-page" href="#" data-document-id=' + PDFTOOLS.document_id + ' data-upload-iteration="' + uploadIteration + '" data-page="' + params.page_num + '">' +
            '<img src="freeconvert/img/download_arrow.svg">' +
            '</a>' +
            '</div>' +
            '<div class="name_doc">' +
            '<h6>' + filename + '</h6>' +
            '</div>' +
            '</div>'
        );
        return tmp_canvas;
    },
}

if (PDF2EPUB.tool_section.length > 0) {
    window.skip_extract = 1;
    PDF2EPUB = $.extend(PDFTOOLS, PDF2EPUB);
    PDF2EPUB.main();
}