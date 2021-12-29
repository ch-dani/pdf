var pixel_to_point_ratio = "1.3333333333333333";
var default_font = "Arial, STSong"
var render_in_slider = false;

var maxRenderPages = 6;


var clip_stack = [];

var clip_image = false;


var pdf_stroke_color = null;
var pdf_fill_color = null;
var pdf_shading = null;
var pdf_dash = null;
var temp_iterator = 0;

var SPACE_FACTOR = 0.3;
var MULTI_SPACE_FACTOR = 1.5;
var MULTI_SPACE_FACTOR_MAX = 4;


function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}

if(typeof electron_remote=='undefined'){
	window.debug = 1;
}

window.debug = window.show_debug = 0;

//window.debug = 1;

window.is_translate = true;


pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/new_editor/libs/pdfjs-dist/build/pdf.worker.js';


fabric.Object.prototype.centeredRotation =false;

var fontsEq = [
	
];

//myText._charWidthsCache = { };
//myText._clearCache();


var zIndex = {
	annot: 120,
	text: 100,
	image: 90,
	rect: 80
}

function getDPI() {
    var div = document.createElement( "div");
    div.style.height = "1in";
    div.style.width = "1in";
    div.style.top = "-100%";
    div.style.left = "-100%";
    div.style.position = "absolute";
    document.body.appendChild(div);
    var result =  div.offsetHeight;
    document.body.removeChild( div );
    return result;
}

var dpi = getDPI();

