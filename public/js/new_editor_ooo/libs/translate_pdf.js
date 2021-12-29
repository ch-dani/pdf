var current_range = false;
var PDFTOOLS = {
	name: "translatedocx_new"
}
var skip_simple_viewer = true;
var document_type = "pdf";

if(typeof operation_id=='undefined'){ 
	operation_id = guid();
}

$(document).ready(function(){
	//$(".target_language").val(navigator.language);
});


$(document).on("click", ".download-result-link", function(e){
	//e.preventDefault(); 
	//saveBlob(viewer.edited_blob, "edited_"+viewer.pdf_name);
});

$(document).on("click", "#download_file", async function(e){
	if(document_type=='pdf'){
		e.preventDefault();
		$("#apply-popup").addClass("active");

		if(spe.upload_in_progress===true){
			window.awaitDownloadInterval = setInterval(()=>{
				if(!spe.upload_in_progress){
					clearInterval(window.awaitDownloadInterval);
					createPdfOnServer();
				}
				console.log("wait upload");
			},1000);
		}else{
			await createPdfOnServer();
		}
	}else{
		window.location = $(this).attr("src");
	}
});

$(document).on("page_proccess", function(e, page, total_pages){
	$(".current_page").html(page);
	$(".total_pages").html(total_pages)
	window.total_pages = total_pages;
	if(page==total_pages){
		$(".document_render_proccess").html("Language detect...");
	}
});


$(document).on("click", "#translate_all", function(){

	if($(".source_language").val() == $(".target_language").val()){
		Swal.fire({
			type: 'error',
			title: 'Error',
			text: "Please select different language rather than original",
		})
		return false;
	}

	$(".t-right-bottom-panel").hide()
	runTranslate();
});

$(".creditcard_pop_open").click(function() {
	$('.credit_card_popup_wrp.one').addClass("active");
	$('body').addClass("credit_card_popup_opens");
	return false;
});


var temp_interval = false;
var temp_interval_page = 0;
$(document).on("translate_proggress", function(e, percent, translated_count){
	if(percent>=100){
		if(!temp_interval){
			temp_interval = setInterval(function(){
				if(temp_interval_page>=total_pages){
					clearInterval(temp_interval);
					temp_interval = false;
					//$(".translate_percent_progress").html("Generating pages <span style='color: #7651dd'>"+(total_pages)+ "</span> out of <span style='color: #7651dd'>"+total_pages+"</span>");
					return false;
				}
				temp_interval_page++
				$(".translate_percent_progress").html("Generating pages <span style='color: #7651dd'>"+(temp_interval_page)+ "</span> out of <span style='color: #7651dd'>"+total_pages+"</span>");
			},500);
		}
	}else{
		$(".translate_percent_progress").html(percent+"%");
	}
});
window.total_chars = 0;
window.texts_for_translate = {};

$(document).on("page_filled_", function(e, pn){
	console.log("page filled", pn);
	var pagenum = pn;
	var page = viewer.pages[pn];

	var texts = page.fcanvas.getObjects("i-text");
	$.each(texts, (it, text) => {
		var unicode_lines = trans.pdfTextToUnicode(text);
		if (unicode_lines){ 
			if (typeof texts_for_translate[pagenum] == 'undefined') {
				texts_for_translate[pagenum] = [];
			};
			texts_for_translate[pagenum].push(unicode_lines)
		};
	});

	$.each(viewer.pages[pn].fcanvas.getObjects("i-text"), (i2, texts)=>{
		window.total_chars += texts.unicode_string.length;
		$(".total_chars").html(total_chars)
	});



});

