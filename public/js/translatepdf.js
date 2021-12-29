window.show_anyway = false;
window.skip_extract = 1;
window.remove_all_texts = 1;
window.use_scale_on_merge = 1;


if(typeof need_payment=='undefined'){
	need_payment = false;
}

window.payment_number = 0;
$(document).ready(function(){

//	setTimeout(function(){
//	window.skip_extract=1;
//	},3000)
});

window.render_count = 0;


$(document).on("pdf_page_render", function(){


	$(".render_proccess").removeClass("hidden");
	$(".render_proccess").html("Page processing: "+window.render_count+"/"+TranslatePDF.total_pages);
	if(window.render_count==PDFTOOLS.total_pages){
		$(".render_proccess").addClass("hidden");
		$(".right_part .buttons_part").removeClass("hidden");
		$(document).trigger("last_render");
	}else{
	
	}
});


$(document).on("before_upload_file", function(e, f){
	switch(f.type){
		case 'application/pdf': //pdf
			PDFTOOLS.name = "translatepdf";
			PDFTOOLS.main = function(file){	
				var $this = PDFTOOLS; 
				if(typeof this.init == 'function'){
					if(!this.tool_section || $($this.tool_section).length==0){
						console.warn("Tool section not found");
					} 
					pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/translate/pdfjs-dist/build/pdf.worker_x.js';
					this.init();
					$(".after_upload").removeClass("hidden");
					PDFTOOLS.fileSelected(false, file);
				}
			};
			
			TranslatePDF = $.extend(PDFTOOLS, TranslatePDF);
			TranslatePDF.main(f);
		break;
		default: //docx
			$("#save_all").html("Download");
			$(".page_num_title").addClass("hidden");
			$("#translate_one_page").addClass("hidden");
			$("#translate_all").html("Translate document");
			$("#save_and_edit_all").addClass("permanently_hidden");
			
			PDFTOOLS.name = "translatedocx";
			PDFTOOLS.main = function(file){	
				var $this = PDFTOOLS; 
				if(typeof this.init == 'function'){
					if(!this.tool_section || $($this.tool_section).length==0){
						console.warn("Tool section not found");
					}
					
					pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker_x.js';
					this.init();
					$(".after_upload").removeClass("hidden");
					PDFTOOLS.fileSelected(false, file);
				}
			};
			

			TranslatePDF = $.extend(PDFTOOLS, TranslatePDF);
			TranslatePDF = $.extend(PDFTOOLS, TranslateDOCX);
			TranslatePDF.main(f);


//			TranslateDOCX = $.extend(PDFTOOLS, TranslateDOCX);
//			TranslateDOCX.main(f);
		break;
	}
});

var epg = false; //empty page generator


var tpp = {};
var current_range = false;


var lastScrollTop = 0;
var scroll_direction = "down";
$(window).scroll(function(event){
	var st = $(this).scrollTop();
	if(st >lastScrollTop){
		scroll_direction = "down";
	}else{
		scroll_direction = "up";
	}
	lastScrollTop = st;
});

var translated_list = [];


var def_lang = $(".source_language").val();
if(def_lang=='auto'){
	$(".buttons_part").addClass("hidden");
}


var fillTexts = function(texts, tl=false, page_num=0){
	var total_texts = 1;

	for(i=0; i!=Object.keys(texts).length; i++){
		var text = texts[i];
  		if(!text){
			continue;
		}
		if(text.remove_elements){
			$.each(text.remove_elements, function(x, x2){
				$(`${x2}`).remove();
			})
		}

		if(!text.element){
			continue;
		}
		
		text.el = $(text.element);
		var texts_length = Object.keys(texts).length;
		if(typeof tpp[page_num]=='undefined'){
			tpp[page_num] = 0;
		}
		tpp[page_num]++;

		text.el.html(text.translated.replace(/\n/g, " "));
		
		text.el.css({
			//"background": "white",
			opacity: 0,
			//border: "1px solid red",
			"white-space": "normal",
			"text-align": "left",
			width: text.size.w*1.02,
			
			height: text.size.h*1.1,
			//"color": "black",
			"font-family": "Arial",
			//transform: "none"
		});


		
//		console.log(text.el);
//		alert(text.size.w);

		var breaker = 0; 
		var oh = 0;
		if(!text.el){
			continue;
		}
		
		
		
		
		while(true){
		
			var sch = text.el[0].scrollHeight,
				inh = text.el.innerHeight(),
				scw = text.el[0].scrollWidth,
				inw = text.el.innerWidth();

			fs = parseInt(text.el.css("font-size"));
			
			if(!oh){
				oh = inh;
			}
			
			if(sch-1>inh || scw>inw){
				fs--;
				text.el.css("font-size", fs+"px");
				text.el.css("line-height", fs+"px");
			}else{
				if(false && oh>inh-3){
					fs++;
					text.el.css("font-size", fs+"px");
					text.el.css("line-height", fs+"px");
				}
				break;
			}
			
			if(breaker>100){
				break;
			}
			breaker++;
		}
		var clone_1 = text.el.clone();
		$(`#translate_preview_${page_num}`).append(clone_1);
	
	}
	
}