function getClip(){
	var len = clip_stack.length;

	return clip_stack[len-1];

	var clip = false;
	for(var v of clip_stack){
		if(v){
			clip =v;
			break;
		}
	}
	return clip;
}

   
function TextFunctions(){
	this.current_editor = false;
	this.current_selection = "text";
	this.defaultFonts = {
		"sans-serif": "sans-serif",
		"Helvetica": "Helvetica"
		//"monospace": "monospace",
		//"times_new_roman.ttf": "Times New Roman",
//		"Times",
//		"Courier",
//		"Verdana",
//		"Georgia",
//		"Comic Sans MS",
	};
	
	this.cleaned = {sup: false, sub:false, linethrough: false, underline: false, italic: false, bold: false};
	
	this.bindEv = () =>{
		$(document).on("all_parts_loaded", ()=>{
			$(".font_family_selector").append(`<option value="default"></options>`);
			$(".font_family_selector").append(`<option disabled>System Fonts</options>`);
			$.each(this.defaultFonts, function(i, v){
				$(".font_family_selector").append(`<option value='${v}'>${v}</options>`);
			});
			
			$(".font_family_selector").append(`<option disabled >Embeded fonts</options>`);
			$(".font_size_selector").append(`<option value="default"></options>`);
			intRange(8,33).forEach(function(v){
				$(".font_size_selector").append(`<option value='${v}'>${v} pt</option>`);
			});
		});
		$(document).on("click", "#settings-text .settings-text-btn,  #settings-add-text .settings-text-btn, .text_styles_clean .settings-main-btn", this.modText);
		$(document).on("change", ".change-color-input", this.modColor);
		$(document).on("click", ".text_align li", this.textAlign);
		
		$(document).on("change", ".font_size_selector", this.changeFontSize);
		$(document).on("change", ".font_family_selector", this.changeFontFamily);
	};
	
	this.getObjectSelection = (object)=>{
		var ss = object.selectionStart,
			se = object.selectionEnd,
			all = (ss-se)?false:true;
		if(all){
			var total_chars = 0;
			$.each(object.styles, function(i, l){
				total_chars += l.length;
			});
			return [0, total_chars];
		}else{
			return [ss, se];
		}
	};
	
	this.textAlign = (e)=>{
		var el = $(e.currentTarget);
		var mode = el.data("mode"),
			current_canvas = viewer.pages[viewer.current_page].fcanvas,
			object = current_canvas.getActiveObject();
		
		if(!object){
			return false;
		}
		
		object.position = [];
		object.textAlign = mode;
		current_canvas.renderAll();
	};
	
	
	this.modColor = (e)=>{
		if(!viewer.current_page){
			return false;
		}
		var current_canvas = viewer.pages[viewer.current_page].fcanvas,
			object = current_canvas.getActiveObject(),
			color = $(e.target).val(),
			ss = object.selectionStart,
			se = object.selectionEnd,
			change_all = (ss-se)?false:true,
			val = "";


		if(!object){
			return ;
		};
		
		if(change_all){
			$.each(object.styles, function(i,line){
				$.each(line,function(i, char){
					char.fill = color;
					
				})
			});
		}else{	
			object.setSelectionStyles({ fill: color }, ss, se);		
		}
		
		//setSelectionStyles
		current_canvas.renderAll();
	};
	
	this.modText = (e) =>{
		if(!viewer.current_page){
			return false;
		}
		var target = $(e.target);
		var el = $(e.currentTarget),
			mode = el.data("mode"),
			current_canvas = viewer.pages[viewer.current_page].fcanvas,
			object = current_canvas.getActiveObject();
			
		if(!object){
			return false;
		}
		var state = true;
		if(el.hasClass("active")){
			state = false;
		}
		
		var ss = object.selectionStart,
			se = object.selectionEnd,
			change_all = (ss-se)?false:true,
			val = "";
		switch(mode){
			case 'bold':
				val = (state)?"bold":"normal"
				if(change_all){
					object.fontWeight = val;
				}else{
					object.setSelectionStyles({ fontWeight: val }, ss, se);
				}
			break;
			case 'italic':
				val = (state)?"italic":"normal"
				if(change_all){
					object.fontStyle = val;
				}else{
					object.setSelectionStyles({ fontStyle: val }, ss, se);
				}			
			break;
			case 'underline':
				if(change_all){
					object.underline = state;
				}else{
					object.setSelectionStyles({ underline: state }, ss, se);
				}			
			break;
			case 'linethrough':
				if(change_all){
					object.linethrough = state;
				}else{
					object.setSelectionStyles({ linethrough: state }, ss, se);
				}
			break;
			
			case 'sub':
				var def_fs = object.styles[0][0].fontSize;
				if(state){
					object.setSelectionStyles({
						fontSize: undefined,
						deltaY: undefined
					},ss,se);
					object.setSubscript(ss, se);
				}else{
					object.setSelectionStyles({
						fontSize: def_fs,
						deltaY: undefined
					},ss,se);
				}
				
			break;
			case 'sup':
				
				var def_fs = object.styles[0][0].fontSize; 
				if(state){
					object.setSelectionStyles({
						fontSize: undefined,
						deltaY: undefined
					}, ss,se);
					object.setSuperscript(ss, se);
				}else{
					object.setSelectionStyles({
						fontSize: def_fs,
						deltaY: undefined
					}, ss,se);
					
				}
			break;
			case 'clean':
				object.setSelectionStyles({
					fontSize: 10*viewer.scale,
					deltaY: undefined,
					underline: false,
					linethrough: false,
					fontStyle: "normal",
					fontWeight: "normal"
				}, 0, object.text.length);
				object.positions = [];
				
				console.log("CLEAN");
				
			break;
			
			default:
			break;
		}
		var temp = {};
		temp[mode] = state;
		this.toggleModifySelection(temp);
		
		current_canvas.renderAll();
	};
	
	this.elementBlur = (e)=>{
		this.toggleModifySelection(this.cleaned);
		$(".font_family_selector").val("default");
		$(".font_size_selector").val("default");
	};
	
	
	
	this.selectionUpdate = (e)=>{
		var object = e.target;
		this.parseStyles(object,false);
	};
	
	this.elementClick = (e)=>{
		var pn = $(e.target.canvas.lowerCanvasEl).data("pn");
		viewer.current_page = pn;
		
		this.parseStyles(e.target, true);
	};
	
	this.changeFontFamily = (e)=>{
		var current_canvas = viewer.pages[viewer.current_page].fcanvas;
		var val = $(e.target).val(),
			object = current_canvas.getActiveObject();
			
		if(!object){
			return false;
		}
		
		object.positions = [];
		var selection = this.getObjectSelection(object); 
		if(isNaN(selection[1])){
			selection[1] = object.text.length;
		}
		
		
		object.setSelectionStyles({ fontFamily: val }, selection[0], selection[1]);
		current_canvas.renderAll();
	};

	this.changeFontSize = (e)=>{
		var current_canvas = viewer.pages[viewer.current_page].fcanvas;
		var val = $(e.target).val(),
			object = current_canvas.getActiveObject();
		if(!object){
			return false;
		}
		
		object.positions = [];
		
		var selection = this.getObjectSelection(object); 
		if(isNaN(selection[1])){
			selection[1] = object.text.length;
		}

		object.setSelectionStyles({ fontSize: parseFloat(val)*viewer.scale }, selection[0], selection[1]);
		current_canvas.renderAll();
	};
	
	this.parseStyles = (object, all)=>{
		//console.log("object type is: ",object.type);
	
		var ss = object.selectionStart,
			se = object.selectionEnd,
			all_selected = (ss-se)?false:true;
		
		if((ss-se)==0){
			se = se+1;
			all = true;
		}
		var bold = true,
			italic = true,
			underline = true,
			linethrough = true,
			sub = true,
			sup = true,
			total_chars = 0;
		
		
		$.each(object.styles, function(i, l){
			if(typeof l == 'undefined'){
			}else{
				total_chars += l.length;
			}
		});
		
		var current_styles = [];
		if(all){
			current_styles = object.getSelectionStyles(0, total_chars, true);
		}else{
			current_styles = object.getSelectionStyles(ss, se, true);
		}
		
		if(current_styles.length==0){//новый текст без прописаных стилей
			current_styles[0] = object.getCompleteStyleDeclaration();
		}
		
		var fonts = {};
		var sizes = {};
		var colors = {};
		//\n
		$.each(current_styles, function(i,style){
			if(typeof viewer.fonts[style.fontFamily]!='undefined'){
				//fonts[viewer.fonts[style.fontFamily].name] = 1;
				fonts[style.fontFamily] = 1;
			}else{
				if(object.text[i]!='\n' && object.text[i]!=' '){
					fonts[style.fontFamily] = 1;
				}
			}
			
			if(style.fill.indexOf("rgb")!==-1){
				var tcolor = style.fill.replace(/rgb\(|rgba\(|\)| /g, "").split(","),
					hx = rgbToHex(...tcolor);
				colors[hx] = hx;
			}else{
				colors[style.fill] = 1; 
			}
			if(object.text[i]!='\n'){
				sizes[style.fontSize] = 1;
			}
			if(style.fontWeight!="bold"){
				bold = false;
			}
			if(style.fontStyle!="italic"){
				italic = false;
			}
			if(!style.underline){
				underline = false;
			}
			if(!style.linethrough){
				linethrough = false;
			}
			if(style.deltaY<=0){
				sub = false;
			}
			if(style.deltaY>=0){
				sup = false;
			}
		});
		
		colors = Object.keys(colors);
		
		if(colors.length==1){
			$(".change-color-input").val(colors[0]);
		}else{
			$(".change-color-input").val("#555555");
		}
		
		var fontSize = Object.keys(sizes);
		var current_font_size = "default";
		if(fontSize.length==1){
			current_font_size = Math.round(fontSize[0]/viewer.scale);
		}
		$(".font_size_selector").val(current_font_size)
		
		var fonts = Object.keys(fonts);
		
		var current_font = "default";
		if(fonts.length==1){
			current_font = fonts[0];
		}
		$(".font_family_selector").val(current_font)
		
		this.toggleModifySelection({sup: sup, sub:sub, linethrough: linethrough, underline: underline, italic: italic, bold: bold});
	};
	

	
	this.toggleModifySelection = (elements) =>{
		$.each(elements, function(name, active){
			if(active){
				$(`[data-action='modify-selection'][data-mode='${name}']`).addClass("active");
			}else{
				$(`[data-action='modify-selection'][data-mode='${name}']`).removeClass("active");
			}
		});
	};
	
	this.replaceChars = function(text, x_font){
		var flag = false;
		if(typeof viewer.chars_table[x_font]!='undefined'){
			$.each(viewer.chars_table[x_font], function(key, val){
				if(key!=' '){
					try{
						var rg = new RegExp(`${escapeRegExp(key)}`,"g");
						text = text.replace(rg, val);
					}catch(e){
						alert("Font parse error "+key);
						console.error(e);
					}
				}
			});
			
			if(typeof viewer.chars_table[x_font][textFunc.pressed_key]=='undefined'){
				flag = true;
			}
			
		}
		
		if(flag){
			//info("Character not found in font. Default font was used");
		}
		
		return text;
	};


	this.updateStylesAfterInput = function(th, char, font){
		var cursor_location = th.get2DCursorLocation(),
		pos = cursor_location.charIndex,
		line = cursor_location.lineIndex,
		styles = th.styles;
		return styles;
	};


	this.getNewCharFont = function(font, char, th, inverse){
		if(inverse){
			if(typeof viewer.chars_table_inverse[font]!='undefined' && typeof viewer.chars_table_inverse[font][char]!='undefined'){
				//console.log("returned font is ", font);
				return font;
			}		
		}else{
			if(typeof viewer.chars_table[font]!='undefined' && typeof viewer.chars_table[font][char]!='undefined'){
				//console.log("returned font is ", font);
				return font;
			}
		}
		return th.fontFamily;
	};
	
	this.bindEv();
	return this;
}

 
var textFunc = new TextFunctions();
var current_scale = 1;
var page_width = window.innerWidth;//$(".page-container").width();
page_width*=0.85;


function likeCanvas(){
	this.type = "likecanvas";
	this.objects = [];
	this.width = 100;
	this.height = 100;

	this.setWidth =(w) => {
		this.width = w;
	}
	this.setHeight = (h)=>{
		this.height = h;
	}

	this.add = (obj)=>{
		this.objects.push(obj);
	}
	this.getObjects = (type)=>{
		return [...this.objects];
	}

	this.remove = (obj)=>{
		var it = this.objects.findIndex(f=>f ==obj);
		this.objects.splice(it, 1);
	}
	return this;
}

 
function Viewer(){
	this.server_path = "";
	this.last_char_width = 0;
	this.merged_texts = false;
	this.inComposite = false;
	this.rect_gradient = false;
	this.prev_composite = false;
	this.last_clip = false;
	this.font_iterator = 0;
	this.total_pages = 0;
	this.rendered_pages = 0;
	this.current_z_index = 1000;
	var CMAP_URL = '/libs/pdfjs-dist/cmaps/';
	var CMAP_PACKED = true;
	this.rect_draw_init = false;
	this.movedImage = false;
	this.scale = -1; //TODO change for scale
	this.current_page = false;
	this.now_render = false;
	this.loadingTask = false;
	this.pages = {};
	this.pdfkit = false;
	this.fonts = {};
	this.pdf_name = "blank.pdf";
	this.embeded_char_widths = {};
	this.chars_table = {};
	this.chars_table_inverse = {};
	this.chars_to_unicode = {};
	this.embedFontsNames = {};
	this.embedFontsNames_reverse = {};

	this.embedFontsNamesFixed = {};
	this.systemFonts = {
		Helvetica: 1, 
		"Arial": 1,
		"Arial-Bold": 1,
		"Helvetica-Bold": 1,
		Courier: 1,
		"Courier-Bold": 1,
		"Courier-Oblique": 1,
		"Courier-BoldOblique": 1,
		"Helvetica-Oblique": 1,
		"Helvetica-BoldOblique": 1,
		"Times-Roman": 1,
		"Times-Bold": 1,
		"Times-Bold": 1,
		"Times-Italic": 1,
		"Times-BoldItalic": 1,
		Symbol: 1,
		"ZapfDingbats": 1,
		"ArialNarrow-Bold": 1,
		"ArialNarrow": 1
	}; 

	this.bg = false;

	this.bind = () =>{
		$(document).on("click", ".save_pdf", this.createPDF);
		$(document).on("open_pdf_file", this.openPDFFile);
		
		$(document).on("update_textbox_from_textarea", function(e, th){
			return th.replace("x", "o");
		});
	}

	
	this.init = (url, data) =>{
		if(url){
			this.loadingTask = pdfjsLib.getDocument({
				url: url, 
				cMapUrl: CMAP_URL,
				cMapPacked: CMAP_PACKED,
			});
		}else{
			this.loadingTask = pdfjsLib.getDocument({
				data: data, 
				cMapUrl: CMAP_URL,
				cMapPacked: CMAP_PACKED,
			});
		}
		this.loadingTask.then(this.renderPDF)
	};
	
	this.getCanvasPosition = (transform=[], x=0, y=0) => {
		return [transform[0] * x + transform[2] * y + transform[4], transform[1] * x + transform[3] * y + transform[5]];
	};

	this.addVector = (obj, page) => {
		if(render_in_slider){
			return false;
		}

		//var ind = Object.keys(this.pages[page].fabricElements).length;
		obj["current_z_index"] = viewer.current_z_index;
		obj['element_type']="path";
		viewer.pages[page].currentZIndex++;
		console.log("create new vector with zindex", viewer.pages[page].currentZIndex++);
		obj.zIndex = viewer.pages[page].currentZIndex;
		viewer.current_z_index+=1;
		this.pages[page].fabricElements.push(obj);
	};

	this.updateVector = (obj, page) => {
		if(render_in_slider){
			return false;
		}		
		if(typeof page=='undefined'){
			return false;
		}
		if(false){
			var ind = Object.keys(this.pages[page].fabricRects).length-1;
			this.pages[page].fabricRects[ind] = $.extend(this.pages[page].fabricRects[ind], obj);
			return this.pages[page].fabricRects[ind];
		}else{
			var ind = this.pages[page].fabricElements.length-1;
			if(page==1 && ind ==1){

				//alert("upd");
			}

			this.pages[page].fabricElements[ind] = $.extend(this.pages[page].fabricElements[ind], obj);
			return this.pages[page].fabricElements[ind];
		}
	};
	this.getLastVector = (pn) => {
		if(typeof this.pages[pn]=='undefined'){
			return {drawIt: false};
		}
		var rr = this.pages[pn].fabricElements[Object.keys(this.pages[pn].fabricElements).length-1];

		if(typeof rr=='undefined'){
			return {drawIt: false};
		}
		return rr;
	};


//	this.setVectorParams = (obj) => {
//	
//	};
	
	this.createThubmnail = (data, pn) =>{
		if($(`#thumbnails .list-item[pn=${pn}]`).length == 0){
			$("#thumbnails .page-list").append(`
			<div class="list-item change_page page_preview " data-pn="${pn}">
				<div class="img-wrap">
					<div class="page-number">${pn}</div>
					<img src="${data}" alt="" style="max-height: 225px;">
				</div>
			</div>`);
		}
	};

	this.openPDFFile = (ev, file) =>{
		if(window.is_browser){
			var path = require('path');
			this.pdf_name = path.basename(file, ".pdf");
		}else{
			this.pdf_name = basename(file);
		}
		
		$(".show_after_file_selected").removeClass("hidden");
		$(".file_name_here").html(basename(file)+".pdf");
	};

    this.getBlob = async function (file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsBinaryString(file);
            reader.onload = () =>
                resolve(reader.result);
            reader.onerror = error =>
                reject(error);
        });
    };
	

	// this.getFallbackFont = function(fo){
	// 	if(typeof viewer.fonts[fo]!='undefined'){
	// 		var tfo = viewer.fonts[fo].name.split("+")[1];
	// 		return tfo;
	// 	}
	// 	return fo;
	// };

	this.getFallbackFont = function(fo){
		if(typeof viewer.fonts[fo]!='undefined'){
			var tfo = viewer.fonts[fo].name.replace(",","-").replace("+", "-"); //.name.split("+")[1];
			return tfo;
		}
		return fo;
	};
	
	this.charBounds = {};
	
	this.afterPageRender = async (pageNumber, page_object) => {
		let total_text_rendered = 0;
		$(document).trigger("after_page_render", [pageNumber, page_object])

		let canvas = this.pages[pageNumber].canvas,
			page_width = canvas.width,
			w = page_width,
			h = canvas.height,
			page_height = h;

		this.pages[pageNumber].canvas = null;

		if(render_in_slider===true && pageNumber<maxRenderPages){
			//TODO
			bg = canvas.toDataURL("image/jpg");
			viewer.bg = {src: bg, width: canvas.width, height: canvas.height};
		}

		if(render_in_slider===true){
			if(pageNumber<maxRenderPages){
				var fcanvas = new fabric.StaticCanvas(`page_canvas_${pageNumber}`, {
					objectCaching: true,
					renderOnAddRemove: false,
					originY: 'bottom',
				});
			}else{
				fcanvas = new likeCanvas();
				// var fcanvas = new fabric.StaticCanvas(null, {
				// 	objectCaching: true,
				// 	renderOnAddRemove: false,
				// 	originY: 'bottom',
				// });
			}
		}else{
			var fcanvas = new fabric.Canvas(`page_canvas_${pageNumber}`, {
				//renderOnAddRemove: true,
				objectCaching: true,
				selection: true,
				originY: 'bottom',
				canvasState             : [],
				currentStateIndex       : -1,
				undoStatus              : false,
				redoStatus              : false,
				undoFinishedStatus      : 1,
				redoFinishedStatus      : 1,
				undoButton              : $(".open_undo_modal")[0],
				redoButton              : $(".open_redo_modal")[0],
			});
			if(typeof canvasMouseDown!='undefined'){
				fcanvas.on("mouse:down",canvasMouseDown);
				fcanvas.on("mouse:up",canvasMouseUp);
			}
		}


		fcanvas.pageNumber = pageNumber;
		this.pages[pageNumber].fcanvas = fcanvas;
		

		// if(render_in_slider==false || pageNumber<maxRenderPages){
		// }
		fcanvas.setHeight(h);
		fcanvas.setWidth(w);
		
		$(document).trigger("set_canvas_size", [pageNumber, w,h]);

		if(render_in_slider===true){
			//fcanvas.setBackgroundImage(bg);
			if(pageNumber<maxRenderPages){
				fcanvas.setBackgroundImage(bg, fcanvas.renderAll.bind(fcanvas));
			}else{
				$(`#page_outer_${pageNumber}`).remove();
			}
			//delete bg;
			//fcanvas.bg = bg;
		}else{
			await renderElements(viewer.pages[pageNumber].fabricElements, fcanvas, pageNumber);
			fcanvas.renderAll();
		}

		$.each(this.pages[pageNumber].fabricText, (bi, block)=>{
			if(!block || block[0].length==0){
				delete this.pages[pageNumber].fabricText[bi];
			}
		});

		this.pages[pageNumber].fabricText = removeFakeSpaces(this.pages[pageNumber].fabricText);

		if(render_in_slider){
			var merged = mergeBlocks(false, pageNumber, this.pages[pageNumber].fabricText, page_width, page_height, this.pages[pageNumber], false);
		}else{
			var merged = mergeBlocks(false, pageNumber, this.pages[pageNumber].fabricText, page_width, page_height, this.pages[pageNumber], true);
		}
		this.merged_texts = merged;
		var temp_scale = this.pages[pageNumber].scale;


		$.each(merged, function(paragraph_num, paragraph){
			if(Number.isInteger(paragraph)){
				return ;
			}

			if(paragraph.top>=h){
				return;	
			}
			var styles = [];
			var string = "";
			var unicode_string = "";
			var last_font = "";
			
			var lineHeighs = [],
				prev_line_bottom = false,
				angle = 0,
				r = 0;
			
			
			var positions = [];
			$.each(paragraph.lines, function(ln, line){

				if(typeof positions[ln]=='undefined'){
					positions[ln] = [];
				}
				
				if(false && line.angle){
				
				}else{
					if(prev_line_bottom==false){
						lineHeighs.push(line.height);
					}else{
						lineHeighs.push(line.height+(line.top-prev_line_bottom));
					}
					if(typeof line.chars=='undefined' || !line.chars){
						return;
					}
					
					try{
						$.each(line.chars, function(cn, char){
							if(typeof viewer.charBounds[char.font]=='undefined'){
								viewer.charBounds[char.font] = {};
							}
							if(!char.inFont){
								string = string+" ";
								unicode_string = unicode_string+" ";
							}else{
								unicode_string = unicode_string+char.unicode;
								string = string+char.character;
								//string = string+char.unicode ;//+char.character;
							}
							
							var epta_offset = (char.charLeft);
							viewer.charBounds[char.font][char.character] = {left: 0, width: char.charWidth, kernedWidth: char.charWidth, height: char.fontSize};

							if(char.type3){
								positions = [];
							}else{
								positions[ln][cn] = { left: epta_offset-line.left };
							}
							
							if(typeof styles[ln]=='undefined'){
								styles[ln] = [];
							};
							
							var fbf = viewer.getFallbackFont(char.font);
							if(typeof fbf=='undefined'){
								fbf = default_font;
							}

							var char_font = !char.font?char.fallbackFont: char.font+", "+fbf+", "+char.fallbackFont
							

							styles[ln][cn] = {
								fontSize: char.fontSize,
								fill: char.color, 
								deltaY: 0, //(text.y?text.y-text.fontSize*scale:0),
								fontFamily: char_font+", STSong"
							};
							last_font = char.font;
						});
					}catch(e){
						console.error(e);
					}
					
					prev_line_bottom = line.bottom;
					string += "\n";
					
					if(paragraph.line_count != parseInt(ln)+1){
//							string += "\n";
					}
				}
				r = line.angle;
				angle = Math.degrees(line.angle);
			});

			string = string.replace(/\n$/, "");
			originY = "top";
			
			if(angle!=0){

			}

			if(typeof viewer.fonts[last_font]!='undefined'){
				fb_font = (viewer.fonts[last_font].fallBack);
			}else{
				fb_font = "Helvetica";
			}
			string = string;
			//font
			if(render_in_slider){
				var itext = new fabric.IText(string, {
					originalFont: last_font,
					unicode_string: unicode_string,
					left: (paragraph.left)-0.5,
					from_pdf: true,
					top: paragraph.top,
					width: paragraph.width+100,
					//height: paragraph.height,
					charSpacing: 1,
					fontSize: 12*temp_scale,
					customLineHeights: lineHeighs,
					//fontFamily: "Arail",
					angle: angle,
					//scaleY: -1,
					//flipY: true,/
					//TODO uncomment
					positions: positions,
					styles: styles,
					originX: 'left',
					originY: originY,
					padding:0,
					hasRotatingPoint: false,
					hasControls: false,
					selectable: true,
					editable: false,
				});
			}else{
				var itext = new fabric.IText(string, {
					originalFont: last_font,
					unicode_string: unicode_string,
					left: (paragraph.left)-0.5,
					from_pdf: true,
					top: paragraph.top,
					width: paragraph.width+100,
					//height: paragraph.height,
					charSpacing: 1,
					fontSize: 12*temp_scale,
					customLineHeights: lineHeighs,
					//fontFamily: "Arail",
					angle: angle,
					//scaleY: -1,
					//flipY: true,/
					//TODO uncomment
					positions: positions,
					styles: styles,
					originX: 'left',
					originY: originY,
					padding:0,
					selectable: true,
					editable: true,
					zIndex: paragraph.zIndex || 999

				});
			}
			
			if(render_in_slider===false){
				itext.setControlsVisibility({ mt: false, mb: false, ml: false, mr: false, bl: false, br: false, tl: false, tr: false, mtr: false, });
				itext.on("mousedown", ()=>{ TextEditor.hideEditor(); });
				itext.on("mouseup", TextEditor.click);
				itext.on("selection:changed", textFunc.selectionUpdate);
			}
			
			fcanvas.add(itext);
			let zz = paragraph.zIndex || 999;
			//itext.moveTo(zz);
			if(!render_in_slider){
				// console.log("zzz", zz);
				// alert("here ___ "+itext.unicode_string);

				viewer.pages[pageNumber].zIndexFix.push({
					obj: itext,
					zIndex: zz
				});

			}
			
			//fcanvas.moveTo(itext, zz);

			//return false;
		});

		if(!render_in_slider){
			await this.getAnnotations(page_object, page_height);
		}
		if(window.is_translate!==true){
			renderLinks(this.pages[pageNumber].fabricLinks, fcanvas, "Link");
			renderAnnotations(this.pages[pageNumber].fabricAnnots, fcanvas, "annotate");
			renderHtmlElements(this.pages[pageNumber].fabricForms, fcanvas);
		}
		
		//renderHighlights(this.pages[pageNumber].fabricHighlights, fcanvas);
		
		//TODO убираем фокус с элемента

		
		if(!render_in_slider){
			fcanvas.on("selection:cleared", textFunc.elementBlur);
			// canvas_data_url = fcanvas.toDataURL();
			// this.createThubmnail(canvas_data_url, pageNumber);
			fcanvas.on('after:render', ()=>{ 
				if(editorMenu.highlight && "highlight" in editorMenu){
					renderBorderBox(fcanvas, editorMenu.highlight); 	
				}
			});
		}
		

		
		$(document).trigger("page_filled_", [pageNumber]);
		$(document).trigger("after_fcanvas_filled", [fcanvas]);
		if(render_in_slider){
			if(pageNumber<maxRenderPages){
				fcanvas.renderAll();	
				this.pages[pageNumber].fcanvas.initialRendered = true;
			}else{
				this.pages[pageNumber].fcanvas.initialRendered = false;
			}
			
		}else{

			var sorted = viewer.pages[pageNumber].zIndexFix.sort(function(a,b) {
				return a.zIndex - b.zIndex;
			});
			sorted.forEach((o, zi)=>{
				o.obj.moveTo(zi);
			});

			fcanvas.renderAll();
		}
		
		this.rendered_pages+=1;


		//delete viewer.pages[pageNumber].fabricText;
		//delete viewer.pages[pageNumber].fabricElements;

		if(this.rendered_pages==this.total_pages){
			$(document).trigger("last_page_rendered");
		}

		return;
	};
	
	this.beforePageRender = async (page) =>{
		//console.log("before page render ", page.pageNumber);



		createPage(page.pageNumber);

		
		var page_canvas = document.getElementById(`page_canvas_${page.pageNumber}`),
			page_context = page_canvas.getContext('2d'),
			block_width = $(".m_page_outer").width(),
			viewport = page.getViewport(1);
		
		viewer.now_render = page.pageNumber;
		
		page_context.pageNumber = page.pageNumber;
		
		var page_width = window.innerWidth;
		if(page_width>=1920){
			page_width = 1920*0.6
		}else{
			if(window.devicePixelRatio==2){
				page_width *=0.6;
			}else{
				page_width *=0.7;
			}
		}

		
		if(this.scale==-1){
			this.scale = page_width/viewport.width;
			//$("#zoom_slider").val(parseFloat(this.scale).toFixed(1));
		}
		//TODO remove

		viewport = page.getViewport(this.scale);
		page_canvas.height = viewport.height;
		page_canvas.width = viewport.width;


		if(typeof this.pages[page.pageNumber]=='undefined'){
			this.pages[page.pageNumber] = {
				zIndexFix: [],
				currentZIndex: 0,
				scale: this.scale,
				matrix_offset: [0, 0],
				pn: page.pageNumber, 
				blockIterator: -1, 
				lineIterator: 0,
				fabricText: {}, 
				fabricElements: [],
				fabricImages: {},
				fabricLines: [],
				fabricRects: {},
				fabricLinks: [],
				fabricAnnots: [],
				fabricForms: [],
				fcanvas: false, 
				trans_stack: [],
				s_scale: false,
				canvas: page_canvas, 
				//context: page_context, 
				width: viewport.width, 
				height: viewport.height, 
				scale: this.scale, 
				//page: page 
			};
		};

//		console.log("page_context is", page_context);
		
		var pn = await page.render({canvasContext: page_context,  viewport: viewport, enableWebGL: true, renderInteractiveForms: false, imageLayer: false });
		// page.cleanup();
		delete viewport;
		//delete page;
		//console.log("render page: ", pn, page_context);
		this.afterPageRender(pn,page);
	};

	this.getAnnotations = async (page, height)=>{
		var annotations = await page.getAnnotations();
		
		annotations.forEach((v,i)=>{
			if('color' in v && v.color != null){
				var color = v.color.join(",");
				if(v.color.length==3){
					color = `rgb(${color})`;
				}else if(v.color.length==4){
					color = `rgba(${color})`;
				}
			}else{
				color = `rgb(200, 200, 1);`;
			}
			v.rect = v.rect.map(pp => pp*viewer.scale);
		
			var object_rect = {
				left: v.rect[0],
				top: height-v.rect[1]-(v.rect[3]-v.rect[1]),
				width: v.rect[2]-v.rect[0],
				height: v.rect[3]-v.rect[1] //(height-v.rect[3])-(height-v.rect[1]),
			}
			
			
			var push_to = "fabricLinks",
				parsed_element = {};
		
			switch(v.subtype){
				case 'Link':
					push_to = "fabricLinks";
					parsed_element = {
						url: v.url,
						subtype: "Link"
					};
				break;
				case 'StrikeOut':
					push_to = "fabricAnnots";
					var eh = ((height-v.rect[3])-(height-v.rect[1]));
					eh = (eh<0?eh*-1:eh);
					object_rect['height'] = 3*viewer.scale;
					object_rect['top'] =  ((height-v.rect[1])-(eh/2)),
					
					parsed_element = {
						subtype: "StrikeOut",
						title: v.title,
						contents: v.contents,
						fill_original: color,
						fill: color
					};
					
				break;
				case 'Highlight':
					push_to = "fabricAnnots";
					parsed_element = {
						subtype: "Highlight",
						title: v.title,
						contents: v.contents,
						fill_original: color,
						fill: color
					};
				break;
				//TODO form elements
				case 'Widget':
					push_to = "fabricForms";
					parsed_element = {
						subtype: "Widged",
						fieldType: getFieldType(v.fieldType, v),
						alternativeText: v.alternativeText,
						color: v.color,
						fieldName: v.fieldName,
						fieldValue: v.fieldValue,
						fieldMaxLen: v.maxLen,
						fieldMultiLine: v.multiLine,
						fieldReadOnly: v.readOnly,
						fieldTextAlignment: v.textAlignment,
						fieldOptions: "options" in v?v.options:false,
					};
				break;
				default:
					console.warn("unsupported annotation", v);
				break;
			}
			
			
			parsed_element  = $.extend(parsed_element, object_rect);
			this.pages[page.pageNumber][push_to].push(parsed_element);			
		});
		
	};
	
	this.setupAnnotations = function (page, viewport, canvas, $annotationLayerDiv) {
		var canvasOffset = $(canvas).offset();
		var promise = page.getAnnotations().then(function(annotationsData) {
			viewport = viewport.clone({
				dontFlip: true
			});

			for (var i = 0; i < annotationsData.length; i++) {
				var data = annotationsData[i];
				var annotation = PDFJS.Annotation.fromData(data);
				if (!annotation || !annotation.hasHtml()) {
					continue;
				}

				var element = annotation.getHtmlElement(page.commonObjs);
				data = annotation.getData();
				var rect = data.rect;
				var view = page.view;
				rect = PDFJS.Util.normalizeRect([
					rect[0],
					view[3] - rect[1] + view[1],
					rect[2],
					view[3] - rect[3] + view[1]
				]);
				element.style.left = (canvasOffset.left + rect[0]) + 'px';
				element.style.top = (canvasOffset.top + rect[1]) + 'px';
				element.style.position = 'absolute';

				var transform = viewport.transform;
				var transformStr = 'matrix(' + transform.join(',') + ')';
				CustomStyle.setProp('transform', element, transformStr);
				var transformOriginStr = -rect[0] + 'px ' + -rect[1] + 'px';
				CustomStyle.setProp('transformOrigin', element, transformOriginStr);

				if (data.subtype === 'Link' && !data.url) {
					// In this example,  I do not handle the `Link` annotations without url.
					// If you want to handle those annotations, see `web/page_view.js`.
					continue;
				}
				$annotationLayerDiv.append(element);
			}
		});
		return promise;
	};
	
	this.renderPDF = async (pdf) =>{
		window.pppdf = pdf;
		this.total_pages = pdf.numPages;
		$(document).trigger("before_pages_render", [pdf.numPages]);

		for(let pi=0; pi!=pdf.numPages; pi++){
			pdf_fill_color = false;

			await sleep(2);
			var page = await pdf.getPage(pi+1); //

			clip_stack = [];
			viewer.last_clip = false;
			$(document).trigger("page_proccess", [pi+1, pdf.numPages]);

			await this.beforePageRender(page);
			page.cleanup();
			if(pi>=1000){
				$(document).trigger("last_page_rendered");
				break;
			}
			delete page;
		}
		delete this.loadingTask;
	};

	this.createPDF = async () => {	
		createPDF = new CreatePDF();
		await createPDF.createDoc();

	}
	
	this.bind();
	return this;
}

 
window.viewer = new Viewer();
 
 
window.reversed_canvas = true;


