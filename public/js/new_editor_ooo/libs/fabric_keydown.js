
function calcPosition(char, positions, pos, line, inst){
    var box = inst._getGraphemeBox(char, line, pos, false, false, true);
    return box;
}

function recalcPositions(positions, line, pos, char_width, del=0){
	$.each(positions[line], function(i,p){
		switch(del){
			case 0: //input
				if(i>pos){
					positions[line][i].left += char_width;
				}
			break;
			case 1: //backspace
				if(i>=pos-1){
					//console.log("backspace");
					positions[line][i].left -= char_width;
				}
			break;
			case 2: //enter
			
			break;
			case 3: //delete
				if(i>=pos){
					console.log("delete");
					positions[line][i].left -= char_width;
				}
			break;
		}

	});
	return positions;
};

function getSelectionWidth(positions, start, end){
	var all = [];
	if(start==end){
		return 0;
	}
	$.each(positions, function(i, line){
		all = all.concat(line);
	});
	
	var slice = all.slice(start, end+1);
	
	var plain = [];
	$.each(slice, function(i,v){
		plain.push(v.left);
	});
	var max = Math.max(...plain);
	var min = Math.min(...plain);
	return max-min;
}


async function createOpenTypeObject(name, base64){

	opentype.load(viewer.fonts[name].base64, (err, font)=>{
		if(err){
			viewer.fonts[name].opentype_object = false;
		}else{
			viewer.fonts[name].opentype_object = font;

		}
	});
}

function remeasure(char, positions=[], size=false, font=false, chinese=false){
	if(typeof viewer.fonts[font]=='undefined'){
		return false;
	}
	// console.log("try to remeasure char ", char, font);

	// $("#ebola").remove();
	// $("body").prepend("<canvas id='ebola' style='border: 1px solid red;'></canvas>");
	// ctx = $("#ebola")[0].getContext("2d");

	var font = viewer.fonts[font].opentype_object;

	var paths = font.getPaths(char, 10, 100, size);

	var width = 0;

	$.each(paths, function(i, path){
		var bbox = path.getBoundingBox();
		width += (bbox.x2-bbox.x1)+(1*viewer.scale);
		// paths[i].draw(ctx);
		// ctx.beginPath();
		// ctx.rect(bbox.x1, bbox.y1, bbox.x2-bbox.x1, bbox.y2-bbox.y1);
		// ctx.stroke();
	});
	return width;
	return (bbox.x2-bbox.x1)+(1*viewer.scale);
}

function findInMeasured(char, font, positions){
	try{
		console.log("width is ", viewer.fonts[font].chars_widths_test[char]);
		console.log("width 2 is ", viewer.fonts[font].chars_widths[char]);
		var char_width = viewer.fonts[font].chars_widths_test[char];
		return typeof char_width=='undefined'?false:char_width;
	}catch(e){
		return false;
	}
	return false;
}


function isCFF(font){

	try{
		switch(viewer.fonts[font].fontType){
			case 2:
				return true;
			break;
		}
		return false;
	}catch(e){
		return false;
	}
}


function findEmbededChar(char, font){
	if(char==' '){
		return ' ';
	}
	try{
		if(typeof viewer.chars_table[font]=='undefined'){
			return char;
		}
		if(typeof viewer.chars_table[font][char]=='undefined'){
			return char;
		}
		return viewer.chars_table[font][char];
	}catch(e){
		return char;
	}
}


var getFabricInputFont = function(th){

	var cursor_location = th.get2DCursorLocation(),
	pos = cursor_location.charIndex,
	line = cursor_location.lineIndex,
	styles = th.styles;
	if(typeof styles[line]!='undefined' && typeof styles[line][pos]!='undefined'){
		return styles[line][pos].fontFamily;
	}

	if(typeof th.originalFont!='undefined'){
		return th.originalFont;
	}

	return default_font;
}

