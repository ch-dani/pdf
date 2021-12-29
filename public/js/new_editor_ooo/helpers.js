function guid(){
    function s4() { return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1); }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}


var basename = name => {return /([^\/]*)\.[^.]*$/gm.exec(name)[1];};
function getSystemFont(nc=""){
	var epta_fonts = {
//		"Arial": "Arial",
//		"Arial-Bold": "Arial-Bold",
		"ArialNarrow": "ArialNarrow",
		"ArialNarrow-Bold": "ArialNarrow-Bold",
		"ArialMT": "Arial",
		"ArialMT-Bold": "Arial-Bold",
		"Arial-BoldMT": "Arial-Bold",
		"Helvetica": "Helvetica",
		"Helvetica-Bold": "Helvetica-Bold",
		"Helvetica-Oblique": "Helvetica-Oblique",
		"Helvetica-BoldOblique": "Helvetica-BoldOblique",
		"Times-Roman": "Times-Roman",
		"Times-Bold": "Times-Roman",
		"Times-Roman-Bold": "Times-Roman-Bold",
		"Times-Italic": "Times-Italic",
		"Symbol": "Symbol",
		"ZapfDingbats": "ZapfDingbats",
		"Courier": "Courier",
		"Courier-Bold": "Courier",
		"Courier-Oblique": "Courier",
		"Courier-BoldOblique": "Courier",
		
		
	};
	
	if(typeof epta_fonts[nc]!='undefined'){
		return epta_fonts[nc];
	}
	return false;
}




var pf2 = function pf(value) {
	if (Number.isInteger(value)) {
		return value.toString();
	}

	var s = value.toFixed(10);
	var i = s.length - 1;

	if (s[i] !== '0') {
		return s;
	}

	do {
		i--;
	} while (s[i] === '0');

	return s.substring(0, s[i] === '.' ? i : i + 1);
};

var pm2 = function pm(m) {
	if (m[4] === 0 && m[5] === 0) {
		if (m[1] === 0 && m[2] === 0) {
			if (m[0] === 1 && m[3] === 1) {
				return '';
			}

			return "scale(".concat(pf(m[0]), " ").concat(pf(m[3]), ")");
		}

		if (m[0] === m[3] && m[1] === -m[2]) {
			var a = Math.acos(m[0]) * 180 / Math.PI;
			return "rotate(".concat(pf(a), ")");
		}
	} else {
		if (m[0] === 1 && m[1] === 0 && m[2] === 0 && m[3] === 1) {
			return "translate(".concat(pf(m[4]), " ").concat(pf(m[5]), ")");
		}
	}

	return "matrix(".concat(pf(m[0]), " ").concat(pf(m[1]), " ").concat(pf(m[2]), " ").concat(pf(m[3]), " ").concat(pf(m[4]), " ") + "".concat(pf(m[5]), ")");
};

function getPrevCharRight(positions, line, pos){
	if(typeof positions[line]=='undefined' || typeof positions[line][pos-1]=='undefined' || typeof positions[line][pos]=='undefined' ){
		return 0;
	}
	return positions[line][pos-1].left+(positions[line][pos].left-positions[line][pos-1].left);
}

function getCharWidth(positions, line, pos){
	if(typeof positions[line]=='undefined' || typeof positions[line][pos]=='undefined' || typeof positions[line][pos-1]=='undefined'){
		return 0;
	}
	return positions[line][pos].left-positions[line][pos-1].left;
}

function escapeRegExp(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
}


function getCharUnicode(code, font){
	
	if(typeof viewer.embedFontsNames_reverse[font]=='undefined'){
		return code;
	}
	
	
	var fn = viewer.embedFontsNames_reverse[font];
	
	var new_codes = [];
	for(let i=0; i!=code.length; i++){
		char_code = code[i];
		var x = String.fromCharCode(char_code);
		if(typeof viewer.chars_table_inverse[fn][x]!='undefined'){
			new_codes[i] = viewer.chars_table_inverse[fn][x].charCodeAt(x);
		}else{
			new_codes[i] = char_code;
		}
	}
	
	return new_codes;
}