var AmazonTranslate = async function(texts=false, tl=false, page_num=0){
	AWS.config.region = 'us-west-2'; // Region
	AWS.config.credentials = new AWS.Credentials(awsk1, awsk2);	
	
	
	var translate = new AWS.Translate({region: AWS.config.region});
	var polly = new AWS.Polly();
	
	async function awaitTranslate(params, el, iterator=0, total_texts=0, page_num=0){
		return $.extend({it: iterator, element: el, total_texts:total_texts, page_num: page_num}, await $.when(translate.translateText(params)).then(function(x){ 
			return x; }).catch(function(x){  
			return x;
		}));
	}
  
	if(texts){
		var i = 0;
		var promises = [];
		for(i=0; i!=Object.keys(texts).length; i++){
			var text = texts[i];
      		if(!text){
				continue;
			}
			
			var params = {
				Text: text.full_text,
				//TODO fix it
				SourceLanguageCode: $(".source_language").eq(0).val(),
				TargetLanguageCode: $(".target_language").eq(0).val(),
			};
			var element = {};

			$.each(text, function(tk, el){
				if(parseInt(tk)===0){
					//element.attr("title", text.full_text);
					element.el = (el.element);
					element.size = text.size;
				}else{
					if(typeof el.element!='undefined'){
						el.element.remove();
					}
				}
			});
			
			var texts_length = Object.keys(texts).length;
			
			//data = await awaitTranslate(params, element, i, texts_length, page_num);
			
			prom = awaitTranslate(params, element, i, texts_length, page_num);
			
			prom.then(function(data){

				if(typeof tpp[data.page_num]=='undefined'){
					tpp[data.page_num] = 0;
				}

				tpp[data.page_num]++;
				var percent = Math.ceil(tpp[data.page_num]*100/data.total_texts);
				
				text_status = "Translating...";
				
				$(document).trigger("update_progress", [text_status, percent, data.page_num, tpp[data.page_num], data.total_texts]);
				
				data.element.el.html(data.TranslatedText);
				
				data.element.el.css({
					//"background": "#f003",
					"white-space": "normal",
					width: data.element.size.width,
					height: data.element.size.height,
					"color": "black",
					"font-family": "'"+data.element.el.attr("fallback-font")+"'"
				});
      			var breaker = 0; 
				
				
				var oh = 0;
				while(true){
					var sch = data.element.el[0].scrollHeight,
						inh = data.element.el.innerHeight(),
						scw = data.element.el[0].scrollWidth,
						inw = data.element.el.innerWidth();

					fs = parseInt(data.element.el.css("font-size"));
					
					if(!oh){
						oh = inh;
					}
					if(data.it==45){
						console.log(data.element.el);
					}
					
					if(sch>inh || scw>inw){
						fs--;
						data.element.el.css("font-size", fs+"px");
					}else{
						if(false && oh>inh-3){
							fs++;
							data.element.el.css("font-size", fs+"px");
						}
						break;
					}
					
					if(breaker>100){
						break;
					}
					breaker++;
				}

			}).catch(function(){
				alert("catch");
			});
			
			promises.push(prom);
		}
		
		
		
		x = await Promise.all(promises);
		$(document).trigger("all_text_on_page_translated", [page_num]);
		return true;
		
	}else{
		$(document).trigger("all_text_on_page_translated", [page_num]);
		return true;
	}
	
	
	
	return true;
	return "xxxx";
	//return this;
};




var savePages = async function(pages=false, need_edit=0){
	
	
//	pages = window.ppp;

	var texts = {};
	pages.forEach(function(pn){
		texts = $.extend(texts, beforeSavePage(pn));
	});
	
	
	
	
	
	trans_pages = {};
	sizes = {};
	it = 0;
	
	pages.forEach(function(pn){
		//var tpn = $(this).data("page-num");
		//if(pages.indexOf(tpn)!=-1){
		trans_pages[pn]= pn; 
		sizes[`page_`+(pn-1)]=  {
			w: PDFTOOLS.pages_sizes[pn-1].width,
			h: PDFTOOLS.pages_sizes[pn-1].height,
		}
		//}
		it++;
	});
	
	

	PDFTOOLS.getCSFR();
	var params = {
		_token: PDFTOOLS.csfr,
		UUID: UUID,
		operation_id: pdfUploader.operation_id,
		changes: {text: texts},
		format: [],
		//TODO fix it
		pages: trans_pages,
		pages_sizes: sizes,
		pdf_password: PDF_PASSWORD,
		need_edit: need_edit,
		operation_type: need_edit?"edit_after_translate":"translatepdf",
		new_download: 1,
	};
	
	
	
	return  $.ajax({
		method: "POST",
		url: "/pdf/createPdf",
		data: params,
		dataType: "json",
		success: function(data){
			console.log("data in success", data);
			return data;
		},
		error: function(e){
			console.error(e);
			alert("error");
		}
	});
}



var beforeSavePage = function(page_num){
	var text_elements = $(`#translated_text_layer_${page_num} .text_content_element`);
	
	var texts = {},
		text_layer = $(`#translated_text_layer_${page_num}`);
		
	text_elements.each(function(i, v){
		var element = $(v),
			id = spe.uniq();


		var size = $(v).getElementOffset(text_layer, true);
		
//		size['left']  *= TranslatePDF.bm;
//		size['top'] *= TranslatePDF.bm;
//		size['width'] *= TranslatePDF.bm;

		try{
			var scale = parseFloat(element.get(0).style.transform.match(/\d+\.\d+/)[0]);
		}catch(e){
			scale = 1;
		}
		var font_size = px2mm(parseInt(element.css("font-size")));
		
		
		//font_size *= TranslatePDF.bm*scale;

		
		texts[id] = {
			element_id: id,
			element_content: $(v).html(),
			is_new: 1,
			page_num: page_num,
			size: size,
			dont_resize: 1,
			css: {
				"line-height": px2mm(parseFloat(element.css("line-height"))),
                "font-style": element.css("font-style"),
                "font-weight": element.css("font-weight"),
                "fontw": element.attr("text_is_bold")=="true"?"bold":"normal",
                "font-size": font_size+ "mm",
                //"test": element.css("font-size") + "px",
                //"font-family": (element.css("font-family")).toLowerCase().replace(" ", ""),
                "transform": element.get(0).style.transform,
                "letter-spacing": element.get(0).style["letter-spacing"],
                "color": element.css("color"),
                "original-font-size": element.css("font-size"),
                "original-height": 0,
                "original-width": 0,
                "bs": parseFloat(element.css("line-height"))-parseFloat(element.css("font-size")),
			},
			time: false,
			ts: 0,
			type: "text"
		};
		//epta();
	});
	$(document).trigger("save_collect_texts", page_num);
	return texts;
}

