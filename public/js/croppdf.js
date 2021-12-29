window.show_anyway = true;
window.skip_extract = 1;
var PDF_PASSWORD = "";
var CropPDF = {
	name: "CropPDF",
	need_preview: false,
	tool_section: $("#crop_section"),
	preview_section: false,
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-crop",
	hide_before_upload: true,
	show_only_first_page: false,	
	data: { pages: {} },
	load_blob: true,
	file_data: false,
	page_preview_width: 1110,
	one_canvas_for_all_pages: false,
	
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	
	toinch: function(v=0){
		return v/PDFTOOLS.bm*0.013888888888889;
	},
	bind: function(){
	
		$(document).on("click", "#start_task", $.proxy(this.save, this));
	
		$(document).on("before_page_render", function(ev, viewport, unscaled_viewport, pn, page, canvas){
			if(!$(canvas).hasClass("jcrop_init")){
				$(canvas).Jcrop({
					onSelect: function(c){
						if(window.manual_input){
							return false;
						}
					
						var selected ={
							x: ((c.x/PDFTOOLS.bm*0.013888888888889)),
							y: ((c.y/PDFTOOLS.bm*0.013888888888889)),
							w: ((c.w/PDFTOOLS.bm*0.013888888888889)),
							h: ((c.h/PDFTOOLS.bm*0.013888888888889))
						};
						var page_num = $(canvas).data("page-id");
						if(typeof CropPDF.data.pages[page_num]=='undefined'){
							CropPDF.data.pages[page_num] = {};
						}
						
						CropPDF.data.pages[page_num]['rotation'] = $(canvas).data("rotate");
												
						var cv = $(canvas).width();
						var ch = $(canvas).height();
						var right = ((cv-c.x-c.w)/PDFTOOLS.bm)*0.013888888888889;
						var bottom = ((ch-c.y-c.h)/PDFTOOLS.bm)*0.013888888888889;
						var ef = $(canvas).closest(".page_outer").find(".crop-edit-form");
						
						$("input[name='left']",ef).val(selected.x);
						$("input[name='top']",ef).val(selected.y);
						$("input[name='right']",ef).val(right);
						$("input[name='bottom']",ef).val(bottom);

						CropPDF.data.pages[page_num]["left"] = selected.x;
						CropPDF.data.pages[page_num]["top"] = selected.y;
						CropPDF.data.pages[page_num]["right"] = right;
						CropPDF.data.pages[page_num]["bottom"] = bottom;
					}
				});
			}
		});
	
		$(document).on("click", ".edit-choose-block", $.proxy(this.beforePreview, this));
		$(document).on("input", ".crop-edit-form", $.proxy(this.changeCrop, this));
		$(document).on("click", ".auto_crop", $.proxy(this.autoCrop, this));
		$(document).on("click", "#crop_pdf", $.proxy(this.save, this));
	},
	save: function(e){
		var $this = this;

		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, pages: $this.data.pages, file_name: $this.file.name, for_all_pages: $this.one_canvas_for_all_pages}).then($this.taskComplete);

			}
		} , 250);	
	
		
		return false;
	},

	fileSelected: function(prom, file){
		$this = CropPDF;
		$this.file_name = file.name;
		$this.file = file;
		var params = {filename: $this.file.name, size: $this.file.size, fileData: $this.file_data};		
		
	    pdfUploader.getBlob(file).then((data) => {
	    	var params = {filename: file.name, size: file.size, fileData: data};
	    	
	    	$this.preview(params);
			$(".r_upload_section").hide();
			$("#rotate_section").removeClass("hidden");
			$("#zoom_section").removeClass("hidden");
			$(".footer-editor").addClass("active");
	    	
	    });

	},
	
	
	autoCrop: function(e){
		var $this = this,
			button = $(e.target), 
			page_block = button.closest(".page_outer"),
			inputs = $(".crop-edit-form input", page_block),
			crop_preview = $(".crop_border", page_block),
			page_num = page_block.data("pagenum"),
			canvas = $("canvas", page_block)[0];
			
		var margins = ($this.removeBlanks(canvas));
		$.each(inputs, function(i, inp){
			var v = margins['inch'][$(inp).attr("name")];
			$(inp).val(v);
			if(typeof $this.data.pages[page_num]=='undefined'){
				$this.data.pages[page_num] = {};
			}
			$this.data.pages[page_num][$(inp).attr("name")] = v;
		});
		$this.data.pages[page_num]['rotation'] = $(canvas).attr("rotation");

		var jcrop = {};
		$.each(margins.px, function(side, val){
		
			jcrop[side] = val; 
			crop_preview.css(`border-${side}`, `${val}px solid #e8e8e8`);
		});
		
		$this.setJcrop(canvas, jcrop);
		
	},
	removeBlanks: function(canvas){
		var context = canvas.getContext("2d");
			imgWidth = canvas.width, imgHeight =canvas.height;
		var imageData = context.getImageData(0, 0, canvas.width, canvas.height),
			data = imageData.data,
			getRBG = function(x, y){
				return {
					red: data[(imgWidth * y + x) * 4],
					green: data[(imgWidth * y + x) * 4 + 1],
					blue: data[(imgWidth * y + x) * 4 + 2]
				};
			},
			isWhite = function(rgb) {
				return rgb.red == 255 && rgb.green == 255 && rgb.blue == 255;
			},
			scanY = function(fromTop) {
				var offset = fromTop ? 1 : -1;
				for (var y = fromTop ? 0 : imgHeight - 1; fromTop ? (y < imgHeight) : (y > -1); y += offset) {
					for (var x = 0; x < imgWidth; x++) {
						if (!isWhite(getRBG(x, y))) {
							return y;
						}
					}
				}
				return null; // all white
			},
			scanX = function(fromLeft) {
				var offset = fromLeft ? 1 : -1;
				for (var x = fromLeft ? 0 : imgWidth - 1; fromLeft ? (x < imgWidth) : (x > -1); x += offset) {
					for (var y = 0; y < imgHeight; y++) {
						if (!isWhite(getRBG(x, y))) {
							return x;
						}
					}
				}
				return null; // all white
			};

		var top = scanY(true),
			bottom =  (canvas.height-scanY(false)),
			left = scanX(true),
			right = (canvas.width-scanX(false));
		
		return { 
			inch : {
				top: top/25.4*pt_to_mm/PDFTOOLS.bm,
				bottom: bottom/25.4*pt_to_mm/PDFTOOLS.bm,
				left: left/25.4*pt_to_mm/PDFTOOLS.bm,
				right: right/25.4*pt_to_mm/PDFTOOLS.bm
			},
			px: {
				top: top,
				bottom: bottom,
				left: left,
				right: right
			}
		}
	},

	changeCrop: function(e){
		var $this = this,
			input = $(e.target),
			val = input.val(),
			page_block = input.closest(".page_outer"),
			canvas = $("canvas", page_block),
			crop_preview = $(".crop_border", page_block),
			page_num = page_block.data("pagenum"),
			croper = canvas;

		$this.data.pages[page_num] = {
			top: $("input[name='top']", page_block).val(),
			right: $("input[name='right']", page_block).val(),
			bottom: $("input[name='bottom']", page_block).val(),
			left: $("input[name='left']", page_block).val(),
			rotation: canvas.attr("rotation")
		};
		
		var nb = {},
			jcrop = {};
		
		
		$.each($this.data.pages[page_num], function(side, value){
			if(!value){
				value = 0;
			}
			var tl = Math.ceil(parseFloat(value)*25.4/pt_to_mm*$this.bm);
			
			if(side=='left' || side=='right'){
				if(tl>canvas.width()){
					tl = canvas.width();
				}
			}
			if(side == 'top' || side=='bottom'){
				if(tl>canvas.height()){
					tl = canvas.height();
				}
			}
			
			nb[`border-${side}`] = `${tl}`;
			jcrop[side] = tl; 
			crop_preview.css(`border-${side}`, `${tl}px solid #e8e8e8`);
		});
		
		$this.setJcrop(canvas, jcrop);
		
		console.log(nb);
	},
	setJcrop: function(canvas, sides){
		var temp = [],
			cv = $(canvas).width();
		
		$.each(sides, function(side, val){
			switch(side){
				case 'top':
					temp[1] = val; side['top'];
				break;
				case 'left':
					temp[0] = val; //side['left'];
				break;
				case 'bottom':
					temp[3] = $(canvas).height()-val;
				break;
				case 'right':
					temp[2] = $(canvas).width()-val;
				break;
			}
		});
		window.manual_input = true;
		$.when($(canvas).Jcrop({setSelect: temp  })).then(function(){
			window.manual_input = false;
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
		var params = {filename: $this.file.name, size: $this.file.size, fileData: $this.file_data};
		switch(editor_type){
			case 'all':
				$this.one_canvas_for_all_pages = true;
				$("#preview_section").removeClass("hidden");
				$this.preview(params);
			break;
			default:
				$this.one_canvas_for_all_pages = false;
				$("#preview_section").removeClass("hidden");
				$this.preview(params);
			break;
		}
		
	},
//	startPreview: function(){
//		
//		var $this = this;
//		$this.pages_ranges_block.removeClass("hidden");
//		pdfUploader.getBlob($this.file).then((data)=>{
//			var params = {filename: $this.file.name, size: $this.file.size, fileData: data};
//			$(".footer-editor").addClass("active");
//			$this.tool_section.removeClass("hidden");
//			$this.preview_section.removeClass("hidden");
//			$this.preview(params);
//		});
//	},
	getPagePreviewTemplate: function(params){
		return `
		    <div class="crop-edit-block page_outer" style='positon: relative;' data-pagenum="${params['page_num']}">
				<div class="crop-edit-photo">
					<div class="crop_border"></div>
					<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
				</div>


			    <div class="crop-edit-top fixed-crop" readonly>
			        <div class="crop-edit-form">
			            <label>
			                <span>Top</span>
			                <input readonly type="text" name="top" value="0" placeholder="0" />
			            </label>
			            <label>
			                <span>Right</span>
			                <input readonly  type="text" name="right" value="0" placeholder="0" />
			            </label>
			            <label>
			                <span>Bottom</span>
			                <input readonly type="text" name="bottom" value="0" placeholder="0" />
			            </label>
			            <label>
			                <span>Left</span>
			                <input readonly type="text" name="left" value="0" placeholder="0" />
			            </label>
			            <span>(inch)</span>
			        </div>
			        <div class="crop-edit-auto">
			            <button class="options-btn auto_crop" type="submit">Auto-crop</button>
			        </div>
			    </div>


		    </div>


		</div>
		`;
	},
	
}
if(CropPDF.tool_section.length>0){
	converterTool = CropPDF = $.extend(PDFTOOLS, CropPDF);
	CropPDF.main();
}

