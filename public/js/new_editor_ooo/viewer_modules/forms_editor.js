var newFormsEditor = {
	element_type: "input",
	moved_element: false,
	selector: ".forms-editable-menu",
	activeCanvas: false,
	new_fields_count: 1,
	init: function(){
		$(document).on("click", ".change_form_item", this.beforeAppend);
        $(document).on("mousemove", ".active_image_moving", this.moveInserted); 
        $(document).on("keyup", "#field_name", this.changeFieldName);
        $(document).on("keyup", "#field_options", this.changeSelectOptions);
        $(document).on("click", ".delete_form_element", this.removeBlock);
	},
	changeFieldName: function(e){
		var obj = newFormsEditor.activeCanvas.getActiveObject();
		obj.field_name = $(this).val();
	},
	changeSelectOptions: function(){
		options = $(this).val().split("\n");
		var obj = newFormsEditor.activeCanvas.getActiveObject();
		obj.options = options;
		newFormsEditor.updateSelectOptions($(`[uniq_id='${obj.uniq_id}']`), options);
	},
	click: function(obj){
		var click_on_html = false;
		setTimeout(()=>{ //TODO таймаут что бы фабрик успел переключить актив рект
			//если клип о хтмл элементу - делаем поиск ректа
			if("element_type" in obj || (typeof obj.target!='undefined' && typeof obj.target.canvas!='undefined')){
				obj = "target" in obj?obj.target:obj;
			}else{
				var id = $(obj).attr("uniq_id");
				newFormsEditor.activeCanvas.getObjects("rect").forEach(function(v,i){
					if(v.uniq_id==id){
						obj = v;
					}
				});
				click_on_html = true;
			}
			
			if("getBoundingRect" in obj == false){
				console.error("unknown error: getBoundingRect not found in object");
				return false;
			}
			
			var position = obj.getBoundingRect(),
				that = newFormsEditor;
			
			$("#field_name").val(obj.field_name);

			var offset = $(obj.canvas.lowerCanvasEl).getElementOffset($("#simplePDFEditor"));
			position.top = position.top+offset.top;
			
			that.activeCanvas = obj.canvas;
			if(click_on_html){
				that.activeCanvas.setActiveObject(obj);
				that.activeCanvas.renderAll();
			}
			
			//obj = that.activeCanvas.getActiveObject();
			that.moveMenu(position, obj);
			that.showEditor();
		},100);
	},
	moveMenu: function(position, obj){
		console.log(obj);

		var canvas_offset = $(obj.canvas.lowerCanvasEl).getElementOffset("#pdf_editor_pages");
		position.top += position.height+10;
		delete position['width'];
		delete position['height'];
		position['left'] += canvas_offset.left;



		$(this.selector).css(position);
	},
	showEditor: function(){
		$(this.selector).show();
		var that = newFormsEditor;
		var obj = that.activeCanvas.getActiveObject();
		switch(obj.element_type){
			case 'textarea':
			case 'checkbox':
			case 'radio':
			case 'input':
				$(".show_if_select",that.selector).hide();
			break;
			case 'select':
			case 'dropdown':
				$(".show_if_select",that.selector).show();
				$("#field_options").val(obj.options.join("\n"));
				//newFormsEditor.updateSelectOptions($(`[uniq_id='${obj.uniq_id}']`), obj.options);
			break;
		}
		$("#field_name").val(obj.field_name);
		
		this.editor_active = true;
	},
	updateSelectOptions: function(select, options){
		select.html("");
		options.forEach((v,i)=>{
			//TODO добавить эскейп
			select.append(`<option value="${v}">${v}</option>`);
		});
	},
	hideEditor: function(){
		$(this.selector).hide();
		this.editor_active = false;
	},

	removeBlock: function(){
		that = newFormsEditor;
		obj = that.activeCanvas.getActiveObject();
		
		$(`[uniq_id='${obj.uniq_id}']`).remove();
		that.activeCanvas.remove(obj);
		that.hideEditor();
	},
	beforeAppend: function(e){
		e.preventDefault();
		newFormsEditor.element_type = $(this).attr("form_element_type");
		$("#pdf_editor_pages").addClass("active_image_moving");
		$("#pdf_editor_pages").append(newFormsEditor.createTempElement(newFormsEditor.element_type));
		
		
		
	},
    moveInserted: function (e) {
        var that = newFormsEditor,
            scene = $(".active_image_moving"),
            offsetX = (e.pageX - scene.offset().left),
            offsetY = (e.pageY - scene.offset().top);

			

        if ($(".follow_the_mouse").length > 0) {
            $(".follow_the_mouse").css({top: offsetY, left: offsetX});
        }
    },
	createTempElement: function(type='input',value="", is_new=true, uniq_id=false, options=false){
	
		var styles = {};
		el = false;
		switch(type){
			case 'checkbox':
				el = $(`<input type='checkbox'>`);
				styles = {
					left: 0,
					top: 0,	
					"font-size": "15px",
					border: "1px solid gray",
					position: "absolute"
				};
			break;
			case 'radio':
				el = $(`<input type='radio' >`);
				styles = {
					left: 0,
					top: 0,
					"font-size": "15px",
					border: "1px solid gray",
					position: "absolute"
				};
			break;
			case 'textarea':
				el = $(`<textarea type='text' style='resize: none;'>${value}</textarea>`);
				styles = {
					left: 0,
					top: 0,
					width: 150*viewer.scale,
					height: 60*viewer.scale,
					"font-size": "15px",
					border: "1px solid gray",
					position: "absolute"
				};
			break;
			
			case 'input':
				el = $(`<input type='text' value="${value}">`);
				styles = {
					left: 0,
					top: 0,
					width: 150*viewer.scale,
					height: 20*viewer.scale,
					"font-size": "15px",
					border: "1px solid gray",
					position: "absolute"
				};
			break;
			case 'select':
			case 'dropdown':
				el = $(`<select>`);
				styles = {
					left: 0,
					top: 0,
					width: 150*viewer.scale,
					height: 20*viewer.scale,
					"font-size": "15px",
					border: "1px solid gray",
					position: "absolute"
				};
				if(options){
					$.each(options, function(io, opt){
						el.append(`<option `+(value==opt?"selected":"")+` value='${opt}'>${opt}</option>`);
					});
				}else{

					el.append("<option value=''></option>");
					el.append("<option value='Value 1'>Value 1</option>");
					el.append("<option value='Value 2'>Value 2</option>");
				}
			break;
		}
		if(!el){
			return false;
		}
		if(is_new){
			el.addClass("follow_the_mouse");
		}
		if(!uniq_id){
			uniq_id =guid();
		}
		el.css(styles).attr("uniq_id", uniq_id);
		newFormsEditor.moved_element = el;
		return el;
	},

    elementAppend: function(fcanvas, point, is_new=true, uniq_id=false, options={}){
		//var element = new fabric.Image(newFormsEditor.moved_element);
		var page_outer = $(fcanvas.lowerCanvasEl).closest(".m_page_outer");
		if(is_new){

			
			var uniq_id = newFormsEditor.moved_element.attr("uniq_id");
			new_element = newFormsEditor.moved_element.clone();
			new_element.removeClass("follow_the_mouse");
			new_element.attr("uniq_id", uniq_id);
			new_element.addClass("ff_element");
			


			page_outer.append(new_element);
			var element_offset = $(new_element).closest(".m_page_outer").find(".canvas-wrap").getElementOffset("#pdf_editor_pages");

			var style = {
				left: parseInt(new_element.css("left"))-3-element_offset.left,
				top: parseInt(new_element.css("top"))-3,
				width: parseInt(new_element.css("width"))+6,
				height: parseInt(new_element.css("height"))+6,
				stroke: "rgb(235,235,235)",
				fill: "rgb(255,255,255)",
				subtype: "form_element_outer",
				element_type: newFormsEditor.element_type
			};
		}else{
			var style = {
				left: point.left-3,
				top: point.top-3,
				width: point.width+6,
				height: point.height+6,
				stroke: "rgb(235,235,235)",
				fill: "rgb(255,255,255)",
				subtype: "form_element_outer",
				element_type: options.element_type
			};
		}

		rect = new fabric.Rect(style);
		rect.field_name = "fieldName" in options?options.fieldName:"New field "+newFormsEditor.new_fields_count;
		rect.uniq_id = uniq_id;
		$(".follow_the_mouse").remove();

		rect.setControlsVisibility({ mt: false, mb: false, ml: false, mr: false, bl: false, br: false, tl: false, tr: false, mtr: false, });
		rect.on('moving', function() { newFormsEditor.positionHtml() });

		rect.on("mousedown", function(obj){ 
			newFormsEditor.activeCanvas = obj.target.canvas;
			newFormsEditor.hideEditor() 
		});
		if(options.options){
			rect.options = options.options;
		}else{
			rect.options = [
				"Value 1",
				"Value 2"
			];
		}


		rect.on("mouseup", newFormsEditor.click);
		$(document).on("click", `[uniq_id='${rect.uniq_id}']`, function(){
			var id = $(this).attr("uniq_id");
			var obj = false;
			newFormsEditor.activeCanvas.getObjects("rect").forEach(function(v,i){
				if(v.uniq_id==id){
					obj = v;
				}
			});
			if(obj){
				newFormsEditor.activeCanvas.setActiveObject(obj);
			}
			newFormsEditor.click(this);
		});
		
		newFormsEditor.moved_element = false;
		newFormsEditor.activeCanvas = fcanvas;
		fcanvas.add(rect);
		fcanvas.setActiveObject(rect);
		fcanvas.renderAll();
		
		newFormsEditor.new_fields_count+=1;
//		this.imageObject = false;
    },
    positionHtml: function(){
    	var canvas = newFormsEditor.activeCanvas;
		var obj = canvas.getActiveObject();
		var cords = obj.getCoords(obj)[0];
		inp = $(`[uniq_id='${obj.uniq_id}']`)[0];

		var element_offset = $(inp).closest(".m_page_outer").find(".canvas-wrap").getElementOffset("#pdf_editor_pages");



		inp.style.left = (obj.left+3+element_offset.left)+"px";
		inp.style.top = (obj.top+3)+"px"
    }
    
    
    
}
//

newFormsEditor.init();
