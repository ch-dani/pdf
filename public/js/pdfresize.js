window.show_anyway = true;
window.skip_extract = 0;
var PDF_PASSWORD =  "";
var ResizePDF = {
	name: "ResizePDF",
	need_preview: false,
	tool_section: $("#resize_section"),
	preview_section: false,
	csfr: false,
	maxPages: 2,
	selectable_pages: true,
	toolurl: "/pdf-resize",
	data: { pages: {} },
	load_blob: true,
	file_data: false,
	page_preview_width: 616,
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	page_preview_items_selector: ".resize-margin-block",
	unitType: "inch",
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	bind: function(){
		//$(document).on("click", ".edit-choose-block", $.proxy(this.beforePreview, this));
		
		$(document).on("click", ".unit__btn", function(e){
			e.preventDefault();
			var type = $(this).data("type");
			$(".unit__unit").html(type);
			ResizePDF.unitType = type;
			ResizePDF.inputMargins(e);
		})
		
		$(document).on("click", "#crop_pdf", $.proxy(this.save, this));
		$(document).on("before_page_render", $.proxy(this.drawPagesSizes, this));
		$(document).on("input", ".page_margins input", $.proxy(this.inputMargins, this));
		$(document).on("click", "#start_task", $.proxy(this.save, this));
		
		$(document).on("on_preview_page_click", function(ev, page, active){
			PDFTOOLS.inputMargins(ev);
		});
		
		$(document).on("change", "#papper_size", function(){
			if($(this).val()=='custom_size'){
				$(".custom_paper_size").removeClass("hidden");
				$(".new_page_size_here").html($(".custom_paper_width").val()+'"'+" x "+$(".custom_paper_height").val()+'"');

			}else{
				$(".custom_paper_size").addClass("hidden");
				$(".new_page_size_here").html($("option:selected", this).html());
			}
			$(".page_size_here").addClass("overline");
		});
		
		$(document).on("input", ".custom_paper_width, .custom_paper_height", function(){
			$(".new_page_size_here").html(parseFloat($(".custom_paper_width").val()).toFixed(2)+'"'+" x "+parseFloat($(".custom_paper_height").val()).toFixed(2)+'"');
		});
		
	},
	save: function(e){
		var $this = this;
		
		var new_paper_size = $("#papper_size").val();
		if(new_paper_size=='custom_size'){
			new_paper_size = $(".custom_paper_width").val()+"in,"+$(".custom_paper_height").val()+"in";
		}

		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, resize_type: $this.data.resize_type, new_paper_size: new_paper_size, pages: $this.data.pages, file_name: $this.file.name }).then($this.taskComplete);
			}
		} , 250);	

		return false;
	},
	
	inputMargins: function(e){
		var $this = this,
			element = $(e.target),
			new_page_size = {},
			new_page_size_plain = {},
			has_selected_pages = false;
			
		if($(".selected_page").length>0){
			has_selected_pages = true;
		}
		
		$(".page_margins input").each(function(i, v){
			let val = parseFloat($(v).val());
			
			switch(ResizePDF.unitType){
				case 'pt':
					new_page_size["margin-"+$(v).attr("name")] = Math.ceil((parseFloat(val)*25.4/pt_to_mm*$this.bm)/72)+"px";
					new_page_size_plain[$(v).attr("name")] = parseFloat(val);					
				break;
				case 'em':
					new_page_size["margin-"+$(v).attr("name")] = va;;;//Math.ceil((parseFloat(val)*25.4/pt_to_mm*$this.bm)/6.022500060225001)+"px";
					new_page_size_plain[$(v).attr("name")] = val;//parseFloat(val/6.022500060225001);					
				break;
				case 'px':
					new_page_size["margin-"+$(v).attr("name")] = parseFloat(val)+"px";
					new_page_size_plain[$(v).attr("name")] = parseFloat(val); //TODO					
				break;
				case 'inch':
					new_page_size["margin-"+$(v).attr("name")] = Math.ceil(parseFloat(val)*25.4/pt_to_mm*$this.bm)+"px";
					new_page_size_plain[$(v).attr("name")] = parseFloat(val*72);
				break;
			}

		});
		
		var last_i = 1;
		$.each($this.data.pages, function(i, v){
			if(has_selected_pages){
				if($(`#page_preview_${v.page_num}`).hasClass("selected_page")){
					$this.data.pages[i]['new'] = new_page_size_plain;
				}else{
					$this.data.pages[i]['new'] = {"top":0, "left": 0, "bottom": 0, "right": 0};
				}
			}else{
				$this.data.pages[i]['new'] = new_page_size_plain;
			}
			last_i = i;
		});
		
		let new_inch_w = $this.data.pages[last_i].original.width+new_page_size_plain['left']+new_page_size_plain['right'];
		let new_inch_h = $this.data.pages[last_i].original.height+new_page_size_plain['top']+new_page_size_plain['bottom'];
		
		$(".page_size_here").removeClass("overline");
		$(".canvas_padding_outer canvas").css({"margin-top": 0, "margin-left":0, "margin-bottom": 0, "margin-right": 0});
		$(".new_page_size_here").html("");
		if(has_selected_pages){
			$(".selected_page .new_page_size_here").html(`${new_inch_w/72}" x ${new_inch_h/72}"`);
			$(".selected_page .page_size_here").addClass("overline");
			$(".selected_page .canvas_padding_outer canvas").css(new_page_size)
		}else{
			$(".new_page_size_here").html(`${new_inch_w/72}" x ${new_inch_h/72}"`);
			$(".page_size_here").addClass("overline");
			$(".canvas_padding_outer canvas").css(new_page_size);
		}
		return false;
	},
	//2.8346
	drawPagesSizes: function(event, viewport, unscaled_viewport, pn, page){
		var page_block = $(`#page_preview_${pn}`),
			$this = this;
			
		$this.data.pages[pn] = {page_num: pn, rotation: 0, original: {width: viewport.viewBox[2], height: viewport.viewBox[3]}, 
			new: {
				width: viewport.viewBox[2], 
				height: viewport.viewBox[3],
				left: 0,
				right: 0,
				width: 0,
				top: 0,
				bottom: 0,
			}
		};
		
		var x = $this.pt2inch(viewport.viewBox[2]).toFixed(2);
		var y = $this.pt2inch(viewport.viewBox[3]).toFixed(2);
		$(".page_size_here", page_block).html(`${x}" x ${y}"`);
		
		return false;
	},


	fileSelected: function(prom, file){
		ResizePDF.file_name = file.name;
		$this = ResizePDF;
		$this.file = file;
		var params = {filename: $this.file.name, size: $this.file.size, fileData: $this.file_data};		
		
	    pdfUploader.getBlob(file).then((data) => {
	    	var params = {filename: file.name, size: file.size, fileData: data};
	    	
	    	ResizePDF.preview(params);
			$(".r_upload_section").hide();
			$("#rotate_section").removeClass("hidden");
			$("#zoom_section").removeClass("hidden");
			$(".footer-editor").addClass("active");
	    	
	    });

	},
	
	
	
	beforePreview: function(e){
		e.preventDefault();
		var $this = this,
			element = $(e.currentTarget),
			editor_type = $(element).data("type");
		
		$("#start_crop").removeClass("hidden");
		$(".select_crop_type").addClass("hidden");
		//TODO забор файла лучше переделать на промис
		$("#page_size_editor").removeClass("hidden");
		
		var params = {filename: $this.file.name, size: $this.file.size, fileData: $this.file_data};
		$(".edit-choose-container").addClass("hidden");
		
		switch(editor_type){
			case 'margins':
				$this.data.resize_type = "margins";
				$this.one_canvas_for_all_pages = false;
				$("#preview_section").removeClass("hidden");
				$("#page_size_editor .page_margins").removeClass("hidden");
				$this.preview(params);
			break;
			default:
				$this.data.resize_type = "paper";
				$this.one_canvas_for_all_pages = false;
				$("#page_size_editor .page_size").removeClass("hidden");
				$("#preview_section").removeClass("hidden");
				$this.preview(params);
			break;
		}
		
	},

	inch2pt: function(){
	
	},
	pt2inch: function(pt){
		return parseFloat(pt)/72;
	},
	getPagePreviewTemplate: function(params){
	
		return `
			<div id="page_preview_${params['page_num']}" class="crop-section__page" data-pagenum="${params['page_num']}">
				<div class="split-main-num"></div>
				<div class="split-main-photo">
					<div class="canvas_padding_outer">
						<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
					</div>
				</div>
			</div>
		`;
	},
	
}
if(ResizePDF.tool_section.length>0){
	converterTool = ResizePDF = $.extend(PDFTOOLS, ResizePDF);
	ResizePDF.main();
}

