window.skip_extract = true;
window.show_anyway = true;
var SplitByTextPDF = {
	name: "SplitByTextPDF",
	need_preview: true,
	tool_section: $("#split_by_text"),
	preview_section: $(".pages_preview_here"),
	csfr: false,
	pages_list: $(".pages_preview_here"),
	pages_ranges_blocs: false,
	page_preview_width: Math.max(800, screen.width*0.4),
	preview_block: $(".pages_preview_here"),
	selectable_pages: false,
	toolurl: "/pdf-split-by-text",
	random_colors: [],
	pages_ranges: false,
	data: {selected: {x: 0, y: 0, w: 0, h: 0}},
	fill_range: false,
	show_only_first_page: true,
	current_page: 1,
	total_pages: 1,
	init: function(){
		this.bind();
	},
	bind: function(){
		var $this = this;
		
		$(document).on("click", "#save", $.proxy(this.save, this));
		
		$(document).on("click", ".next_prev", function(e){
			e.preventDefault();
			var tar = $(this).data("type");
			if($(this).hasClass("disabled")){
				return false;
			}
			
			switch(tar){
				case 'next':
					$this.current_page++;
				break;
				default: 
					$this.current_page--;
				break;
			}

			if($this.current_page<=1){
				$(".prev-page-btn").addClass("disabled");
			}else{
				$(".prev-page-btn").removeClass("disabled");			
			}
			
			if($this.current_page==$this.total_pages){
				$(".next-page-btn").addClass("disabled");
				$this.current_page = $this.total_pages;
			}else{
				$(".next-page-btn").removeClass("disabled");
			}
			$this.renderPage($this.current_page);
		
		});
		
		$(document).on("file_selected", ()=>{ $(".after_upload").removeClass("hidden"); $(".before_upload").addClass("hidden"); });
		$(document).on("click", "#start_task", $.proxy($this.save, this));
	},


	preview: function(params){
		var $this = this,
			CMAP_URL = '/libs/pdfjs-dist/cmaps/',
			CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
		var params = {
	        data: params.fileData,
	        //url: DEFAULT_URL,
	        cMapUrl: CMAP_URL,
	        cMapPacked: CMAP_PACKED,
	        password: PDF_PASSWORD
		};
        $(document).trigger("before_start_preview_pages", [params]);
		
		var loadingTask = pdfjsLib.getDocument(params);

///		$(document).trigger("pdf_loading_task", [loadingTask]);
		
		return loadingTask.promise.then(function(pdfDoc_){ 
			$(document).trigger("before_render_pages_blocks", [pdfDoc_]);
			PDFTOOLS.pages_list.html("");
			$this.renderPagesBlocks(pdfDoc_, $this) 
		}).catch(function(err){
			alert(err.message);
		});
	},

	renderPage: function(pn){
		var $this = this,
			pdfDoc = window.pdfDoc_temp;
		//$this.pages_list.html("");
		//$this.pages_list.append($this.getPagePreviewTemplate({"page_num": pn}));
		pdfDoc.getPage(pn).then(function(page){ $this.renderPageCanvas(page, $this) });
	},

	renderPageCanvas: function(page, $this){ 
		var pn = page.pageIndex+1, 
			canvas = $this.canvas_list[1];
			
		if(typeof canvas=='undefined'){
			alert("Error: canvas is undefined, check selectors in tool setings");
			return false;
		}
		
		var ctx = canvas.getContext('2d'),
			unscaled_viewport = page.getViewport({ scale: 1 }),
			bm = $this.page_preview_width/unscaled_viewport.width,
			viewport = page.getViewport({ scale: bm });
			
		console.log("unscaled_viewport ", unscaled_viewport);
		console.log("viewport ", viewport);
		$this.bm = bm;
		
		canvas.setAttribute("pt-width", unscaled_viewport.width*pt_to_mm);
		canvas.setAttribute("pt-height", unscaled_viewport.height*pt_to_mm);
		
		canvas.height = viewport.height;
		canvas.width = viewport.width;
		
		$(document).trigger("after_set_canvas_size", [pn, viewport.width, viewport.height, canvas]);
		
		$(canvas).attr("rotation", viewport.rotation);
		
		if($this.one_canvas_for_all_pages && pn != 1){
			var renderContext = {
				canvasContext: ctx,
				background: 'rgba(0,0,0,0)',
				viewport: viewport
			};		
		}else{
			var renderContext = {
				canvasContext: ctx,
				//background: 'rgba(0,0,0,0)',
				viewport: viewport
			};
		}
		
        $(document).trigger("before_page_render", [viewport, unscaled_viewport, pn, page]);
		
		$('.preview_page_block').Jcrop({
			onSelect: function(c) {
				PDFTOOLS.data.selected ={
//					x: (Math.round(c.x/PDFTOOLS.bm*pt_to_mm)),
//					y: (Math.round(c.y/PDFTOOLS.bm*pt_to_mm)),
//					w: (Math.round(c.w/PDFTOOLS.bm*pt_to_mm)),
//					h: (Math.round(c.h/PDFTOOLS.bm*pt_to_mm))

					x: (Math.round(c.x/PDFTOOLS.bm)),
					y: (Math.round(c.y/PDFTOOLS.bm)),
					w: (Math.round(c.w/PDFTOOLS.bm)),
					h: (Math.round(c.h/PDFTOOLS.bm))
					
				};
			}
		
		});
		
		$(".next_prev").addClass("loading_button");
		
		var renderTask = page.render(renderContext);
		
		renderTask.then(function(){
			$(".next_prev").removeClass("loading_button");
		});
		
	}, 


	renderPagesBlocks: function(pdfDoc, $this){ 
		var $this= this;
		blocker.hide();
		console.log("start render page blocks");
		if($this.pages_list.length==0){
			console.error("Page list block not found, {$this.pages_list}");
			return false;
		}
		
		$this.pages_list.html("");
		
		$("#dummy_page").remove();
		var flag = false;
		window.pdfDoc_temp = pdfDoc;
		$this.total_pages = pdfDoc.numPages;
		
		var i = $this.current_page;
		
		$this.pages_list.append($this.getPagePreviewTemplate({"page_num": i}));
		var tcanvas = $(`#page_canvas_${i}`)[0];
		
		$this.canvas_list[i] = tcanvas;
		pdfDoc.getPage(i).then(function(page){ 
		window.pdf_page = page; 
		
		$this.renderPageCanvas(page, $this) });
	
	},

	getPagePreviewTemplate(params){
		return `
			<div class="preview_page_block page_${params['page_num']}" data-page-id='${params['page_num']}'>
				<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
			</div>
		`;
	},
	save: function(e){
		e.preventDefault();
		var $this = this;



		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, text_start_from: $("#text_start_from").val()||"", filename_pattern: $("#file_name_pattern").val()||"[BASENAME]-[CURRENTPAGE]", data: $this.data, page: $this.current_page, file_name: $this.file.name}).then($this.taskComplete);
			}
		} , 250);	



		return false;
	},
	
	
}
if(SplitByTextPDF.tool_section.length>0){
	SplitByTextPDF = $.extend(PDFTOOLS, SplitByTextPDF);
	SplitByTextPDF.main();
}