fabric.IText.prototype.onKeyDown = (function(onKeyDown) {
	return function(e) {
		this.lastKeyCode = e.keyCode;
		if(viewer.inComposite){
			this.positions = [];
		}
		//console.log('key code is ' + e.keyCode);
		onKeyDown.call(this, e);
	}
})(fabric.IText.prototype.onKeyDown)


var remove_las_composite = false;
fabric.IText.prototype.onInput = (function(onInput) {
	return function(e){
		TextEditor.hideEditor();
		var th = this;
		/*
		this.positions = [];
		onInput.call(this, e);
		return;
		*/
		if(!this.from_pdf || remove_las_composite){
			remove_las_composite = false;
			onInput.call(this, e);
			return;
		}
		
		var cursor_location = this.get2DCursorLocation(),
			pos = cursor_location.charIndex,
			line = cursor_location.lineIndex,
//			char = e.key,
			char = e.data,
			str_line = this.textLines[line],
			char_at_pos = str_line.charAt(pos),
//			keycode = e.keyCode, //TODO fix to many
			keycode = this.lastKeyCode, //TODO fix to many
			current_position = 0,
			x_font = getFabricInputFont(this).split(",")[0];
			new_char = findEmbededChar(char,x_font);
		
		var selection_width = getSelectionWidth(this.positions, this.selectionStart, this.selectionEnd);

				
		if(this.selectionEnd-this.selectionStart==this.text.length && this.text.length!=0){
			this.positions = [];
			console.log("====clean styles====");
			this.fontFamily = "Arial";
			this.from_pdf = false;
			this.originalFont = "Arial";
			this.setSelectionStyles({"fontFamily": "Arial"}, 0, 9999);
			//this.from_pdf = false;
			onInput.call(this, e);
			return;			
		}
		//composite


		var printable = 
			(keycode > 47 && keycode < 64)   || // number keys
			keycode == 32 // || keycode == 13 
			 || keycode==173  || // spacebar & return key(s) (if you want to allow fdfsdcarriage returns)
			(keycode > 64 && keycode < 91)   || // letter keys
			(keycode > 95 && keycode < 112)  || // numpad keys
			(keycode > 185 && keycode < 193) || // ;=,-./` (in order)
			(keycode > 218 && keycode < 223);   // [\]' (in order)
			
		if (e.ctrlKey && e.keyCode == 90) {
			this.positions = [];
			onInput.call(this, e);
			return;
		}
		// if(this.inCompositionMode && !e.data){
		// 	printable = false;
		// 	keycode = 8;
		// 	console.log("=============== here ============= ");


		// }else
		var use_bbox = false;



		if((this.inCompositionMode) && e.data){ //для пиньиня
			console.log("in composite ", viewer.prev_composite, "||", e.data, window.ebola);

			var style = (this.getSelectionStyles(this.selectionStart,this.selectionStart+1))[0];

			var measured_width = remeasure(e.data[e.data.length-1], style.fontSize,style.fontSize, x_font, true);
			
			if(viewer.prev_composite.length>e.data.length){
				printable = false;
				keycode = 8;
				var box = calcPosition(viewer.prev_composite[viewer.prev_composite.length-1], this.positions, pos, line, this);
			}else{
				printable = true;
				var box = calcPosition(e.data[e.data.length-1], this.positions, pos, line, this);
			}
			this._updateCompo = false;
			viewer.prev_composite = e.data;

		}else if(viewer.prev_composite && !e.data){ //удаляем последний символ из пиньиня
			remove_las_composite = true;

			console.log("pinyin delete time===", viewer.prev_composite);
			printable = false;
			keycode = 8;
			var box = calcPosition(viewer.prev_composite[viewer.prev_composite.length-1], this.positions, pos, line, this);
			use_bbox = true;
			console.log("char width is ", box.width);
		}else{
			//console.log("x_font is ", x_font, "new char is ", new_char, "ol char is ", char);
			if(isCFF(x_font) && printable && new_char!=char){
				var style = (this.getSelectionStyles(this.selectionStart,this.selectionStart+1))[0];
				var measured_width = remeasure(new_char, style.fontSize,style.fontSize, x_font);
				
				if(!measured_width){
					var box = calcPosition(new_char, this.positions, pos, line, this);
				}else{
					var box = {width: measured_width};		
				}
			}else{
				var box = calcPosition(new_char, this.positions, pos, line, this);
			}
		}

		if(printable){
			textFunc.pressed_key = e.key;
			if(typeof this.positions[line]!='undefined' && typeof this.positions[line][pos]!='undefined'){
				//console.log("here", selection_width);
				current_position = this.positions[line][pos].left;
				if(selection_width && !this.inCompositionMode){
					sw = selection_width;
					this.positions[line].splice(pos, 99999);
				}else{
					var prev_char_right = getPrevCharRight(this.positions, line, pos);
					this.positions[line].splice(pos, 0, {left: prev_char_right});	
					this.positions = recalcPositions(this.positions, line, pos, box.width, 0);
				}
			}else{
				console.log("oh nooooooo... else....");
				onInput.call(this, e);
				this.positions = [];
				return ;
			}
		}else{
			//console.log("char not printable ============== ");
			switch(keycode){
				case 13: //enter 
					this.positions.splice(line+1, 0, []);
//					if(typeof this.positions[line]=='undefined'){
//						break;
//					}
					//TODO ошибка
					this.positions[line].splice(pos, this.positions[line].length);
					//this.positions[line][pos].splice(pos-1, 0, []);
				break;
				case 8:
				
					if(typeof this.positions[line]=='undefined'){
						break;
					}
					if(selection_width){
						this.positions[line].splice(pos, 99999);
					}else{
						
						var char_width =  getCharWidth(this.positions, line, pos);
						console.log("deleted size", char_width, pos);

						if(pos==0){
							this.positions.splice(line, 1);
							$.each(this.positions, function(ix, x){
								if(ix>=line-1){
									th.positions[ix] = [];
								}
							});
						}else{
							this.positions[line].splice(pos-1, 1);
							this.positions = recalcPositions(this.positions, line, pos, char_width, 1);
						}
					}
				break;
				case 46:
					if(typeof this.positions[line]=='undefined'){
						break;
					}
					if(selection_width){
						this.positions[line].splice(pos, 99999);
					}else{							
						var char_width =  getCharWidth(this.positions, line, pos+1); //get next char width
						this.positions[line].splice(pos, 1);
						this.positions = recalcPositions(this.positions, line, pos, char_width, 3);
					}

				break;
			}
		}

		onInput.call(this, e);
		
	}
})(fabric.IText.prototype.onInput)

