var newAnnotateEditor = {
	rect_draw_init: false,
	canvas : false,
	drawEnabled: true,
	width: 0,
	height: 0,	
	selector: ".annotate-editable-menu",
	activeCanvas: false,
	activeObject: false,
	init: function(){
		$(document).on("keyup", "#annotate_title", newAnnotateEditor.updateAnnotateTitle);
		$(document).on("keyup", "#annotate_content", newAnnotateEditor.updateAnnotateContents);	
		$(document).on("click", ".delete_annotate", this.removeBlock);
	},
	
	removeBlock: function(){
		that = newAnnotateEditor;
		obj = that.activeCanvas.getActiveObject();
		that.activeCanvas.remove(obj);
		that.hideEditor();
	},
	changeLink: function(){
		var url = $(this).val(),
			that = newAnnotateEditor;
		var obj = that.activeCanvas.getActiveObject();
		obj.url = url;
	}, 
	updateAnnotateTitle: function(){
		var val = $(this).val();
		newAnnotateEditor.activeObject.title = val;
	},
	updateAnnotateContents: function(){
		var val = $(this).val();
		newAnnotateEditor.activeObject.contents = val;
	},
	click: function(obj){
		obj = "target" in obj?obj.target:obj;
		var position = obj.getBoundingRect(),
			that = newAnnotateEditor;
		that.activeCanvas = obj.canvas;
		obj = that.activeCanvas.getActiveObject();

		var offset = $(obj.canvas.lowerCanvasEl).getElementOffset($("#simplePDFEditor"));
		position.top = position.top+offset.top;
		
		$("#annotate_title").val(obj.title);
		$("#annotate_content").val(obj.contents);
		that.activeObject = obj;
		
		that.moveMenu(position);
		that.showEditor();
	},
	moveMenu: function(position){
		position.top += position.height+10;

		delete position['width'];
		delete position['height'];
		$(this.selector).css(position);
	},
	showEditor: function(){
		$(this.selector).show();
		this.editor_active = true;
	},
	hideEditor: function(){
		$(this.selector).hide();
		this.activeObject = false;
		this.editor_active = false;
	},

	onChangeEditor: function(e, editor){
		
	},
	makeUnderline: function(obj){
		if(obj.selectionStart != obj.selectionEnd){
			var that = newAnnotateEditor;
			var bound = obj.getBoundingRect();

			var points = [bound.left+obj.selectBoundaries.start, bound.top+bound.height, bound.left+obj.selectBoundaries.start+obj.selectBoundaries.end, bound.top+bound.height];

			rect = new fabric.Line(points, {
				subtype: "StrikeOut",
				strokeWidth: 2*viewer.scale,
				fill: 'red',
				stroke: 'red',
				type: 'arrow'
			});
			obj.canvas.add(rect);
			obj.canvas.renderAll();
			$("#annotate_title").val("");
			$("#annotate_content").val("");
			rect.contents = "";
			rect.title = "";
			newAnnotateEditor.activeObject = rect;
			rect.on("mousedown", function(){ newAnnotateEditor.hideEditor() });
			rect.on("mouseup", newAnnotateEditor.click);
			var position = rect.getBoundingRect();

			position.top = position.top;
			that.moveMenu(position);
			that.showEditor();
		}
	}
};

newAnnotateEditor.init();