window.scale = false;

var current_block_width = 0;






document.addEventListener('pagesinit', function () {
  // We can use pdfViewer now, e.g. let's change default scale.
  pdfViewer.currentScaleValue = 1;
});



fabric.IText.prototype.onKeyUp = (function(onKeyUp) {
	return function(e){
		var th = this,
			keycode = e.keyCode,
			cursor_location = this.get2DCursorLocation(),
			line = cursor_location.lineIndex,
			pos = cursor_location.charIndex;

		if(keycode==9){
			alert("del");
		}
		
		var printable = 
			(keycode > 47 && keycode < 64)   || // number keys
			keycode == 32 || keycode == 13  || keycode==173  || // spacebar & enter 
			(keycode > 64 && keycode < 91)   || // letter keys
			(keycode > 95 && keycode < 112)  || // numpad keys
			(keycode > 185 && keycode < 193) || // ;=,-./` (in order)
			(keycode > 218 && keycode < 223);   // [\]' (in order)
		
		
		//th.styles[line][pos].fontFamily = "ArialNarrow-Bold";
		
		this.canvas.renderAll();
		
		onKeyUp.call(this, e);
	}
})(fabric.IText.prototype.onKeyUp)






function selectFileBrowser(contentType, multiple){
    return new Promise(resolve => {
        let input = document.createElement('input');
        input.type = 'file';
        input.multiple = multiple;
        input.accept = contentType;

        input.onchange = _ => {
            let files = Array.from(input.files);
            if (multiple){
                resolve(files);
            }else{
                resolve(files[0]);
            }
        };

        input.click();
    });
};