window.total_chars = 0;
window.total_chars_on_page = {};



async function sendTranslateRequest(texts, tl, page_num, cumulative){
	
	var url = "/translate-pdf-chunk";
	data = {
		charge_id: PDFTOOLS.charge_id,
		cumulative: window.cumulative,
		from: $(".source_language").val(),
		to: $(".target_language").val(),
		total_chars: total_chars,
		texts: texts,
		_token: PDFTOOLS.csfr,
		payment_number: payment_number,
		debug_it: $("#debug_text").is(":checked")
		
	};
	
	return fetch(url, {
		method: 'POST',
		mode: 'cors',
		cache: 'no-cache',
		credentials: 'same-origin',
		headers: { 'Content-Type': 'application/json' },
		redirect: 'follow',
		referrer: 'no-referrer', 
		body: JSON.stringify(data), 
	}).then(response => response.json()); // парсит JSON ответ в Javascript объект

}



async function runTranslate(tl, page_num, cumulative){
	
	PDFTOOLS.getCSFR()
	$(".text_content_element").prop("contenteditable", true);
	TranslatePDF.page_texts[page_num] = collectTexts(page_num);
	
	
	var tr = await sendTranslateRequest(TranslatePDF.page_texts[page_num], tl, page_num, cumulative);
	if(tr.success==false){
		swal("Error", tr.message, "error");
		return false;
	}
	
	fillTexts(tr['texts'],false, page_num);
	return true;
}

var GenerateEmptyPages = function(){
	var $this = this,
		CMAP_URL = '/libs/pdfjs-dist/cmaps/',
		CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
	this.pages_list = $("#empty_pages");
	this.canvas_list = {};
	this.bm = 0;
	this.pdfDoc = false;
	window.pdf_without_text = false;
	
	this.renderEmptyPage = async (page_num) => {
		//$(".buttons_part").addClass("hidden");
		
		//alert("=========================================================");
		
		if(typeof window.pdfDoc_temp=='undefined'){
			alert("Error: pdfDoc is undefined...");
			return false;
		}
		this.pdfDoc = window.pdfDoc_temp;
		var page  = await this.pdfDoc.getPage(page_num);
		

		unscaled_viewport = page.getViewport({ scale: 1 }),
		//bm = $(".before_ocr").eq(0).width()/unscaled_viewport.width,
		viewport = page.getViewport({ scale: PDFTOOLS.bm });
		$("#tempo").css({"position": "absolute", "top": "-9999px"});

		$("#tempo").append(`<div 
			style='opacity: 1; pointer-events: all; background: white; position: absolute; top: 100px; left: 10px; border: 1px solid red; 
			width: ${PDFTOOLS.pages_sizes[page_num-1].width/pt_to_mm*PDFTOOLS.bm}px; 
			height: ${PDFTOOLS.pages_sizes[page_num-1].height/pt_to_mm*PDFTOOLS.bm}px;;' 
			class="translated_items_outer" id="translated_text_layer_${page_num}">
			<canvas style='width: 500px; height: 500px;'  id="translated_canvas_${page_num}"></canvas></div>`);

		var textContent = await page.getTextContent(); //.then( function(text){ return text;  } );

		var ret = await pdfjsLib.renderTextLayer({
			textContent: textContent,
			container: $(`#translated_text_layer_${page_num}`).get(0),
			viewport: viewport,
			//textDivs: []
		});
		
		//alert("after renderTextLayer");

		var text_layer = ret._container;
//		$(document).trigger("update_progress", ["Prepare document...", 0, page_num]);
		
		//TODO
		//x = mergeLetters(text_layer);
//		$(".total_progress").html("Pages translated: "+page_num);
		//$(document).trigger("after_translate_merged_letters", [$(text_layer), page_num]);
		
		return [$(text_layer), page_num];
	};
	this.getPagePreviewTemplate = (params) => {
		return `
			<canvas class='empty_page_canvas' data-rotate="0" data-page-id="${params['page_num']}" id="empty_page_canvas_${params['page_num']}"></canvas>		
		`;
	};
	return this;
};

