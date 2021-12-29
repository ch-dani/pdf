var newRectangleEditor = {
	rect_draw_init: false,
	rects_editors: ['whiteout', 'rectangle', 'ellipse', 'links', 'annotate'],
	drawType: "rectangle",
	canvas : false,
	drawEnabled: true,
	width: 0,
	height: 0,	
	annotate_type: false,
	selector: ".whiteout-editable-menu",
	activeCanvas: false,
	styles: {
		'whiteout': {
			fill: "rgb(255,255,255)",
			stroke: "rgb(255,255,255)",
			strokeWidth: 3,
			subtype: 'whiteout'
		},
		'rectangle': {
			fill: "rgb(255,255,255,0)",
			stroke: "rgb(255,0,255)",
			strokeWidth: 3,
			subtype: 'rectangle'
		},
		'ellipse': {
			fill: "rgb(255,255,255,0)",
			stroke: "rgb(255,0,255)",
			strokeWidth: 3,
			subtype: 'ellipse'
		},
		'links': {
			fill: "rgba(0,0,255,0.4)",
			stroke: "rgb(255,255,255)",
			strokeWidth: 1,
			subtype: 'link'
		},
		'annotate_strike':{
		
		}
		
	},
	init: function(){
		$(document).on("change_editor", this.onChangeEditor);
		$(document).on("click", ".whiteout-editable-menu .border-selector .set-border", this.updateBorder);
		$(document).on("click", ".whiteout-editable-menu .change_border_color .color-swatch", this.updateBorderColor);		
		$(document).on("click", ".whiteout-editable-menu .change_background_color .color-swatch", this.updateBackgroundColor);		
		$(document).on("click", ".whiteout-editable-menu .delete_whiteout, .link-editable-menu .delete", this.removeBlock);
		$(document).on("keyup", "#internal_link", this.changeLink);
	},
	
	removeBlock: function(){
		that = newRectangleEditor;
		obj = that.activeCanvas.getActiveObject();
		that.activeCanvas.remove(obj);
		that.hideEditor();
	},
	changeLink: function(){
		var url = $(this).val(),
			that = newRectangleEditor;
		var obj = that.activeCanvas.getActiveObject();
		obj.url = url;
	},
	updateBackgroundColor: function(e){
		e.preventDefault();
		var color = $(this).css("background-color");
		that = newRectangleEditor;		
		var obj = that.activeCanvas.getActiveObject();
		obj.set({fill: color});
		that.activeCanvas.renderAll();
	},
	updateBorderColor: function(e){
		e.preventDefault();
		var color = $(this).css("background-color");
		that = newRectangleEditor;		
		var obj = that.activeCanvas.getActiveObject();
		obj.set({stroke: color});
		that.activeCanvas.renderAll();		
	},
	updateBorder: function(e){
		e.preventDefault();
		height = parseInt($(this).css("height"));
		that = newRectangleEditor;		
		var obj = that.activeCanvas.getActiveObject();
		obj.set({strokeWidth: height});
		that.activeCanvas.renderAll();
	},
	click: function(obj){
		obj = "target" in obj?obj.target:obj;
		
	
		var position = obj.getBoundingRect(),
			that = newRectangleEditor;

		var offset = $(obj.canvas.lowerCanvasEl).getElementOffset($("#simplePDFEditor"));
		position.top = position.top+offset.top;

		
		if(obj.subtype=='link'){
			$("#internal_link").val(obj.url);
		}
		newAnnotateEditor.activeCanvas = obj.canvas;
		that.activeCanvas = obj.canvas;
		
		obj = that.activeCanvas.getActiveObject();
		that.selector = that.getOptionMenuSelector(obj.subtype);
		
		that.moveMenu(position);
		that.showEditor();
	},
	moveMenu: function(position){
		position.top += position.height+10;
		delete position['width'];
		delete position['height'];
		$(this.selector).css(position);
	},
	
	getOptionMenuSelector: function(type){	
		switch(type){
			case 'rect':
			case 'whiteout':
			case 'rectangle':
			case 'ellipse':
				return ".whiteout-editable-menu";
			break;
			case 'link':
				return '.link-editable-menu';
			break;
			case 'annotate':
				return '.annotate-editable-menu';
			break;
			
		}
	},
	hideEditors: function(){
		[".link-editable-menu", ".whiteout-editable-menu"].forEach(function(v,i){
			$(v).hide();
		});
	},
	showEditor: function(){
		$(newRectangleEditor.selector).show();
		newRectangleEditor.editor_active = true;
	},
	hideEditor: function(){
		$(newRectangleEditor.selector).hide();
		newRectangleEditor.editor_active = false;
	},

	onChangeEditor: function(e, editor){
		console.log("change editor");
	
		var that = newRectangleEditor;
		//если не инициализирована рисовалка - добавляем ее на каждую страницу
		if(!viewer.rect_draw_init && that.rects_editors.indexOf(current_editor)!=-1){
			$.each(viewer.pages, function(i,page){
				newRectangleEditor.draw(page.fcanvas)
			});
			viewer.rect_draw_init = true;
		}
		//если инициализировано - но не тот редактор - отключаем.
		if(viewer.rect_draw_init && that.rects_editors.indexOf(current_editor)==-1){
			$.each(viewer.pages, function(i,page){
				newRectangleEditor.drawEnabled = false;
			});
		}
		//если инициализировано - включаем.
		if(viewer.rect_draw_init && that.rects_editors.indexOf(current_editor)!=-1){
			$.each(viewer.pages, function(i,page){
				newRectangleEditor.drawEnabled = true;
			});
		}
		
		newRectangleEditor.drawType = current_editor;
		
	},
	draw: function(canvas){
		this.canvas = canvas;
		canvas.selection = false;
		var rect, ellipse, line, triangle, isDown, origX, origY, freeDrawing = true, activeObj;
		var isRectActive = false, isCircleActive = false, isArrowActive = false;
		isRectActive = true;
		freeDrawing = true;
		var start_point = false;
		
		canvas.on('mouse:down', (o) =>{
			if(o.target!=null){//  || o.drawn!==true){
				return;
			}
			
			if(newRectangleEditor.drawEnabled){
				isDown = true;
				var pointer = canvas.getPointer(o.e);
				origX = pointer.x;
				origY = pointer.y;
				console.log(this.drawType);
				
				switch(this.drawType){
					case 'whiteout':
						var style = $.extend({ left: origX, top: origY, width: pointer.x - origX, height: pointer.y - origY, type: 'rect' }, newRectangleEditor.styles[this.drawType]);
						rect = new fabric.Rect(style);
					break;
					case 'annotate':
						var points = [pointer.x, pointer.y, pointer.x, pointer.y];
						start_point = pointer;
						if(newRectangleEditor.annotate_type=='strike'){
							rect = new fabric.Line(points, {
								subtype: "StrikeOut",
								strokeWidth: 2*viewer.scale,
								fill: 'red',
								stroke: 'red',
								originX: 'center',
								originY: 'center',
								id: 'arrow_line',
								type: 'arrow'
							});
						}else{
							var style = $.extend({ left: origX, top: origY, width: pointer.x - origX, height: pointer.y - origY, type: 'rect' }, {
								subtype: "Highlight",
								fill: newRectangleEditor.annotate_highlite_color
							});
							rect = new fabric.Rect(style);						
						}
						rect.hasControls = rect.hasBorders= false;
					break;
					
					case 'links':
					case 'rectangle':
						var style = $.extend({ left: origX, top: origY, width: pointer.x - origX, height: pointer.y - origY, type: 'rect' }, newRectangleEditor.styles[this.drawType]);
						rect = new fabric.Rect(style);
					break;
					case 'ellipse':
						var style = $.extend({ type: 'ellipse', left: origX, top: origY, originX: 'left', originY: 'top', rx: 10+pointer.x - origX, ry: 10+pointer.y - origY, angle: 0 }, newRectangleEditor.styles[this.drawType]);
						rect = new fabric.Ellipse(style);
					break;
					
					
					case 'arrow':
					break;
				}
				rect.drawn = true;

				canvas.add(rect);
				activeObj = rect;
			}
		});

		canvas.on('mouse:move', function(o) {
			if (isDown && newRectangleEditor.drawEnabled) {
				var pointer = canvas.getPointer(o.e);
				
				switch(newRectangleEditor.drawType){
					case 'annotate':
						var pointer = canvas.getPointer(o.e);
						if(newRectangleEditor.annotate_type=='strike'){
							rect.set({ x2: pointer.x, y2: start_point.y });
						}else{
							if(origX > pointer.x){
								rect.set({left: Math.abs(pointer.x) });
							}
							if(origY > pointer.y){
								rect.set({top: Math.abs(pointer.y)});
							}
							newRectangleEditor.width = Math.abs(origX - pointer.x);
							newRectangleEditor.height = Math.abs(origY - pointer.y)
							rect.set({
								width: newRectangleEditor.width,
								height: newRectangleEditor.height
							});							
						}
					break;

					case 'whiteout':
					case 'links':

					case 'rectangle':
						if(origX > pointer.x){
							rect.set({left: Math.abs(pointer.x) });
						}
						if(origY > pointer.y){
							rect.set({top: Math.abs(pointer.y)});
						}
						newRectangleEditor.width = Math.abs(origX - pointer.x);
						newRectangleEditor.height = Math.abs(origY - pointer.y)
						rect.set({
							width: newRectangleEditor.width,
							height: newRectangleEditor.height
						});
					break;
					case 'ellipse':
						var rx = Math.abs(origX-pointer.x)/2;
						var ry = Math.abs(origY-pointer.y)/2;
						if(rx > rect.strokeWidth){ rx -= rect.strokeWidth / 2; }
						if (ry > rect.strokeWidth) { ry -= rect.strokeWidth / 2; }
						rect.set({
							rx: rx,
							ry: ry
						});
						if(origX>pointer.x){
							rect.set({originX: 'right'});
						}else{
							rect.set({originX: 'left'});
						}
						if (origY > pointer.y) {
							rect.set({ originY: 'bottom'});
						 }else{
							rect.set({originY: 'top'});
						}
					break;
					case 'arrow':
						line.set({
							x2: pointer.x,
							y2: pointer.y
						});
						triangle.set({
							'left': pointer.x + deltaX,
							'top': pointer.y + deltaY,
							'angle': _FabricCalcArrowAngle(line.x1, line.y1, line.x2, line.y2)
						});
					break;
				}
				canvas.renderAll();
			}
		});

		canvas.on('mouse:up', function(o) {
			if(rect){
				rect.drawn = false;
				if(rect.width==0 || rect.height===0 & rect.subtype!='StrikeOut'){
					rect.canvas.remove(rect);
					
					console.log(rect);
				}else{
					console.log(rect.width, rect.height);
					rect.scale(1,1);
					canvas.setActiveObject(rect);
					canvas.renderAll();
					canvas.setActiveObject(rect);
					newRectangleEditor.click(rect);
					if(rect.subtype=='StrikeOut' || rect.subtype=='Highlight'){
						$("#annotate_title").val("");
						$("#annotate_content").val("");
						rect.contents = "";
						rect.title = "";
						newAnnotateEditor.activeObject = rect;
						rect.on("mousedown", function(){ newAnnotateEditor.hideEditor() });
						rect.on("mouseup", newAnnotateEditor.click);					
					}else{
						rect.on("mousedown", function(){ newRectangleEditor.hideEditor() });
						rect.on("mouseup", newRectangleEditor.click);
					}
				}
				if (newRectangleEditor.drawEnabled) {
					isDown = false;
					rect = false;
				}
				canvas.fire("object:enddraw",rect);
			}
		});
	}
	
};