async function openFile(selected_file=false){
	var data = false;
	if(selected_file){
		data = await viewer.getBlob(selected_file);
	}else{
		if(typeof electron_remote!=='undefined'){
			var fs = require('fs'),
				util = require('util'),
				readFile = util.promisify(fs.readFile);

			file = electron_remote.dialog.showOpenDialog({
				properties: ['openFile'],
				filters: [
					{
					"name": "Portable Document Format",
					"extensions": ["pdf"]
					}
				]
			});
			$(document).trigger("open_pdf_file", file[0]);
			
			if(typeof file=='undefined'){ return false; };
			data =  await readFile(file[0], "binary");
		}else{
			file = await selectFileBrowser("application/pdf", false);
			$(document).trigger("open_pdf_file", file[0]);
			data = await viewer.getBlob(file);
		}
	}

	viewer.pages = {};
	viewer.fonts = {};
	$("#pdf_editor_pages").html("");
	$(".page-list").html("");
	
	window.viewer.init(false, data);
	
	return false;
};

$(document).on("all_parts_loaded", function(){

	//renderPDF();
	window.is_browser = false;
	try{
		window.electron_remote = require('electron').remote
		
	}catch(err){
		window.is_browser = true;
	}
	
	$(document).on("click", "#open_file", openFile);

});