$(document).on("last_page_rendered", async function (e){
	$(".document_render_proccess").addClass("hidden");
	$(".language_choise").removeClass("hidden");
	// $.each(viewer.pages, (i, page)=>{
	// 	$.each(page.fcanvas.getObjects("i-text"), (i2, texts)=>{
	// 		total_chars += texts.unicode_string.length;
	// 		$(".total_chars").html(total_chars)
	// 	});
	// });
	if(price = await trans.getPriceRange(total_chars)){
		$(".show_if_free_translate").addClass("hidden");
		$(".show_if_paid_translate").removeClass("hidden");
		$(".pricing").html(price);
	}else{
		$(".show_if_paid_translate").addClass("hidden");
		$(".show_if_free_translate").removeClass("hidden");
	};
	
	$('.pdf_files_slider canvas.page_canvas').each(function(i,v){
		//return false;
		var img_data = v.toDataURL('image/png');

		if(i<5){
			var template = `						
			<div style='cursor: pointer;' class="t-block-item" onclick='$(".pdf_files_slider").scrollTo($("#translate_preview_${i+1}"), 300)'>
				<div class="img-wrap">
					<img style='height: 166px; max-height: 166px;' src="${img_data}" alt="">
					<div class="item-number">${i+1}</div>
				</div>
			</div>
			`;
			$("#thumbs_block").append(template);
		}
	});
	console.log("language detect");
	//detectLanguage();
});
var document_texts = false;

$(document).on("file_selected", async function(e, file, uploadPromise){
	//window.viewer.init("/", false);
	viewer.scale = 1;
	viewer.pdf_name = (file.name);

	if(file.type=='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
		document_type = "docx";
		$(".translate-left").css("padding-bottom", "0");
		$(".translate-block-items-wrap").css("display", "none");
		$(".pdf_files_slider").css("height", "calc(100vh - 30px)")
		$(".translate-left").css("overflow", "hidden");
		$("#pages_previews_here").css("overflow", "hidden");
		$(".document_render_proccess").addClass("hidden");
		$(".language_choise").removeClass("hidden");

		var resp = await spe.uploadFile(file, "DOCX", false);
		document_texts = await trans.parseDocx(resp.document_content);
		await trans.createTempDocxPage(document_texts);
		trans.renderDocx(window.location.origin+"/uploads/docx/"+resp.file_path2);

		detectLanguage();
	}else{
		document_type = "pdf";
		pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/new_editor/libs/pdfjs-dist/build/pdf.worker.js';
		$(".after_upload").removeClass("hidden");
		$(".before_upload").addClass("hidden")
		var data = await viewer.getBlob(file);

		trans.renderPdf(data);
		var resp = await spe.uploadFile(file, "PDFTranslate", false);
		viewer.server_path = resp.file_path;
	}
});


const pick = (...props) => o => props.reduce((a, e) => ({ ...a, [e]: o[e] }), {});

var create_interval = false;
var create_page_num = 0;

