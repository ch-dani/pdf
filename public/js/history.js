class WHistory{
	constructor(){
		this.history = [];
		this.redo_history = [];
		$(document).on("whistory", (e,data)=>{
			this.pushToHistory(data);
		});
	}
	pushToHistory(data){
		this.history.push({...data});
		this.redo_history = [];
		$(".undo_redo").removeClass("hidden");
		$(".undo_redo .undo").removeClass("disabled");
		$(".undo_redo .redo").addClass("disabled");
	}
	undo(redo=false){
		var state = false;
		var current_state = false;
		if(redo){
			state = this.redo_history.pop();
		}else{
			state = this.history.pop();
		}
		if(!state){
			return false;
		}
		
		let el = $(`${state.html}`);
		let target = $(`#${state.id}`);
		let inner = target.find(".element_inner_wrpr");
		let image_outer = target.closest(".outer_image_div");
		switch(state.type){
			case 'add_text': //+
				if(redo){
					target.removeClass("hidden");
				}else{
					target.addClass("hidden");
				}
			break;
			case 'move_text': //+
				current_state = {
					left:  target.css("left"),
					top: target.css("top")
				};
				target.css({
					left: state.left,
					top: state.top,  
				});
			break;
			case 'rotate_text': //+
				current_state = {
					angle: target.find(".document_add_element").data('uiRotatable').elementCurrentAngle
				};
				target.find(".document_add_element").data('uiRotatable').angle(state.angle);
			break;
			case 'resize_text': //+
				current_state = {
					'font-size': target.find(".element_inner_wrpr").css('font-size'),
					'line-height': target.find(".element_inner_wrpr").css('line-height'),
					width: target.find(".document_add_element").css('width'),
					height: target.find(".document_add_element").css('height'),
				};
				target.find(".element_inner_wrpr").css({ "font-size": state['font-size'], "line-height": state['line-height'], });
				target.find(".document_add_element").css({ "width": state['width'], "height": state['height'], });
			break;
			case 'change_bold': //+
				if(state.value){
					inner.css("font-weight", "bold");
					inner.attr("bold", 1);
				}else{
					inner.css("font-weight", "normal");
					inner.attr("bold", 0);
				}
				current_state = {
					value: !state.value
				};
			break;
			case 'change_underline': //+
				if(state.value){
					inner.css("text-decoration", "underline");
					inner.attr("underline", 1);
				}else{
					inner.css("text-decoration", "none");
					inner.attr("underline", 0);
				}
				current_state = {
					value: !state.value
				};				
			break;
			
			case 'change_italic': //+
				if(state.value){
					inner.css("font-style", "italic");
					inner.attr("italic", 1);
				}else{
					inner.css("font-style", "normal");
					inner.attr("italic", 0);
				}
				current_state = {
					value: !state.value
				};				
			break;
			case 'change_font': //+
				current_state = {
					value: target.find(".text_content_element").css("font-family")
				};	
				target.find(".text_content_element").css("font-family", state.value);
				
			break;
			case 'change_color': //+
				current_state = {
					value: target.find(".text_content_element").css("color")
				};							
				target.find(".text_content_element").css("color", state.value);
			break;
			case 'change_opacity':
				current_state = {
					value: target.find(".text_content_element").css("opacity")
				};	
				target.find(".text_content_element").css("opacity", state.value);
			break;
			case 'delete': //+
				if(redo){
					target.addClass("hidden");
				}else{
					target.removeClass("hidden");
				}
			break;
			case 'add_image': //+
				if(redo){
					target.removeClass("hidden");
				}else{
					target.addClass("hidden");
				}
			break;
			case 'move_image': //+
				current_state = {
					left:  target.css("left"),
					top: target.css("top")
				};
				target.css({ left: state.left, top: state.top, });				
			break;
			case 'resize_image': //+
				current_state = {
					width:  target.css("width"),
					height: target.css("height")
				};			
				target.css({ width: state.width, height: state.height });
				target.find(".document_add_element").css({ width: state.width, height: state.height });
			break;
			case 'rotate_image':
				current_state = {
					angle: target.find(".rotatable_helper").data('uiRotatable').elementCurrentAngle
				};			
				target.find(".rotatable_helper").data('uiRotatable').angle(state.angle);
			break;
		}
		
		
		if(redo){
			current_state = $.extend(state, current_state);
			console.log(current_state);
			this.history.push(current_state);
			
		}else{
			current_state  = $.extend(state, current_state);
			this.redo_history.push(current_state);
		}

		if(this.history.length===0){
			$(".undo_redo .undo").addClass("disabled");
		}else{
			$(".undo_redo .undo").removeClass("disabled");
		}

		if(this.redo_history.length===0){
			$(".undo_redo .redo").addClass("disabled");
		}else{
			$(".undo_redo .redo").removeClass("disabled");
		}				
		
		return false;
	}

}

var whistory = new WHistory();

//"213.8px"