function charInFont(char, font){
	if(typeof viewer.chars_table_inverse[font]!= 'undefined' && typeof viewer.chars_table_inverse[font][char]!='undefined'){
		return true;
	}
	return false;
}

function info(msg){
	$.toast({
		heading: 'Information',
		text: msg,
		icon: 'info',
		loader: true,        // Change it to false to disable loader
		loaderBg: '#9EC600'  // To change the background
	});
};


const toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
});

const convertEptaCode = char => {


	var p = document.createElement("P");
	var c = document.createTextNode(char)
	p.appendChild(c);

	return p.innerHTML;
}


var saveBlob = (function () {
    var a = document.createElement("a");
    document.body.appendChild(a);
    a.style = "display: none";
    return function (blob, fileName) {
        var url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
    };
}());


function rgb2hex(rgb){
	if(rgb.includes("#")){
		return rgb;
	}

	rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
	return (rgb && rgb.length === 4) ? "#" +
	("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
	("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
	("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}




async function awaitImage(url) {
    var cont = await fetch(url);
    var buff = await cont.blob();
    return readFileAsync(buff);
}


function readFileAsync(file) {
  return new Promise((resolve, reject) => {
    let reader = new FileReader();
    reader.onload = () => {
      resolve(reader.result);
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  })
};

async function base64ToBufferAsync(base64) {
  var dataUrl = base64;
  var cont = await fetch(dataUrl);
  var buff = await cont.arrayBuffer();
  return new Uint8Array(buff);
}

function getBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
  });
}

function getFieldType(f, v){
	switch(f){
		case 'Tx':
			return 'input';
		break;
		case 'Ch':
			return 'select';
		break;
		case 'Btn':
			if(v.checkBox){
				return "checkbox";
			}
			if(v.radioButton){
				return "radio";
			}
			if(v.pushButton){
				return "button";
			}
			console.warn("Unknown form element 1");
			return "unk";
		break;
		
		default:
			alert(f);
			return f;
		break;
	}

}

function searchElementByHtmlId(id){
	var f = false;
	$.each(viewer.pages, function(i,page){
		page.fcanvas.getObjects().forEach((v, i)=>{
			if(typeof v.uniq_id!='undefined'){
				if(v.uniq_id==id){
					f = v;
					return f;	
				}
			}
		});
	});
	return f;
}


function positionHtml(obj, canvas){
    var absCoords = canvas.getAbsoluteCoords(obj);
    html = $(`[uniq_id=${obj.uniq_id}]`)[0];
	console.log(html);
	
    html.style.left = (absCoords.left - 1 / 2) + 'px';
    html.style.top = (absCoords.top - 1 / 2) + 'px';
}


function isFloat(n) {
    return n === +n && n !== (n|0);
}

function isInteger(n) {
    return n === +n && n === (n|0);
}


var updateCanvasState = function(fcanvas) {
	// if ((fcanvas.undoStatus == false && fcanvas.redoStatus == false)) {
	// 	var jsonData = fcanvas.toJSON();
	// 	var canvasAsJson = JSON.stringify(jsonData);
	// 	if (fcanvas.currentStateIndex < fcanvas.canvasState.length - 1) {
	// 		var indexToBeInserted = fcanvas.currentStateIndex + 1;
	// 		fcanvas.canvasState[indexToBeInserted] = canvasAsJson;
	// 		var numberOfElementsToRetain = indexToBeInserted + 1;
	// 		fcanvas.canvasState = fcanvas.canvasState.splice(0, numberOfElementsToRetain);
	// 	} else {
	// 		fcanvas.canvasState.push(canvasAsJson);
	// 	}
	// 	fcanvas.currentStateIndex = fcanvas.canvasState.length - 1;
	// 	if((fcanvas.currentStateIndex == fcanvas.canvasState.length - 1) && fcanvas.currentStateIndex != -1) {
	// 		fcanvas.redoButton.disabled = "disabled";
	// 	}
	// }
}


var canvasUndo = function(fcanvas) {
	// if (fcanvas.undoFinishedStatus) {	
	// 	if (fcanvas.currentStateIndex == -1) {
	// 		fcanvas.undoStatus = false;
	// 	}else if(fcanvas.currentStateIndex===0){


	// 	} else {
	// 		if (fcanvas.canvasState.length >= 1) {
	// 			fcanvas.undoFinishedStatus = 0;
	// 			if (fcanvas.currentStateIndex != 0) {
	// 				fcanvas.undoStatus = true;
	// 				fcanvas.loadFromJSON(fcanvas.canvasState[fcanvas.currentStateIndex - 1], function() {
	// 					var jsonData = JSON.parse(fcanvas.canvasState[fcanvas.currentStateIndex - 1]);
	// 					fcanvas.renderAll();
	// 					fcanvas.undoStatus = false;
	// 					fcanvas.currentStateIndex -= 1;
	// 					fcanvas.undoButton.removeAttribute("disabled");
	// 					if (fcanvas.currentStateIndex !== fcanvas.canvasState.length - 1) {
	// 						fcanvas.redoButton.removeAttribute('disabled');
	// 					}
	// 					fcanvas.undoFinishedStatus = 1;
	// 				});
	// 			} else if (fcanvas.currentStateIndex == 0) {
	// 				fcanvas.clear();
	// 				fcanvas.undoFinishedStatus = 1;
	// 				fcanvas.undoButton.disabled = "disabled";
	// 				fcanvas.redoButton.removeAttribute('disabled');
	// 				fcanvas.currentStateIndex -= 1;
	// 			}
	// 		}
	// 	}
	// }
}

var canvasRedo = function(fcanvas) {
	// if (fcanvas.redoFinishedStatus) {
	// 	if ((fcanvas.currentStateIndex == fcanvas.canvasState.length - 1) && fcanvas.currentStateIndex != -1) {
	// 		fcanvas.redoButton.disabled = "disabled";
	// 	} else {
	// 		if (fcanvas.canvasState.length > fcanvas.currentStateIndex && fcanvas.canvasState.length != 0) {
	// 			fcanvas.redoFinishedStatus = 0;
	// 			fcanvas.redoStatus = true;
	// 			fcanvas.loadFromJSON(fcanvas.canvasState[fcanvas.currentStateIndex + 1], function() {
	// 				var jsonData = JSON.parse(fcanvas.canvasState[fcanvas.currentStateIndex + 1]);
	// 				fcanvas.renderAll();
	// 				fcanvas.redoStatus = false;
	// 				fcanvas.currentStateIndex += 1;
	// 				if (fcanvas.currentStateIndex != -1) {
	// 					fcanvas.undoButton.removeAttribute('disabled');
	// 				}
	// 				fcanvas.redoFinishedStatus = 1;
	// 				if ((fcanvas.currentStateIndex == fcanvas.canvasState.length - 1) && fcanvas.currentStateIndex != -1) {
	// 					fcanvas.redoButton.disabled = "disabled";
	// 				}
	// 			});
	// 		}
	// 	}
	// }
}



function setInputSelection(el, startOffset, endOffset) {
//	el.focus();
	if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
		el.selectionStart = startOffset;
		el.selectionEnd = endOffset;
	} else {
		var range = el.createTextRange();
		var startCharMove = offsetToRangeCharacterMove(el, startOffset);
		range.collapse(true);
		if (startOffset == endOffset) {
			range.move("character", startCharMove);
		} else {
			range.moveEnd("character", offsetToRangeCharacterMove(el, endOffset));
			range.moveStart("character", startCharMove);
		}
		range.select();
	}
}


function getInputSelection(el) {
	var start = 0,
		end = 0,
		normalizedValue, range,
		textInputRange, len, endRange;
	if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
		start = el.selectionStart;
		end = el.selectionEnd;
	} else {
		range = document.selection.createRange();
		if (range && range.parentElement() == el) {
			len = el.value.length;
			normalizedValue = el.value.replace(/\r\n/g, "\n");
			// Create a working TextRange that lives only in the input
			textInputRange = el.createTextRange();
			textInputRange.moveToBookmark(range.getBookmark());
			// Check if the start and end of the selection are at the very end
			// of the input, since moveStart/moveEnd doesn't return what we want
			// in those cases
			endRange = el.createTextRange();
			endRange.collapse(false);
			if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
				start = end = len;
			} else {
				start = -textInputRange.moveStart("character", -len);
				start += normalizedValue.slice(0, start).split("\n").length - 1;
				if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
					end = len;
				} else {
					end = -textInputRange.moveEnd("character", -len);
					end += normalizedValue.slice(0, end).split("\n").length - 1;
				}
			}
		}
	}
	return {
		start: start,
		end: end
	};
}