newRectangleEditor.init();


var _FabricCalcArrowAngle = function(x1, y1, x2, y2) {
	var angle = 0,
		x, y;
	x = (x2 - x1);
	y = (y2 - y1);
	if (x === 0) {
		angle = (y === 0) ? 0 : (y > 0) ? Math.PI / 2 : Math.PI * 3 / 2;
	} else if (y === 0) {
		angle = (x > 0) ? 0 : Math.PI;
	} else {
		angle = (x < 0) ? Math.atan(y / x) + Math.PI :
			(y < 0) ? Math.atan(y / x) + (2 * Math.PI) : Math.atan(y / x);
	}
	return (angle * 180 / Math.PI + 90);
};

//function generateUUID() {
//	var d = new Date().getTime();
//	if (window.performance && typeof window.performance.now === "function") {
//		d += performance.now(); //use high-precision timer if available
//	}
//	var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
//		var r = (d + Math.random() * 16) % 16 | 0;
//		d = Math.floor(d / 16);
//		return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
//	});
//	return uuid;
//}
//canvas.on("object:modified", function(e) {
//	try {
//		var obj = e.target;
//		if (obj.type === 'ellipse') {
//			obj.rx *= obj.scaleX;
//			obj.ry *= obj.scaleY;
//		}
//		if (obj.type !== 'arrow') {
//			obj.width *= obj.scaleX;
//			obj.height *= obj.scaleY;
//			obj.scaleX = 1;
//			obj.scaleY = 1;
//		}
//		//find text with the same UUID
//		var currUUID = obj.uuid;
//		var objs = canvas.getObjects();
//		var currObjWithSameUUID = null;
//		for (var i = 0; i < objs.length; i++) {
//			if (objs[i].uuid === currUUID &&
//				objs[i].type === 'text') {
//				currObjWithSameUUID = objs[i];
//				break;
//			}
//		}
//		if (currObjWithSameUUID) {
//			currObjWithSameUUID.left = obj.left;
//			currObjWithSameUUID.top = obj.top - 30;
//			currObjWithSameUUID.opacity = 1;
//		}
//	} catch (E) {}
//});

//var _hideText = function(e) {
//	try {
//		var obj = e.target;
//		//        	 	console.log(obj);
//		//find text with the same UUID
//		var currUUID = obj.uuid;
//		var objs = canvas.getObjects();
//		var currObjWithSameUUID = null;
//		for (var i = 0; i < objs.length; i++) {
//			if (objs[i].uuid === currUUID && objs[i].type === 'text') {
//				currObjWithSameUUID = objs[i];
//				break;
//			}
//		}
//		if (currObjWithSameUUID) {
//			currObjWithSameUUID.opacity = 0;
//		}
//	} catch (E) {}
//}

//canvas.on("object:moving", function(e) {
//	_hideText(e);
//});
//canvas.on("object:scaling", function(e) {
//	_hideText(e);
//});
//canvas.renderAll();


