
function getRandomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}


async function renderElements(elements, canvas, pageNumber){
	var canvas = viewer.pages[pageNumber].fcanvas;



	//$.each(elements, async (i,v)=>{
	for(let eit=0;eit!=elements.length; eit++){
		v = elements[eit];
		
		if(typeof v=='undefined'){
			continue;
		} 

		switch(v.element_type){
			case 'image':
				await renderSingleImage(v,canvas, eit, pageNumber);
			break;
			case 'path':
				await renderSinglePath(v, canvas, eit, pageNumber);
			break;
		}
	};
}

async function renderSinglePath(rect, canvas, i, pageNumber=1){
	if(rect.drawIt!==true){
		return;
	}

	if(i>0){
		//return ;
	}

	if(!rect.path || typeof rect.path=='undefined'){
		return;
	}

	switch(rect.type){
		case 'path':

			//console.log(i, rect.fill, rect.strokeColor, rect.gradient);
			if(typeof rect.fill=='undefined' && typeof rect.strokeColor=='undefined' && typeof rect.gradient=='undefined'){
				return ;
			}
			// console.log(rect);
			// alert("render single "+i);
			// if("fillStyle" in rect){
			// 	var style = {
			// 		fillStyle: rect.fillStyle,
			// 	};
			// }else{
			var style = {
				original_path: rect.path,
				//pdfNum: i,
				xalpha: rect.alpha,
				fill: ("fill" in rect)?rect.fill:"rgba(255,255,255,0)",
				strokeDashArray: ("dash" in rect)?rect.dash.map(x => x*viewer.scale):[1,1],
				stroke: ("strokeColor" in rect)?rect.strokeColor:"rgba(255,255,255,0)",
				strokeWidth: ("strokeWidth" in rect)?rect.strokeWidth:0,
				selectable: false,
				evented: false,
				//left: 0,
			};
			// }
			

			//TODO костыль
			style.strokeWidth /= viewer.scale; 
			var obj = new fabric.Path(rect.path, style);
			
			//alert("zzz "+rect.zIndex);
			obj.zIndex = rect.zIndex;
			obj.hasRotatingPoint = false;
			obj.subtype = "rect";
			obj.hasControls = false;
			obj.skipTargetFind = false
			obj.current_z_index = rect.current_z_index;
			//ob.hasBorders = false

			if("gradient" in rect && rect.gradient){
				
				obj.fill = "red";
				
				$.each(rect.gradient.colorStops, function(i,v){
					rect.gradient.colorStops[i] = v=='transparent'?"rgba(255,255,255,0)":v
				});

				if(rect.gradient.revert_color){
					alert("inverse colors");
				}

				obj.setGradient('fill', {
					type: 'linear',

					// x1: obj.width*rect.gradient.x1,
					// y1: obj.height*rect.gradient.y1,
					// x2: obj.width*rect.gradient.x2,
					// y2: obj.height*rect.gradient.y2,

					x1: rect.gradient.x1,
					y1: rect.gradient.y1,
					x2: rect.gradient.x2,
					y2: rect.gradient.y2,



					colorStops: rect.gradient.colorStops
					
					//rect.gradient.colorStops
				});

				// console.log("gradient pos ");
				// console.log(rect.gradient);

				// console.log(obj);
				// alert("time to gradient");
			}
			obj.it = i;
			

			obj.on("mousedown", function(){ newRectangleEditor.hideEditor() });
			obj.on("mouseup", newRectangleEditor.click);
			canvas.add(obj);
			window.last_obj = obj;
			if(render_in_slider){
				obj.moveTo(rect.current_z_index);
			}else{
				viewer.pages[pageNumber].zIndexFix.push({
					obj: obj,
					zIndex: rect.zIndex
				});
				//canvas.moveTo(obj, rect.zIndex);
			}
			//obj.moveTo(rect.zIndex);

		break;
		default:
			console.warn("Unknown rect", rect);
		break;
	}
	return true;

}


async function waitFabricImage(data_url){
	return new Promise((resolve, reject) => {
		fabric.Image.fromURL(data_url, function(myImg, opt) {

			resolve(myImg);
		});
	});
}