function transformTypedChar(charStr){ //useless
	return charStr;
	return charStr == "a" ? "z" : charStr;
}

function changeTextArea(evt){
	if (evt.which) {
		var charStr = String.fromCharCode(evt.which);
		var transformedChar = transformTypedChar(charStr);
		if (transformedChar != charStr) {
			var sel = getInputSelection(this),
				val = this.value;
			this.value = val.slice(0, sel.start) + transformedChar + val.slice(sel.end);
			this.value.replace(/a/g, "z");
			console.log(this.value);
			// Move the caret
			setInputSelection(this, sel.start + 1, sel.start + 1);
		}
	}
}

function getFontLoadedName(loaded){
	var ret = false;
	$.each(viewer.embedFontsNames, function(i,v){
		if(v==loaded){
			ret = i;
		}
	});
	return ret;
}

//used in pdfjs
function uniqFontName(name){
	var x_font_name = name.split("+");
	if (typeof x_font_name[1] != 'undefined') {
		var niceName = x_font_name[1];
	} else {
		var niceName = x_font_name[0];
	}
	var ret = niceName.replace(",","_")+"xx"+viewer.font_iterator;
	viewer.font_iterator+=1;
	return ret;
}


$.fn.getElementOffset = function (parent, in_mm) {
    //this
    if (typeof parent === 'undefined' || !parent) {
        parent = $(this).closest(".page");
    }
    
    var bs = 0;
    if($(this).hasClass("text_content_element")){
    	var el = $(this);
    	var bs1 = parseFloat($(this).height())-parseFloat($(this).css("font-size"));
    	bs = parseFloat(el.css("line-height"))-parseFloat(el.css("font-size"));
    }
    

    var childPos = this.offset();
    var parentPos = this.closest(parent).offset();
    var childOffset = {
        top: childPos.top - parentPos.top,
        topbs: childPos.top - parentPos.top-bs,
        left: childPos.left - parentPos.left,
        width: this[0].getBoundingClientRect().width,
        height: this[0].getBoundingClientRect().height,
    }
    if (in_mm) {
        $.each(childOffset, function (i, v) {
            childOffset[i] = px2mm(v);
        })
    }
    return childOffset;
};


