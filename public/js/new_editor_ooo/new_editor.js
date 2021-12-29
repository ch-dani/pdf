
function isSafari(){
	var ua = navigator.userAgent.toLowerCase(); 
	if (ua.indexOf('safari') != -1){
		if(ua.indexOf('chrome') > -1){
			return false;
		}else{
			return true;
		}
	}
	return false;
}

$(document).on("selected_file_name", function(e,file_name){
	viewer.pdf_name = file_name;
})

$(document).on("keydown",".hidden-text-textarea", function(e){
	if(current_editor==='underline'){
		return false;
	}
})

$(document).on("click", ".delete-page", function(e){
	e.preventDefault();
	$(this).closest(".m_page_outer").remove();
});

$(document).on(".hidden-text-textarea","focus focusin", function(e){
	console.log("focus in");
})

document.addEventListener('touchstart', function(e) {e.preventDefault()}, false);
document.addEventListener('touchmove', function(e) {e.preventDefault()}, false);

var total_images_drawed = 0;
$(document).on("new_editor_file_selected", fileSelectedNew);
$(document).on("before_pages_render", beforePageRender);
$(document).on("after_page_render", afterPageRender);
$(document).on("after_fcanvas_filled", afterPageCanvasFilled);
$(document).on("click", "#start_find_text", findText);
$(document).on("click", "#start_replace", replaceText);
$(document).on("click", "#start_replace_and_find_text", replaceAndFind);
$(document).on("click", ".new-pdf", startBlank);

$(document).on("change", "#zoom_slider", changeZoom);

$(document).on("click", ".insert-page", insertNewPage);
$(document).on("click", ".download-result-link", function(e){
	e.preventDefault();
	saveBlob(viewer.edited_blob, viewer.pdf_name+"-edited."+"pdf")
});

$(document).on("click", ".highlight-text.hl_higlight", function(e){
	e.preventDefault();
})

$(document).on("last_page_rendered", function(e){
	$(`[data-editor-name="text"]`).click();
})

$(document).on("click", ".apply-btn", async function(){
	createPDF = new CreatePDF();
	await createPDF.createDoc();
});

$(document).bind('pagerendered', function (e) {
    console.log('Page rendering complete.');
});

$(document).on("click", ".app-tools .tools-menu .tools-menu-item, .app-tools .tools-menu .sub_menu_item, [data-editor-name='annotate'], .free_draw_color, .hl_underline", changeEditor);

$(document).on('click', '.zoom-plus', function(){
	var slider = $('#zoom_slider');
	var newVal = parseInt(slider.val()) + 1;
	slider.val(newVal);
});

$(document).on('click', '.zoom-minus', function () {
	var slider = $('#zoom_slider');
	if(newVal > 0) {
		var newVal = parseInt(slider.val()) - 1;
		slider.val(newVal);
	}	
});

$(document).on("set_canvas_size", function (e, pn, width, height) {
	$(`#page_outer_${pn} .canvas-wrap`).css({
			'max-width': width,
			'max-height': height,
			"overflow": 'auto'
	});
	
	if (pn == 1) {
		var scrolled = 0,
			scrollState = true,
			zoom,
			zoomTop;

		$(window).on('scroll', function () {
			if (scrollState) {
				zoom = $('.zoom-outer');
				zoomTop = zoom.offset().top;
				scrollState = false;
			}
			scrolled = window.pageYOffset;

			if (scrolled > (zoomTop - 100)) {
				zoom.addClass('fixed');
			} else {
				zoom.removeClass('fixed');
			}
		});
	}
});

$(document).ready(function(){
	//TODO for debug
	pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/new_editor/libs/pdfjs-dist/build/pdf.worker.js';

	$("[data-editor-name='text']").addClass("active");
	$("[data-editor-name='text']").closest("li").addClass("active");
 
//	window.show_debug =  true;
// 	blocker.hideUploader();
// 	blocker.showEditor();
// //	window.viewer.init("/example_pdf/19_ Mobile section 13 12.pdf", false);
//	window.viewer.init("/example_pdf/gekko.pdf", false);

});

function startBlank(){
	//TODO for debug
	blocker.hideUploader();
	blocker.showEditor();
	window.viewer.init("/example_pdf/blank.pdf", false);
}

var current_editor = "text";

async function canvasMouseUp(obj){
	if(current_editor==='underline' && obj.target){
		newAnnotateEditor.makeUnderline(obj.target);
	}
}

async function canvasMouseDown(object){
	var point = this.getPointer();
	switch(current_editor){
		case 'skip_it':

		break;
		case 'images':
		case 'sign':
		case 'forms':
			if(newImagesEditor.imageObject){
				newImagesEditor.imageAppend(this, point);
			}
			if(newFormsEditor.moved_element){
				newFormsEditor.elementAppend(this, point, true, false);
			}
			
		break;
		
		case 'rectangle':
		case 'elipse':
		case 'whiteout':
			
		break;
	}
	if(object.target===null){
		newRectangleEditor.hideEditors();
		newImagesEditor.hideEditor();
		newFormsEditor.hideEditor();
	
		if(TextEditor.editor_active){
			TextEditor.hideEditor();
			TextEditor.active_object = false;
		}else{
			if(current_editor=='text' && !TextEditor.active_object){
				TextEditor.addText(this, point);
			}else{
				TextEditor.active_object = false;
			}
		}
	}
}