async function renderSingleImage(img, fcanvas, eit, pageNumber=1){
	var clip_it = false;
	var rotated = false; //img.width<0||img.height<0?true:false;

	if(("useClipPath" in img)){
		if(typeof img.useClipPath!='undefined' && typeof img.useClipPath.path != 'undefined'){
			var pairs = img.useClipPath.path.split(",").map((p)=>{ return !isNaN(p)?parseFloat(p):p });

			var crop_top_offset = 0;
			try{
				if(img.useClipPath.trans.f){
					crop_top_offset = img.useClipPath.trans.f;
				}
			}catch(e){
			}


			if(img.minusHeight===true){ //TODO вероятность 95% что хуерга и что может резать
				var clip_it = new fabric.Path(img.useClipPath.path, {left: img.left, top: img.top, absolutePositioned: true, fill: "red", "stroke": "blue"});
			}else{
				var clip_it = new fabric.Path(img.useClipPath.path, {left: pairs[1], top: pairs[2]-crop_top_offset, absolutePositioned: true, fill: "red", "stroke": "blue"});
			}
		}
	}

	if(img.src=='canvas'){
		var data_url = await resizeImage(img.element, img.width, img.height, true);
		//clip_it = false;
	}else{
		var temp_canvas = await resizeImage(img.element, img.width, img.height);
		var data_url = temp_canvas.canvas[0].toDataURL();
	}

	//$("body").prepend(`<img src="${data_url}">`)


	var myImg = await waitFabricImage(data_url);

	if(fcanvas.pageNumber==12){
		//clip_it = false;
		//console.log(img.left, img.top);
	}

	//fabric.Image.fromURL(data_url, function(myImg, opt) {
		var image = myImg.set({
			left: img.left, 
			top: img.top, 
			opacity: img.opacity,
			width: parseInt(img.width)>=0?img.width:img.width*-1, 
			height: parseInt(img.height)>=0?img.height:img.height*-1,
			evented: false,
			subtype: "image",
		});
		//alert(image.width + " || "+image.height);
		
		//TODO uncomment
		if(clip_it && !rotated){
			image.clipPath = clip_it;

			// if(fcanvas.pageNumber==12 && eit > 22){
			// 	console.log(pairs);
			// 	console.log(img);
			// 	console.log(clip_it);
			// 	alert(eit);
			// }

			group = new fabric.Group([image]);
			
			group.evented = false;
			group.selectable = false;
			group.subtype = "image_group";

			group.on("mousedown", function(){ newImagesEditor.hideEditor() });
			group.on("mouseup", newImagesEditor.click);
			group.on("scaling", function(obj){
				var sy = obj.target.scaleY;
				var sx = obj.target.scaleX;
				var img = obj.target.getObjects("image");
				img[0].clipPath = null;
				img[0].useClipPath = null;
				// img[0].clipPath.scaleX = sx;
				// img[0].clipPath.scaleY = sy;
			})
			group.on("scaled", function(obj){
				return false;
			})

			viewer.pages[pageNumber].zIndexFix.push({
				obj: image,
				zIndex: img.zIndex
			});
		

			fcanvas.add(group);
		}else{

			image.evented = false;
			image.selectable = false;
			image.subtype = "image_group";
			image.on("mousedown", function(){ newImagesEditor.hideEditor() });
			image.on("mouseup", newImagesEditor.click);
			fcanvas.add(image);

			viewer.pages[pageNumber].zIndexFix.push({
				obj: image,
				zIndex: img.zIndex
			});
		

			//x = image.moveTo(5);
		}
		
	//});
	return true;

}


function renderHtmlElements(elements, fcanvas){
	elements.forEach(function(v,i){
		var uniq_id = guid();
		var point = {
			left: v.left,
			top: v.top,
			width: v.width,
			height: v.height
		}

		var offset = $(fcanvas.lowerCanvasEl).getElementOffset($("#simplePDFEditor"));
		point.top = point.top+offset.top;

		var select_vals = false;
		if(v.fieldType=='select'){
			select_vals = [];
			select_vals.push("");
			$.each(v.fieldOptions, function(fi, fv){
				select_vals.push(fv.displayValue);
			});
		};

		var template = newFormsEditor.createTempElement(v.fieldType,v.fieldValue, false, uniq_id, select_vals);
		template.css(point);
		$("#pdf_editor_pages").append(template);

		var options = {
			element_type: v.fieldType,
			fieldName: v.fieldName,
			fieldValue: v.fieldValue,
			options: select_vals
		};
		newFormsEditor.elementAppend(fcanvas, point, false, uniq_id, options);
	});
	//fcanvas.renderAll();
}

function renderLinks(links, fcanvas, subtype){
	links.forEach(function(v,i){
		if(typeof v.url=='undefined'){
			return ;
		}
		if(!subtype){
		}else{
			v.subtype = subtype;
		}

		v.fill = 'rgba(0,0,255,0.4)';
		var link = new fabric.Rect(v);
		fcanvas.add(link);
		fcanvas.moveTo(link, zIndex.annot);
		link.on("mousedown", function(){ newRectangleEditor.hideEditor() });
		link.on("mouseup", newRectangleEditor.click);		
		
	});
};

