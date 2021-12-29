'use strict';
var user_updatePassword = false
var PDFTOOLS = {
	name: false,
	file: false,
	new_file_name: false,
	need_preview: false,
	tool_section: false,
	calculate_words: false,
	toolurl: false,
	page_preview_width: 261,
	selectable_pages: false,
	load_blob: false,
	selected_pages: {},
	uploader_section: $("#upload_section"),
	preview_block: $("#pages_preview_section"),
	page_preview_items_selector: ".preview_page_block",
	page_preview_selected_selector: "selected_page",
	pages_list: $("#pages_previews_here"),
	pages_ranges_block: $("#pages_ranges"),
	zoomer: "#preview_zoom",
	one_canvas_for_all_pages: false,
	multiple_upload: false,
	canvas_list: {},
	canvas_translated_list: {},
	random_colors: [],
	hide_before_upload: false,
	show_only_first_page: false,
	total_pages: 0,
	page_preview_width_auto: false,
	need_scrool: true,
	render_pages: 200,
	pages_sizes: {},
	main: function(){
		var $this = this;
		$(document).on("getting_blob", function(){

			if($this.hide_before_upload){
				$(".before_upload").addClass("hidden");
			}

			$this.tool_section.removeClass("hidden");
		});
		
		if(typeof this.init == 'function'){
			if(!this.tool_section || $($this.tool_section).length==0){
				console.warn("Tool section not found");
			}
			pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
			this.init();
			
			$(document).on("file_selected", $.proxy(this.fileSelected, this));
			if(this.selectable_pages){
				for(let i=0; i<250; i++){
					this.random_colors.push(this.getRandomColor());
				}
				$(document).on("click", `${$this.page_preview_items_selector}`, $.proxy(this.selectPage, this));
			}
			if($this.zoomer.length>0){
				$(document).on("input", $this.zoomer, $this.changeZoom);
			}
		}
	},
	selectPage: function(e){
		var page = $(e.currentTarget),
			$this = this,
			sl = 0;
		let section_counter = 0;
		if(page.hasClass($this.page_preview_selected_selector)){
			page.removeClass($this.page_preview_selected_selector); //.css("background-color", "initial");
		    $(document).trigger("on_preview_page_click", [page, false]);
		}else{
			page.addClass($this.page_preview_selected_selector); //.css("background-color", $this.random_colors[sl]);
		    $(document).trigger("on_preview_page_click", [page, true]);
		}
		
		
		return false;
	},
	scroolToSectionBegin: function(){
		var $this = this;
		if($this.need_scrool){	
			setTimeout(function(){ $('html, body').animate({scrollTop: $this.tool_section.eq(0).offset().top}, 300); }, 500)
		}
	},
	getCSFR: function(){
		this.csfr = $("#editor_csrf").val();
	},

	changeZoom: function(e){
		console.log(e.target);
	
		var val = parseInt(e.target.value)
		
		var new_width = PDFTOOLS.page_preview_width*val/100;
		$("canvas", PDFTOOLS.page_preview_items_selector).css("width", new_width+"px");
		
	},
	startTask: function(){
		$("#apply-popup .modal-header").addClass("hidden");
	
		$("#apply-popup").addClass("active");
		$(".creating_document").show();
		$(".create_file_box").hide();
		$(".apply_changes_1").html("Wait...");
	},
	taskComplete: function(data){
		if(!data.success){
			$("#apply-popup").removeClass("active");
			swal("Error_1", data.message, "error");
			return false;
		}

		$(document).trigger("after_save_file", [data]);

	
		var $this = this;
		if(typeof data.new_file_name!= 'undefined'){
			$this.new_file_name = data.new_file_name;
		}
		
		$("#apply-popup .modal-header").removeClass("hidden");
		
		$(".creating_document").hide();
		$(".create_file_box").show();
		$(".result-top-line .download_file_name").html($this.new_file_name);
		$(".download-result-link").attr({"href": "/"+data.url, "download": $this.new_file_name });
		$("#save-dropbox").attr({'data-url': "/"+data.url, 'data-file_name': $this.new_file_name});
		$("#save-gdrive").attr({'data-src': "/"+data.url, 'data-filename': $this.new_file_name});
	},
	/*
	fileSelected2: function(file){
		var $this = this;
		$this.file = file;
		console.log(`PDFTOOL: google drive file selected`);
		
		$("span[data-value='filename']").html(file.name);
		if($this.hide_before_upload){
			$(".before_upload").addClass("hidden");
		}
		$this.scroolToSectionBegin();
		blocker.hide();
		$this.file_name = file.name;
		$(".current_filename", $this.tool_section).html(file.name);
		if($this.need_preview && $this.preview_block.length==0){
			console.warn("Preview block not found {$this.preview_block}", $this.preview_block);
		}
		
		
		if($this.need_preview && $this.preview_block.length>0){
			pdfUploader.getBlob(file).then((data)=>{
				window.current_file_data = data;
				var params = {filename: file.name, size: file.size, fileData: data, password: PDF_PASSWORD};
				$(".footer-editor").addClass("active");
				$this.tool_section.removeClass("hidden");
				($this.preview_section.length>0)?$this.preview_section.removeClass("hidden"):"";
				$this.preview(params);
			});
		}else{
			$this.tool_section.removeClass("hidden");
		}
		if($this.load_blob){
			pdfUploader.getBlob(file).then((data)=>{
				$this.file_data = data;
			});	
		}
		
		$this.uploader_section.hide();
		
	},
	*/
	fileSelected: function(prom, file){
		var $this = this;
		$this.file = file;
		//$(document).trigger("pdftool_file_selected", [file]);
		
		//trigger
		console.log(`PDFTOOL: ${$this.name} file selected`);
		
		$("span[data-value='filename']").html(file.name);
		if($this.hide_before_upload){
			$(".before_upload").addClass("hidden");
		}
		$this.scroolToSectionBegin();
		blocker.hide();
		$this.file_name = file.name;
		$(".current_filename", $this.tool_section).html(file.name);
		if($this.need_preview && $this.preview_block.length==0){
			console.warn("Preview block not found {$this.preview_block}", $this.preview_block);
		}
		
		if($this.need_preview && $this.preview_block.length>0){ 
			pdfUploader.getBlob(file).then((data)=>{
				window.current_file_data = data;
				var params = {filename: file.name, size: file.size, fileData: data, password: PDF_PASSWORD};
				$(".footer-editor").addClass("active");
				$this.tool_section.removeClass("hidden");
				($this.preview_section.length>0)?$this.preview_section.removeClass("hidden"):"";
				$this.preview(params);
			});
		}else{
			$this.tool_section.removeClass("hidden");
		}
		if($this.load_blob){
			pdfUploader.getBlob(file).then((data)=>{
				$this.file_data = data;
			});	
		}
		
		$this.uploader_section.hide();
	},
	renderPageCanvas: function(page, $this){ //TODO рендерим канвас страницы preview -> renderPagesBlocks -> renderPageCanvas
		// console.log('page', page);
		var pn = page.pageIndex+1, 
			canvas = $this.canvas_list[pn];
		if(typeof canvas=='undefined'){
			console.error("Error: canvas is undefined, check selectors in tool setings");
			return false;
		}
		if($this.page_preview_width_auto){
			$this.page_preview_width= $("#pages_previews_here").width()*0.7;
		}
		
		var ctx = canvas.getContext('2d'),
			unscaled_viewport = page.getViewport({ scale: 1 }),
			bm = $this.page_preview_width/unscaled_viewport.width,
			viewport = page.getViewport({ scale: bm });
			
//		console.log("unscaled_viewport ", unscaled_viewport);
//		console.log("viewport ", viewport);

		$this.bm = bm;
		
		canvas.setAttribute("pt-width", unscaled_viewport.width*pt_to_mm);
		canvas.setAttribute("pt-height", unscaled_viewport.height*pt_to_mm);
		
		canvas.height = viewport.height;
		canvas.width = viewport.width;
		
		if($(`#translate_preview_${pn}`).length>0){
			$(`#translate_preview_${pn}`).css("width", viewport.width)
			$(`#translate_preview_${pn}`).css("height", viewport.height)
		}
		
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
		
		if($this.one_canvas_for_all_pages && pn != 1){
			return false;
		}
		
        $(document).trigger("before_page_render", [viewport, unscaled_viewport, pn, page, canvas]);
		
		var renderTask = page.render(renderContext);
		
	}, 
	renderPagesBlocks: function(pdfDoc, $this){ //TODO рендерим блоки страниц и запускаем рендер канваса страниц preview -> renderPagesBlocks
		var $this= this;
		console.log('renderPagesBlocks $this', $this);
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
		
		for(let i=1; i!=pdfDoc.numPages+1; i++){
			if($this.one_canvas_for_all_pages){
				console.log("render into one canvas");
				if(!flag){
				
					console.log("create first page template");
					$this.pages_list.append($this.getPagePreviewTemplate({"page_num": i}));
					var tcanvas = $(`#page_canvas_${i}`)[0];
					var tcanvas_translated = $(`#translated_canvas_${i}`)[0];
					console.log(tcanvas);
					flag = true;
				}else{
					
				}
			}else{
				if(i<=this.render_pages){
					var template = $this.getPagePreviewTemplate({"page_num": i});
					$this.pages_list.append(template);
					$(document).trigger("page_temaplate_appended", [template]);
					var tcanvas = $(`#page_canvas_${i}`)[0];
					var tcanvas_translated = $(`#translated_canvas_${i}`)[0];
				}
			}
			
			$this.canvas_list[i] = tcanvas;
			//console.log($(`#translated_canvas_${i}`)[0]);
			$this.canvas_translated_list[i] = tcanvas_translated;
			pdfDoc.lang_detected = false;
			pdfDoc.getPage(i).then(function(page){
				var viewport = page.getViewport({ scale: 1 });
				//page
//				$this.pages_sizes[page.pageIndex] = {pn: page.pageIndex, width: viewport.width, height: viewport.height};
				$this.pages_sizes[page.pageIndex] = {pn: page.pageIndex, width: viewport.width*pt_to_mm, height: viewport.height*pt_to_mm};				
				
				if(PDFTOOLS.calculate_words){
					var detect_language_string = "";
					
					page.getTextContent().then(function(texts){
						if(i==pdfDoc.numPages){
							setTimeout(function(){
								$(document).trigger("last_page_text_collect");
							}, 400);
						}
					
						$.each(texts.items, function(i2, t){
							if(PDFTOOLS.testText(t.str, 0)){
								if(typeof window.total_chars_on_page[i]=='undefined'){
									window.total_chars_on_page[i] = 0;
								}else{
									window.total_chars_on_page[i] += parseInt(t.str.length);
								}
								window.total_chars += parseInt(t.str.length);
								if(i2<10){
									detect_language_string += " " + t.str;
								}
								$(".total_chars").html(window.total_chars);
							}
						});
						
						if(typeof window.render_count){
							window.render_count++;
							$(document).trigger("pdf_page_render");
						}
						
						
						if(detect_language_string && !pdfDoc.lang_detected){
							pdfDoc.lang_detected = true;
							var l = franc(detect_language_string);
							$(document).trigger("lang_detected", [l]);
						}
						

					});
					
				}
				
				if(page.pageIndex+1<=$this.render_pages){
					//alert((page.pageIndex+1)+" || "+$this.render_pages);
					$this.renderPageCanvas(page, $this) 
				}

				//$this.renderPageCanvas(page, $this) 
//				if(i==pdfDoc.numPages){
//					$(document).trigger("after_last_page_render")
//				}
			}).catch(function(err){
				console.error(err);
			});
			if($this.show_only_first_page && i==1){
				$(document).trigger("show_only_first_page_render");
			
				break;
			}
			
		}
	},
	
	preview: function(params){ //TODO запускаем обработку документа 
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
		PDFTOOLS.loadingTask = loadingTask;
		//$(document).trigger("pdf_loading_task", [loadingTask]);
		
//		loadingTask.onPassword = function(updatePassword, reason){
//			user_updatePassword = updatePassword;
//			showPasswordPrompt(updatePassword, reason);
//		}
		
		loadingTask.promise.then(function(pdfDoc_){ 
			$(document).trigger("before_render_pages_blocks", [pdfDoc_]);
			PDFTOOLS.pages_list.html("");
			$this.renderPagesBlocks(pdfDoc_, $this) 
		}).catch(function(err){
			alert("Get document error: "+err.message);
		});
		return loadingTask;
		
	},
	getPagePreviewTemplate(params){
		return `
			<div class="preview_page_block split-main-block page_${params['page_num']}" data-page-id='${params['page_num']}'>
				<div class="split-main-num">${params['page_num']}</div>
				<div class="split-main-photo">
					<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
				</div>
			</div>
		`;
	},
	ajax: function(data){

	
		data['operation_id'] = pdfUploader.operation_id;
		var $this = this;
		$this.startTask();
		$this.getCSFR();
		return $.ajax({
			method: "POST",
			url: $this.toolurl,
			headers: { 'X-CSRF-TOKEN': $this.csfr},
			data: data,
			dataType: "json",

			xhr: function(){
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt){

				}, false);
				xhr.addEventListener("progress", function(evt){
					console.log("progress", evt);

				}, false);
				return xhr;
			},


			error: function(error){
				console.log("Ajax error ", error.responseText);
				alert("error");
			}
		});
	},

	testText: function(el, is_element=1){
		if(is_element){
			var t = $(el),
				text = t.html();
			if(t.hasClass("not_editable_rotated_text")){
				return false;
			}
		}else{
			text = el;
		}
			
		if(text.length<1){
			return false;
		}
		if(/^\d+$/.test(text)){
			return false;
		}
		
		if(/[^\u0000-\u007F]+/.test(text) && text.length>0){
			return true;
		}

		if(/\b[^\d\W]+\b/.test(text)){
			return true;
		}
		return false;
	},


	getRandomColor: function(){
		var o = Math.round, r = Math.random, s = 255;
		return 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + 0.5 + ')';
	}

};