var TranslatePDF = {
	calculate_words: true,
	name: "translatepdf",
	need_preview: true,
	tool_section: $("#editor_csrf"),
	preview_block: $(".pages_preview_block"),
	preview_section: $("#editor_csrf"),
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-ocr",
	data: { },
	load_blob: true,
	file_data: false,
	page_preview_width_auto: true,
	page_preview_width: 420,
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	page_preview_items_selector: ".resize-margin-block",
	show_only_first_page: false,
	page_texts: {},
	pay_for: 0,
	paid_pages: {},
	current_page: 1,
	render_pages: 5,
	charge_id: false,
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	
	ppages: {},


	update_preview: function(params){ //TODO запускаем обработку документа
	
		var $this = this,
			CMAP_URL = '/libs/pdfjs-dist/cmaps/',
			CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
		var params = {
			url: params.url,
			cMapUrl: CMAP_URL,
			cMapPacked: CMAP_PACKED,
			password: PDF_PASSWORD
		};
		$(document).trigger("before_start_preview_pages", [params]);

		var loadingTask = pdfjsLib.getDocument(params);
		PDFTOOLS.loadingTask = loadingTask;

		loadingTask.promise.then(function(pdfDoc_){
			$(document).trigger("before_render_pages_blocks", [pdfDoc_]);
			PDFTOOLS.pages_list.html("");
			$this.renderPagesBlocks(pdfDoc_, $this)
		}).catch(function(err){
			alert("Get document error: "+err.message);
		});
		return loadingTask;

	},


	getPriceRange: function(words=0){
		if(words<pricing.free_count && !window.need_payment){
			return 0;
		}
		if(window.need_payment){
			$(".not_blocked_transalte").addClass("hidden");
			$(".blocked_translate").removeClass("hidden");
			var price = (((words)/pricing.translate_count)*pricing.translate_pricing).toFixed(2);
			if(parseFloat(price)<0.51){
				price = 0.51;
			}
			current_range = {price: price, count: words};
			return price;
		}


		$(".not_blocked_transalte").removeClass("hidden");
		$(".blocked_translate").addClass("hidden");


		
		if(words>0 && parseInt(words)>pricing.translate_count && pricing.translate_pricing>0){
			var price = (((words-pricing.free_count)/pricing.translate_count)*pricing.translate_pricing).toFixed(2),
				temp_price = price.split(".");

			if(parseFloat(price)<0.51){
				price = 0.51;
			}
			
			current_range = {price: price, count: words};


			if(temp_price[1]==0){
				return parseFloat(price).toFixed(2);
			}else{
				return parseFloat(price).toFixed(2);
			}
		}
		return 0;
	},
	testText: function(el, is_element = 1) {
		if (is_element) {
        	var t = $(el),
		        text = t.html();
		    if (t.hasClass("not_editable_rotated_text")) {
		        return false;
		    }
		} else {
		    text = el;
		}
		if (text.length < 1) {
		    return false;
		}
		if (/^\d+$/.test(text)) {
		    return false;
		}
		if (/[^\u0000-\u007F]+/.test(text) && text.length > 0) {
		    return true;
		}
		if (/\b[^\d\W]+\b/.test(text)) {
		    return true;
		}
		return false;
	},
	
	bind: function(){
		var $this = this;
		
		$(document).on("click", "#download_file", async function(e){
			e.preventDefault();
			downloadURI($(this).attr("href"), "translated_"+PDFTOOLS.file_name);
			return false;
		
//			e.preventDefault();
//			showLoading1($(".download_outer"));
//			var resp = await savePages(range(1, PDFTOOLS.total_pages+1),0);
//			hideLoading()
//			window.location = resp.url
			
		});

		$(document).on("submit", "#stripe_form", function(e){
			e.preventDefault();
			
			
			showLoading1($("#stripe_form"))
			
			var form = $(e.target);
			if(!$("#email").is(":valid")){
				$(".loading").remove();
				$(".stripe_errors").removeClass("hidden");
				$(".stripe_errors").html("Please, enter valid email");
				return false;
			}
			
			var extraDetails = {
		//		name: form.find('input[name=cardholder-name]').value,
				email: $("#email").val()
			};
			stripe.createToken(card, extraDetails).then(function(data){
				$(".stripe_errors").addClass("hidden");
				
				if(typeof data.error!='undefined'){
					Swal.fire({
						type: 'error',
						title: 'Error',
						text: data.error.message,
					})
					hideLoading()
					return false;
				}
				
				data.token.price = current_range;
				data.token.email = $("#email").val();
				
				PDFTOOLS.getCSFR();
				
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': PDFTOOLS.csfr
					},
					method: "POST",
					url: "/createStripeCharge",
					data: data.token,
					dataType: "json",
					success: function(data){
						hideLoading();
						if(data.success){
						
						
							if(PDFTOOLS.name=='translatedocx'){
								$("#translate_all").click();
							}else{
								PDFTOOLS.charge_id = data.payment_id;
								TranslatePDF.startTranslate();
								$('.credit_card_popup_wrp.one').removeClass("active");
								$('body').removeClass("credit_card_popup_opens");
							}
						}else{
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: data.message,
							});		
							hideLoading();
												
						}
					},
					error: function(error){
						hideLoading();
						console.log(error);
						alert(error.responseJSON.message);
					}
				});
			}).catch(function(error){
				console.log(error);
				hideLoading();
				alert("error");
				
			});
		});



//		$(document).on("change", ".source_language", function(){
//			if($(this).val()=='auto'){
//				$(".language_not_selected").removeClass("hidden");
//				$(".buttons_part").addClass("hidden");
//			}else{
//				$(".language_not_selected").addClass("hidden");
//				//$(".buttons_part").removeClass("hidden");
//			}
//		});

		$(document).on("lang_detected", function(e, lang){
			var nc = "unk"; //normal code
			switch(lang){
				case 'cmn':
					nc = "zh";
				break;
				case 'eng':
					nc = "en";
				break;
				case 'arb':
					nc = "ar";
				break;
				case 'ces':
					nc = "cs";
				break;
				case 'deu':
					nc = "de";
				break;
				case 'spa':
					nc = "es";
				break;
				case 'fra':
					nc = "fr";
				break;
				case 'ita':
					nc = "it";
				break;
				case 'jpn':
					nc = "ja";
				break;
				case 'por':
					nc = "pt";
				break;
				case 'rus':
					nc = "ru";
				break;
				case 'tur':
					nc = "tr";
				break;
			}
			if(nc!='unk'){
				$(`.lang_source option[value='${nc}']`).prop("selected", "selected").change();
			}
		});



		$(document).on("last_page_text_collect", function(){
			if($.isEmptyObject(window.total_chars_on_page)){
				Swal.fire({
					title: "Error",
					allowOutsideClick: false,
					text: "There is not text in document. Please use our OCR tool to recognize text.",
					type: "error",
					confirmButtonText: "<a href='/ocr-pdf'>Go to OCR</a>",
				});
				return false;
			}	
		});


//		var getViewportPage = function(){
//			var current_page = 1,
//				pages_in_viewport = [];
//			$(".page_item").each(function(){
//				if($(this).isInViewport()){
//					pages_in_viewport.push(parseInt($(this).data("page-num")));
//				}
//			});
//			if(scroll_direction=="down"){
//				return Math.max(...pages_in_viewport);
//			}else{
//				return Math.min(...pages_in_viewport);
//			}
//		}

//		var scrollFunc = debounce(function(){
//			//TODO fix selector
//			if($(".before_translate_page").length>0){
//				var page = getViewportPage(scroll_direction);
//				$(document).trigger("current_page_view", [page]);
//			}
//		}, 100);
		
		