var last_pressed_key = "";

function proccessShadingPoints(point0, point1, transform){
	var new_point0 = [point0[0], point0[1]];
	var new_point1 = [point1[0], point1[1]];
	var revert_color = false;



	if(transform[0]<=0 && transform[3]<=0){
		new_point0 = point1.reverse();
		new_point1 = point0.reverse();
	}else{
		if(transform[0]<=0){
			new_point0[0] = point0[1];
			new_point0[1] = point0[0];
			revert_color = true;
		}
		if(transform[3]<=0){
			new_point1[0] = point1[1];
			new_point1[1] = point1[0];
		}
	}
	return [new_point0, new_point1, revert_color];
}

function proccessShading(args, that, trans, trans2){
	//this
	switch (args[0]) {
		case 'RadialAxial':
			var shadingId = "shading";
			var colorStops = args[2];
			var gradient = {};


			
			var new_points = proccessShadingPoints(args[3], args[4], trans);

			switch (args[1]) {
				case 'axial':

					var point0 = new_points[0];
					var point1 = new_points[1];

					//поворот с помощью трансформы
					if(trans[3]<0){ 
						gradient= {
							type: "linear",
							gradientUnits: "userSpaceOnUse",
							x1: point0[1],
							y1: point0[0],
							x2: point1[1],
							y2: point1[0],
							revert_color: new_points[2],
							colorStops: {}
						};

					}else{
						gradient= {
							type: "linear",
							gradientUnits: "userSpaceOnUse",
							x1: point0[0],
							y1: point0[1],
							x2: point1[0],
							y2: point1[1],
							revert_color: new_points[2],
							colorStops: {}
						};
					}
					// console.log(trans);
					// console.log(point0);
					// console.log(point1);
					// alert("axial");

					break;
				case 'radial':
					console.error("cant parse radial gradient");
					return false;

					var focalPoint = args[3];
					var circlePoint = args[4];
					var focalRadius = args[5];
					var circleRadius = args[6];

					gradient= {
						id: shadingId,
						gradientUnits: "userSpaceOnUse",
						x1: point0[0],
						y1: point0[1],
						x2: point1[0],
						y2: point1[1],
						stopCo: []
					};

					gradient = that.svgFactory.createElement('svg:radialGradient');
					gradient.setAttributeNS(null, 'id', shadingId);
					gradient.setAttributeNS(null, 'gradientUnits', 'userSpaceOnUse');
					gradient.setAttributeNS(null, 'cx', circlePoint[0]);
					gradient.setAttributeNS(null, 'cy', circlePoint[1]);
					gradient.setAttributeNS(null, 'r', circleRadius);
					gradient.setAttributeNS(null, 'fx', focalPoint[0]);
					gradient.setAttributeNS(null, 'fy', focalPoint[1]);
					gradient.setAttributeNS(null, 'fr', focalRadius);
					break;

				default:
					throw new Error("Unknown RadialAxial type: ".concat(args[1]));
			}

			var _iteratorNormalCompletion5 = true;
			var _didIteratorError5 = false;
			var _iteratorError5 = undefined;

			try {
				for (var _iterator5 = colorStops[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
					var colorStop = _step5.value;
					// var stop =  {
					// 	stype: "stop_gradient",
					// 	offset: colorStop[0],
					// 	"stop-color": colorStop[1]
					// };
					gradient.colorStops[colorStop[0]] = colorStop[1];
				}
				return gradient
			} catch (err) {
				console.error("gradient error", err);
			} 

			return false;

		case 'Mesh':
			console.warn('Unimplemented pattern Mesh');
			return null;

		case 'Dummy':
			return 'hotpink';

		default:
			throw new Error("Unknown IR type: ".concat(args[0]));
	}


}


function hex2rgb(hex, opacity = 1) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	var result = result ? {
	  r: parseInt(result[1], 16),
	  g: parseInt(result[2], 16),
	  b: parseInt(result[3], 16)
	} : null;
	return "rgba("+Object.values(result).join(",")+","+opacity+")";
  }