function renderAnnotations(links, fcanvas, subtype){
	links.forEach(function(v,i){
		v.annotation_type = v.subtype;
		v.subtype = "annotate";
		v.opacity = 0.4;
		//v.fill = 'rgba(0,0,0,0)';
		var link = new fabric.Rect(v);
		fcanvas.add(link);
		fcanvas.moveTo(link, zIndex.annot);
		link.on("mousedown", function(){ newAnnotateEditor.hideEditor() });
		link.on("mouseup", newAnnotateEditor.click);					
	});
};
function toggleLinksAndAnnotationsView(show, type){
	var fill = "rgba(0,0,255,0.4)",
		selectable = true,
		editable = true,
		evented = true;
		
	$.each(viewer.pages, (pn,page)=>{
		page.fcanvas.getObjects("rect").forEach((v)=>{
			if(typeof v.subtype== 'undefined'){
				console.warn("v.subtype is undefined");
				v.subtype = "";
			}
			if(v.subtype.toLocaleLowerCase()==type.toLocaleLowerCase()){
				if(!show){
					evented = false;
					selectable = false;
					editable = false;
					fill = "rgba(0,0,0,0)";
				};
				if("fill_original" in v){
					fill = v.fill_original;
				}
				if(fill.indexOf("rgb(")===0){
					fill = fill.replace(")", ",0.4)");
				}
				//console.log("toggle link");
				v.set("fill", fill);
				v.set("selectable", selectable);
				v.set("editable", editable);
				v.set("evented", evented);
			};
		});
		if(!show){
			page.fcanvas.discardActiveObject();
			page.fcanvas.renderAll();
		}
		
		page.fcanvas.renderAll();
	});
};

function renderHighlights(highlights, fcanvas){
	

}

function renderBorderBox(fcanvas, type="i-text"){

	fcanvas.contextContainer.strokeStyle = '#089de3';
	fcanvas.forEachObject(function(obj) {
		if(obj.type==type){
			var bound = obj.getBoundingRect(true, true);
			fcanvas.contextContainer.setLineDash([6]);
			fcanvas.contextContainer.strokeRect(
				bound.left + 0.5,
				bound.top + 0.5,
				bound.width,
				bound.height
			);
		}
	});
	fcanvas.contextContainer.strokeStyle = "red";
}



var clipByName = function(ctx){
    this.setCoords();
    var clipRect = findByClipName(this.clipName);
    var scaleXTo1 = (1 / this.scaleX);
    var scaleYTo1 = (1 / this.scaleY);
    ctx.save();
    
    var ctxLeft = -( this.width / 2 ) + clipRect.strokeWidth;
		var ctxTop = -( this.height / 2 ) + clipRect.strokeWidth;
		var ctxWidth = clipRect.width - clipRect.strokeWidth;
		var ctxHeight = clipRect.height - clipRect.strokeWidth;

    ctx.translate( ctxLeft, ctxTop );
    
    ctx.rotate(degToRad(this.angle * -1));
    ctx.scale(scaleXTo1, scaleYTo1);
    ctx.beginPath();
    ctx.rect(
        clipRect.left - this.oCoords.tl.x,
        clipRect.top - this.oCoords.tl.y,
        clipRect.width,
        clipRect.height
    );
    ctx.closePath();
    ctx.restore();
};


async function addImageProcess(src){
	return new Promise((resolve, reject) => {
		let img = new Image()
		img.onload = () => resolve(img);
		img.onerror = reject
		img.src = src;
	})
}

async function resizeImage(image, w, h,is_canvas=false){
	var rotate = 0;
	var xw = w;
	var xh = h;

	if(is_canvas){
		var canvas_e = $(`<canvas style='border: 1px solid red;' width="${Math.abs(w)}" height="${Math.abs(h)}"></canvas>`);
		var ctx = canvas_e[0].getContext("2d");
		if(w<0){
			ctx.scale(-1,1)
		}
		if(h<0){
			ctx.scale(1,-1);
		}
//		$("body").prepend(canvas_e);
		var imgobj = await addImageProcess(image);
		await ctx.drawImage(imgobj, 0, 0, imgobj.width, imgobj.height, 0, 0,w, h);
		//ctx.drawImage(imgobj, 0, 0, 200, 200, 0, 0,w, h);
		return canvas_e[0].toDataURL();;//{canvas: canvas_e., w: w, h:h};
	}else{
		//TODO добавить проверку на поворот в одну сторону
		if(w<0 || h<0){ //перевернутая картинка
			ow = w;
			w = h*-1;
			h = ow*-1;
			rotate = true;
		}
		var canvas_e = $(`<canvas style='border: 1px solid red;' width="${w}" height="${h}"></canvas>`);
		//$("body").prepend(canvas_e);
		var ctx = canvas_e[0].getContext("2d");
		
		if(rotate){
			await ctx.drawImage(image, 0, 0, image.width, image.height, -w, -h,w, h);
		}else{
			await ctx.drawImage(image, 0, 0, image.width, image.height, 0, 0,w, h);
		}

		return {canvas: canvas_e, w: w, h: h};
	}
};


var ga=0;

function renderText(){


}