//		$(document).on('scroll', scrollFunc);
		
		
//		$(document).on("all_pages_translated", function(){
//			$(".on_all_translated").removeClass("hidden");
//			$("#save_one_page").addClass("permanently_hidden");
//			$("#edit_one_page").addClass("permanently_hidden");
//			$("#translate_all").addClass("permanently_hidden");
//		});
		
		
		
		$(document).on("click", "#save_all", function(e){
			PDFTOOLS.startTask()
			var translated_pages = [];
			$(".page_item.translated").each(function(i, pp){
				translated_pages.push($(pp).data("page-num"));
			});
			var intervalID = setInterval(function(){
				console.log("upload progress is "+spe.upload_in_progress);
				if(!spe.upload_in_progress){
					clearInterval(intervalID);
					$.when(savePages(translated_pages,0)).then($this.taskComplete);
				}
			} , 250);	
			return false;
		});		
		
//		$(document).on("after_translate_merged_letters", function(event, tl, page_num){
//			$(".text_content_element").prop("contenteditable", true);
//		
//			//var texts = tl.find(".text_content_element");
//			$(document).trigger("update_progress", ["Collect texts...", 0, page_num]);
//			
//			$(`.trans_canvas_outer_${page_num}`).removeClass("hidden");
//			$this.page_texts[page_num] = collectTexts(page_num);
//			
//			new AmazonTranslate($this.page_texts[page_num], tl, page_num)
//		});
		
		$(document).on("click", "#translate_all", async function(e){
			e.preventDefault();
			TranslatePDF.startTranslate();
		});
		
		
	},
	
	
	startTranslate: async function(){


		function timeout(ms) {
			return new Promise(resolve => setTimeout(resolve, ms));
		}

		async function sleep() {
			await timeout(3000);
			return true;
		}

	
		var price = PDFTOOLS.getPriceRange(window.total_chars);
		$("#save_one_page").addClass("permanently_hidden");
		$("#edit_one_page").addClass("permanently_hidden");
		
		$(".hide_after_start").addClass("hidden");
		

		var pages = $(".page_item:not(.translated)");
		if(!epg){
			epg = new GenerateEmptyPages();
		}
		
		
		
		var combinedTexts = [];
		var pages = jQuery.makeArray(pages);
		
		//todo translate all
		$(".before_translate").addClass("hidden");
		$(".translate_proggress").removeClass("hidden");
		
		var total_translated = 0;
		
		if($(".source_language").val()==$(".target_language").val()){
			swal("Error", "Please, select different languages", "error");
			return false;
		}
		
		
		for(page_it=0; page_it!=TranslatePDF.total_pages; page_it++){
			if(typeof window.brr != 'undefined'){
				break;
			}
//			alert(page_it);
		
			var page_num = page_it+1;
			var for_trans = await epg.renderEmptyPage(page_num);
			
			
			var r = await runTranslate(...for_trans);
			if(typeof window.total_chars_on_page[page_it+1]=='undefined'){
				total_translated+= 0;
			}else{
				total_translated+=window.total_chars_on_page[page_it+1];
			}
			$(".total_translated").html(total_translated);
		}



		showLoading1($(".download_outer"));
		var resp = await savePages(range(1, PDFTOOLS.total_pages+1),0);
		hideLoading()
		
		$("#download_file").attr({"href": resp.url, "target": "_blank"});
		$(".preview_title").html("Translate preview");
		

		$(".upload_another_pdf").removeClass("hidden");
		$(".t-upload-another-pdf").css("border", "none");
		
		$(".before_translate").addClass("hidden");
		$(".translate_proggress").addClass("hidden");		
		$(".after_translate").removeClass("hidden");		
		PDFTOOLS.update_preview({url: resp.url});
		
	},

	taskComplete: function(data){
		if(!data.success){
			$("#apply-popup").removeClass("active");
			swal("Error_1", data.message, "error");
			return false;
		}
		
		var $this = this;
		if(typeof data.new_file_name!= 'undefined'){
			$this.new_file_name = data.new_file_name;
		}
		
		$("#apply-popup .modal-header").removeClass("hidden");
		$(".creating_document").hide();
		$(".create_file_box").show();
		$(".result-top-line .download_file_name").html($this.new_file_name);
		$(".download-result-link").attr({"href": data.url, "download": $this.new_file_name });
		$("#save-dropbox").attr({'data-url': data.url, 'data-file_name': $this.new_file_name});
		$("#save-gdrive").attr({'data-src': data.url, 'data-filename': $this.new_file_name});

		if(typeof data.redirect!='undefined' && data.redirect){
			$(".download-result-link").attr("_target", "blank");

			var win = window.open(data.edit_link, '_blank');
			win.focus();
		}else{
		}
	},
	save: function(e){
		var $this = this;
		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, type: $(".output-btn-active").data("val"), lang: $(".lang_select").val(), file_name: $this.file.name }).then($this.taskComplete);
			}
		} , 250);	

		return false;
	},
	getPagePreviewTemplate(params){

		return `
			<div class="slider_item slide_${params['page_num']}" style="position: relative;">
				<div class="oo">
					<canvas class='page_canvas'	data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
					<div class="translate_preview" id="translate_preview_${params['page_num']}" style="position: absolute;">
						
					</div>
					<div style='display: none !important;' class="hidden after_translate_page translated_canvas trans_canvas_outer_${params['page_num']}">
						<canvas  id="translated_canvas_${params['page_num']}"></canvas>
					</div>
				</div>
			</div>		
		`;
	},
	
	createProgressBar: function(element){
		return ;
	},
}


$.fn.isInViewport = function() {
	var elementTop = $(this).offset().top;
	var elementBottom = elementTop + $(this).outerHeight();

	var viewportTop = $(window).scrollTop();
	var viewportBottom = viewportTop + $(window).height();
	var percbottom = elementBottom*0.25,
		perctop = elementTop/0.25;

	return elementBottom > viewportTop && elementTop < viewportBottom;
};


function debounce(func, wait, immediate){
	var timeout;
	return function(){
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if(callNow){ func.apply(context, args); }
	};
};



