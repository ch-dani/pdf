var default_font = "Times New Roman";
var EditorMenu = function(){
	this.tab = "file";
	this.editor = "";
	this.highlight = false;
	
	this.zooms = [50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300, 400, 500];
	this.current_zoom = -1;
	this.bindEv = ()=>{
		$(document).on("before_pages_render", this.beforePagesRender);
		$(document).on("change", ".change_page", this.changePage);
		$(document).on("click", ".change_page.page_preview",this.changePage);
		$(document).on("change", "#zoom_selector", this.changeZoom);
		$(document).on("zoom_init", this.afterZoomInit);
		$(document).on("change", ".zoom_slider", this.changeZoom);
		$(document).on("click", ".tab_panel", this.changeTab);
		$(document).on("click", "[data-editor]", function(e){ editorMenu.changeEditor(e) });
		$(document).on("change", ".change-color-input", this.changeColor);
		$(document).on("click", ".toggle_highlight", this.toggleHighlight);
	};
	
	this.toggleHighlight = (e) =>{
		var el = $(e.currentTarget),
			type = $(el).data("type");
		
		this.highlight = type;
		viewer.pages[1].fcanvas.renderAll();
		$.each(viewer.pages, function(i,page){
			renderBorderBox(page.fcanvas, type);
		});
		
		
	
//		var el = $(e.currentTarget),
//			type = $(el).data("type");
//		
//		if(el.hasClass("active_el")){
//			el.removeClass("active_el");
//			this.removeHighlight();
//		}else{
//			el.addClass("active_el");
//			this.higlightObjects(type);
//		}
		return false;
	} 
	
//	this.disableAllImages = () => {
//		$.each(viewer.pages, function(i,page){
//			var objects = page.fcanvas.getObjects("image");
//			$.each(objects, function(i,ob){
//				ob.selectable = false;
//				ob.editable = false;
//			});
//		});	
//	};

//	this.enableAllImages = () => {
//		$.each(viewer.pages, function(i,page){
//			var objects = page.fcanvas.getObjects("image");
//			$.each(objects, function(i,ob){
//				ob.selectable = true;
//				ob.editable = true;
//			});
//		});	
//	};
//	
//	this.disableAllTexts = () => {
//		$.each(viewer.pages, function(i,page){
//			var objects = page.fcanvas.getObjects("i-text");
//			$.each(objects, function(i,ob){
//				ob.selectable = false;
//				ob.editable = false;
//			});
//		});
//	};
//	this.enableAllTexts = ()=>{
//		$.each(viewer.pages, function(i,page){
//			var objects = page.fcanvas.getObjects("i-text");
//			$.each(objects, function(i,ob){
//				ob.selectable = true;
//				ob.editable = true;
//			});
//		});	
//	};

//	this.enableAllIText = ()=>{
//		$.each(viewer.pages, function(i,page){
//			var objects = page.fcanvas.getObjects("i-text");
//			$.each(objects, function(i,ob){
//				ob.selectable = true;
//				ob.editable = true;
//			});
//		});	
//	};
	
	
	this.changeColor = (e)=>{
		var color = $(e.target).val();
		$(".change-color-input").val(color);
	};
	
	this.higlightObjects = (type="texts") =>{
		$.each(viewer.pages, function(i,page){
			$.each(page.fcanvas.getObjects(type), function(i2, obj){
				obj.drawHightlight(obj.canvas.getContext("2d"), obj);
			});
		});
	};

	this.removeHighlight = (type="texts") =>{
		$.each(viewer.pages, function(i,page){
			page.fcanvas.renderAll();
		});
	};
	
	this.changeEditor = (e) =>{
		var current_editor = $(e.currentTarget).data("editor");
		editorMenu.editor = current_editor;
		
		$.each(viewer.pages, function(i,page){
			var objects = page.fcanvas.getObjects();
			$.each(objects, function(i,ob){
				if(ob.type==current_editor){
					ob.selectable = true;
					ob.editable = true;				
				}else{
					ob.selectable = false;
					ob.editable = false;
				}
			});
		});	
		
//		editorMenu.disableAllTexts();
//		editorMenu.disableAllImages();
//		
//		
//		
//		switch(x){
//			case 'add_text':
//				$(".font_family_selector").val("Times New Roman");
//				$(".font_size_selector").val("12");
//			break;
//			case 'edit_text':
//				editorMenu.enableAllTexts();
//			break;
//			default:
//				editorMenu.enableAllImages();
//			break;
//		}
//		
	};
	
	this.changeTab = (e)=>{
		var tab = $(e.target).data("target").replace(/menu-/, "");
		this.tab = tab;
		
		switch(this.tab){
			case 'link-tools':
				toggleLinksAndAnnotationsView(true, 'Link');
				
			break;
			case 'comment-and-markup':
				toggleLinksAndAnnotationsView(true, 'Annotation');
			break;
			default:
				toggleLinksAndAnnotationsView(false, 'all');
			break;
		}
		
		$(".chose_editor").removeClass("active");
		$(".content-edit-block").removeClass("active");
	};
	
	this.changeZoom = (e) =>{
		var that = this,
			el = $(e.target),
			nz = el.val()/100,
			tz = that.initial_zoom/100;
		$.each(viewer.pages, function(i, v){
			let canvas = v.fcanvas,
			cz = canvas.getZoom();
			new_zoom = nz/tz;
			canvas.setZoom(new_zoom);
			viewer.scale = new_zoom;
		});
		$(".zoom_slider").val(nz*100);
	};
	
	this.beforePagesRender = (ev, numPages) =>{
		$("#pages_selector").html("");
		intRange(1, numPages+1).forEach(function(v){
			$("#pages_selector").append(`<option value='${v}'>${v} / ${numPages}</option>`);
		});
	};
	
	this.changePage = (e) =>{
		var el = $(e.target),
			pn = 1;
		if(el.is("select")){
			pn = el.val();
		}else{
			pn = el.closest(".page_preview").data("pn");
		}
		$('#pdf_editor_pages').animate({
			scrollTop: $(".m_page_outer").eq(pn-1).offset().top-200
		}, 1000);
	};
	
	this.afterZoomInit = () =>{
		$("#zoom_selector").html("");
		this.fillZoomList();
	};
	
	this.fillZoomList = () =>{
		var that = this;
		this.initial_zoom = toInt(viewer.scale*100);
		
		this.zooms.push(this.initial_zoom); //.sort();
		this.zooms = this.zooms.sort((a,b)=>{ return a-b });
		
		this.zooms.forEach(function(v){
			let selected = false;
			if(that.initial_zoom==v){
				selected = true;
			}
			$("#zoom_selector").append(`<option ${selected?"selected":""} value='${v}'>${v}%</option>`);
		});
	};

	this.bindEv();
	return this;
};

var editorMenu = new EditorMenu();