//===================================================================

var fabricText = [],
	blockIterator = "ebola",
	currentFontSize = 0,
	currentColor = [255,255,255],
	currentFont = "Helvetica";
	
var matrix_offset = [0, 0],
	line_offset_top = 0;

var lineIterator = false,
	blockIterator = false;
	
	
var last_dash = [];
	//fontSize


function createNewTextBlock(){


}

var colletctTexts = function(fn, args, th, pageNumber){
	
	if(typeof pageNumber=='undefined'){
		//pageNumber = 1;
	}
	var last_dash = [];
	// if(pageNumber==1){
	//console.log(fn, args);
	// 	alert();
	// }
	
	switch(fn){
		case 2: 
		//TODO новая линия

		break;
		
		case 6: //TODO set line dash
			last_dash = args[0];
		break;
	
		case 10: // save
//			console.log("=== save ===", viewer.pages[pageNumber].trans_stack);
			//viewer.pages[pageNumber].trans_stack.push(viewer.pages[pageNumber].s_scale);
		break;
		case 11: //restore
		break;
		case 12: //transform

			
		break;
		case 31: //start text

			createNewBlock(pageNumber);


		break;
		case 32: //end text
			createNewBlock(pageNumber);
		break;
		case 33:
			
		break;
		
		case 36: //set Leading
			
		break;
		
		case 37: //set font
			viewer.pages[pageNumber].currentFontSize = args[1];
			viewer.pages[pageNumber].currentFont = args[0];
		break;
		
		case 40: //moveText
			//Todo добавить проверку на ширину
			if(args[1]<0 || args[1]>0 || args[0]>0){// || text_offset>double_char_width){ //если идет смещение строки - создаем новую линию
				viewer.pages[pageNumber].line_offset_top = viewer.pages[pageNumber].line_offset_top+args[1];
				createNewBlock(pageNumber);
			}
		break;
		
		
		case 41: //TODO добавлено на документе renter.pdf
			createNewBlock(pageNumber);
			//createNewLine(pageNumber);
		break;
		
		case 42: //text matrix setTextMatrix
			createNewBlock(pageNumber);
			//createNewLine(pageNumber);
			//viewer.pages[pageNumber].line_offset_top = 0;
		break;
		
		case 43: //next line
			createNewLine(pageNumber);
		break;
		
		case '44': //show text default
			viewer.pages[pageNumber].first_in_block = false;
		break;
		
		case 44.1: //show fabric text
			viewer.pages[pageNumber].first_in_block = false;
		break;
		
		//TODO colors here
		case 59:
			viewer.pages[pageNumber].currentColor = "rgb("+args.join(',')+")";
		break;
		
		case 91:
//			viewer.addVector({
//			}, pageNumber);
		break
		
	}
	
	
}