function proccessTextChunks(glyphs, current, ctx, _util, that){

	//this
	var font = current.font;
	var pageNumber = ctx.pageNumber;
	var temp_fb = viewer.pages[pageNumber];
	var is_type3 = false;



	if(typeof temp_fb=='undefined'){
		temp_fb = viewer.pages[viewer.now_render];
		pageNumber = viewer.now_render;
		console.warn("canvas is not defined", temp_fb.fabricText);
	}

	if (typeof temp_fb.fabricText[temp_fb.blockIterator][temp_fb.lineIterator] === 'undefined') {
		viewer.pages[pageNumber].fabricText[temp_fb.blockIterator][temp_fb.lineIterator] = [];
		
	}

	if (font.isType3Font) {
		is_type3 = true
		return that.showType3Text(glyphs);
	}

	var fontSize = current.fontSize;
	var fontSizeScale = current.fontSizeScale;
	var charSpacing = current.charSpacing;
	var wordSpacing = current.wordSpacing;
	var fontDirection = current.fontDirection;
	var textHScale = current.textHScale * fontDirection;
	var glyphsLength = glyphs.length;
	var vertical = font.vertical;
	var spacingDir = vertical ? 1 : -1;
	var defaultVMetrics = font.defaultVMetrics;
	var widthAdvanceScale = fontSize * current.fontMatrix[0];
	var simpleFillText = current.textRenderingMode === _util.TextRenderingMode.FILL && !font.disableFontFace && !current.patternFill;


	if (fontSize === 0) {
		return undefined;
	}


	ctx.save();
	var patternTransform;

	//TODO
	if (current.patternFill) {
		patternFill();
	}

	var ct = (ctx.mozCurrentTransform);
	var font_scale = ct[0]; //* tm[0];
							
	if(!font_scale){
		if(ct[1]!=0){
			font_scale = ct[1];
		}else{
			font_scale = 1;
		}
	}
	ctx.transform.apply(ctx, current.textMatrix);
	ctx.translate(current.x, current.y + current.textRise);

	if (fontDirection > 0) {
		ctx.scale(textHScale, -1);
	} else {
		ctx.scale(textHScale, 1);
	}


	var lineWidth = current.lineWidth;
	var scale = current.textMatrixScale;

	if (scale === 0 || lineWidth === 0) {
		var fillStrokeMode = current.textRenderingMode & _util.TextRenderingMode.FILL_STROKE_MASK;
		if (fillStrokeMode === _util.TextRenderingMode.STROKE || fillStrokeMode === _util.TextRenderingMode.FILL_STROKE) {
			that._cachedGetSinglePixelWidth = null;
			lineWidth = that.getSinglePixelWidth() * MIN_WIDTH_FACTOR;
		}
	} else {
		lineWidth /= scale;
	}
	if (fontSizeScale !== 1.0) {
		ctx.scale(fontSizeScale, fontSizeScale);
		lineWidth /= fontSizeScale;
	}
	ctx.lineWidth = lineWidth;

	var x = 0,
		i;

	//======================================
	//======================================
	//======================================
	//======================================

	var blockWidth = 0;
	var blockLeft = false;
	var blockTop = false;
	var char_offset = false;
	var blockWidth = 0;
	var prependPosition = 0;
	var breakFlag = false;


	// if(temp_fb.blockIterator){
	// 	console.log(font);
	// 	console.log(current);
	// 	alert();
	// }
	var full_str = "";


	for (i = 0; i < glyphsLength; ++i) {
		var glyph = glyphs[i];
		if ((0, _util.isNum)(glyph)) {
			x += spacingDir * glyph * fontSize / 1000;
			if(breakFlag){
				blockLeft += (spacingDir * glyph * fontSize / 1000)*current.textMatrixScale;
			}
			continue;
		}
		var restoreNeeded = false;
		var spacing = (glyph.isSpace ? wordSpacing : 0) + charSpacing;
		var character = glyph.fontChar;
		var accent = glyph.accent;
		var scaledX, scaledY, scaledAccentX, scaledAccentY;
		var width = glyph.width;

		if (vertical) {
			var vmetric, vx, vy;
			vmetric = glyph.vmetric || defaultVMetrics;
			vx = glyph.vmetric ? vmetric[1] : width * 0.5;
			vx = -vx * widthAdvanceScale;
			vy = vmetric[2] * widthAdvanceScale;
			width = vmetric ? -vmetric[0] : width;
			scaledX = vx / fontSizeScale;
			scaledY = (x + vy) / fontSizeScale;
		} else {
			scaledX = x / fontSizeScale;
			scaledY = 0;
		}

		if (font.remeasure && width > 0) {
			var measuredWidth = ctx.measureText(character).width * 1000 / fontSize * fontSizeScale;

			if (width < measuredWidth && that.isFontSubpixelAAEnabled) {
				var characterScaleX = width / measuredWidth;
				restoreNeeded = true;
				ctx.save();
				ctx.scale(characterScaleX, 1);
				scaledX /= characterScaleX;
			} else if (width !== measuredWidth) {
				scaledX += (width - measuredWidth) / 2000 * fontSize / fontSizeScale;
			}
			// alert("remeasure");
		}

		if (window.debug == 1) {
			if (glyph.isInFont || font.missingFile) {

				if (simpleFillText && !accent) {
					ctx.fillText(character, scaledX, scaledY);
				} else {
					//TODO draw here
						//alert("time to draw not simpled text 2");
						that.paintChar(character, scaledX, scaledY, patternTransform);

					if (accent) {
						scaledAccentX = scaledX + accent.offset.x / fontSizeScale;
						scaledAccentY = scaledY - accent.offset.y / fontSizeScale;
						//TODO draw here
						that.paintChar(accent.fontChar, scaledAccentX, scaledAccentY, patternTransform);
					}
				}

				alert("debug text "+viewer.pages[pageNumber].currentZIndex);
			}
		}

		var charWidth = width * widthAdvanceScale + spacing * fontDirection;
		x += (charWidth);
		var spaceWidth = font.defaultWidth * widthAdvanceScale + spacing * fontDirection;

		if (restoreNeeded) {
			ctx.restore();
		}
		var cw = (charWidth * current.textMatrixScale) * font_scale;
		var original_char_width = cw;
		viewer.last_char_width = cw;
		blockWidth += cw
		var current_transform = (ctx.getTransform());
		var fs = (viewer.pages[pageNumber].currentFontSize * current.textMatrixScale) * font_scale;

		if(blockLeft===false){
			var transform = ctx.mozCurrentTransform;
			var current_canvas_position = viewer.getCanvasPosition(transform, 0,0);
			blockLeft = current_canvas_position[0];
			blockTop = current_canvas_position[1]-fs;
			if(prependPosition){
				blockLeft+=prependPosition;
			}
		}
		
		temp_iterator++;


		if(true){
			var char_offset = 0;
			//x = 
			if (!scaledX) {
				char_offset = blockLeft;
			} else {
				if (i == 0) {
					char_offset = blockLeft;
				} else {
					char_offset = (blockLeft + (((x * current.textMatrixScale) - (charWidth * current.textMatrixScale)) * font_scale))
				}
			}
		}
		var char_scaled_x = ( (((x * current.textMatrixScale) - (charWidth * current.textMatrixScale)) * font_scale));
		
		if(breakFlag){
			//blockLeft = char_offset;
			//breakFlag = false;
			//blockLeft = char_offset;
			breakFlag = false;
			// console.log(blockLeft, char_scaled_x, char_offset);
			// alert();
		}

		


		var rad = Math.atan2(current_transform.b, current_transform.a)
		//sp = spaceWidth;
		var sp = 278 * widthAdvanceScale + spacing * fontDirection

		buildCharsTable(font.missingFile ? font.fallbackName : font.loadedName, character, glyph.unicode, original_char_width);
		

		
		if (blockLeft > 0 && blockTop > 0){
			var use_fallback = false;
			var fontFamily = font.missingFile ? false : font.loadedName;
			if (!fontFamily) {
				//console.log(font);
			}

			var x_font_name = font.name.split("+");
			if (typeof x_font_name[1] != 'undefined'){
				var niceName = x_font_name[1];
			}else{
				var niceName = x_font_name[0];
			}

			var new_nc = getSystemFont(niceName);
			if(new_nc){
				fontFamily = new_nc;
				character = glyph.unicode
			}
			var loadedFont = font.name.replace(",", "-").replace("+", "-");
			var cy = viewer.pages[pageNumber].line_offset_top - scaledY * fontSizeScale;

			//TODO бьем строку на несколько блоков, если длинный пробел
			if(false && spaceWidth>0 && glyph.isSpace && spaceWidth*2<cw){
				//console.log("==break==",temp_fb.blockIterator);
				breakFlag = true;
				full_str = "";
				//char_offset
				x = 0;
				//alert(blockLeft + " || " +char_offset);
				console.log("prevvvvv");
				console.log(blockLeft, char_offset, cw,blockWidth,x);
				console.log("eeeeeprevv");
				blockLeft = char_offset+cw;
				char_offset = 0;
				blockWidth = 0;
				createNewBlock(pageNumber);
				var temp_fb = viewer.pages[pageNumber];
				current.fillColor = "rgb(255,0,0)";

				if (vertical) {
					current.y -= x * textHScale;
				} else {
					current.x += x * textHScale;
				}


				continue;
			}


			full_str += glyph.unicode


			viewer.pages[pageNumber].fabricText[temp_fb.blockIterator][temp_fb.lineIterator].push({
				type3: false,
				rad: rad,
				charSpacing: charSpacing * fs,
				fontSizeScale: fontSizeScale,
				blockLeft: blockLeft,
				blockTop: (blockTop), //+font.ascent-font.descent+0.5),
				blockWidth: blockWidth,
				fontSize: fs,
				fallbackFont: font.fallbackName,
				loadedFont: loadedFont,
				font: is_type3?"Arial":fontFamily,
				character: is_type3?glyph.unicode:character,
				unicode: glyph.unicode,
				isSpace: glyph.isSpace,
				spacing: spacing,
				fontSpaceWidth: spaceWidth,  
				inFont: glyph.isInFont,
				lineBreaker: false,
				color: current.fillColor,
				charWidth: cw,
				spaceWidth: sp,
				inBlockLeft: 0,
				charLeft: char_offset,
				x: x,
				y: cy, 
			});
			
		}
		prev_char_offset = char_offset;
	}

	//endfor

	if (vertical) {
		current.y -= x * textHScale;
	} else {
		current.x += x * textHScale;
	}
	window.prev_block_right = blockLeft + cw;
	ctx.restore();
}