var stripe = Stripe(stripe_pub);
var elements = stripe.elements();

var card = elements.create('card', {
  style: {
    base: {
      iconColor: '#666EE8',
      color: '#31325F',
      lineHeight: '40px',
      fontWeight: 300,
      fontFamily: 'Helvetica Neue',
      fontSize: '15px',

      '::placeholder': {
        color: '#CFD7E0',
      },
    },
  }
});

card.mount('#card-element');





$.fn.getRealDimensions = function (outer) {
    var $this = $(this);
    if ($this.length == 0) {
        return false;
    }
    
    
    var $clone = $this.clone()
        .show()
        .css('visibility','hidden')
        .insertAfter("#ocr_section");
        
        
        
    var result = {
        width:      (outer) ? $clone.outerWidth() : $clone.innerWidth(), 
        height:     (outer) ? $clone.outerHeight() : $clone.innerHeight(), 
        offsetTop:  $clone.offset().top, 
        offsetLeft: $clone.offset().left
    };
    $clone.remove();
    return result;
}


function collectTexts(page_num) {
	$(".not_editable_rotated_text").remove();
	
	
	var page_size = PDFTOOLS.pages_sizes[page_num-1],
		page_width = page_size.width/pt_to_mm;

	
	var items = $(`#translated_text_layer_${page_num} .text_content_element:not(.not_editable_rotated_text)`);
	
	var page_height = PDFTOOLS.pages_sizes[page_num-1].height/pt_to_mm*PDFTOOLS.bm;
	
	$.each(items, function(i,v){
		var str = $(v).html(),
			el = $(v),
			el_offset = el.getElementOffset($(`#translated_text_layer_${page_num}`));
		
		
		str = str.replace(/\s\s+/g, ' ');
		$(v).html(str);
		
		if(el_offset.top>=page_height-parseInt(el.height()) || el_offset.top<=0){
			el.remove();
		}
		
		if($(v).hasClass("not_editable_rotated_text")){
			$(v).remove();
		}
		if(str == "" || str.charCodeAt(0)==8203){
			$(v).remove();
		}
		
		if($(v).attr("text_is_bold")=='true'){
			$(v).css("font-weight", "bold");
		}
		
	});



//	var new_items = [];
//	$.each(items, function(it, item){
//		$(item).attr("totop", $(item).offset().top);
//		$(item).attr("leleft", $(item).offset().left);
//		new_items.push(item);
//	});

//	new_items = new_items.sort(function (a, b) {
//		 return $(a).attr("totop") - $(b).attr("totop") ||;
//	});

//	new_items = new_items.sort(function (a, b) {
//		 return $(a).attr("leleft") - $(b).attr("leleft");
//	});
//	
//	
//	console.log(new_items);
//	
//	alert();



	var items = $(`#translated_text_layer_${page_num} .text_content_element:not(.not_editable_rotated_text)`);
	var parent = $(`#translated_text_layer_${page_num}`);
		
	var rows = {},
		blocks = {},
		ri = 0,
		max_interval = 25;
	var prev_item = false;
	block_it = 0;
	
	
	$.each(items, function(i, item) {
		var temp_clone = $(item).clone();
		if($(item).html().length<=1 || !PDFTOOLS.testText($(item).html(), false)){
			return;
		}
		
		temp_clone.html("");

	
		if(false && $(item).overflowing(parent)){
			$(item).remove();
		}else{
			item = $(item);
			ri = parseInt(item.css("top"));

			if (prev_item) {
				
				//console.log(prev_item);
				var prev_bound = prev_item[0].getBoundingClientRect(),
					item_bound = item[0].getBoundingClientRect();
				
				
				pi_height = parseInt(prev_item.css("height"))
				pi_top = parseInt(prev_item.css("top"));
				i_width = parseInt(item[0].getBoundingClientRect().width);
				pi_width = parseInt(prev_item[0].getBoundingClientRect().width);

				i_top = parseInt(item.css("top"));
				pi_left = parseInt(prev_item.css("left"));
				i_left = parseInt(item.css("left"));
				pi_right = pi_left + prev_item[0].getBoundingClientRect().width;
				i_right = i_left + (item[0].getBoundingClientRect().width); //TODO fix for hidden

				text = item.text();
				first_l = text[0];
				first_lu = first_l.toUpperCase();

				max_interval = pi_height * 2;
				
//				console.log(item);
//				$(item).css("border", "1px solid red");
//				alert();

				
				if(
				pi_left < i_left &&
				(pi_top == i_top || inRange(pi_top, i_top, 2) || inRange(i_top, pi_top, 2)) && //TODO добавить проверку на фонт сайз и прочую хуергу.
				(pi_right*1.1>=i_left)  // проверяем растояние между блоками, что бы не было слишком много
				){
					var new_html = prev_item.html()+" "+item.html();
					
					var space = item_bound.left-prev_bound.right ;
					
					prev_item.css({
						transform: "scaleX(1)",
						width: prev_bound.width+item_bound.width+space
					});
					
					//alert(prev_bound.width+item_bound.width);
					
					prev_item.html(new_html.replace("  ", " "));
					item.remove();
					item = prev_item;
				}
			}
			
			prev_item = item;
		}
	});
	
	
	
	
	
	var positions = {};
	
	function findInPosition(ele, positions, uniq_id, dbg){
//		console.log(tb);
//		console.log(positions);
//		alert("here");
		if(!positions){
			return true;
		}
		
//		if(ele.html=='$97,257.43'){
//		
//			console.log(ele);
//			console.log(positions);
//		
//			alert("ep");
//		}
		
		
		var flag = true;
		$.each(positions, function(i, po){
			if(i!= uniq_id && ele.top<=po.top && ele.bottom>=po.bottom && parseInt(ele.left)==parseInt(po.left)){
				flag = false;
				return false;
			}
		});
		
		return flag;
	}
	
	
	//remove

  	//======================== ВТОРОЙ ПРОХОД ОБЬЕДЕНЯМ СТРОКИ ====================	
	var prev_item = false;
	var items = $(`#translated_text_layer_${page_num} .text_content_element:not(.not_editable_rotated_text)`);

	$.each(items, function(i, item) {

		if($(item).html().length<=1){
			return;
		}

		
		if(false && $(item).overflowing(parent)){
			$(item).remove();
		}else{
			item = $(item);
			ri = parseInt(item.css("top"));
			var item_bound = item[0].getBoundingClientRect();

			if (prev_item) {
				pi_height = parseInt(prev_item.css("height"))
				pi_top = parseInt(prev_item.css("top"));
				var pi_bot = pi_top+parseInt(prev_item.css("height"));
				
				
				var prev_bound = prev_item[0].getBoundingClientRect()
				
				i_width = parseInt(item[0].getBoundingClientRect().width);
				pi_width = parseInt(prev_item[0].getBoundingClientRect().width);

				i_top = parseInt(item.css("top"));
				pi_left = parseInt(prev_item.css("left"));
				i_left = parseInt(item.css("left"));
				pi_right = pi_left + prev_item[0].getBoundingClientRect().width;
				i_right = i_left + (item[0].getBoundingClientRect().width); //TODO fix for hidden

				text = item.text();
				first_l = text[0];
				first_lu = first_l.toUpperCase();
				
				
				var pi_font = parseInt(prev_item.css("font-size"))
				var i_font = parseInt(item.css("font-size"));


				max_interval = parseInt(prev_item.css("font-size")) * 1.1;
				
//				console.log(item);
//				$(item).css("border", "1px solid blue");
//				alert();




				if (
					prev_item &&
					(
						pi_left == i_left &&
						pi_top != i_top &&
						pi_right != i_right && //если текст не выровнян по правому краю
						pi_bot + max_interval >= i_top && //растояние до строки в диапазоне.
						pi_top < i_top && //текущий элемент ниже предыдущего. 
						pi_font==i_font
						
					)
				) {
					
					var total_lines = prev_item.html().split(/\r\n|\r|\n/).length
					var uniq_id = prev_item.attr("text_element_uniq_id");
					
					var prev_top = prev_item.offset().top;
					var item_bot = item.offset().top+item.height();
					
					
					if(item_bot-prev_top<0){
						var lh=1;
					}else{
						var lh = ((item_bot-prev_top)/(total_lines+1));
					}

					//width
					var new_width = Math.max(item_bound.width, prev_bound.width);
					var new_height = item_bound.bottom-prev_bound.top;
					
						

					var temp_bound = {top: parseInt(prev_bound.top), bottom: parseInt(prev_bound.top+new_height), left: prev_bound.left, right: prev_bound.left+new_width, html: prev_item.html(), el: prev_item};
					if(!findInPosition(temp_bound, positions, uniq_id, prev_item)){
						block_it++;
						return;
					}

//					console.log(prev_item, item);
//					console.log(prev_bound.width, item_bound.width);
//					$(item).css("border", "1px solid blue");
//					alert("max from : "+item_bound.width+" &  "+prev_bound.width+" = "+new_width);



					var new_html = prev_item.html()+"\r\n"+item.html();
					
					prev_item.html(new_html.replace("  ", " "));
					
					prev_item.css({
						"line-height": lh+"px", 
						width: new_width,
						height: new_height,
						"transform": "none",
					});

					positions[prev_item.attr("text_element_uniq_id")] = {
						el: prev_item,
						top: parseInt(prev_bound.top), 
						bottom:parseInt(prev_bound.top+new_height),
						left: parseInt(prev_bound.left),
						right: parseInt(prev_bound.left+new_width)
					};

					
					
					
					item.remove();
					item = prev_item;

				} else {


					positions[prev_item.attr("text_element_uniq_id")] = {
						el: prev_item,
						top: parseInt(prev_bound.top), 
						bottom:parseInt(prev_bound.top+prev_bound.height),
						left: parseInt(prev_bound.left),
						right: parseInt(prev_bound.left+prev_bound.width)
					};



					block_it++;
				}
			}
			prev_item = item;
		}
	});
	
	
	window.sa = positions;



	var items = $(`#translated_text_layer_${page_num} .text_content_element:not(.not_editable_rotated_text)`);
	var blocks = [];
	
	$.each(items, function(i,item){
		try{
			var scale = parseFloat($(item).get(0).style.transform.match(/\d+\.\d+/)[0]);
		}catch(e){
			var scale = 1;
		}
		var cls = `text_${page_num}_${i}`;
		item = $(item);
		var block_it = i;
		if (typeof blocks[block_it] == 'undefined') {
			blocks[block_it] = {};
		}
		item.addClass(cls)
		
		
		var bw = parseFloat(item[0].getBoundingClientRect().width);
		var bl = parseFloat(item[0].getBoundingClientRect().left);
		
		blocks[block_it] = {
			text: item.text().replace(/\t/g, " ").replace(/[\s]+/g, " "),
	  		element: "."+cls,
	  		full_text: item.text(),
			size: {
				l: parseFloat(item.css("left")),
				t: parseFloat(item.css("top")),
				w: parseFloat(item[0].getBoundingClientRect().width),
				h: parseFloat(item.css("height")),
				scale: scale,
				temp: item
			}
		};
	
	});
	
	$.each(blocks, function(i,b){
		if(b.text.length==1){
			delete blocks[i];
		}
	
	});
	return blocks;

}