String.prototype.replaceAt=function(index, replacement) {
	return this.substr(0, index) + replacement+ this.substr(index + replacement.length);
}



$(document).on("updateIText", function(e, textarea, font, th){
	if(viewer.inComposite){
		return ;
	}
	var sel = getInputSelection(textarea);
	fonts_array = font.split(", ");
	var current_value = textarea.value;
	
	if(typeof fonts_array[0] != 'undefined' && typeof viewer.chars_table[fonts_array[0]]!='undefined'){
		chars_table = viewer.chars_table[fonts_array[0]];
	}else{
		return;
	}
	
	char_at = current_value.charAt(sel.start-1);
	
	
	//console.log(current_value, sel.start);
	//console.log("chars table", chars_table);
	//console.log("char_at", char_at);
	
	if(char_at!= " " && typeof chars_table[char_at]!='undefined'){
		th.setSelectionStyles({fontFamily: font+", "+th.fontFamily}, sel.start-1,sel.start);
		//current_value = current_value.replace(new RegExp(`${char_at}`, "g"), chars_table[char_at]);
		current_value = current_value.replaceAt(sel.start-1, chars_table[char_at]);
	}

	last_pressed_key = current_value.charAt(sel.start-1);
	textarea.value = current_value;
	setInputSelection(textarea, sel.start, sel.start);
});