async function createPdfOnServer(){
	$("#apply-popup").addClass("active");
	var pages_content = {};
	var props = ["width", "height", "left", "top", "text", "fontSize", "unicode_string", "fill"];

	$.each(viewer.pages, (i,page)=>{
		var tpage = {
			width: page.fcanvas.width,
			height: page.fcanvas.height,
			left: page.fcanvas.left,
			top: page.fcanvas.top,
			objects: []
		};

		page.fcanvas.getObjects().forEach((obj)=>{
			tpage.objects.push(pick(...props)(obj));
		});
		
		page_json = tpage;

		page_json.width = page.fcanvas.width
		page_json.height = page.fcanvas.height;
		pages_content[i]= (page_json);
	});

	var data = {
		_token: $("[name='csrf-token']").attr("content"),
		operation_id: operation_id,
		uuid: UUID,
		file_name: viewer.pdf_name,
		pages: pages_content
	};

	
	try{

		create_interval = setInterval(function(){
			create_page_num++;
			if(create_page_num>total_pages){
				clearInterval(create_interval);
				return false;
			}
			$(".current_speed_and_percent").removeClass("hidden");
			$(".current_speed_and_percent").html("Saving page <span style='color: #7651dd'>"+create_page_num+"</span> out of <span style='color: #7651dd'>"+total_pages+"</span>");

		}, 300);

		data['server_path'] = viewer.server_path;
		data['scale'] = viewer.scale;

		const resp = await fetch("/create_translate_pdf", {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': $("[name='csrf-token']").attr("content"),
				"Content-Encoding":"zlib"
			},
			body: JSON.stringify(data)
		}).then((b)=>{ return b.json(); });

		if(resp.success){
			$(".creating_document").hide();
			$(".create_file_box").show();
			$(".result-top-line .download_file_name").html(resp.file_name);
			$(".download-result-link").attr({"href": window.origin+"/"+resp.url, "download": resp.file_name});
			$("#save-dropbox").attr({'data-url': window.origin+"/"+resp.url, 'data-file_name': resp.file_name});
			$("#save-gdrive").attr({'data-src': window.origin+"/"+resp.url, 'data-filename': resp.file_name});
			$(".modal-header").removeClass("hidden");
		}
	}catch(e){
		alert("error");
		return false;
	}
	// $.ajax({
	// 	method: "POST",
	// 	url: "/create_translate_pdf",
	// 	type: "json",
	// 	data:{
	// 		_token: $("[name='csrf-token']").attr("content"),
	// 		operation_id: operation_id,
	// 		uuid: UUID,
	// 		file_name: viewer.pdf_name,
	// 		pages: pages_content
	// 	},
	// 	error: function(){
	// 		alert("error");
	// 	},
	// 	success: function(resp){

	// 		alert("success");
	// 		// if(resp.success){
	// 		// 	$(".creating_document").hide();
	// 		// 	$(".create_file_box").show();
	// 		// 	$(".result-top-line .download_file_name").html(resp.file_name);
	// 		// 	$(".download-result-link").attr({"href": window.origin+"/"+resp.url, "download": resp.file_name});
	// 		// 	$("#save-dropbox").attr({'data-url': window.origin+"/"+resp.url, 'data-file_name': resp.file_name});
	// 		// 	$("#save-gdrive").attr({'data-src': window.origin+"/"+resp.url, 'data-filename': resp.file_name});
	// 		// 	$(".modal-header").removeClass("hidden");
	// 		// }
	// 	}
	// });

	


}