function Block(){
	this.lines = {};
	this.top = 0;
	this.left = 0;
	this.width = 0;
	this.height = 0;
	this.bottom = 0;
	this.line_count = 0;
	this.lefts = [];
	this.rights = [];
	this.tops = [];
	this.bottoms = [];
	this.widths = [];
	this.avg_lh = 0
	
	this.getText = ()=>{
		var string = "";
		$.each(this.lines, function(i,line){
			if(typeof line.chars!='undefined'){
				$.each(line.chars, function(ci, char){
					string += char.unicode;
				});
			}
		});
		return string;
	};
	
	
	this.addLine = (line) =>{	
	
		this.lefts.push(line.left);
		this.rights.push(line.right);
		this.tops.push(line.top);
		this.widths.push(line.width);
		this.bottoms.push(line.bottom);
		if(typeof this.lines[this.line_count] == 'undefined'){
			this.lines[this.line_count] = {};
		}
		
		this.lines[this.line_count] = line;
		this.line_count++;
		this.caclBlockSize();
	};


	this.getLastLine = ()=>{
		return typeof this.lines[this.line_count-1]!='undefined'?this.lines[this.line_count-1]:false;
	}

	this.updateLine = (line, page_height) =>{
	
		this.lefts.push(line.left);
		this.rights.push(line.right);
		this.tops.push(line.top);
		this.widths.push(line.width);
		this.bottoms.push(line.bottom);
		if(typeof this.lines[this.line_count] == 'undefined'){
			this.lines[this.line_count] = {};
		}
		
		this.lines[this.line_count-1] = line;
		this.caclBlockSize();
	};

	
	
	
	this.getCurrentLine = () =>{
		return this.lines[this.line_count-1];
	}
	
	this.cl = () => {
		return this.line_count;;
	}
	
	this.caclBlockSize = () =>{
		this.left = Math.min(...this.lefts);
		this.right = Math.max(...this.rights);
		this.top = Math.min(...this.tops); // для декартовой Math.max(...this.tops);
		this.bottom = Math.max(...this.bottoms); // для декартовой min
		this.width = Math.max(...this.widths);
		
		
		this.height = this.bottom-this.top;
		this.avg_lh = this.height/this.line_count; 
	};
	
	return this;
}

