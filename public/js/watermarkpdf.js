var signaturePad = false;
window.show_anyway = true;
window.skip_extract = 0;
PDF_PASSWORD = '';
var current_page = 1;
var WatermarkPDF = {
	name: "WatermarkPDF",
	need_preview: true,
	tool_section: $("#watermark_section"),
	preview_block: $("#preview_block"),
	preview_section: $("#preview_section"),
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf/createPdf",
	data: { watermark: {} },
	load_blob: true,
	file_data: false,
	maxPages: 9999,
	page_preview_width: Math.max(800, screen.width*0.4),
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	page_preview_items_selector: ".resize-margin-block",
	show_only_first_page: $(".fill-sign-tool").length?false:true,
	is_fill: $(".fill-sign-tool").length,
	init: function(){
	
		window.show_anyway = true;
		this.bind();
	},
	bind: function(){
		var $this = this;
		
		$(document).on("click", ".signature_picker li a", function(){
			var color = $(this).css("background-color");
			signaturePad.penColor = color;
			$('.create-signature-modal .signature-wodrds .signaturePreview').css('color', color);
			console.log("set color");
			return false;
		});

		$(document).on("click", ".watermark edit_trigger", function(e){
			e.preventDefault();
		});

		$(document).on("click", ".watermark-color li a", function(e){
			e.preventDefault();
			let element = $(this).closest(".watermark_draggable_text");
			$(document).trigger("whistory", {
				id: element.attr("id"), 
				page: element.closest('.page_block_p').data('pagenum'), 
				type: "change_color",
				value: element.find(".text_content").css("color")
			});
			element.find(".text_content").css("color", $(this).css("background-color"));
		});

		$(document).on("file_selected", function(){
			$(".after_upload").removeClass("hidden");
			$('main').addClass('file_uploaded').removeClass('file_not_loaded').removeClass('step1').removeClass('step3').addClass('step2');
		});

		$(document).on("click", "#start_task", $.proxy($this.save, this));

		$(document).on("click", "#add_sign", (e)=>{
			e.preventDefault();
			this.addSign(e);
			console.log("addSign");
			return false;
		});
		$(document).on("click", "#add_text_wattermark", $.proxy($this.addText, this));
		$(document).on("change", "#image_upload", $.proxy($this.addImage, this));

		$(document).on("click hover", ".outer_image_div, .watermark_draggable_text", function(e){
			editor = $(".image-editable-menu")
			if($(this).hasClass("watermark_draggable_text")){
				var type = 'text';
			}else{
				var type = 'image';
			}
			$this.moveEditor($(e.target), editor, type);
		});

		$(document).on("input", ".change_opacity", function(){
			let element = $(this).closest(".watermark_draggable_text");
			//TODO fix
			var val = parseInt($(this).val())/100;
			$(document).trigger("whistory", {
				id: element.attr("id"), 
				page: element.closest('.page_block_p').data('pagenum'), 
				type: "change_opacity",
				value: element.find(".text_content_element").css("opacity")
			});

			//TODO
			element.find(".outer_image_div img").css("opacity", val);
			element.find(".text_content_element").css("opacity", val);
		});

		$(document).on("click", "#font_family li a", function(){
			var font_name = $(this).data("font-name"),
				element = $(this).closest(".watermark_draggable_text");
				$(".resizable_helper", element).resizable("destroy");

			let outer = element;
			$(document).trigger("whistory", {
				id: outer.attr("id"), 
				page: outer.closest('.page_block_p').data('pagenum'), 
				type: "change_font",
				value: outer.find(".text_content_element").css("font-family")
			});
			element.find(".text_content_element").css("font-family", font_name);


			setTimeout(function(){
				$(".resizable_helper", element).css("width", "auto")
				var aspect = parseInt(element.css("width"))/parseInt(element.css("height"))

				$(".resizable_helper",element).resizable({
					handles: "ne, se, nw, sw",
					resize: (event, ui) => { $this.textResize(event, ui, element)},
					aspectRatio: aspect, //6.27131132
					}
				);

				$this.moveEditor(false, $("#image_editor"), false);
			},300);

			return false;
		});


		$(document).on("click", ".center_wattermark", function(){

		});

		$(document).on("click", ".delete_wattermark", function(){
			let outer = $(this).closest(".wattermark_block");
			$(document).trigger("whistory", {
				id: outer.attr("id"), 
				page: outer.closest('.page_block_p').data('pagenum'), 
				type: "delete",
			});
			$(this).closest(".wattermark_block").addClass("hidden");
			
//			$(".watermark_draggable_text").remove();
//			$(".outer_image_div").remove();
//			$("#image_editor").addClass("hidden");
			

			PDFTOOLS.data.watermark = {};
		});

//		$(document).on("show_only_first_page_render", function(){
//			$(".canvas_outer___").append(`
//				<div class="image-editable-menu hidden" id="image_editor">
//					<div class="btn-group-wrap">

//						<div class="btn-group">
//							<button class="editable-btn">
//							<i class="fas fa-eye"></i>&nbsp;Opacity
//							<img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
//							</button>
//							<ul class="tools-dropdown-menu font-size-opts">
//								<li>
//									<input class="font-size-range change_opacity" value="25" max="75" min="5" type="range">
//								</li>
//							</ul>
//						</div>

//						<div class="btn-group" data-view='text'>
//							<button class="editable-btn">
//							<i class="fas fa-palette"></i>&nbsp;Color
//							<img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
//							</button>
//							<div class="tools-dropdown-menu ">
//							<ul class="color-opts watermark-color">
//								<li><a style="background-color: #01579B" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #0277BD" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #0288D1" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #039BE5" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #03A9F4" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #29B6F6" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #4FC3F7" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #81D4FA" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #B3E5FC" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #E1F5FE" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #1B5E20" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #2E7D32" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #388E3C" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #43A047" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #4CAF50" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #66BB6A" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #81C784" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #A5D6A7" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #C8E6C9" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #E8F5E9" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #F57F17" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #F9A825" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FBC02D" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FDD835" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFEB3B" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFEE58" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFF176" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFF59D" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFF9C4" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFFDE7" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #B71C1C" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #C62828" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #D32F2F" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #E53935" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #F44336" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #EF5350" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #E57373" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #EF9A9A" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFCDD2" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #FFEBEE" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #4A148C" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #6A1B9A" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #7B1FA2" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #8E24AA" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #9C27B0" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #AB47BC" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #BA68C8" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #CE93D8" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #E1BEE7" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #F3E5F5" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #3E2723" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #4E342E" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #5D4037" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #6D4C41" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #795548" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #8D6E63" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #A1887F" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #BCAAA4" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #D7CCC8" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #EFEBE9" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #212121" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #424242" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #616161" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #757575" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #9E9E9E" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #BDBDBD" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #E0E0E0" class="color-swatch" href="#"></a></li>
//								<li><a style="background-color: #EEEEEE" class="color-swatch" href="#"></a></li>
//							<li><a style="background-color: #FFFFFF; border-color: #CCC;" class="color-swatch" href="#"></a></li>
//							<li>
//							<a style="background-color: transparent; border-color: #CCC; position: relative;" class="color-swatch" href="#">
//							<div class="diagonal-red-line"></div>
//							</a>
//							</li>
//							</ul>
//							</div>
//						</div>


//						<div class="btn-group" data-view='text'>
//							<button class="editable-btn">
//							<i class="fas fa-eye"></i>
//							Font
//							<img class="caret" src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
//							</button>
//							<ul class="tools-dropdown-menu font-size-opts" id="font_family">
//								<li data-f="Helvetica2">
//									<a class="change_text_font" data-font-name="Helvetica2" href="#" style="font-family: 'Helvetica2'">Helvetica</a>
//								</li>
//								<li data-f="Courier">
//									<a class="change_text_font" data-font-name="Courier" href="#" style="font-family: 'Courier'">Courier</a>
//								</li>
//							</ul>
//						</div>

//						<div class="btn-group">
//							<button class="editable-btn center_wattermark" onclick='PDFTOOLS.centerWatermark(); PDFTOOLS.moveEditor(false, $("#image_editor"), false)'>
//								<i class="fas fa-arrows-alt"></i>&nbsp;Recenter
//							</button>
//						</div>

//
//						<div class="btn-group">
//							<button class="editable-btn delete delete_wattermark">
//								<i class="far fa-trash-alt"></i>
//							</button>
//						</div>
//					</div>
//				</div>
//			`)
//		});

		$(document).on("input keyup change", "#sign_text_input", this.typeTextSign);
		$(document).on("click", "#sign_previews .sign_preview", this.insertTextSign);
	},

	moveEditor: function(target, editor, type){

		var outer = $(".wattermark_block .rotatable_helper");
		var handles = [$(".ui-resizable-handle.ui-resizable-ne"), $(".ui-resizable-handle.ui-resizable-se"), $(".ui-resizable-handle.ui-resizable-sw"), $(".ui-resizable-handle.ui-resizable-nw")];

		var top = 0;
		var left = 999999;
		var bottom_h = handles[0];
		var left_h = handles[0];
		$.each(handles, function(key, handle){
			// var tt = handle.offset().top,
			// 	lt = handle.offset().left;
			var tt = $(handle).getElementOffset(".canvas_outer", false).top,
				lt = $(handle).getElementOffset(".canvas_outer", false).left;

			if(tt>top){
				top = tt;
				bottom_h = handle;
			}
			if(lt<left){
				left = lt;
				left_h = handle;
			}
		});

		var parent_offset = $(".canvas_outer").getElementOffset("body", false);

		// top = parseInt(top)-parent_offset['top']+10;
		// left = left-parent_offset['left']+20;
		top = parseInt(top)+10; //-parent_offset['top']+10;
		left = left+10; //-parent_offset['left']+20;

		if(left<0){
			left = 0;
		}

		if((left+editor.width())>$(".canvas_outer").width()){
			left = $(".canvas_outer").width()-editor.width();
		}

		if(top<0){
			top=0;
		}

		if(top+editor.height()>$('.canvas_outer').height()){
			top = $(".canvas_outer").height()-editor.height();
		}


		var css = {
			display: "block",
			position: "absolute",
			top: top,
			left: left
		}

		if($(".inserted_image").length>0){
			$("div[data-view='text']").hide();
		}else{
			$("div[data-view='text']").show();
		}


		editor.css(css).removeClass("hidden");
	},
	
	addSign: async function(ev){
		var page_num = $(ev.target).closest(".document_add_element_submenu").data("after-page") || 1;
		console.log(page_num);
		var picker = WatermarkPDF.getPicker("signature")[0].outerHTML;
		var img = false;

		var html = `
			<div class="create-signature-modal">
				<ul class="signature-btns">
					<li data-type="draw" class="signature-btn-block draw_sign active"><i class="fas fa-signature"></i>Draw</li>
					<li data-type="text" class="signature-btn-block text_sign"><i class="far fa-keyboard"></i>Type</li>
				</ul>
				<div class="create-tab-container">
					<div class="create-tab-block active">
						<canvas class='sign_canvas' width="500" height=500></canvas>
					</div>
					<div class="create-tab-block">
						<input class="signature-input" id="sign_text_input" autocomplete="off" type="text" name="name" value="John Smith">
						<div class="signature-wodrds" id="sign_previews">
							<span style="font-family: 'Gamja Flower'; font-size: 35px; display: inline-block;  " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Indie Flower'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview first" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Charmonman'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Pacifico'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Gloria Hallelujah'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Amatic SC'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Shadows Into Light'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Dancing Script'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Dokdo'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Permanent Marker'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Patrick Hand'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
							<span style="font-family: 'Courgette'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview" data-page="`+page_num+`">John Smith</span>
						</div>
					</div>
				</div>
				<div class='picker_outer signature_picker'>${picker}</div>
			</div>
		`;

		var res = await Swal.fire({
			title: '<strong></strong>',
			icon: 'info',
			html: html,
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			onOpen: function(){
				signaturePad = new SignaturePad($('.sign_canvas')[0]);
			},
			onClose: async function(){
				img = signaturePad.toDataURL();
			},
			confirmButtonText: '<i class="fa fa-check-circle"></i> Add',
			cancelButtonText: '<i class="fa fa-times-circle"></i>',
		});
		if(!res.value){
			return ;
		}
		
		var file = await fetch(img).then(res => res.blob()).then(blob => {
			return new File([blob], "sign",{ type: "image/png" })
		});
		
		WatermarkPDF.addImage(false, file, page_num);
		
		
		return false;
	
	},
	typeTextSign: function (e) {
		$("#sign_previews .sign_preview").html($(this).val());
	},
	insertTextSign: async function (e, type='text') {
		$('html').addClass('loading');

		var img = false;
		var element = type=='text' ? e.target:$("#sign_draw_canvas")[0];
		var page_num = $(e.target).data('page') || 1;

		$(element).css({"background-color": "none", "background": "none", 'padding-bottom': '20px'/*, "color": "black", "width": "200px"*/});

		html2canvas(element, {backgroundColor: null}).then(async function(canvas){
			img = canvas.toDataURL();

			var file = await fetch(img).then(res => res.blob()).then(blob => {
				return new File([blob], "sign",{ type: "image/png" })
			});

			WatermarkPDF.addImage(false, file, page_num);

			//that.imageUpload(e, canvas, "text_sign");
			Swal.close();
			$('html').removeClass('loading');
		});
	},
	addImage: function(ev, file, page_num = false){
		if(!ev){

		}else{
			var file = ev.target.files[0];
		}
        var uniq = spe.uniq(),
        	canvas = $("#page_canvas_1"),
        	$this = this;


        //opacity

		if(!page_num){
			page_num = $(ev.target).closest(".document_add_element_submenu").data("after-page") || 1;
		}

		if(!this.is_fill){
			$(".watermark_draggable_text").remove();
			$(".outer_image_div").remove();
		}

        pdfUploader.getBase64(file).then((data) => {
	        var image = $("<img>")
	        			.addClass("inserted_image")
	        			.attr({"src": data, "element-id": uniq, "uploaded": 1, "db_id":"wait_db_id"});
	        			

			$(document).trigger("whistory", {
				id: uniq, 
				page: page_num,
				type: "add_image",
				element: "image"
			});	        
	        
	        var image_helper = $("<div id='"+uniq+"' class='outer_image_div wattermark_block' style='pointer-events: auto !important;'> <div class='rotatable_helper'><div class='resizable_helper document_add_element'><a href=\"\" class=\"edit_trigger left_top\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger top\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger right_top\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger right\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger bottom_right\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger bottom\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger bottom_left\"></a>\n" +
				"\t\t\t\t\t\t<a href=\"\" class=\"edit_trigger left\"></a></div></div> </div>")
	        $(".resizable_helper", image_helper).append(image);


	        $(`#page_preview_${page_num} .canvas_outer`).append(image_helper);


	        var tmpImg = new Image() ;
			tmpImg.src = image.attr('src') ;

	        tmpImg.onload = function(){
	        	var height = tmpImg.height;
	        	var width = tmpImg.width;

	        	var max_image_width = Math.max(600, canvas.width()*0.9);
	        	var max_image_height = canvas.height()*0.8;



				if($this.is_fill){
					var height = 200/(width/height);
					var width = 200;
				}else{
		    		var width = max_image_width;
		    		var height = (height)*(tmpImg.width/max_image_width);

		    		if(height>canvas.height()){
		    			height = canvas.height()*0.8;
		    			width = width/(tmpImg.height/max_image_width)
		    		}
        		}

				var helper_css = {
					//top: top,
					//left: left,
					width: width,
					height: height,
					'position': "absolute",
				}
				var image_css = {
					top: 0,
					left: 0,
					width: width,
					height: height,
					opacity: $this.is_fill?1:0.3,
				}
				
				//element-id
				
				//drag


				$("img", image_helper).css("opacity", image_css.opacity)

				$(image_helper).css(helper_css);
				$(".resizable_helper", image_helper).css({width: width, height: height});
				image.css({width: "100%", height: "100%", left: 0, top: 0});
				
				$(".rotatable_helper", image_helper).rotatable({degrees: 0, wheelRotate: false,
					start: (event, ui)=>{
						startDrawOrRotateImage(event, ui);
						$("#image_editor").addClass("hidden");
					},
					stop: (event, ui)=> $this.rotateImage(event, ui, image_helper) });

				image_helper.draggable({
					drag: (event, ui)=>{ $this.dragImage(event, ui, image_helper)  },
					start: (event, ui)=>{
						startDrawOrRotateImage(event, ui);
						$("#image_editor").addClass("hidden");
					},					
				});

				$(".resizable_helper", image_helper).resizable({
					// handles: "ne, se, nw, sw",
					handles: "all",
					start: (event, ui)=>{
						startDrawOrRotateImage(event, ui);
					},
					resize: (event, ui) => {  $this.imageResize(event, ui, image_helper) },
					aspectRatio: width/height,
					}
				);

				$('.rotatable_helper', image_helper).append(`
					<a class="editable-btn delete_wattermark" href="">
						<svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.25 2H10.5V1.5C10.5 0.671562 9.82844 0 9 0H7C6.17156 0 5.5 0.671562 5.5 1.5V2H2.75C2.05966 2 1.5 2.55966 1.5 3.25V4.25C1.5 4.52616 1.72384 4.75 2 4.75H14C14.2762 4.75 14.5 4.52616 14.5 4.25V3.25C14.5 2.55966 13.9403 2 13.25 2ZM6.5 1.5C6.5 1.22438 6.72437 1 7 1H9C9.27563 1 9.5 1.22438 9.5 1.5V2H6.5V1.5Z" fill="#5283e5"></path>
							<path d="M2.44921 5.75C2.35999 5.75 2.2889 5.82456 2.29315 5.91369L2.70565 14.5712C2.74377 15.3725 3.4019 16 4.20377 16H11.7969C12.5988 16 13.2569 15.3725 13.295 14.5712L13.7075 5.91369C13.7118 5.82456 13.6407 5.75 13.5515 5.75H2.44921ZM10.0003 7C10.0003 6.72375 10.2241 6.5 10.5003 6.5C10.7766 6.5 11.0003 6.72375 11.0003 7V13.5C11.0003 13.7762 10.7766 14 10.5003 14C10.2241 14 10.0003 13.7762 10.0003 13.5V7ZM7.50034 7C7.50034 6.72375 7.72409 6.5 8.00034 6.5C8.27659 6.5 8.50034 6.72375 8.50034 7V13.5C8.50034 13.7762 8.27659 14 8.00034 14C7.72409 14 7.50034 13.7762 7.50034 13.5V7ZM5.00034 7C5.00034 6.72375 5.22409 6.5 5.50034 6.5C5.77659 6.5 6.00034 6.72375 6.00034 7V13.5C6.00034 13.7762 5.77659 14 5.50034 14C5.22409 14 5.00034 13.7762 5.00034 13.5V7Z" fill="#5283e5"></path>
						</svg>
					</a>`);

				if(!this.is_fill){
					PDFTOOLS.centerWatermark(image_helper);
				}
				$this.moveEditor(false, $("#image_editor"), false);
	        };
	        spe.uploadFile(file, "Image", uniq);
        });

		$('main').removeClass('step3').addClass('step2');
	},

	rotateImage: function(event, ui, image_helper){
		var $this = this;
		var element_id = $("img", image_helper).attr("element-id");
		var rot = $this.matrixToDeg(image_helper.css("transform"));
		//PDFTOOLS.moveEditor($(event.target), editor, "image");

		$('main').removeClass('step3').addClass('step2');
	},
	dragImage: function(event, ui, element){
		var element_id = $("img", element).attr("element-id");
		editor = $(".image-editable-menu")
		PDFTOOLS.moveEditor($(event.target), editor, "image");

		$('main').removeClass('step3').addClass('step2');
	},
	imageResize: function(event, ui, element){
		var t = $(ui.element);
		var element_id = $("img", element).attr("element-id");
		element.css({width: t.css("width"), "height": t.css("height")});

		$('main').removeClass('step3').addClass('step2');
	},

	centerWatermark: function(watermark = false){
		if(!watermark){
			watermark = $(".wattermark_block");
		}

		var canvas = $("#page_canvas_1"),
			cw = canvas.width(),
			ch = canvas.height(),
			ww = watermark.width(),
			wh = watermark.height();

		var left = (cw/2)-(ww/2),
			top = (ch/2)-(wh/2);
		watermark.css({"top": top, "left": left});

		$('main').removeClass('step3').addClass('step2');
	},
	
	getPicker(x){
		return $(`<ul class="color-opts watermark-color">
					<li><a style="background-color: #01579B" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #0277BD" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #0288D1" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #039BE5" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #03A9F4" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #29B6F6" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #4FC3F7" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #81D4FA" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #B3E5FC" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #E1F5FE" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #1B5E20" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #2E7D32" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #388E3C" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #43A047" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #4CAF50" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #66BB6A" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #81C784" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #A5D6A7" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #C8E6C9" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #E8F5E9" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #F57F17" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #F9A825" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FBC02D" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FDD835" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFEB3B" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFEE58" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFF176" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFF59D" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFF9C4" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFFDE7" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #B71C1C" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #C62828" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #D32F2F" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #E53935" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #F44336" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #EF5350" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #E57373" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #EF9A9A" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFCDD2" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFEBEE" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #4A148C" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #6A1B9A" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #7B1FA2" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #8E24AA" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #9C27B0" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #AB47BC" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #BA68C8" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #CE93D8" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #E1BEE7" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #F3E5F5" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #3E2723" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #4E342E" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #5D4037" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #6D4C41" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #795548" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #8D6E63" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #A1887F" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #BCAAA4" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #D7CCC8" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #EFEBE9" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #212121" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #424242" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #616161" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #757575" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #9E9E9E" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #BDBDBD" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #E0E0E0" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #EEEEEE" class="color-swatch" href="#"></a></li>
					<li><a style="background-color: #FFFFFF; border-color: #CCC;" class="color-swatch" href="#"></a></li>
		</ul>`);
	},
	
	
	getElement(element_id){
		return $(`<div class="document_add_element_wrpr watermark watermark_draggable_text wattermark_block" id="${element_id}">
				<div class="document_add_element edit_state rotated rotatable_helper resizable_helper">
					<div class=''>
						<a href="" class="edit_trigger left_top"></a>
						<a href="" class="edit_trigger top"></a>
						<a href="" class="edit_trigger right_top"></a>
						<a href="" class="edit_trigger right"></a>
						<a href="" class="edit_trigger bottom_right"></a>

						<a href="" class="edit_trigger bottom"></a>

						<a href="" class="edit_trigger bottom_left"></a>

						<a href="" class="edit_trigger left"></a>

						<div class="element_inner_wrpr text_content_element text_content" contenteditable="true" style="pointer-events: all !important;">

							Click to edit

						</div>
					</div>
				</div>
				<ul class="document_add_element_menu">



				
					<li>
						<a class="editable-btn set_bold" href="">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M11.2665 7.64643C12.1328 6.99632 12.8871 5.94998 12.8871 4.27737V4.23454C12.8871 3.17137 12.5303 2.24799 11.8265 1.49C11.8232 1.48636 11.8197 1.48277 11.8162 1.47925C10.8637 0.51151 9.43846 0 7.69442 0H2.82398C2.54127 0 2.31213 0.229139 2.31213 0.511851V15.4881C2.31213 15.7709 2.54127 16 2.82398 16H7.96132C9.63251 16 11.0493 15.5897 12.0586 14.8135C13.1244 13.9937 13.6878 12.814 13.6878 11.4018V11.359C13.6878 9.68115 12.8555 8.41369 11.2665 7.64643ZM8.00238 13.4574H4.93719V9.19654H7.71495C8.83732 9.19654 9.72481 9.39849 10.2815 9.78062C10.7796 10.1225 11.0217 10.6038 11.0217 11.252V11.2948C11.0217 13.2466 8.91018 13.4574 8.00238 13.4574ZM7.42632 6.67522H4.93719V2.5427H7.55076C9.24777 2.5427 10.221 3.24513 10.221 4.46988V4.51265C10.221 6.39427 8.47004 6.67522 7.42632 6.67522Z" fill="black"/>
							</svg>
						</a>
					</li>

					<li>
						<a class="editable-btn set_italic" href="">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M14 0.571429V1.71429C14 1.86584 13.9368 2.01118 13.8243 2.11835C13.7117 2.22551 13.5591 2.28571 13.4 2.28571H11.0465L8.0465 13.7143H9.8C9.95913 13.7143 10.1117 13.7745 10.2243 13.8817C10.3368 13.9888 10.4 14.1342 10.4 14.2857V15.4286C10.4 15.5801 10.3368 15.7255 10.2243 15.8326C10.1117 15.9398 9.95913 16 9.8 16H2.6C2.44087 16 2.28826 15.9398 2.17574 15.8326C2.06321 15.7255 2 15.5801 2 15.4286V14.2857C2 14.1342 2.06321 13.9888 2.17574 13.8817C2.28826 13.7745 2.44087 13.7143 2.6 13.7143H4.9535L7.9535 2.28571H6.2C6.04087 2.28571 5.88826 2.22551 5.77574 2.11835C5.66321 2.01118 5.6 1.86584 5.6 1.71429V0.571429C5.6 0.419876 5.66321 0.274531 5.77574 0.167368C5.88826 0.0602039 6.04087 0 6.2 0H13.4C13.5591 0 13.7117 0.0602039 13.8243 0.167368C13.9368 0.274531 14 0.419876 14 0.571429Z" fill="black"/>
							</svg>
						</a>
					</li>

					<li>
						<a class="editable-btn set_underline" href="">
							<svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="">
							<path d="M6.20833 12.4583C4.73611 12.4583 3.47917 11.9375 2.4375 10.8958C1.39583 9.85417 0.875 8.59722 0.875 7.125V0.7C0.875 0.3134 1.1884 0 1.575 0H2.425C2.8116 0 3.125 0.313401 3.125 0.7V7.125C3.125 7.98611 3.42361 8.71528 4.02083 9.3125C4.61806 9.90972 5.34722 10.2083 6.20833 10.2083C7.06944 10.2083 7.80556 9.90972 8.41667 9.3125C9.02778 8.71528 9.33333 7.98611 9.33333 7.125V0.7C9.33333 0.3134 9.64673 0 10.0333 0H10.8417C11.2283 0 11.5417 0.313401 11.5417 0.7V7.125C11.5417 8.59722 11.0208 9.85417 9.97917 10.8958C8.9375 11.9375 7.68056 12.4583 6.20833 12.4583ZM0 14.7083C0 14.4322 0.223858 14.2083 0.5 14.2083H11.9583C12.2345 14.2083 12.4583 14.4322 12.4583 14.7083V15.5C12.4583 15.7761 12.2345 16 11.9583 16H0.5C0.223858 16 0 15.7761 0 15.5V14.7083Z" fill="black"/>
							</g>
							<defs>
							<clipPath id="clip0">
							<rect width="12.6667" height="16" fill="white"/>
							</clipPath>
							</defs>
							</svg>
						</a>
					</li>

				
					<li>
						<a class="editable-btn" href="">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M2.52632 2.27632H2.27632V2.52632V4.54653C2.27632 5.10606 1.82269 5.55968 1.26316 5.55968C0.703629 5.55968 0.25 5.10606 0.25 4.54653V1.26316C0.25 0.703629 0.703629 0.25 1.26316 0.25H14.7368C15.2964 0.25 15.75 0.703629 15.75 1.26316V4.54653C15.75 5.10606 15.2964 5.55968 14.7368 5.55968C14.1773 5.55968 13.7237 5.10606 13.7237 4.54653V2.52632V2.27632H13.4737H9.26316H9.01316V2.52632V13.4737V13.7237H9.26316H11.2138C11.7733 13.7237 12.227 14.1773 12.227 14.7368C12.227 15.2964 11.7733 15.75 11.2138 15.75H4.78619C4.22666 15.75 3.77303 15.2964 3.77303 14.7368C3.77303 14.1773 4.22666 13.7237 4.78619 13.7237H6.73684H6.98684V13.4737V2.52632V2.27632H6.73684H2.52632Z" fill="#0C3E70" stroke="white" stroke-width="0.5"></path>
						</svg>
						</a>
						<ul class="tools-dropdown-menu font-size-opts" id="font_family">
							<li data-f="Helvetica2">
							<a class="change_text_font" data-font-name="Helvetica2" href="#" style="font-family: 'Helvetica2'">Helvetica</a>
							</li>
							<li data-f="Courier">
							<a class="change_text_font" data-font-name="Courier" href="#" style="font-family: 'Courier'">Courier</a>
							</li>
						</ul>
					</li>
					
					<li>
						<a class="editable-btn" href="">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M7.51883 0.888672C6.24539 0.888672 4.93178 1.09025 3.6151 1.48795C3.23579 1.60236 2.08221 1.95103 1.66068 3.09238C1.2446 4.2201 1.84319 5.15273 2.28004 5.83407C2.52927 6.22291 2.81222 6.66352 2.81222 6.97031C2.81222 7.48854 2.02875 8.56384 1.51019 9.27548C0.566686 10.5707 -0.503477 12.0379 0.259903 13.5395C0.968465 14.9329 2.68828 15.1109 4.10711 15.1109C10.332 15.1109 15.9998 11.3491 15.9998 7.21853C15.9998 4.17482 12.758 0.888672 7.51883 0.888672ZM5.61821 11.614C5.60901 12.3611 5.00226 12.9648 4.25284 12.9648C3.49797 12.9648 2.88611 12.3526 2.88611 11.5977C2.88611 10.8421 3.49797 10.2299 4.25284 10.2299C4.48505 10.2299 4.70024 10.2939 4.89194 10.3954C5.323 10.6252 5.61957 11.0733 5.61957 11.596C5.61923 11.6035 5.61821 11.6093 5.61821 11.614ZM5.79186 5.13536C5.50312 4.88476 5.31687 4.5194 5.31687 4.10706C5.31687 3.35183 5.92907 2.73996 6.68394 2.73996C7.43915 2.73996 8.05101 3.35183 8.05101 4.10706C8.05101 4.17686 8.04046 4.2436 8.03024 4.31102C7.93116 4.96852 7.36935 5.47416 6.68394 5.47416C6.34107 5.47416 6.0319 5.34341 5.79186 5.13536ZM8.3864 11.9433C7.90154 11.9433 7.47797 11.6893 7.2352 11.3086C7.10036 11.0965 7.01899 10.8465 7.01899 10.5765C7.01899 9.82164 7.63153 9.20942 8.38708 9.20942C9.05955 9.20942 9.61523 9.69667 9.72895 10.3365C9.74291 10.4148 9.75278 10.4951 9.75278 10.5765C9.75313 11.3311 9.14194 11.9433 8.3864 11.9433ZM9.35884 4.50953C9.35169 4.4513 9.34113 4.3941 9.34113 4.33383C9.34113 3.70221 9.77185 3.1758 10.3541 3.01917C10.4675 2.98852 10.5849 2.96707 10.7082 2.96707C11.2445 2.96707 11.7041 3.27897 11.9278 3.72877C12.0198 3.91195 12.0749 4.11557 12.0749 4.33383C12.0749 4.8061 11.8362 5.22219 11.4719 5.46769C11.2537 5.61512 10.9911 5.70093 10.7085 5.70093C10.0133 5.70093 9.446 5.18065 9.35884 4.50953ZM11.4883 9.78691C11.2581 9.78691 11.0439 9.72425 10.8539 9.62381C10.4212 9.39397 10.1226 8.94417 10.1226 8.42015C10.1226 7.66492 10.7344 7.05237 11.4893 7.05237C11.9176 7.05237 12.2949 7.25326 12.5462 7.56107C12.7375 7.79601 12.8574 8.09293 12.8574 8.41947C12.8553 9.17469 12.2435 9.78691 11.4883 9.78691Z" fill="#0C3E70"></path>
						</svg>
						</a>
						<div class="tools-dropdown-menu ">
						<ul class="color-opts watermark-color">
						<li><a style="background-color: #01579B" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #0277BD" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #0288D1" class="color-swatch" href="#"></a></li>

						<li><a style="background-color: #039BE5" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #03A9F4" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #29B6F6" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #4FC3F7" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #81D4FA" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #B3E5FC" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #E1F5FE" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #1B5E20" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #2E7D32" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #388E3C" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #43A047" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #4CAF50" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #66BB6A" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #81C784" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #A5D6A7" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #C8E6C9" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #E8F5E9" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #F57F17" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #F9A825" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FBC02D" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FDD835" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFEB3B" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFEE58" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFF176" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFF59D" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFF9C4" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFFDE7" class="color-swatch" href="#"></a></li>

						<li><a style="background-color: #B71C1C" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #C62828" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #D32F2F" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #E53935" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #F44336" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #EF5350" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #E57373" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #EF9A9A" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFCDD2" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFEBEE" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #4A148C" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #6A1B9A" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #7B1FA2" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #8E24AA" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #9C27B0" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #AB47BC" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #BA68C8" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #CE93D8" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #E1BEE7" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #F3E5F5" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #3E2723" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #4E342E" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #5D4037" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #6D4C41" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #795548" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #8D6E63" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #A1887F" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #BCAAA4" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #D7CCC8" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #EFEBE9" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #212121" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #424242" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #616161" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #757575" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #9E9E9E" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #BDBDBD" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #E0E0E0" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #EEEEEE" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: #FFFFFF; border-color: #CCC;" class="color-swatch" href="#"></a></li>
						<li><a style="background-color: transparent; border-color: #CCC; position: relative;" class="color-swatch" href="#"><div class="diagonal-red-line"></div></a></li>
						</ul>
						</div>
					</li>
					
					<li>
						<a class="editable-btn" href="">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="">
						<path d="M15.9375 8.24689C16.0208 8.09332 16.0208 7.90757 15.9375 7.754C14.409 4.93981 11.4276 3.0293 8.00001 3.0293C4.57238 3.0293 1.59102 4.93983 0.062549 7.754C-0.0208497 7.90757 -0.0208497 8.09332 0.062549 8.24689C1.59105 11.0611 4.57233 12.9716 7.99999 12.9716C11.4276 12.9716 14.409 11.0611 15.9375 8.24689ZM7.99999 11.8137C5.894 11.8137 4.18678 10.1064 4.18678 8.00046C4.18678 5.8945 5.894 4.18723 7.99999 4.18723C10.106 4.18723 11.8132 5.89447 11.8132 8.00046C11.8132 10.1064 10.106 11.8137 7.99999 11.8137Z" fill="#0C3E70"></path>
						<path d="M7.99992 6.05664C7.8395 6.05664 7.68371 6.07622 7.53462 6.11285C7.68732 6.28958 7.77992 6.51973 7.77992 6.77162C7.77992 7.32849 7.3285 7.77994 6.77162 7.77994C6.51972 7.77994 6.28958 7.68737 6.11285 7.53463C6.07622 7.68373 6.05664 7.83952 6.05664 7.99994C6.05664 9.0732 6.92667 9.94322 7.99989 9.94322C9.07311 9.94322 9.94315 9.07318 9.94315 7.99994C9.94315 6.92671 9.07317 6.05664 7.99992 6.05664Z" fill="#0C3E70"></path>
						</g>
						<defs>
						<clipPath id="clip0">
						<rect width="16" height="16" fill="white"></rect>
						</clipPath>
						</defs>

						</svg>
						</a>
						<ul class="tools-dropdown-menu font-size-opts">
							<li>
								<input class="font-size-range change_opacity" value="50" max="100" min="5" type="range">
							</li>
						</ul>
					</li>
					<li>
						<a class="editable-btn delete_wattermark" href="">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13.25 2H10.5V1.5C10.5 0.671562 9.82844 0 9 0H7C6.17156 0 5.5 0.671562 5.5 1.5V2H2.75C2.05966 2 1.5 2.55966 1.5 3.25V4.25C1.5 4.52616 1.72384 4.75 2 4.75H14C14.2762 4.75 14.5 4.52616 14.5 4.25V3.25C14.5 2.55966 13.9403 2 13.25 2ZM6.5 1.5C6.5 1.22438 6.72437 1 7 1H9C9.27563 1 9.5 1.22438 9.5 1.5V2H6.5V1.5Z" fill="#0C3E70"></path>
						<path d="M2.44921 5.75C2.35999 5.75 2.2889 5.82456 2.29315 5.91369L2.70565 14.5712C2.74377 15.3725 3.4019 16 4.20377 16H11.7969C12.5988 16 13.2569 15.3725 13.295 14.5712L13.7075 5.91369C13.7118 5.82456 13.6407 5.75 13.5515 5.75H2.44921ZM10.0003 7C10.0003 6.72375 10.2241 6.5 10.5003 6.5C10.7766 6.5 11.0003 6.72375 11.0003 7V13.5C11.0003 13.7762 10.7766 14 10.5003 14C10.2241 14 10.0003 13.7762 10.0003 13.5V7ZM7.50034 7C7.50034 6.72375 7.72409 6.5 8.00034 6.5C8.27659 6.5 8.50034 6.72375 8.50034 7V13.5C8.50034 13.7762 8.27659 14 8.00034 14C7.72409 14 7.50034 13.7762 7.50034 13.5V7ZM5.00034 7C5.00034 6.72375 5.22409 6.5 5.50034 6.5C5.77659 6.5 6.00034 6.72375 6.00034 7V13.5C6.00034 13.7762 5.77659 14 5.50034 14C5.22409 14 5.00034 13.7762 5.00034 13.5V7Z" fill="#0C3E70"></path>
						</svg>
						</a>
					</li>
					</ul>
			</div>
		`);



	
	},
	
	addText: function(e){
		e.preventDefault();
		var page_num = $(e.target).closest(".document_add_element_submenu").data("after-page") || 1;

		var canvas = $("#page_canvas_1"),
			$this = this;

		if(!this.is_fill){
			$(".watermark_draggable_text").remove();
			$(".outer_image_div").remove();
		}

		let font_size = (this.is_fill?26:76)*PDFTOOLS.bm,
			color = "rgba(255, 0, 0)",
			opacity = this.is_fill?1:0.25,
			font = "Helvetica2, serif",
			rotate = this.is_fill?0:"-20";

		if($(e.currentTarget).closest('main').hasClass('fill-sign')){
			// font_size = 20*PDFTOOLS.bm,
			rotate = "0";
		}

		var element_id = spe.uniq();

		var element = $this.getElement(element_id);
		$(".text_content", element).css({"opacity": opacity, "font-size": font_size+"px", "font-family": font, color: color});


		if(this.is_fill){
			$(`#page_preview_${page_num} .canvas_outer`).append(element);
		}else{
			$(".canvas_outer").append(element);
		}

		//TODO rotate
        $(".rotatable_helper", element).rotatable({degrees: rotate, wheelRotate: false, 
			start: (event, ui)=>{
				startDrawOrRotate(event, ui);
			},
        	stop: (event, ui)=>{ 
        		$this.textRotate(event, ui, element) 
        	} 
        });

		//TODO drag
        element.draggable({
            drag: (event, ui)=>{ $this.dragText(event, ui, element); },
			start: (event, ui)=>{
				startDrawOrRotate(event, ui);
			},
            stop: (event, ui)=>{

            }
        });


		setTimeout(function(){
			if($this.is_fill){

			}else{
				$this.centerWatermark();
			}
			var aspect = parseInt($(element).css("width"))/parseInt($(element).css("height"));
			console.log('aspect', aspect);
		    $(".resizable_helper", element).resizable({
		    	handles: "all",
				start: (event, ui)=>{
					startDrawOrRotate(event, ui);
				},
		    	resize: (event, ui) => { $this.textResize(event, ui, element)},
		    	aspectRatio: aspect, //6.27131132
		    	}
		    );
			$(document).trigger("whistory", {id: element_id,page: page_num, type: "add_text"});
		},300);
		


		$('main').removeClass('step3').addClass('step2');
	},
	textRotate: function(event, ui, element){
		var $this = this,


		rot = $this.matrixToDeg(element.css("transform"));
		var editor = $(".image-editable-menu")
		$this.moveEditor(element, editor, "text");

		$('main').removeClass('step3').addClass('step2');
	},
	dragText: function(event, ui, element){
		var $this = this,
			element_id = element.attr("id");

		$this.moveEditor(false, $("#image_editor"), false);
		$('main').removeClass('step3').addClass('step2');
	},
	textResize: function(event, ui, element){
		console.log('element', element);
		var el = $(ui.element),
			element_id = element.attr("id");
		console.log('el', el);
		console.log('element', element);
		console.log('element_id', element_id);

		$(".text_content", element).css("font-size", (element.height()-4)+"px");
		$(".text_content", element).css("line-height", (element.height()-4)+"px");

		$('main').removeClass('step3').addClass('step2');
	},

	save: function(e){

		if(!this.is_fill){
			if($(".wattermark_block").length==0){
				swal("Error", "Please, add watermark", "error");
				return false;
			}
		}

		var $this = this;
		var data = [];
		if(this.is_fill){
			$(".page_block_p").each(function(){
				var page = $(this);
				var page_num = page.data("pagenum");

				var page_data = [];
				page.find(".wattermark_block:not(.hidden)").each(function(){

					var font_family = $(this).find(".text_content_element").css("font-family");

					if(typeof font_family!='undefined'){
						font_family= font_family.split(",")[0]
					}else{
						font_family = false;
					}

					let color = $(this).find(".text_content_element").css("color");
					let opacity = $(this).find(".text_content_element").css("opacity");
					if(typeof opacity=='undefined'){
						opacity = $(this).find("img").css("opacity");
					}
					
					let inner = $(this).find(".element_inner_wrpr");
					var bold_undefined = (typeof inner.attr("bold") =='undefined')?1:0;
					var bold = 1;
					if(bold_undefined){
						bold = 1;
					}else{
						bold = parseInt(inner.attr("bold"))?inner.attr("bold"):0
					}

					page_data.push({
						pt_size: {
							width: $(`#page_canvas_${page_num}`).attr("pt-width"),
							height: $(`#page_canvas_${page_num}`).attr("pt-height"),
						},
						//font
						//top
						bold: bold, ///(bold_undefined)?1:inner.attr("bold")?inner.attr("bold"):0,
						italic: inner.attr("italic")?inner.attr("italic"):0,
						underline: inner.attr("underline")?inner.attr("underline"):0,
						color: typeof color!='undefined'?rgb2hex(color):false,
						bm: PDFTOOLS.bm,
						width: $this.prepare(parseFloat($(this).css("width"))),
						height: $this.prepare(parseFloat($(this).css("height"))),
						font: font_family,
						"font-size": px2mm(parseFloat($(this).find(".text_content").css("font-size"))),
						"font-size-scaled": (parseFloat($(this).find(".text_content").css("font-size")))/PDFTOOLS.bm,

						top: $this.prepare( parseFloat( $(this).css("top") )),
						left: $this.prepare(parseFloat($(this).css("left"))),
						// rotate: PDFTOOLS.matrixToDeg($(this).find(".rotatable_helper").css("transform")),
						rotate: PDFTOOLS.getRotationDegrees($(".wattermark_block .rotatable_helper")),
						opacity: parseFloat(opacity),
						// text: $(this).find(".text_content_element").html(),
						text: $(this).find(".text_content_element").text(),
						image_id: $(this).find(".inserted_image").attr("db_id")
					});
				});
				data.push(page_data);
			});


			PDFTOOLS.startTask();
			var intervalID = setInterval( function() {
				console.log("upload progress is "+spe.upload_in_progress);
				if(!spe.upload_in_progress){
					clearInterval(intervalID);
					$this.ajax({uuid: UUID, fill_sign: PDFTOOLS.is_fill || 0, total_pages: $this.total_pages, data: data, file_name: $this.file.name }).then($this.taskComplete);

					$('main').removeClass('step1').removeClass('step2').addClass('step3');
				}
			}, 1000);


		}else{
			var font_family = $(".wattermark_block .text_content_element").css("font-family");

			if(typeof font_family!='undefined'){
				font_family= font_family.split(",")[0]
			}else{
				font_family = false;
			}

			var opacity = $(".wattermark_block .text_content_element").css("opacity");
			if(typeof opacity=='undefined'){
				var opacity = $(".wattermark_block img").css("opacity");
			}
			var color = $(".wattermark_block .text_content_element").css("color");


			PDFTOOLS.startTask();
			var intervalID = setInterval( function() {
				console.log("upload progress is "+spe.upload_in_progress);
				if(!spe.upload_in_progress){
					clearInterval(intervalID);


					PDFTOOLS.data.watermark = {
						pt_size: {
							width: $("#page_canvas_1").attr("pt-width"),
							height: $("#page_canvas_1").attr("pt-height"),
						},
						color: typeof color!='undefined'?rgb2hex(color):false,
						bm: PDFTOOLS.bm,
						width: $this.prepare(parseFloat($(".wattermark_block").css("width"))),
						height: $this.prepare(parseFloat($(".wattermark_block").css("height"))),
						font: font_family,
						"font-size": px2mm(parseFloat($(".wattermark_block .text_content").css("font-size"))),
						"font-size-scaled": (parseFloat($(".wattermark_block .text_content").css("font-size")))/PDFTOOLS.bm,
						top: $this.prepare(parseFloat($(".wattermark_block").css("top"))),
						left: $this.prepare(parseFloat($(".wattermark_block").css("left"))),
						// rotate: PDFTOOLS.matrixToDeg($(".wattermark_block .rotatable_helper").css("transform")),
						rotate: PDFTOOLS.getRotationDegrees($(".wattermark_block .rotatable_helper")),
						opacity: parseFloat(opacity),
						// text: $(".wattermark_block .text_content_element").html(),
						text: $(".wattermark_block .text_content_element").text(),
						image_id: $(".inserted_image").attr("db_id")
					};

					$this.ajax({uuid: UUID, total_pages: $this.total_pages, data: $this.data, file_name: $this.file.name }).then($this.taskComplete);

					$('main').removeClass('step1').removeClass('step2').addClass('step3');
				}
			}, 1000);
		}

		return false;
	},

	prepare: function(val){
		return val/PDFTOOLS.bm*pt_to_mm;
	},
	getPagePreviewTemplate: function(params){
		var subm = $(".document_add_element_submenu").html();

		var html = `
			<div id="page_preview_${params['page_num']}" class="page_block_p" data-pagenum="${params['page_num']}" style='display: flex;'>
				<div class="canvas_outer" style='margin: 0 auto; position: relative;'>
					<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
				</div>
			</div>
		`;
		if(this.is_fill){
			html += `<div class='document_add_element_submenu' data-after-page="${params['page_num']}">
				${subm}
			</div>`

		}

		return html;
	},
	matrixToDeg: function(matrix){
		if(matrix !== 'none') {
			var values = matrix.split('(')[1].split(')')[0].split(',');
			var a = values[0];
			var b = values[1];
			var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
		} else { var angle = 0; }
		return (angle < 0) ? angle + 360 : angle;
	},
	getRotationDegrees: function getRotationDegrees(obj) {
		var matrix = obj.css("-webkit-transform") ||
			obj.css("-moz-transform")    ||
			obj.css("-ms-transform")     ||
			obj.css("-o-transform")      ||
			obj.css("transform");
		if(matrix !== 'none') {
			var tr;
			if (tr = matrix.match('matrix\\((.*)\\)')) {
				tr = tr[1].split(',');
				if (typeof tr[0] != 'undefined' && typeof tr[1] != 'undefined') {
					var angle = Math.round(Math.atan2(tr[1], tr[0]) * (180 / Math.PI));
				} else {
					var angle = 0;
				}
			} else if (tr = matrix.match('rotate\\((.*)deg\\)')) {
				var angle = parseInt(tr[1]);
			}
		}else if(tr = obj[0].style.transform.match('rotate\\((.*)rad\\)')){
			var angle = parseInt(tr[1] * (180 / Math.PI));
		} else { var angle = 0; }

		return (angle < 0) ? angle + 360 : angle;
	}
}
if(WatermarkPDF.tool_section.length>0){
	function rgb2hex(rgb){
		rgb = rgb.replace(/(\s)|(rgba\()|rgb\(|\)/g, "").split(",");
		var red = rgb[0], green = rgb[1], blue= rgb[2];

		var rgb = blue | (green << 8) | (red << 16);
		return '#' + (0x1000000 + rgb).toString(16).slice(1)
	}

	converterTool = WatermarkPDF = $.extend(PDFTOOLS, WatermarkPDF);
	WatermarkPDF.main();
}

$(document).on('click', '.signature-btn-block', function(e){
	e.preventDefault();

	if($(this).hasClass('text_sign')){
		$('.swal2-actions').hide();
	}else{
		$('.swal2-actions').show();
	}

	$(".signature-btn").hide();
	if($(this).hasClass("draw_sign")){
		$(".signature-btn").show();
	}

	$(".signature-btn-block").removeClass("active");
	$(this).addClass("active");
	$(".create-tab-block").removeClass("active");
	$(".create-tab-block").eq($(this).index()).addClass("active");
});


function startDrawOrRotateImage(event, ui){
	var element = $(event.target).closest(".outer_image_div"),
		element_id = element.attr("id")
		other = {};
	
	switch(event.type){
		case 'resizestart':
			type = "resize_image";
			other = {
				width: element.css("width"),
				height: element.css("height")
			};
		break;
		case 'rotatestart':
			other.angle = $(`#${element_id}`).find(".rotatable_helper").data('uiRotatable').elementCurrentAngle;
			type = "rotate_image";
		break;
		case 'dragstart':
			type = "move_image";
			other = {
				left: element.css("left"),
				top: element.css("top")
			}
		break;
	}
	
	$(document).trigger("whistory", {
		...other,
		id: element_id, 
		page: $(`#${element_id}`).closest('.page_block_p').data('pagenum'), 
		type: type
	});
}

function startDrawOrRotate(event, ui){

	var element = $(event.target),
		element_id = element.attr("id");
	
	if(element.hasClass("rotatable_helper")){
		element = element.closest(".wattermark_block"),
		element_id = element.attr("id");
	}
		

	let type = "";
	let other = {};
	
	switch(event.type){
		case 'resizestart':
			let inner = $(`#${element_id} .element_inner_wrpr`);
			let inner2 =  $(`#${element_id} .document_add_element`);
			//document_add_element
			type = "resize_text";
			other = {
				"font-size": inner.css("font-size"),
				"line-height": inner.css("line-height"),
				"width": inner2.css("width"),
				"height": inner2.css("height")
			}
//					other.resize = $(`#${element_id}`).find(".document_add_element").data('uiRotatable').elementCurrentAngle;
		break;
		case 'rotatestart':
			type = "rotate_text";
			other.angle = $(`#${element_id}`).find(".document_add_element").data('uiRotatable').elementCurrentAngle;
		break;
		case 'dragstart':
			type = "move_text";
			other = {
				left: element.css("left"),
				top: element.css("top"),
			}
		break;
	}
	$(document).trigger("whistory", {
		...other,
		id: element_id, 
		page: $(`#${element_id}`).closest('.page_block_p').data('pagenum'), 
		type: type
	});
}


