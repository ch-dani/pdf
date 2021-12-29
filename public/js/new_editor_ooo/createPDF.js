var last_baseline = 0;
var CreatePDF = function() {
	this.doc = false;
	this.texts = {};
	this.parsedFonts = [];
	this.registered_fonts = {};
	this.createDoc = async () => {
		$("#apply-popup").addClass("active");
		$("#apply-popup .modal-header").removeClass("hidden");
		$(".creating_document").show();
		$(".create_file_box").hide();
		//$(".apply_changes_1").html("Wait...");
		
		var that = this;
		//this.parsedFonts = await this.parseFonts();
		var that = this;
		pages = Object.values(viewer.pages);
		embeded_fonts = Object.values(viewer.fonts);

		var pages_array = [];
		$(".lower-canvas").each(function(i,v){
			var page_number = $(v).data("pn");
			if(typeof page_number=='undefined'){
				page_number = $(v).data("page-id");
			}
			pages_array.push(viewer.pages[page_number]);
		});


		for(i=0; i!=pages_array.length; i++){
			page = pages_array[i];

			if (!that.doc) {
				that.doc = new PDFDocument({
					compress: false,
					size: [page.width, page.height]
				});
				if(true){ //typeof render_in_slider===false){
					def = Object.values(default_fonts);
					def_keys = Object.keys(default_fonts);
					for (fi = 0; fi != def.length; fi++) {

						font_base = await base64ToBufferAsync(def[fi]);
						that.doc.registerFont(def_keys[fi], font_base);
					};
					for (fi = 0; fi != embeded_fonts.length; fi++) {
						var font = embeded_fonts[fi];
						font_base = await base64ToBufferAsync(font.base64);
						var emb_name = "emb_" + font.niceName+"_"+fi;
						emb_name = font.loadedName;
						that.doc.registerFont(emb_name, font_base);
						this.registered_fonts[font.loadedName] = emb_name;
					};
				}
				this.doc.initForm();
			}else{
				that.doc.addPage({size: [page.width, page.height]});
			}

			if (false && viewer.bg) {
				that.doc.image(viewer.bg.src, 0, 0, {
					width: viewer.bg.width,
					height: viewer.bg.height
				});
			};
			
			//images = Object.values(page.fabricImages);

			await this.proccessElements(page);

			// for (im = 0; im != images.length; im++) {
			// 	var image = images[im];
			// 	img_base64 = await awaitImage(image.src);
			// 	that.doc.image(img_base64, image.left, image.top, {
			// 		width: image.width,
			// 		height: image.height
			// 	});
			// }
			//await this.parseTexts(page);
		}
		this.save();
	};

	this.proccessElements = async (page)=>{
		var objects = page.fcanvas.getObjects();
		if(render_in_slider){
			this.doc.image(page.fcanvas.bg, 0, 0, {
				width: page.width,
				height: page.height
			});
		}

		


		//$.each(objects, async (i,obj)=>{
		for(let i=0; i!=objects.length; i++){
			let obj = objects[i];
			console.log("obj type", obj.type);
			//window.open(obj.toDataURL());

			if(obj.type!='i-text'){
				//return;
			}
			
			switch(obj.type){
				case 'i-text':
					this.parseText(obj);
				break;
				case 'arrow':
					bound = obj.getBoundingRect();

					this.doc.highlight(bound.left, bound.top-obj.strokeWidth, obj.width, obj.strokeWidth, {height: 100, color: obj.fill, "T": new String(obj.title), "Contents": new String(obj.contents)});
				break;
				case 'path':
					if(obj.subtype=='rect'){
						this.saveRect(obj);
					}else{
						this.saveRect(obj);
//						alert("unknow "+obj.subtype);
					}
				break;
				case 'image':
				case 'group':
					//window.open(obj.toDataURL());
					if(obj.subtype=='image_group' || obj.subtype=='image'){
						
						var img_base64 = await obj.toDataURL({multiplier: window.dpr}); //await awaitImage(image.src);
						var brect = obj.getBoundingRect();
						//console.log("obj is", obj);
						//window.open(img_base64);
						
						this.doc.image(img_base64, obj.left, obj.top, {
							width: brect.width,
							height: brect.height
						});
					}
				break;
				case 'ellipse':
					obj64 = obj.toDataURL(); //await awaitImage(image.src);
					bound = obj.getBoundingRect();
					
					this.doc.image(obj64, bound.left, bound.top, {
						width: obj.width*obj.scaleX,
						height: obj.height*obj.scaleY
					});
				break;
				
				case 'rect': 
					field_name = "fieldName" in obj?obj.fieldName.replace(/[^a-zA-Z0-9]/g, "_"):"field"+i;
					switch(obj.subtype){
						case 'whiteout':
						case 'rectangle':
							bound = obj.getBoundingRect();
							//TODO возможно стоит сохранять как векторы
							obj64 = obj.toDataURL(); //await awaitImage(image.src);
							this.doc.image(obj64, bound.left, bound.top, {
								width: obj.width*obj.scaleX,
								height: obj.height*obj.scaleY
							});

						break;
						case 'highlight':
						case 'Highlight':
							this.doc.highlight(obj.left, obj.top, obj.width, obj.height, {color: rgb2hex(obj.fill), "T": new String(obj.title), "Contents": new String(obj.contents)});
						break;
						
						case 'annotate':
							if(obj.annotation_type=='StrikeOut'){
								this.doc.strike(obj.left, obj.top, obj.width, 55, {color: "red"});
							}else{
								this.doc.highlight(obj.left, obj.top, obj.width, obj.height, {color: "red"});
							}
						break;

						case 'form_element_outer':
							this.proccessForm(obj);
						break;
						case 'link':
							if("url" in obj){
								if(obj.url.search(new RegExp("http:\/\/|https:\/\/"))===-1){
									obj.url = "http://"+obj.url;
								}
								this.doc.link(obj.left, obj.top, obj.width*obj.scaleX, obj.height*obj.scaleY, obj.url, {});
							}
						break;
						default: //TODO добавить проверку на хттпс
							
						break;
					}
				break;
				default:
					console.log("default obj.type", obj.type);
				break;
			}
		};
	};
	this.proccessForm = (obj)=>{
		var form_field_params = {
			backgroundColor: "#ebebeb",
			borderColor: "#a83232",
		};
		switch(obj.element_type){
			case 'input':
				this.formInput(obj, form_field_params);
			break;
			case 'select':
			case 'dropdown':
				this.formSelect(obj, form_field_params);
			break;
			case 'textarea':
				this.formTextArea(obj, form_field_params);
			break;
			case 'checkbox':
				this.formCheckbox(obj, form_field_params);
			break;
			case 'radio':
				this.formRadio(obj, form_field_params);
			break;
			defautl:
				alert("unsupported "+obj.element_type);
			break;
		}
	};
	this.formRadio = (obj, form_field_params)=>{
		form_field_params['AS'] = $(`[uniq_id='${obj.uniq_id}']`).is(":checked")?true:false;
		this.doc.formRadioButton(obj.field_name, obj.left, obj.top, obj.width, obj.height, $.extend({}, form_field_params));							
	};
	this.formCheckbox = (obj, form_field_params)=>{
		form_field_params['AS'] = $(`[uniq_id='${obj.uniq_id}']`).is(":checked")?true:false;
		this.doc.formCheckbox(obj.field_name, obj.left, obj.top, obj.width, obj.height, $.extend({}, form_field_params));							
	};
	this.formSelect = (obj, form_field_params)=>{
		var val = $(`[uniq_id='${obj.uniq_id}']`).val();
		opts = {
			select: obj.options,
			value: val,
			defaultValue: '',
		};
		this.doc.formCombo(obj.field_name, obj.left, obj.top, obj.width, obj.height, $.extend(opts, form_field_params));
	};

	this.formTextArea = (obj, form_field_params) => {
		var val = $(`[uniq_id='${obj.uniq_id}']`).val();
		form_field_params['value'] =val;
		form_field_params['multiline'] = true;
		this.doc.formText(obj.field_name, obj.left, obj.top, obj.width, obj.height, form_field_params);
	};

	this.formInput = (obj, form_field_params) => {
		var val = $(`[uniq_id='${obj.uniq_id}']`).val();
		obj.width-=6;
		obj.height-=6;
		obj.top+=3;
		obj.left+=3;
		form_field_params['value'] =val;
		this.doc.formText(obj.field_name, obj.left, obj.top, obj.width, obj.height, form_field_params);
	};
	
	this.rect_it = 0;
	this.saveRect = (rect)=>{
		var path_array = rect.path;
		if(window.xx == 1){
			//return ;
		}
		var pairs = path_array.map((p)=>{ 
			var ret = [];
			var flag = true;
			$.each(p, (i,p2)=>{
				if(i>0){
					var half_stroke = rect.strokeWidth?rect.strokeWidth/2:0;
					if(flag){ //left
						p2 = p2-rect.pathOffset.x+(rect.width/2)+rect.left+(half_stroke);
					}else{ //top
						p2 = p2-rect.pathOffset.y+(rect.height/2)+rect.top+(half_stroke);
					}
					ret.push(p2);
					flag = !flag;
				}
			});
			return p[0]+" "+ret.join(" ");
		});
		var path = pairs.join(" ");


		//TODO альфа не добавляется

		if(rect.strokeDashArray && rect.strokeDashArray.length>0 && rect.strokeDashArray[0] && rect.strokeDashArray[1]){
			this.doc.dash(rect.strokeDashArray)
		}else{
			this.doc.dash(99999);
		}
		
		var opacity = 1;
		var fill_color = false;
		var stroke_color = false;
		var is_gradient = false;


		if(typeof rect.fill !='undefined' && typeof rect.fill.type!='undefined'){

			
			is_gradient = true;
		}else{
			if(rect.fill && rect.fill!='rgba(255,255,255,0)'){
				var fill_color = rgb2hex(rect.fill);
			}
			if(rect.stroke && rect.stroke != "rgba(255,255,255,0)"){
				var stroke_color = rgb2hex(rect.stroke);
			}

			if(rect.stroke && rect.stroke.indexOf("rgba")!=-1){
				var temp = rect.stroke.replace(/[^\d+,.]/g, "").split(",");
				opacity = temp[temp.length-1];
			}
		}

		if(rect.strokeLineCap){
			this.doc.lineCap(rect.strokeLineCap);
		}else{
			this.doc.lineCap("butt");
		}

		if(is_gradient){

			switch(rect.fill.type){
				case 'linear':
					this.doc.path(path);

					var grad = this.doc.linearGradient(rect.fill.coords.x1,rect.fill.coords.y1 , rect.fill.coords.x2, rect.fill.coords.y2);
					rect.fill.colorStops.forEach(function(v){
						grad.stop(v.offset, rgb2hex(v.color));
					})
					this.doc.fill(grad);
				break;
				//TODO нету радиального


			}

		}else{
			if(fill_color && stroke_color){
				opacity = this.getOpacity(rect.fill);
				opacity2 = this.getOpacity(rect.stroke);
				opacity = opacity||opacity2;
				if(rect.strokeWidth){
					this.doc.path(path).opacity(opacity).lineWidth(rect.strokeWidth).fillAndStroke(fill_color, stroke_color);
				}else{
					this.doc.path(path).opacity(opacity).fill(fill_color);
				}
			}else if(fill_color){
				opacity = this.getOpacity(rect.fill);
				this.doc.path(path).opacity(opacity).fill(fill_color);
			}else if(stroke_color){
				opacity = this.getOpacity(rect.stroke);
				this.doc.path(path).opacity(opacity).lineWidth(rect.strokeWidth).stroke(stroke_color).fill("non-zero");//.fill("rgb(255,0,0)");
			}
		}
	
		this.doc.opacity(1);
	};
	
	this.getOpacity = (color)=>{
		try{
			if(color.indexOf("rgba")==-1){
				return 1;
			}
			var temp = color.replace(/[^\d+,.]/g, "").split(",");
			return temp[temp.length-1];
		}catch(er){
			console.error("error on parse opacity, use visible ", color);
			return 1;
		}
	}

	this.getStyleFont = (fonts) => {
		var embed = Object.values(viewer.fonts);
		fonts = fonts.split(", ");
		var i = 0;

		if (embed.length == 0) {
			return fonts[0];
		}

		if (typeof viewer.fonts[fonts[0]] != 'undefined') {
			return "emb_" + viewer.fonts[fonts[0]].niceName;
		}

		for (i = 0; i != embed.length - 1; i++) {
			if (fonts.includes(embed[i].niceName)) {
				return "emb_" + embed[i].niceName;
			}
		}
		return fonts[fonts.length - 1];
	};

	this.parseFonts = async () => {
		return fetch("fonts/comic.ttf").then(res => res.arrayBuffer())
			.then(fontBlob => {
				return fontBlob;
		});
	};


	this.saveDoc = () => {

	};

	this.getFontSize = () => {

	};
	
	this.parseText = async(text)=>{
		var that = this;
		var block_left = text.left,
			block_top = text.top;

		if (block_top > page.height) {
			return;
		}

		var lines = text._unwrappedTextLines,
			bounds = text.__charBounds,
			styles = text.styles,
			line_top = 0,
			char_position = 0,
			textTopOffset = 0;


		$.each(lines, (li, line) => {
			var ff = "sans-serif";
			var draw_underline = true;
			var debug_flag = false;
			// if(line.join("")=='\ue007\ue008\ue006 \ue007\ue010\ue00c\ue00b\ue00d\ue011 \ue007\ue00a\ue010\ue00b \ue009\ue011\ue00a\ue011\ue00c\ue00e\ue00c\ue00f\ue011\ue012\ue004\ue002\ue0039\ue001\ue003\ue000'){
			// 	debug_flag = true;
			// }else{
			// 	return;
			// }

			var height_of_line = text.getHeightOfLine(li);

			$.each(line, (ci, char) => {
				if (typeof styles[li] == 'undefined'  || typeof styles[li][ci] == 'undefined') {
					style = text.getStyleAtPosition(char_position, true);
				} else {
					style = styles[li][ci];
				}
				
				text._measureLine(li);
				if (typeof bounds[li] == 'undefined') {
					bound = {
						left: 0
					};
					block_top = 0;
					block_left = 0;
				} else {
					bound = bounds[li][ci];
				}
				
				if(typeof bound =='undefined'){
					bound = {left: 0};
				}
				if(typeof style.fill=='undefined'){
					color = "#000000";
				}else{
					if(style.fill.indexOf("#") != -1) {
						color = style.fill;
					}else{
						color = rgb2hex(style.fill);
					}
				}
				var fontSize = 0;
				if(typeof style.fontSize=='undefined'){
					fontSize = text.fontSize;
				}else{
					fontSize = style.fontSize;
				}
					

				that.doc.fillColor(color);
				that.doc.fontSize(fontSize);


				function getRegisterFontFamily(str){
					var matches = str.match(/g_d\d+_f\d+/)
					if(matches){
						//rfn -registered font name
						var rfn = createPDF.registered_fonts[matches[0]];
					}else{
						return str.split(",")[0];
						//console.warn("fallback", str);
					}
					return rfn;
				}


				if (typeof style.fontFamily == 'undefined') {
					ff = style.fontFamily;
				} else {
					//var ff = that.getStyleFont(style.fontFamily);
					ff = getRegisterFontFamily(style.fontFamily);
				}

				var first_font = style.fontFamily.split(", ")[0];
				
				//TODO берем фалбек
				if(typeof viewer.systemFonts[first_font]!='undefined'){
					ff = first_font;
				}else{
					baseline = "bottom";
					inFont = charInFont(char, first_font);
					if (!inFont) {
						x = style.fontFamily.split(", ");
						ff = x[x.length - 2];
						// baseline = 0.5 * (createPDF.doc._font.descender + createPDF.doc._font.ascender)
						// baseline = (baseline/1000*fontSize)*-1;
//						console.log("xx", createPDF.doc._font.xHeight, baseline);
					}
				}
				
				
				//font
				if(char==' '){
					ff = "sans-serif";
				}
				// console.log(style.fontFamily);
				// console.log(ff);
				// alert();

				//TODO for chinese chars
				
				var char_is = "normal";
				var is_zh = char.match(/[\u4E00-\u9FCC]/);
				
				if(is_zh){
					char_is = "chinese";
					ff = "STsong";
				}


				var is_ja = char.match(/[\u3040-\u30FF]/);
				if(is_ja){
					char_is = "japanese";
					ff = "rootegaki"
				}

				var is_ko = char.match(/[\u3130-\u318F]/);
				if(is_ko){
					char_is = "korean";
					ff = "dodam";
				}

				if(ff=='Arial'){
					// console.log(style.fontFamily);
				}
				if(render_in_slider===true){
					ff = "Courier";
					if(!is_ko && !is_ja && !is_zh){
					}
				}

				that.doc.font(ff);
				if(typeof style.underline!='undefined' && style.underline=='underline' && draw_underline==true){
					var bbox = text.getBoundingRect();
					that.doc.moveTo(block_left, block_top+bbox.height).lineTo(block_left+bbox.width, block_top+bbox.height).stroke();
					draw_underline = false;
				}
				baseline = 0;
				if(li==0){
					var bs1 = height_of_line;
				}else{
					bs1 = height_of_line;
				}


				switch(char_is){
					case 'chinese':
						that.doc.text(char, block_left + bound.left, block_top +bs1 + line_top, {lineBreak: false, baseline: 0});
					break;
					case 'japanese':
						that.doc.text(char, block_left + bound.left, block_top +bs1 + line_top, {lineBreak: false, baseline: 0});
					break;
					case 'korean':
						that.doc.text(char, block_left + bound.left, block_top +bs1+ line_top, {lineBreak: false, baseline: 0});
					break;
					default:
						that.doc.text(char, block_left + bound.left, block_top +bs1+ line_top, {lineBreak: false, baseline: baseline});
					break;
				}
				char_position++;
			});
			
			var lineHeight = text.getHeightOfLine(li);
			textTopOffset = lineHeight * (1 - text._fontSizeFraction) / text.lineHeight;
			var epta = 0;
			try{
				epta = text.getHeightOfLine(li+1)-text.getHeightOfLine(li);
			}catch(e){
				epta = 0;
			}

			line_top += text.getHeightOfLine(li); //+epta;
		});
	
	
	}

//	this.parseTexts = async (page) => {
//		var page_texts = {};
//		var that = this;
//		texts = page.fcanvas.getObjects("i-text");

//		that.doc.fontSize(25);
//		this.doc.font("Courier");
//		that.doc.text("X", 10, 10);

//		$.each(texts, (ti, text) => {
//			var block_left = text.left,
//				block_top = text.top;

//			if (block_top > page.height) {
//				return;
//			}

//			var lines = text._unwrappedTextLines,
//				bounds = text.__charBounds,
//				styles = text.styles,
//				line_top = 0,
//				char_position = 0,
//				textTopOffset = 0;

//			$.each(lines, (li, line) => {
//				var ff = "sans-serif";
//				$.each(line, (ci, char) => {
//					if (typeof styles[li][ci] == 'undefined') {
//						style = text.getStyleAtPosition(char_position, true);
//					} else {
//						style = styles[li][ci];
//					}
//					
//					text._measureLine(li);
//					if (typeof bounds[li] == 'undefined') {
//						bound = {
//							left: 0
//						};
//						block_top = 0;
//						block_left = 0;
//					} else {
//						bound = bounds[li][ci];
//					}
//					
//					if(typeof bound =='undefined'){
//						bound = {left: 0};
//					}
//					

//					if (style.fill.indexOf("#") != -1) {
//						color = style.fill;
//					} else {
//						color = rgb2hex(style.fill);
//					}

//					that.doc.fillColor(color);
//					that.doc.fontSize(style.fontSize);

//					if (typeof style.fontFamily == 'undefined') {
//						ff = style.fontFamily;
//					} else {
//						var ff = that.getStyleFont(style.fontFamily);
//					}
//					var first_font = style.fontFamily.split(", ")[0];
//					

//					if(typeof viewer.systemFonts[first_font]!='undefined'){
//						ff = first_font;
//					}else{
//						inFont = charInFont(char, first_font);
//						if (!inFont) {
//							x = style.fontFamily.split(", ");
//							ff = x[x.length - 1];
//						}
//					}
//					that.doc.font(ff);
//					that.doc.text(char, block_left + bound.left, block_top + line_top, {lineBreak: false, baseline: "top"});
//					char_position++;
//				});
//				
//				var lineHeight = text.getHeightOfLine(li);
//				textTopOffset = lineHeight * (1 - text._fontSizeFraction) / text.lineHeight;
//				var epta = 0;
//				try{
//					epta = text.getHeightOfLine(li+1)-text.getHeightOfLine(li);
//				}catch(e){
//					epta = 0;
//				}
//				
////				if(typeof text.customLineHeights[li+1]!='undefined'){
////					var epta = text.customLineHeights[li+1]-text.customLineHeights[li];
////				}
//				line_top += text.getHeightOfLine(li)+epta;
//			});
//		});
//	};

	this.save = () => {
		let stream = this.doc.pipe(blobStream());
		
		stream.on('finish', function() {
			let blob = stream.toBlob('application/pdf');
			viewer.edited_blob = blob
			finisCreate({filename: "edited_"+viewer.pdf_name, url: false}, "");
			
//			if (navigator.msSaveOrOpenBlob) {
//				navigator.msSaveOrOpenBlob(blob, pdf_name+"-edited."+"pdf");
//			} else {
//				//window.open(URL.createObjectURL(blob), '_blank');
//			}
		}).on("error", function(err){
			alert("error");
		});

		this.doc.end();
	};


	return this;
};




function finisCreate(data, x=false){
	$(".creating_document").hide();
	$(".create_file_box").show();
	$(".result-top-line .download_file_name").html(data.filename ? data.filename : "edited_blank.pdf");

	$("#save-dropbox").attr({
	  'data-url': data.url,
	  'data-file_name': "edited_" + (data.filename ? data.filename : "blank.pdf")
	});
	$("#save-gdrive").attr({
	  'data-src': data.url,
	  'data-filename': "edited_" + (data.filename ? data.filename : "blank.pdf")
	});


}