function Line(){
	this.top = false;
	this.left = false;
	this.right = false;
	this.width = false;
	this.height = false;
	this.chars = {};
	this.chars_count = 0;
	this.spaceWidth = 0;
	this.text = "";
	
	this.tops = [];
	this.lefts = [];
	this.heights = [];
	this.rights = [];

	this.getPlainText = () =>{
		var str = "";
		$.each(this.chars, function(i, v){
			str += v.unicode;
		});
		return str;
	}
	
	this.addText = (text, page_height, dbg=false) => {
		var that = this;// lefts = [], tops = [], heights = [], rights = [];
			//bottoms = [];
		ff = 0;
		var rotate_flag = false;
		$.each(text, function(i, char){
			rotate_flag = char.rad;
			//alert(char.blockLeft);
			that.rights.push(char.blockLeft+char.blockWidth);
			//that.rights.push(char.blockLeft+char.charWidth);

			that.lefts.push(char.blockLeft);
			that.tops.push(char.blockTop); //TODO для декартовой убери фонт сайз			
			that.heights.push(char.fontSize);
			
			//bottoms.push(char.blockTop-char.fontSize);
			that.spaceWidth = char.spaceWidth;
			that.chars[that.chars_count] = char;
			that.chars_count++;
			ff = char.fontSize
			that.text += char.unicode;
		});	


		if(render_in_slider===true){
//			this.text+=" ";
		}

		if(this.left!==false){
			that.lefts.push(this.left);
		}
		this.updateDimension(rotate_flag);
	};
	
	this.updateDimension = (rotate_flag)=>{
		if(rotate_flag<0){
			console.log("rotate << 0 ");
			this.top = Math.max(...this.tops);
			this.left = Math.min(...this.lefts);
			this.right = Math.max(...this.rights);
			this.width = this.right-this.left;
			this.height = Math.max(...this.heights);
			this.bottom = this.top+this.height;						
		}else{

			this.top = Math.min(...this.tops);
			this.left = Math.min(...this.lefts);
			this.right = Math.max(...this.rights);
			this.width = this.right-this.left;
			this.height = Math.max(...this.heights);
			this.bottom = this.top+this.height;
		}
	}
	
	this.onObjectSelected = function(e){
	
		console.log(e);
	}
	
	
	return this;
}

// class Line{
// 	constructor(text){
// 		this.top = 0;
// 		this.left = 0;
// 		this.right = 0;
// 		this.height = 0;
// 		this.bottom = 0;
// 		$.each(text, (i, char)=>{
// 			rotate_flag = char.rad;

// 			that.rights.push(char.blockWidth);
// 			that.lefts.push(char.blockLeft);
// 			that.tops.push(char.blockTop); 
// 			that.heights.push(char.fontSize);
// 			that.spaceWidth = char.spaceWidth;
// 			that.chars[that.chars_count] = char;
// 			that.chars_count++;
// 			ff = char.fontSize
// 			this.text += char.unicode;
			
// 		});		



// 		this.texts = "";
// 	}
	
// 	addText(){

// 	};
// 	getTop(){
// 		return toInt(this.top);
// 	}
// 	getLeft(){
// 		return toInt(this.left);
// 	}
// 	getHeight(){
// 		return toInt(this.height);
// 	}
// 	getBottom(){
// 		return toInt(this.bottom);
// 	}

// }


