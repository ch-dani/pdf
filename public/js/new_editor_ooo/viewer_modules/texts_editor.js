
var TextEditor = {
	selector: ".text-editable-menu",
	editor_active: false,
	obj: false,
	start: 0,
	end: 0,
	length: 0,
	active_object: false,
	addText: function(canvas, point){
		//editorMenu.disableAllTexts();
		var default_string = "Start typing text...";

		var customtxt = new fabric.IText(default_string, {
			from_pdf: false,
			fontFamily: default_font,
			fontSize:12*viewer.scale,
			fontWeight:'normal',
			//fill:'black', 
			width: 100*viewer.scale+40,
			fontStyle: 'normal', 
			fill: "black",
			top: point.y-(10*viewer.scale),
			left: point.x//-(93*viewer.scale/2),
		});
		canvas.setActiveObject(customtxt);
		customtxt.setSelectionStyles({fill: "rgb(0,0,0)"}, 0, default_string.length);


		customtxt.setSelectionStart(0);
		customtxt.setSelectionEnd(default_string.length);
		customtxt.setSelectionStyles({ 
			fontFamily: default_font, 
			fontSize: 12*viewer.scale,
			fontWeight: "normal",
			fill: "rgb(0,0,0)"
			},0, default_string.length);

		customtxt.setControlsVisibility({ mt: false, mb: false, ml: false, mr: false, bl: false, br: false, tl: false, tr: false, mtr: false, });

		customtxt.on("mousedown", TextEditor.hideEditor);
		customtxt.on("mouseup", TextEditor.click);
		customtxt.on("mousedown", function(){
			viewer.prev_composite = false;
		});

		TextEditor.active_object = customtxt; 

		canvas.add(customtxt);
		customtxt.enterEditing();
		customtxt.hiddenTextarea.focus();
		canvas.renderAll();
		
		//customtxt.on("mousedown", textFunc.elementClick);
		//viewer.current_page = $(th.lowerCanvasEl).data("pn");
	},
	hideEditor: function(){
		$(this.selector).hide();
		this.editor_active = false;
		//
	},
	showEditor: function(){
		if(current_editor=='text'){
			$(this.selector).show();
			this.editor_active = true;
		}
	},
	moveMenu: function(position){
		position.top += position.height+10;
		delete position['width'];
		delete position['height'];
		$(this.selector).css(position);
	},
	click: function(obj){
		TextEditor.active_object = obj;
		var position = obj.target.getBoundingRect(),
			that = TextEditor;
			
			
		var offset = $(obj.target.canvas.lowerCanvasEl).getElementOffset($("#simplePDFEditor"));
		position.top = position.top+offset.top;
		
		
		that.obj = obj.target;
		that.start = that.obj.selectionStart;
		that.end = that.obj.selectionEnd?that.obj.selectionEnd:that.obj._text.length;
		if(that.start == that.end){
			that.start = 0;
			that.end = that.obj._text.length;
		}
		that.text_length = that.obj._text.length;
		that.moveMenu(position);
		that.showEditor();
	},
	getStyle: function(type, value, start,end, length){
		if(start==end){
			end = length;
		}
		
		var ret = false;
		for(i=start; i!=end; i++){
			style = this.obj.getSelectionStyles(i,i+1)[0];
			if(typeof style[type]=='undefined' || style[type]!=value){
				return true;
			}
		}
		return false;
	},
	changeBold: function(){
		that = TextEditor;
		obj = (that.obj);
		var set_value = that.getStyle("fontWeight", "bold", that.start, that.end, that.text_length);
		obj.setSelectionStyles({ fontWeight: set_value?"bold":"normal" }, that.start, that.end?that.end:that.text_length);
		obj.canvas.renderAll();
	},
	changeUnderline: function(){
		that = TextEditor;
		obj = (that.obj);
		var set_value = that.getStyle("underline", "underline", that.start, that.end, that.text_length);
		console.log("underline ");
		
		obj.setSelectionStyles({ underline: set_value?"underline":"normal" }, that.start, that.end?that.end:that.text_length);
		obj.canvas.renderAll();	
	},
	changeItalic: function(){
		that = TextEditor;
		obj = (that.obj);
		var set_value = that.getStyle("fontStyle", "italic", that.start, that.end, that.text_length);
		obj.setSelectionStyles({ fontStyle: set_value?"italic":"normal" }, that.start, that.end?that.end:that.text_length);
		obj.canvas.renderAll();
	},
	changeFontSize: function(){
		var that = TextEditor;
		var size = $(this).val()*viewer.scale;
		that.obj.setSelectionStyles({ fontSize: parseInt(size) }, 0, that.text_length);
		that.obj.positions = [];
		that.obj.customLineHeights = [];
		that.obj.canvas.renderAll();
	},
	changeFont: function(e){
		e.preventDefault();
		var that = TextEditor;
		var font = $(this).data("font-name");
		obj = (that.obj);
		
		obj.setSelectionStyles({ fontFamily: font }, 0, that.text_length);
		obj.canvas.renderAll();
	},
	changeFontColor: function(e){
		e.preventDefault();
		var color = $(this).css("background-color");
		that = TextEditor;
		obj = (that.obj);
		
		var set_value = that.getStyle("fill", color, that.start, that.end, that.text_length);
		obj.setSelectionStyles({ fill: set_value?color:"rgb(0,0,0)" }, that.start, that.end?that.end:that.text_length);
		obj.canvas.renderAll();
	},
	removeBlock: function(){
		that = TextEditor;
		that.obj.canvas.remove(that.obj);
		that.hideEditor();
	},
	init: function(){
		$(document).on("click", `${this.selector} .set_bold`, this.changeBold);
		$(document).on("click", `${this.selector} .set_italic`, this.changeItalic);

		$(document).on("click", `${this.selector} .set_underline`, this.changeUnderline);
		
		$(document).on("change", `${this.selector} .font-size-number`, this.changeFontSize);		
		$(document).on("click", `${this.selector} .change_text_font`, this.changeFont);
		
		$(document).on("click", `${this.selector} .color-swatch`, this.changeFontColor);
		$(document).on("click", `${this.selector} .delete_text`, this.removeBlock);

	}
};
TextEditor.init();