function removeFakeSpaces(texts) {
	var new_texts = {};
	var block_iterator = 0;
	var fakeSpaceFactor = 3;

	
	
	$.each(texts, function(ti, text_block){
		
		if(ti!=36){
			//return;
		}
		if(typeof new_texts[block_iterator]=='undefined'){
			new_texts[block_iterator] = {zIndex: text_block.zIndex};
		}
		$.each(text_block, function(i, line){
			if(i=='zIndex'){
				return;
			}
			var prepend = 0;
			if(typeof new_texts[block_iterator][i]=='undefined'){
				//new_texts[block_iterator][i] = [];
			}
			try{

			line.forEach(function(char, ci){
				// console.log(char.fontSpaceWidth, char.isSpace, char.fontSpaceWidth, char.charWidth);

				var char_offset = false;
				if(typeof line[ci+1]!='undefined'){ 
					next_left = line[ci+1].charLeft;
					current_right = line[ci].charLeft+line[ci].charWidth;
					char_offset = parseInt(next_left)-parseInt(current_right);
					//new_texts[block_iterator][i].push(char);
					//return ;
					// console.log("undefined");
					// alert("is undefined");
				}
				
				// console.log(char.unicode,next_left,"-", current_right, char_offset, char.charWidth);
				// alert(char.unicode);

				if(
					char.isSpace && ((char.fontSpaceWidth>1 && char.fontSpaceWidth*fakeSpaceFactor<char.charWidth))
					|| (char_offset && char.charWidth && char.charWidth*fakeSpaceFactor < char_offset)
				){ //if char is space and he is very long - create new text block, and save offset
					//alert("break");

					block_iterator++;
					new_texts[block_iterator] = {};
					if(typeof line[ci+1]=='undefined'){ 
						prepend = 0;
					}else{
						prepend = line[ci+1].charLeft-char.blockLeft;
					}

				}else{
					if(typeof new_texts[block_iterator][i]=='undefined'){
						new_texts[block_iterator][i] = [];
					}
					char.blockLeft += prepend; //recalculate 
					char.blockWidth -=  prepend;
					new_texts[block_iterator][i].push(char);
				}
				//alert();
			});
			}catch(e){
				console.log(e)
				alert("error 14");

			}
		});
		block_iterator++;
	});
	$.each(new_texts, function(i,v){
		if(!Object.entries(v).length){
			delete new_texts[i];
		}
	});

	return new_texts;
}