async function changeEditor(e){
	e.preventDefault();
	current_editor = $(this).data("editor-name");
	$(document).trigger("change_editor", [current_editor]);


	
	disableAllElements(false);
	//toggleLinksAndAnnotationsView(false, 'Link');
	newRectangleEditor.hideEditors();
	newImagesEditor.hideEditor();
	newAnnotateEditor.hideEditor();
	newFormsEditor.hideEditor();
	TextEditor.hideEditor()
	DrawMode.leaveDrawMode();
	
	$(".ff_element").css("pointer-events", "none");
	newRectangleEditor.annotate_type = false;


	switch(current_editor){

		case 'underline':
			disableAllElements(false);
			enableElements(["i-text", "rect"], [false], true);
			
		break;
		case 'free_draw':
			disableAllElements(false);
			var color = 
			DrawMode.drawingColor = $(e.target).attr("draw-style")  || $(e.target).css("background-color");
			DrawMode.enterDrawMode();

		break;
		case 'skip_it':
			disableAllElements(false);
		break;
		case 'images':
			enableElements(["image", "group"], ["image", "image_group"]);
//			enableElements(["image"], ["image"]);
		break;
		case 'links':
			//console.log("toggle links");
			//toggleLinksAndAnnotationsView(true, 'Link');
			enableElements(["Link", "rect"], [false, "link"]);
		break;
		case 'text':
			enableElements(["i-text"], [false]);
		break;
		case 'ellipse':
		case 'rectangle':
			enableElements(["path", "rect", "ellipse"], ["rect", "ellipse", "rectangle"]);
		break;
		case 'annotate':
			enableElements(["rect"], ["annotate"]);
			//toggleLinksAndAnnotationsView(true, 'annotate');
			enableElements(["rect", "arrow"], ["annotate", "StrikeOut", "Highlight"]);
			newRectangleEditor.annotate_type = $(this).data("annotate-type");
			newRectangleEditor.annotate_highlite_color = $(this).css("background-color");
		break;
		case 'forms':
			$(".ff_element").css("pointer-events", "all");
			enableElements(["rect"], ["form_element_outer"]);
		break;
		default:
			disableAllElements(false);
		break;
	}
}

function enableElements(types, subtypes=false, lockMovement=null){
	types = types.map(x=>x.toLowerCase());
	if(subtypes!=false){
		subtypes = subtypes.map((x)=>{
			if(typeof x=='string'){
				return x.toLowerCase()
			}
			return x;
		});
	}

	$.each(viewer.pages, (i, page)=>{
		$.each(page.fcanvas.getObjects(), function(i2,obj){
			if("subtype" in obj==false){ obj.subtype = false; }
			if(typeof obj.subtype=='string'){ 
				obj.subtype = obj.subtype.toLocaleLowerCase();
			}
			///if(typeofobj.type==type && obj.subtype==subtype){
			if(types.indexOf(obj.type)!==-1 && subtypes.indexOf(obj.subtype)!==-1){
				obj.set({"evented": true, "selectable": true, "lockMovementX": lockMovement===null?false:lockMovement, "lockMovementY": lockMovement===null?false:lockMovement});
			}else{
				obj.set({"evented": false, "selectable": false, "lockMovementX": lockMovement===null?true:lockMovement, "lockMovementY": lockMovement===null?true:lockMovement});
			}
		});
	});	
}



function disableAllElements(fcanvas, skip_it=false){
	$(".ff_element").css("pointer-events", "none");
	if(fcanvas){

		fcanvas.getObjects().forEach((obj,i)=>{
			if(skip_it && skip_it==obj.type){

			}else{
				obj.set({
					evented: false,
					selectable: false,
					lockMovementX: true,
					lockMovementY: true
				})
				// obj.set("evented", false);
				// obj.set("selectable", false)
			}
		});
	}else{
		$.each(viewer.pages, (i, page)=>{
			$.each(page.fcanvas.getObjects(), function(i2,obj){
				if(skip_it && skip_it==obj.type){
					
				}else{
					obj.set({
						evented: false,
						selectable: false,
						lockMovementX: true,
						lockMovementY: true
					})
					// obj.set("evented", false);
					// obj.set("selectable", false);
				}
			});
		});
	}
}

async function fileSelectedNew(e, file){
	await openFile(file); //viewer.js
};

$(document).on("click", ".open_undo_modal", function(){
	viewer.pages[1].fcanvas.undo(viewer.pages[1].fcanvas);
});

$(document).on("click", ".open_redo_modal", function(){
	viewer.pages[1].fcanvas.redo();
});

var footer_is_display = false;
async function afterPageCanvasFilled(e, fcanvas){
	disableAllElements(fcanvas, "i-text");
	// fcanvas.on('object:modified',function(){
	// 	updateCanvasState(this);
	// });

	// fcanvas.on('object:moving',function(){
	// 	updateCanvasState(this);
	// });


	// fcanvas.on('object:added',function(){
	// 	updateCanvasState(this);
	// });



	if(!footer_is_display){
		$(".footer-editor").css({"max-height": "auto", "pointer-events": "all", "opacity": 1, "height": "65px", "max-height": "65px"});
		footer_is_display = true;
	}
}