let Translator = class {
	constructor(from="", to="") {
		this.token = operation_id;
		this.from = $(".lang_source").val();
		this.to = $(".target_language option:selected").val();
		this.translate_url = false;
		this.iframe = false;
		$("body").on("after_google_translate", (e,texts)=>{
			//alert("after google translate");
			switch(document_type){
				case 'pdf':
					this.fillPDF(e,texts);
				break;
				case 'docx':
					this.fillDocx(e, texts);
				break;
			}
		});

		$("body").on("lang_detected", (e,lang)=>{
			$(".source_language").val(lang);
			if(navigator.language.split("-")[0]==lang){
				//$(".target_language").val("en");
			}
			console.log("lang_detected", lang);


			//$("#detect_canvas").remove();
		});
	};

	set translateFrom(val){
		this.from = val;
	};
	
	set translateTo(val){
		this.to = val;
	};

	isANumber( n ) {
		var numStr = /^-?(\d+\.?\d*)$|(\d*\.?\d+)$/;
		return numStr.test( n.toString() );
	};
	



	async parseDocx(xml){
	
		window.temp_xml = xml;
	
		var document_texts = {},
			parser = new DOMParser()
//			doc1 = parser.parseFromString(xml, "text/xml"),
//			paragraphs = doc1.getElementsByTagName("w:p"),
		
		var total_chars = 0;


		var regex = /<w:p[^>]*>(.*?)<\/w:p>/gi;
		var paragraphs = xml.match(regex);


		var text_iterator = 0;;
		$.each(paragraphs, (i,v)=>{
			var textContent = $(v).text();

			if(typeof document_texts[text_iterator]=='undefined'){
				document_texts[text_iterator] = {
					text: [],
					original: []
				};
			};
			if(textContent && !this.isANumber(textContent)){
				total_chars += textContent.length;
				document_texts[text_iterator].text.push(textContent);
				document_texts[text_iterator].original.push(v);
				text_iterator++;;
			}
		});

		let price = await trans.getPriceRange(total_chars);
		$(".total_chars").html(total_chars);

		if(price){
			$(".show_if_free_translate").addClass("hidden");
			$(".show_if_paid_translate").removeClass("hidden");
			$(".pricing").html(price);
		}else{
			$(".show_if_paid_translate").addClass("hidden");
			$(".show_if_free_translate").removeClass("hidden");
		}
		//TODO fix empty strings
		return document_texts;
	};	



	renderDocx(url){

		$('.before_upload').addClass('hidden');
		$('.after_upload').removeClass('hidden');

		// $(".after_translate").removeClass("hidden");
		// $(".translate_proggress").addClass("hidden");
//		$(document).trigger("after_translate_docx");
//		hideLoading();

		this.updateDocxPreview(url);

	};


	updateDocxPreview(url){
		var tts = new Date().getTime();
		$("#pages_previews_here").html("");
		$("#pages_previews_here").append("<div class='docx_loading'><dic class='docx_loader'></div></div>");
		
		$("#pages_previews_here").append(`<iframe 
			style='width: 100%; height: 100%;'
			class="doc" src="https://view.officeapps.live.com/op/embed.aspx?src=${url}?tts=${tts}&embedded=true"></iframe>
		`);
		setTimeout(function(){
			$(".docx_loading").remove();
		},3000);

	};

	renderPdf(file){


		render_in_slider = true;
		blocker.hideUploader();
		blocker.showEditor();
		window.viewer.init(false, file);
	};


	pdfTextToUnicode(obj){
		var styles = obj.styles;
		var plines = {};
		$.each(obj.textLines, (ln, line)=>{
			for(let tn=0; tn!=line.length; tn++){
				if(typeof styles[ln][tn]=='undefined'){
					continue;
				}
				var char = line[tn];
				try{
					var char_font_family = styles[ln][tn].fontFamily.split(",")[0];
				}catch(e){

				}
				try{
					var unicode_char = viewer.chars_table_inverse[char_font_family][char];
				}catch(error){
					var unicode_char = char;
				}
				if(typeof unicode_char=='undefined'){
					unicode_char = char;
				}
				if(typeof plines[ln]=='undefined'){
					plines[ln] = "";
				}
				plines[ln]+= (unicode_char);
			}
		});
		return plines;
	};
	calculateFontSize(width, height, content){
		var area = width*height;
		var contentLength = content.length;
		return  Math.sqrt(area/contentLength); //this provides the font-size in points.
	};
	replaceItext(obj, text){
		var fill = "rgb(0,0,0)";
		var dominant_colors = {};
		dominant_colors['rgb(0,0,0)'] = 1;
		var font_styles = {};

		if(typeof obj.styles!='undefined' && obj.styles.length){

			obj.styles.forEach((line)=>{
				line.forEach((style)=>{
					if(typeof style.fill!='undefined'){
						if(typeof dominant_colors[style.fill]=='undefined'){
							dominant_colors[style.fill] = 0;
						}
						dominant_colors[style.fill]++;
					}

					if(typeof style.fontFamily!='undefined' &&  style.fontFamily.toLowerCase().indexOf("bold")!=-1){
						font_styles['fontWeight'] = "bold";
					}

					if(typeof style.fontFamily!='undefined' &&  style.fontFamily.toLowerCase().indexOf("italic")!=-1){
						font_styles['fontStyle'] = "italic";
					}

				});
			})
		}
		var fill = Object.keys(dominant_colors).reduce((a, b) => dominant_colors[a] > dominant_colors[b] ? a : b);


		var fontSize = (obj.height/text.length/1.5*obj.lineHeight)+0.5;
		//alert(fontSize);
		//var fontSize = this.calculateFontSize(obj.width,obj.height, text);
		var fontSize  = obj.fontSize;
		text = text.join("");
		
		return new fabric.IText(text, {
			...font_styles,
			left: obj.left,
			top: obj.top,
			width: obj.width,
			height: obj.height,
			borderColor: 'red',
			fixedWidth: obj.width,
			fixedHeight: obj.height,
			fixedFontSize: obj.fontSize,
			fontSize: fontSize,
			fontFamily: "Verdana",
			fill: fill,
			originX:"left",
			originY: "top",
			lineHeight: 1.0,
			selectable: true,
			editable: true,
	//fontSize: 16,
			//textAlign: 'center'
		});
	}
	
	async fillDocx(e, texts){
		$(".translate_percent_progress").html("Create document");

		this.ajax_data = {"texts": texts};
		var resp = await this.sendAjax(`/fill_docx/${operation_id}`, {"texts": texts});
		$("#download_file").attr("src", resp.download_url);
		$("#download_file").unbind("click");
		$(".translate_proggress").addClass("hidden");
		$(".after_translate").removeClass("hidden");
		this.updateDocxPreview(window.location.origin+"/"+resp.download_url);
	}

	calculateFontSize(width, height, content){
		var area = width*height;
		var contentLength = content.length;
   
		return  Math.sqrt(area/contentLength); //this provides the font-size in points.
   
   }

	fillPDF(e, texts){
		var that = this;
		$.each(texts, (pn, page)=>{
			console.log("fill page ", pn);
			var fcanvas = viewer.pages[pn].fcanvas;
			var page_texts = viewer.pages[pn].fcanvas.getObjects("i-text");
			
			//console.log("page_texts",page_texts);

			$.each(page, (tn, text)=>{
				var text_obj = page_texts[tn];
				var textbox = this.replaceItext(page_texts[tn], page[tn]);
				//heig
				var fsFlag = false;
				// if(textbox.width>textbox.fixedWidth){
				// 	fsFlag = true;
				// 	textbox.originalFontSize = textbox.fontSize;
				// 	textbox.fontSize *= (textbox.fixedWidth/(textbox.width));
				// }
				// if(textbox.height>textbox.fixedHeight){
				// 	textbox.originalFontSize = textbox.fontSize;
				// 	textbox.fontSize *= (textbox.fixedHeight/(textbox.height));
				// }
				textbox.fontSize = (this.calculateFontSize(textbox.fixedWidth, textbox.fixedHeight, textbox.text));
				if(textbox.text.length){
					for(let z=0; z<textbox.text.length/2; z++){
						if(textbox.text[z].match(/[\u4E00-\u9FCC]/)){
							var tt = textbox.fontSize / 1.5;
							var t2 = textbox.fontSize-tt;

							textbox.fontSize = tt;
							//textbox.top -= t2;
							break;
						}
						
					}
				}
				//textbox.fontSize -=1;
				if(textbox.fontSize<1){
					textbox.fontSize = 1;
				}

				fcanvas.remove(page_texts[tn]);
				if(textbox.text && textbox.width>1 && textbox.height>1){
					fcanvas.add(textbox);
					// textbox.set({
					// 	height: textbox.fixedHeight
					// });
					
				}
			});
			if($("#page_outer_"+pn).length>0 && $("#page_outer_"+pn).isOnScreen()){
				viewer.pages[pn].fcanvas.initialRendered = true;
				viewer.pages[pn].fcanvas.renderAll();
			}else{
				viewer.pages[pn].fcanvas.initialRendered = false;

			}
			//$("#pages_previews_here").scroll();

		});

		$(".translate_proggress").addClass("hidden");
		$(".after_translate").removeClass("hidden");
	}

	get iframeObject(){
		return this.iframe;
	};

	get translateUrl(){
		return this.translate_url;
	};

	async createTempDocxPage(texts_for_translate){ 
		var data = {texts: texts_for_translate, type: "docx"};
		this.ajax_data = data;

		this.from = $(".lang_source").val();
		this.to = $(".target_language option:selected").val();

		var resp = await this.sendAjax(`/create_translate_page/${operation_id}`, data);
		if("success" in resp && resp.success==true){
			this.translate_url = `/new_translate/${resp.token}?from=${this.from}#googtrans(${this.from}|${this.to})`;
			return true;
		}else{
			alert("Error 101");
			return false;
		}
	};

	async createTempPDFPage () {
		// var texts_for_translate = {};

		// $.each(viewer.pages, (pagenum, page) => {
		// 	var texts = page.fcanvas.getObjects("i-text");
		// 	$.each(texts, (it, text) => {
		// 		var unicode_lines = this.pdfTextToUnicode(text);
		// 		if (unicode_lines){ 
		// 			if (typeof texts_for_translate[pagenum] == 'undefined') {
		// 				texts_for_translate[pagenum] = [];
		// 			};
		// 			texts_for_translate[pagenum].push(unicode_lines)
		// 		};
		// 		//return false;
		// 	});
		// 	//return false;
		// });
		var data = {texts: texts_for_translate, type: "pdf"};
		this.ajax_data = data; //for safari

		this.from = $(".lang_source").val();
		this.to = $(".target_language option:selected").val();

		var resp = await this.sendAjax(`/create_translate_page/${operation_id}`, data);
		if("success" in resp && resp.success==true){
			this.translate_url = `/new_translate/${resp.token}?from=${this.from}`;
			return resp;
		}else{
			alert("Error 101");
			return false;
		}
	};
	async createIframe(){

		this.from = $(".lang_source").val();
		this.to = $(".target_language option:selected").val();


		this.iframe = $(`<iframe id='trans_canvas' style='position: fixed; left: 0; top: 0; width: 98%; height: 98%; opacity: 0; z-index: 1000; pointer-events: none; ' src='${this.translate_url}#googtrans(${this.from}|${this.to})' />`);
		$("body").prepend(this.iframe);
	}
	
	async getPriceRange(words=0){
		if(words<pricing.free_count && !window.need_payment){
			return 0;
		}
		current_range = false;
		//current
		if(window.need_payment){
			$(".not_blocked_transalte").addClass("hidden");
			$(".blocked_translate").removeClass("hidden");
			var price = (((words)/pricing.translate_count)*pricing.translate_pricing).toFixed(2);
			if(parseFloat(price)<0.51){
				price = 0.51;
			}
			current_range = {price: price, count: words};
			if(parseFloat(price)>0.51){
				$(".free_count").html(pricing.free_count);
				$(".price_count_1").html(pricing.translate_pricing);
				$(".price_count_2").html(pricing.translate_count);
			}
			return price;
		}


		$(".not_blocked_transalte").removeClass("hidden");
		$(".blocked_translate").addClass("hidden");


		
		if(words>0 && parseInt(words)>pricing.translate_count && pricing.translate_pricing>0){
			var price = (((words-pricing.free_count)/pricing.translate_count)*pricing.translate_pricing).toFixed(2),
				temp_price = price.split(".");

			console.log("price 1", price, temp_price);

			if(parseFloat(price)<0.51){
				price = 0.51;
			}

			current_range = {price: price, count: words};
			if(parseFloat(price)>0.51){
				$(".free_count").html(pricing.free_count);
				$(".price_count_1").html(pricing.translate_pricing);
				$(".price_count_2").html(pricing.translate_count);
			}

			if(temp_price[1]==0){
				return parseFloat(price).toFixed(2);
			}else{
				return parseFloat(price).toFixed(2);
			}
		}
		return 0.51;

		return 0;
	}



	async sendAjax (url, ajax_data){
		console.log(url, ajax_data);
		console.log(arguments);

		if(typeof ajax_data=='undefined'){
			ajax_data = this.ajax_data
		}

		if(!url || ! ajax_data){
			return Promise.reject(new Error('fail'))
		}
		var ajax_data = $.extend({_token: $('[name="csrf-token"]').attr("content")}, ajax_data);
		return $.ajax({
			url: url,
			method: "POST",
			data: ajax_data
		});
	};
	

};