(function($){
  $.fn.overflowing = function(options, callback){
    var self = this
    var overflowed = []
    var hasCallback = callback && typeof callback === 'function' ? true : false;
    var status = false
    this.options = options || window

    this.each(function(){
      if ($.isWindow(this)) return false
      var $this = $(this)
      elPosition = $this.position()
      elWidth = $this.width()
      elHeight = $this.height()
      var parents = $this.parentsUntil(self.options)
      var $parentsTo = $(self.options)
      parents.push($parentsTo)

      for(var i=0; i<parents.length; i++){
        var $parent = $(parents[i])
        if ($.isWindow($parent[0])) break
        var absPosition = !!~['absolute', 'fixed'].indexOf($parent.css('position'))
        var parentPosition = $parent.position()
        var parentWidth = $parent.width()
        var parentHeight = $parent.height()
        var parentToBottom = absPosition ? parentHeight : (parentHeight+parentPosition.top)
        var parentToRight = absPosition ? parentWidth : (parentWidth+parentPosition.left)

        if ( elPosition.top < 0
        || elPosition.left < 0
        || elPosition.top > parentToBottom
        || elPosition.left > parentToRight
        || (elPosition.top + elHeight) > parentToBottom
        || (elPosition.left + elWidth) > parentToRight) {
          status = true
          $(parents[i]).addClass('overflowed')
          overflowed.push(parents[i])
          if (hasCallback) callback(parents[i])
        }
      }

      if($this.parents(self.options).hasClass('overflowed')) $this.addClass('overflowing')
    })

    if (!hasCallback) return overflowed.length > 1 ? overflowed : status
  }

})(jQuery)




Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}