window.epta_scale = 1;
function mergeBlocks(stopMerge=false, pageNumber, blocks, page_width, page_height, scale, merge_lines=true){

	window.epta_scale = scale;
	var group_iterator= 0,
		grouped = [],
		draw_group = false,
		groupItems = [],
		prev_block_rigth = false,
		prev_block_top = false,
		prev_block_left = false,
		prev_block_lefts = [],
		prev_str = false,
		prev_space_width = false,
		new_blocks = {},
		block_iterator = 0,
		line_iterator = 0,
		//bline = new Line(),
		prev_line = false,
		line_bkp = false;
	

	var create_new_block = false;
	
	$.each(blocks, function(i, block){
		if(typeof new_blocks[block_iterator]=='undefined'){
			var cblock = new Block();
			new_blocks[block_iterator] = cblock;
		}


//		if(typeof bline =='undefined'){
//			var bline = new Line();
//		}
//zIndex

		

		$.each(block, function(ln, line){
			if(line.length==0){ return; }

			var current_line = new Line();
			current_line.addText(line, page_height, true);
			if(current_line.chars_count<1){
				return;
			}
			
			var angle = line[0].rad;
			
			current_line.angle = angle;
			debug_merge = false;
			if(block_iterator>15){
				debug_merge = true;
			}
			
			if(!prev_line){
				//getLastLine
				new_blocks[block_iterator].addLine(current_line);
				new_blocks[block_iterator].zIndex = block.zIndex || 1;

			}else{
				if(prev_line.spaceWidth<1){
					prev_line.spaceWidth = 3;
				}

				if(!stopMerge &&
					current_line.left > prev_line.left &&
					(parseInt(prev_line.right+prev_line.spaceWidth)>= parseInt(current_line.left)) 
					&& toInt(current_line.bottom)==toInt(prev_line.bottom)
				){
					cl = new_blocks[block_iterator].getCurrentLine(); 


					cl.addText(line, page_height,false);

					new_blocks[block_iterator].updateLine(cl, 0);
				}else{

					if(Number.isInteger(new_blocks[block_iterator])){
						return;

					}

					prev_line = new_blocks[block_iterator].getLastLine();
					// добавляем линии в абзац
					if(merge_lines
						&& current_line.left >= prev_line.left-2
						&& prev_line.top < current_line.top 
						&& current_line.top < new_blocks[block_iterator].bottom+new_blocks[block_iterator].avg_lh+1
						&& current_line.top != new_blocks[block_iterator].top
						&& current_line.height == new_blocks[block_iterator].lines[0].height
						&& toInt(current_line.left) == toInt(new_blocks[block_iterator].left)
						){
						new_blocks[block_iterator].addLine(current_line);
//						console.log("===to exist ==== ");

						//create_new_block = false;
					}else{
						block_iterator++;
						new_blocks = cb(new_blocks, block_iterator);
						new_blocks[block_iterator].addLine(current_line);
					}					
				}
			}
			//window.eba = new_blocks[block_iterator];
			//console.log(current_line);
			//			console.log(current_line.lefts);
			//			alert();
			
			prev_line = current_line;
		});
	});
	window.cblock = new_blocks;
	return new_blocks;
}

function cb(new_blocks, i){
	new_blocks[i] = new Block();
	return new_blocks;
}

function getBlockLeft(chars=[]){
	if(chars.length==0){
		return 0;
	}
	var lefts = [];
	chars.forEach(function(v,i){
		lefts.push(v.blockLeft);
	});
	return Math.round(Math.min(...lefts).toFixed(0));
}




function createNewBlock(pageNumber){

	viewer.pages[pageNumber].lineIterator = 0;	
	viewer.pages[pageNumber].blockIterator++;

	lineIterator = viewer.pages[pageNumber].lineIterator;
	blockIterator = viewer.pages[pageNumber].blockIterator;

	viewer.pages[pageNumber].currentZIndex++;
	console.log("create new text block with zindex", viewer.pages[pageNumber].currentZIndex);
	var temp = {zIndex: viewer.pages[pageNumber].currentZIndex};
	

	viewer.pages[pageNumber].fabricText[blockIterator] = temp;
	viewer.pages[pageNumber].fabricText[blockIterator][lineIterator] = [];

}

function createNewLine(pageNumber){
	viewer.pages[pageNumber].lineIterator++;
	lineIterator = viewer.pages[pageNumber].lineIterator;
	blockIterator = viewer.pages[pageNumber].blockIterator;

	if(typeof viewer.pages[pageNumber].fabricText[blockIterator]=='undefined'){
		alert("error 1s");
		viewer.pages[pageNumber].lineIterator = 0;
		//	fabricText[blockIterator] = {};
		viewer.pages[pageNumber].fabricText[blockIterator] = {};
	}
	viewer.pages[pageNumber].fabricText[blockIterator][lineIterator] = [];
	return;

}

function drawGroup(grouped, fcanvas){
	if(grouped.length>0){
		grp = new fabric.Group(grouped, {
			left: 0,
			top: 0,
			angle: 0
		});
		fcanvas.add(grp);
		grouped = [];
	}
};


function createPage(pn, append=true){
	if(render_in_slider){
		var page_prev = `
		<div class="slider_item slide_${pn}" style="position: relative;" data-page-id="${pn}" id="page_outer_${pn}">
			<div class="oo">
				<canvas class='page_canvas'	data-rotate="0" data-page-id="${pn}" id="page_canvas_${pn}"></canvas>
				<div class="translate_preview" id="translate_preview_${pn}" style="position: absolute;">
				</div>
				<div style='display: none !important;' class="hidden after_translate_page translated_canvas trans_canvas_outer_${pn}">
					<canvas  id="translated_canvas_${pn}"></canvas>
				</div>
			</div>
		</div>`;
		$("#pages_previews_here").append(page_prev);
		return page_prev;
	}else{
		var page_prev = `
		<div class="page-main-part m_page_outer" data-page-id="${pn}" id="page_outer_${pn}" style=''>
			<div class="canvas-wrap" style="min-height: auto;">
				<canvas style='' data-pn="${pn}" id="page_canvas_${pn}"></canvas>
			</div>
			<div class="page-side-bar">
				<div class="page-tools-menu">
					<a href="#" class="delete-page">
						<img src="img/icon-red-basked.svg" alt="">
					</a>
				</div>
			</div>
		</div>

		<div class="page-between">
			<a href="#" class="insert-page">Insert Page Here</a>
		</div>`;
	}
	if(append){
		$("#pdf_editor_pages").append(page_prev)
	}else{
		return page_prev;
	}
}

function toInt(x){
	return Math.round(x);
}

Math.degrees = function(radians) {
	return radians * 180 / Math.PI;
};

var intRange = ( a , b ) => Array.from( new Array( b > a ? b - a : a - b ), ( x, i ) => b > a ? i + a : a - i );


function rgbToHex(red, green, blue){
	var out = '#';
	for (var i = 0; i < 3; ++i) {
		var n = typeof arguments[i] == 'number' ? arguments[i] : parseInt(arguments[i]);
		if (isNaN(n) || n < 0 || n > 255) {
			return false;
		}

		out += (n < 16 ? '0' : '') + n.toString(16);
	}
	return out
};


function average(nums) {
	return nums.reduce((a, b) => (a + b)) / nums.length;
}


function patternFill(){
	console.warn("pattern fill not support");
	stop_it();
	return false;
	ctx.save();
	var pattern = current.fillColor.getPattern(ctx, this);
	patternTransform = ctx.mozCurrentTransform;
	ctx.restore();
	ctx.fillStyle = pattern;
	return false;
}

function buildCharsTable(font, char, unicode, char_width) {
	if (typeof viewer.chars_table[font] == 'undefined') {
		viewer.chars_table[font] = {};
		viewer.chars_table_inverse[font] = {};
		viewer.embeded_char_widths[font] = {};
	}
	viewer.chars_table[font][unicode] = char;
	viewer.chars_table_inverse[font][char] = unicode;

	viewer.embeded_char_widths[font][char] = char_width;
	
	//viewer.chars_to_unicode[font][char] = unicode;
}