trans = new Translator("en", "ru");

async function runTranslate(){
	$(".translate_percent_progress").html("0%");
	$(".translate_proggress").removeClass("hidden");
	$(".before_translate").addClass("hidden");

	trans.translateFrom = $(".lang_source").val();
	trans.translateTo = $(".target_language option:selected").val();

	switch(document_type){
		case 'pdf':
			var x = await trans.createTempPDFPage()
			console.log(x);
			trans.createIframe();
		break;
		case 'docx':
			trans.createIframe();
			//alert("translate docx");
		break;

	}
}


jQuery.fn.scrollTo = function(elem, speed) { $(this).animate({ scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top }, speed == undefined ? 1000 : speed); return this; };

if(typeof Stripe !== 'undefined'){
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
}
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
		
		
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $("[name='csrf-token']").attr("content"),
			},
			method: "POST",
			url: "/createStripeCharge",
			data: data.token,
			dataType: "json",
			success: function(data){
				hideLoading();
				$(".credit_card_popup_wrp").removeClass("active");
				$('body').removeClass("credit_card_popup_opens");
				if(data.success){
					runTranslate();
				
					// if(PDFTOOLS.name=='translatedocx'){
					// 	$("#translate_all").click();
					// }else{
					// 	PDFTOOLS.charge_id = data.payment_id;
					// 	TranslatePDF.startTranslate();
					// 	$('.credit_card_popup_wrp.one').removeClass("active");
					// 	$('body').removeClass("credit_card_popup_opens");
					// }
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
  
function detectLanguage(){

	var test_string = ""; // "London is the capital of Great Britain";
	switch(document_type){
		case 'pdf':
		$.each(viewer.pages[1].fcanvas.getObjects("i-text"), function(_i, text){
			test_string += " ||| "+ (text.unicode_string);
		})

		break;
		case 'docx':
			$.each(document_texts,function(i,t){
				if(typeof t.text!='undefined' && typeof t.text[0]!='undefined'){
					test_string += " ||| "+t.text[0];
				}
			})
		break;
	}
	test_string = test_string.substr(0, 100);
	this.iframe = $(`<iframe id='detect_canvas' style='position: fixed; left: 0; top: 0; width: 98%; height: 98%; opacity: 0; z-index: 1000; pointer-events: none; ' 
		src='/langDetect/${operation_id}?test_string=${test_string}#googtrans(auto|${navigator.language.split("-")[0]})' />`);
	$("body").prepend(this.iframe);
}


function isAnyPartOfElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    const windowHeight = (window.innerHeight || document.documentElement.clientHeight);
    const windowWidth = (window.innerWidth || document.documentElement.clientWidth);
    const vertInView = (rect.top <= windowHeight) && ((rect.top + rect.height) >= 0);
    const horInView = (rect.left <= windowWidth) && ((rect.left + rect.width) >= 0);
    return (vertInView && horInView);
}

$.fn.isOnScreen = function(){

    var win = $(window);

    var viewport = {
        top : win.scrollTop(),
        left : win.scrollLeft()
    };
    viewport.right = viewport.left + win.width();
    viewport.bottom = viewport.top + win.height();

    var bounds = this.offset();
    bounds.right = bounds.left + this.outerWidth();
    bounds.bottom = bounds.top + this.outerHeight();

    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
};

$("#pages_previews_here").on("scroll", function(){
	$(".slider_item").each(function(){
		if($(this).isOnScreen()){
			var pn = $(this).data("page-id");
			if(typeof viewer.pages[pn]!='undefined' && !viewer.pages[pn].fcanvas.initialRendered){
				viewer.pages[pn].fcanvas.initialRendered = true;
				viewer.pages[pn].fcanvas.renderAll();
				console.log("render page ", pn);
			}

		}
	})
});