$(document).on("last_render", function(){
	$("#translate_all").removeClass("hidden");
	
	$(".free_count").html(pricing.free_count);
	$(".price_count_1").html(pricing.translate_pricing);
	$(".price_count_2").html(pricing.translate_count);


	
	var x = PDFTOOLS.getPriceRange(total_chars)
	
	if(price = PDFTOOLS.getPriceRange(total_chars)){
		$(".show_if_free_translate").addClass("hidden");
		$(".show_if_paid_translate").removeClass("hidden");
		$(".pricing").html(price);
	}else{
		$(".show_if_paid_translate").addClass("hidden");
		$(".show_if_free_translate").removeClass("hidden");		
	}
	
	$(function() {
	
		$('.pdf_files_slider canvas.page_canvas').each(function(i,v){
			setTimeout(function(){
				var img_data = v.toDataURL('image/png');
				
				var template = `						
				<div style='cursor: pointer;' class="t-block-item" onclick='$(".pdf_files_slider").scrollTo($("#translate_preview_${i+1}"), 300)'>
					<div class="img-wrap">
						<img style='height: 166px; max-height: 166px;' src="${img_data}" alt="">
						<div class="item-number">${i+1}</div>
					</div>
				</div>
				`;
				$("#thumbs_block").append(template);
				
				
			},1000)
		});



//		$('.pdf_files_slider').slick({
//		    centerMode: true,
//		    variableWidth: true,
//		    centerPadding: '0px',
//		    slidesToShow: 3,
//		    slidesToScroll: 1,
//		    infinite: true,
//		    prevArrow: '<button type="button" class="slick-prev"><img src="img/pdf_slider_prev.svg" alt=""></button>',
//		    nextArrow: '<button type="button" class="slick-next"><img src="img/pdf_slider_next.svg" alt=""></button>',
//		    responsive: [
//		      {
//		        breakpoint: 768,
//		        settings: {
//		          centerMode: false,
//		          centerPadding: '0px',
//		          slidesToShow: 1,
//		          variableWidth: false,
//		        }
//		      },
//		    ]
//		  });

//		$('#thumbs_block').slick({
//			slidesToShow: 5,
//			slidesToScroll: 1,
//			arrows: false,
//			dots: false,
//			infinite: false,
//			focusOnSelect: true,
//			asNavFor: '.pdf_files_slider',
//		});


		  $(".creditcard_pop_open").click(function() {
		    $('.credit_card_popup_wrp.one').addClass("active");
		    $('body').addClass("credit_card_popup_opens");
		    return false;
		  });

		  $(".credit_card_close_wrp").click(function() {
		    $('.credit_card_popup_wrp.one').removeClass("active");
		    $('body').removeClass("credit_card_popup_opens");
		  });
		  $(".mobile_close_button").click(function() {
		    $('.credit_card_popup_wrp.one').removeClass("active");
		    $('body').removeClass("credit_card_popup_opens");
		  });

		  $('.popup_card_number').inputmask("9999 9999 9999 9999",{ "oncomplete": function(){ $('.popup_card_mmyy').focus();} });
		  $('.popup_card_mmyy').inputmask("99/99",{ "oncomplete": function(){ $('.popup_card_cvc').focus();} });
		  $('.popup_card_cvc').inputmask("999");

		$(".popup_credit_continue").click(function() {
		    $('.credit_card_popup_wrp.two').removeClass("active");
		    $('body').removeClass("credit_card_popup_opens");

		    $('.pay-and-translate-action-box').removeClass("show");

		    $('.translated-doc-box.loading-in-process').addClass("show");

		    setTimeout( function() {
		        $('.translated-doc-box').removeClass("loading-in-process");
		    }, 2000);

		    return false;
		});

	});
});

const range = (start, stop, step = 1) =>
  Array(Math.ceil((stop - start) / step)).fill(start).map((x, y) => x + y * step)
  
  
function inRange(x, y, rr=5){
	x = parseInt(x);
	y = parseInt(y);
	var r = range(x-rr, x+1);
	for(i=0; i!=r.length; i++){
		if(r[i]==y){
			return true;
		}
	}

	return false;
}  
  
  
function showLoading1(parent){
	var l = `<div id='loading1'>
	<div class="img-loading">
		<img src="img/spinner-of-dots.svg" alt="">
	</div>
	</div>
	`;
	
	parent.append(l);
	
}
  
function hideLoading(){
	$("#loading1").remove();
}  
  
  
  
function downloadURI(uri, name) {
	var link = document.createElement("a");
	link.download = name;
	link.href = uri;
	$(link).attr("download", name)
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
	delete link;
}
  
jQuery.fn.scrollTo = function(elem, speed) { $(this).animate({ scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top }, speed == undefined ? 1000 : speed); return this; };





  
