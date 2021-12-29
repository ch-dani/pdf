var newImagesEditor = {
	selected_images: false,
	imageObject: false,
	width: 0,
	height: 0,
    $sign_canvas: $('#sign_draw_canvas'),
    sign_canvas: document.getElementById('sign_draw_canvas'),
    sign_canvas_cxt: false,
    selector: ".image-editable-menu",
    activeCanvas: false,
    init: function () {
    	$(document).on("change", "#new_image_uploader", this.imageUpload);
        $(document).on("mousemove", ".active_image_moving", this.moveInsertedImages); //TODO двигаем картинку за мышкой
        $(document).on("click", ".user_image_outer, .image_form_item", this.imageFromUrl);
        $(document).on("keyup", "#sign_text_input", this.typeTextSign);
        $(document).on("click", "#sign_previews .sign_preview", this.insertTextSign);
		$(document).on("click", "#save_new_sign", this.saveSign);
		$(document).on("click", ".image-editable-menu .delete", this.removeBlock);
        this.initSignDraw();
    },

	click: function(obj){
		obj = "target" in obj?obj.target:obj;
	
		var position = obj.getBoundingRect(),
			that = newImagesEditor;
		
	
	
		var offset = $(obj.canvas.lowerCanvasEl).getElementOffset($("#simplePDFEditor"));
		position.top = position.top+offset.top;

		that.activeCanvas = obj.canvas;
		obj = that.activeCanvas.getActiveObject();
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
		this.editor_active = false;
	},

	removeBlock: function(){
		that = newImagesEditor;
		obj = that.activeCanvas.getActiveObject();
		that.activeCanvas.remove(obj);
		that.hideEditor();
	},
    
	saveSign: function(e){
    	switch($(".signatore-btn-block.signatore-btn-active").data("type")){
    		case 'text':
    			//$(".signaturePreview.sign_preview").eq(0).click();
    		break;
    		case 'draw':
    			newImagesEditor.insertTextSign(e, "draw");
    		break;
    	}
    },

    imageFromUrl: function(e){
    	e.preventDefault();
    	$("#pdf_editor_pages").addClass("active_image_moving");
    	var src = $(this).find("img").attr("src");
		var imgObj = new Image();
		imgObj.src = src;
		newImagesEditor.width = imgObj.width;
		newImagesEditor.height = imgObj.height;
		newImagesEditor.imageObject = imgObj;
		if($(this).hasClass("image_form_item")){
			$(imgObj).addClass("follow_the_mouse");
		}else{
			$(imgObj).addClass("follow_the_mouse").css({width: "200px"});
		}
		$("#pdf_editor_pages").append(imgObj);
    },
    imageUpload: async function(e, x){
		var reader = new FileReader();
		reader.onload = (event)=>{
			$("#pdf_editor_pages").addClass("active_image_moving");
			
			var imgObj = new Image();
			imgObj.src = event.target.result;
			imgObj.onload = function () {
				newImagesEditor.width = imgObj.width;
				newImagesEditor.height = imgObj.height;
				$(imgObj).addClass("follow_the_mouse").css({width: "200px"});
				$("#pdf_editor_pages").append(imgObj);
				newImagesEditor.imageObject = imgObj;
			};
		};
		reader.readAsDataURL(e.target.files[0]);
    },
    imageAppend: function(fcanvas, point){
		var image = new fabric.Image(this.imageObject);
		
		if(this.width<200){
			scale=1;
		}else{
			scale = 200/this.width;
		}
		image.set({
			scaleX: scale,
			scaleY: scale,
			left: point.x,
			top: point.y,
			subtype: "image"
		});
		image.canvas = fcanvas;
		fcanvas.setActiveObject(image);
		fcanvas.renderAll();
		
		newImagesEditor.click(image);
		image.on("mousedown", function(){ newImagesEditor.hideEditor() });
		image.on("mouseup", newImagesEditor.click);
		fcanvas.add(image);
		fcanvas.renderAll();
		$(".follow_the_mouse").remove();
		this.imageObject = false;
    },
    moveInsertedImages: function (e) {
        var that = newImagesEditor,
            scene = $(".active_image_moving"),
            offsetX = (e.pageX - scene.offset().left),
            offsetY = (e.pageY - scene.offset().top);
            
        if ($(".follow_the_mouse").length > 0) {
            $(".follow_the_mouse").css({top: offsetY, left: offsetX});
        }
    },
    typeTextSign: function (e) {
        $("#sign_previews .sign_preview").html($(this).val());
    },
    
    insertTextSign: function (e, type='text') {
    	$("#pdf_editor_pages").addClass("active_image_moving");
        var element = type=='text'?e.target:$("#sign_draw_canvas")[0];
        var that = newImagesEditor;// canvas = document.createElement('canvas');
		$(element).css({"background-color": "none", "background": "none", "color": "black", "width": "200px"});
		
        html2canvas(element, {backgroundColor: null}).then(canvas => {
        	$(canvas).addClass("follow_the_mouse");
            $("#pdf_editor_pages").append(canvas);
            newImagesEditor.imageObject = canvas;
			newImagesEditor.width = canvas.width;
			newImagesEditor.height = canvas.height;
            
            //that.imageUpload(e, canvas, "text_sign");
            $(".create-signature-modal").hide();
        });
    },
    typeTextSign: function (e) {
        var val = $(this).val();
        $("#sign_previews .sign_preview").html(val);
    },
    eraseSignDraw: function () {
        var m = confirm("Want to clear");
        if (m) {
            imagesEditor.sign_canvas_cxt.clearRect(0, 0, imagesEditor.sign_canvas.width, imagesEditor.sign_canvas.height);
            //document.getElementById("canvasimg").style.display = "none";
        }
    },
    initSignDraw: function () {
        var canvas,$canvas, ctx, flag = false,
            prevX = false,
            prevY = false,
            currX = 0,
            currY = 0,
            dot_flag = false,
            color = "black",
            lineWidth = 4,
            w = 0,
            h = 0;
            
        function init() {
            canvas = newImagesEditor.sign_canvas;
            $canvas = newImagesEditor.$sign_canvas;
            newImagesEditor.sign_canvas_cxt = ctx = canvas.getContext("2d");
            w = canvas.width;
            h = canvas.height;
            canvas.addEventListener("mousemove", function (e) {
                findxy('move', e)
            }, false);
            canvas.addEventListener("mousedown", function (e) {
                findxy('down', e)
            }, false);
            canvas.addEventListener("mouseup", function (e) {
                findxy('up', e)
            }, false);
            canvas.addEventListener("mouseout", function (e) {
                findxy('out', e)
            }, false);
        }

        function findxy(res, e) {

            var offset = $canvas.offset();

            if (res == 'down') {
                currX = e.clientX - (offset.left);
                currY = e.clientY - (offset.top - $(document).scrollTop());
                prevX = currX;
                prevY = currY;
                flag = true;
                dot_flag = true;
                if (dot_flag) {
                    ctx.beginPath();
                    ctx.fillStyle = color;
                    ctx.fillRect(currX, currY, 2, 2);
                    ctx.closePath();
                    dot_flag = false;
                }
            }
            if (res == 'up' || res == "out") {
                flag = false;
            }
            if (res == 'move') {
                if (flag) {
                    prevX = currX;
                    prevY = currY;
                    currX = e.clientX - (offset.left);
                    currY = e.clientY - (offset.top - $(document).scrollTop());
                    ctx.beginPath();
                    ctx.moveTo(prevX, prevY);
                    ctx.lineTo(currX, currY);
                    ctx.strokeStyle = color;
                    ctx.lineWidth = lineWidth;
                    ctx.stroke();
                    ctx.closePath();
                }
            }
        }

        init();
    },
    now_rotated: 0, 
    deleteImage: function (e) {
        var that = imagesEditor;
        e.preventDefault();
        $(`.outer_image_div[element-id='${that.element_id}']`).remove();
        spe.removeFromHistoryByID(that.element_id);
        that.el.hide();
    },
};


newImagesEditor.init();