async function afterPageRender(e, pn, pageObject){
	$("#simplePDFEditor").addClass("proccessed");
}
async function beforePageRender(e){
	blocker.hideUploader();
	blocker.showEditor();
}


var prev_search = "";
var finded_texts = [];
var searched_iterator = 0;
function findText(){
	finded_texts= [];
	var v = $("#find_text_input").val();
	caseSensetive = $("#find_match_case").is(":checked")?"":"i";
	var founded = false;
	if(prev_search==v){
		searched_iterator++;
	}else{
		searched_iterator = 0;
	}
	prev_search = v;
	$.each(viewer.pages, function(i, page){
		texts = page.fcanvas.getObjects("i-text");
		texts.forEach((text)=>{
			try{
				if(typeof text.unicode_string=='undefined'){
					text.unicode_string = text.text;
				}
				start = text.unicode_string.search(new RegExp(v, caseSensetive));
				end = v.length;
				finded_texts.push();
				if(start!=-1){
					finded_texts.push({
						text: text,
						start: start,
						end: start+end,
						page: page
					});
				}
			}catch(e){
				console.log(text.unicode_string);
				console.error(e);
			}
		});
		
		$(".found_matches").html(finded_texts.length);
		if(finded_texts.length>0){
			if(typeof finded_texts[searched_iterator]=='undefined'){
				searched_iterator = 0
			}
			finded_texts[searched_iterator].page.fcanvas.setActiveObject(finded_texts[searched_iterator].text);
			finded_texts[searched_iterator].text.enterEditing()
			finded_texts[searched_iterator].text.setSelectionStart(finded_texts[searched_iterator].start);
			finded_texts[searched_iterator].text.setSelectionEnd(finded_texts[searched_iterator].end);
		}
		
	});
}

function replaceText(){
	var obj = finded_texts[searched_iterator].text;
	var v = $("#find_text_input").val();
	var replace_to = $("#replace_text_input").val();
	obj.positions = [];
	obj.set("fontFamily", "Helvetica");
	obj.setSelectionStyles({fontFamily: "Helvetica"}, 0, obj.text.length);
	obj.text = obj.unicode_string;
	obj.unicode_string = obj.text = obj.text.replace(v, replace_to);
	delete finded_texts[searched_iterator];
	$(".found_matches").html(finded_texts.length);
	obj.canvas.renderAll();
}

function replaceAndFind(){
	replaceText();
	findText();
}


function insertNewPage(e){
	e.preventDefault();
	
	var before_first = false;
	if($(this).hasClass("insert_first_page")){
		before_first = true;
	}
	//insert
	
	bt = $(this).closest(".page-between");
	var pageNumber = Object.keys(viewer.pages).length+1;
	var page_template = $(createPage(pageNumber,false));
	if(before_first){
		$("#pdf_editor_pages").prepend(page_template);
	}else{
		page_template.insertAfter(bt);
	}
	viewer.pages[pageNumber] = {};

	var fcanvas = new fabric.Canvas(`page_canvas_${pageNumber}`, {
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

	fcanvas.pageNumber = pageNumber;
	fcanvas.on("mouse:down",canvasMouseDown);

	viewer.pages[pageNumber].fcanvas = fcanvas;
	viewer.pages[pageNumber].width = viewer.pages[1].width;
	viewer.pages[pageNumber].height = viewer.pages[1].height;
	
	fcanvas.setWidth(viewer.pages[1].width);
	fcanvas.setHeight(viewer.pages[1].height);
	$(".canvas-container").css({background: "white"})

}


function changeZoom(){
	var val = $(this).val();
	console.log("zoom to ", val);
	$.each(viewer.pages, function(pn,page){
		//page.fcanvas.setZoom(val);
		$(page.fcanvas.lowerCanvasEl).width(page.fcanvas.lowerCanvasEl.width*val);
		$(page.fcanvas.upperCanvasEl).width(page.fcanvas.upperCanvasEl.width*val);

		$(page.fcanvas.lowerCanvasEl).height(page.fcanvas.lowerCanvasEl.height*val);
		$(page.fcanvas.upperCanvasEl).height(page.fcanvas.upperCanvasEl.height*val);

		$(page.fcanvas.upperCanvasEl).closest(".epta_canvas").width(page.fcanvas.lowerCanvasEl.width*val);
		$(page.fcanvas.upperCanvasEl).closest(".epta_canvas").height(page.fcanvas.lowerCanvasEl.height*val);
		

		// $(page.fcanvas.upperCanvasEl).closest(".canvas-wrap").css(
		// 	{ 
		// 		"overflow": "auto",
		// 		"max-width": page.fcanvas.lowerCanvasEl.width*val,
		// 		"max-height": page.fcanvas.lowerCanvasEl.height*val
		// 	}
		
		// );




	});
	//console.log("change zoom to ", val);
}







