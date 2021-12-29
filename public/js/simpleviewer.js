'use strict';


/**
 * Upper code can be replaced this code
 * TODO: whrite code under :)
 */
const pt_to_mm =  0.3527777777778;

function mm2px() {
    var e = document.createElement("div");
    e.style.position = "absolute";
    e.style.width = "1000mm";
    document.body.appendChild(e);
    var rect = e.getBoundingClientRect();
    document.body.removeChild(e);
    return rect.width / 1000;
}

function px2mm(px, scale=false){
	if(scale){
		
	}else{
		if(typeof PDFTOOLS.name!='undefined' && PDFTOOLS.name=='translatepdf'){
			scale = PDFTOOLS.bm*0.75;
		}else{
			if(typeof spe.pdfViewer.currentScale=='undefined'){
				console.warn('spe.pdfViewer.currentScale is undefined');
			}
			scale = typeof spe.pdfViewer.currentScale=='undefined'?1:spe.pdfViewer.currentScale;
		}
	}
    return px / pixel_ratio / scale;
}

var dpr = window.devicePixelRatio;
var ww = $(window).width()/dpr;


window.pixel_ratio = mm2px();

if (!pdfjsLib.getDocument || !pdfjsViewer.PDFViewer) {
    alert('PDF LIB NOT FOUND');
}

pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
var CMAP_URL = '/libs/pdfjs-dist/cmaps/';
var CMAP_PACKED = true;
var BLANK_PDF = '/blank.pdf';
var DEFAULT_URL = '/blank.pdf';
var SEARCH_FOR = '';

var UUID = getCookie("spe_uuid");
if (typeof UUID == 'undefined') {
	if(window.new_uuid && typeof window.new_uuid!='undefined'){
		UUID = window.new_uuid
	}else{
    	UUID = guid();
    }
    
    setCookie("spe_uuid", UUID);
}


$(document).on("click", ".weburl-chooser", function(e){
	e.preventDefault();

	let url = "";
	if(url = prompt("Enter pdf url", "")){
		async function loadFile(url){
			var x = await createFile2(`/pdf/getExternalFile?url=${url}`, "file from url.pdf","sss");
			// pdfUploader.fileSelected(false, [x]);

			if(typeof A_TOOL!='undefined' && A_TOOL){
				A_TOOL.fileSelected(false, x);
			}else if(typeof PDFTOOLS!='undefined' && PDFTOOLS.name){
				pdfUploader.fileSelected(false, [x]);
			}else{
				pdfUploader.fileSelected(false, [x]);
			}
		}
		loadFile(url);
//		if(typeof PDFTOOLS.name != 'undefined'){
//		}else{
//			//blocker.show();
//			spe.init({container_selector: "simplePDFEditor", external_url: `/pdf/getExternalFile?url=${url}`});
//		}
	}
});
var editor_is_init = false;
var file_is_selected = false;

if(typeof window.operation_id=='undefined' || !window.operation_id){
	window.operation_id = guid();
}


var currentUploads = {

	getTotalPercent: function(){
		var perc = 0;
		var count =0;
		$.each(this.progress, function(i, v){
			if(v!=false){
				if(isNaN(v)){
					v = 1;
				}
				count++;
				perc += parseInt(v);
			}
		});
		var ret = (perc/count).toFixed(0);
		return isNaN(ret)?0:ret;
	},
	operations: {
	
	},
	progress: {
		
	},
	changeUploadProgress: function(key, status){
		this.progress[key] = status;
	},
	changeStatus: function(key, status){
		this.operations[key] = status;
	},
	isIncomplete: function(){
		if(!this.operations){
			return false;
		}
		var flag = false;
		$.each(this.operations, function(i,v){
			if(v==true){
				flag = true;
			}
		});
		return flag;
	}
};

var pdfUploader = {
    el: false,
    operation_id: window.operation_id,
    init: function (element) {
        this.el = element;
        this.bind();
    },
    bind: function () {
        this.el.on("change", this.fileSelected);
      //  $("#uploader_section .upload-bottom-text .new-pdf").on("click", this.startBlank);
    },
    startBlank: function () {
    	spe.start_blank = 1;
        spe.init({container_selector: "simplePDFEditor", external_url: BLANK_PDF});
        return false;
    },
    startDropbox: function (link) {
        spe.init({container_selector: "simplePDFEditor", external_url: link});
        return false;
    },
    fileSelected: function (e, files = false) {
    	console.log('sv: fileSelected');

        if(skip_simple_viewer===true){
            $(document).trigger("file_selected", [this.files[0], false]);
            return false;
        }
        //upload

        if(typeof this.files!='undefined' && typeof this.files[0]!='undefined'){
            $(document).trigger("selected_file_name", [this.files[0].name]);
        }

    	if(file_is_selected && !PDFTOOLS.multiple_upload ){
    		return false;
        }
        
    	if(files){
    		this.files = files;
    	}
    	console.log("Selected files: ",this.files);
    	
	    $(".file_name_here").html($.map(this.files, function(val) { return val.name; }).join(", "));

    	return $.each(this.files, function(i, file){
			var file_ext = file.name.split(".").pop().toLowerCase();
			
			if(file_ext == 'docx'){
				
				$(document).trigger("before_upload_file", [file]);
				
				var uploadPromise = spe.uploadFile(file, "DOCX", false);
				$(document).trigger("file_selected", [file, uploadPromise]);
				
				// return uploadPromise;
			}else if(typeof PDFTOOLS!='undefined' && PDFTOOLS.name == 'EPUB2PDF'){
				$(document).trigger("before_upload_file", [file]);
				var uploadPromise = spe.uploadFile(file, "EPUB", false);
				$(document).trigger("file_selected", [file, uploadPromise]);				
			}else{
				if(file.type=='application/vnd.ms-powerpoint' || file.type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
					file_is_selected = true;
					var that = pdfUploader,
						//file = (this.files[0]),
						file_ext = file.name.split(".").pop().toLowerCase();
	//				if(file_ext!='ppt' || file_ext!='pptx'){
	//					swal("Error", "Please select PPT file", "error");
	//				}
	//				
					$(document).trigger("before_upload_file", [file]);
					var uploadPromise = spe.uploadFile(file, "PDF", false);
					$(document).trigger("file_selected", [file, uploadPromise]);
				}else{

					if(typeof new_editor!='undefined' && new_editor){
						pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/new_editor/libs/pdfjs-dist/build/pdf.worker.js';
						$(document).trigger("new_editor_file_selected", [file]);
						
					}else{
						pdfUploader.getBlob(file).then((data) => {
							
							$(document).trigger("getting_blob", [false]);
							pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
							var loadingTask = pdfjsLib.getDocument({data: data});
							
							$(document).trigger("pdf_loading_task", [loadingTask]);

							loadingTask.promise.then(function(pdfDoc_){
								spe.file_num++;
								file_is_selected = true;
								var that = pdfUploader,
									//file = (this.files[0]),
									file_ext = file.name.split(".").pop().toLowerCase();
								if(file_ext!='pdf'){
									swal("Error", "Please select PDF file", "error");
								}
								
								console.log("============== file "+i+" selected ====================");
								
								$(document).trigger("before_upload_file", [file]);
								var uploadPromise = spe.uploadFile(file, "PDF", false);
								
								$(document).trigger("file_selected", [file, uploadPromise]);
								
								if($("#simplePDFEditor").length>0){
									if(typeof window.is_fill_and_sign!='undefined' && window.is_fill_and_sign){
										FillAndSign.init(file);
									}else{
									
										console.log("time to init");
			//							that.getBlob(file).then((data) => {
											//blocker.show();
										spe = $.extend(spe, {filename: file.name, size: file.size, fileData: data});
										spe.init({container_selector: "simplePDFEditor", data: data});
			//							});
									}
								}
								return uploadPromise;
							
							})
						});
					}
				}
			}
    	});
//        return uploadPromise;
    },
    getBlob: function (file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsBinaryString(file);
            reader.onload = () =>
                resolve(reader.result);
            reader.onerror = error =>
                reject(error);
        });
    },
    getBase64: function (file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () =>
                resolve(reader.result);
            reader.onerror = error =>
                reject(error);
        })
            ;
    },
    saveFile: function (){
    
	    var formData = new FormData(),
	        pages = {},
	        page_width = $(".textLayer").eq(0).width(),
	        page_height = $(".textLayer").eq(0).height();

	    $(".page:not(.deleted)").each(function (i, el) {
	        var v = $(this).data("page-number");
	        pages[i] = v; // ($(this).data("page-number"));
	    })
	    var fonts = {};

	    for(const name in spe.document_fonts){
	        if(spe.document_fonts.hasOwnProperty(name)){
	            fonts[name] = spe.document_fonts[name].base64;
	        }
	    }
	    //spe.history
	    var history  = $.extend(spe.history, {})
		$.each(history, function(type, items){
			$.each(items, function(id, item){
				delete history[type][id]['img_base64'];
				delete history[type][id]['clone'];
				if(typeof history[type][id]["action"]!=='undefined' && history[type][id]["action"]=='deleted'){
					delete history[type][id];
				}
			});
		});
		//собираем существующие элементы формы
		if($(".form_element").length>0){
			$(".form_element").each(function(){
				var element = $(this).find("input, textarea, select"),
					type = "",
					id  = spe.uniq(),
					options = "",
					selected= "",
					value = "",
					page = element.closest(".page").data("page-number");
				if(element.is("input")){
					if(element.is(":checkbox")){
						type = "checkbox";
						if(element.is(":checked")){
							value = "checked='checked'";
						}
					}else{
						type =  "input";
						var val = element.val().replace(/"/g, '&quot;');
						value = `value="${val}"`;
					}
				}else if(element.is("textarea")){
					type = "textarea"
					value = $(this).val();

				}else if(element.is("select")){
					type = "select";
					options = "";
					element.find("option").each(function(i, opt){
						if($(opt).is(":selected")){
							selected = $(opt).html();
						}
						options +="\r\n"+$(opt).html(); 
					});
				}

				var  form_element = {
					element_content: type,
					element_id: id,
					field_params: {
						field_name: id,
						field_type: type,
						field_options: options,
						selected: selected,
						value: value
					},
					is_new: 0,
					page_num: page,
					size: element.getElementOffset(false, true),
					time: "",
					ts: "",
					type: "forms"
				};
				if(typeof history['forms']=='undefined'){
					history['forms'] = {};
				}
				history['forms'][id] = form_element;
				
			});
		}
		
		try{
			//var compresed_data = LZString.compressToBase64(JSON.stringify(spe.default_text_elements));
			
			var pages_sizes = {}; 
			$("#viewer .page").each(function(i,v){
				pages_sizes["page_"+i] = {w: px2mm($(this).width()), h: px2mm($(this).height()) } ;
			});
			
			var is_blank = 0;
			if(typeof spe.start_blank!='undefined' && spe.start_blank){
				is_blank = 1;
			}
			
			
			
			
			return $.ajax({
			    url: `/pdf/createPdf`,
			    type: "POST",
			    headers: {
			        'X-CSRF-TOKEN': $("#editor_csrf").val()
			    },
			    dataType: "json",
			    data: {
			    	is_blank: is_blank,
					operation_id: pdfUploader.operation_id,
			        UUID: UUID,
			        pages_sizes: pages_sizes,
			        format: [px2mm(page_width), px2mm(page_height)],
			        changes: history,
			        pages: pages,
			        deleted_pages: spe.deleted_pages,
			        //default_text_elements_compresed: compresed_data,
			        //default_text_elements: spe.default_text_elements,
			        deleted: spe.deleted_text,
			        fonts:fonts,
			        start_blank: spe.start_blank
			    },
			    success: function (data) {
			    
			        if(data.success==false){
			        	swal("Error", data.message, "error");
			        }else{
$("#apply-popup .modal-header").removeClass("hidden");			        
					    $(".apply_changes_1").html("Apply changes");
					    $(document).trigger("after_save_file", [data]);
			        }
			    },
			    error: function (error) {
			    	try{
			    		var xx = JSON.parse(error.responseText);
				        console.log(xx);
				        alert("Error: "+xx.message+"::"+xx.file+"::"+xx.line);
			    	}catch(e){
			    		alert(error.message);
			    		console.log(data);
			    	}
			        $(".apply_changes_1").html("Apply changes");
			    }
			});	
		}catch(e){
			console.log(e);
			alert(e.message);
		}
    },
};

//TODO uncomment

pdfUploader.init($(".user_pdf"));

var shareForm = {
	share_form: $(".share_form"),
	download_result: $(".download-result"),
	init: function(){
	
		this.bind();
	},
	bind: function(){
		$(document).on("click", "#show_share_form", (e)=>{this.showShareForm(e)});
		$(document).on("click", ".share_form .like_a_tab", (e)=>{ this.showTab(e) });
		$(document).on("click", "#hide_share_form", (e)=>{
			e.preventDefault();
			this.share_form.hide();
			this.download_result.show();
			
		});
		if(this.share_form.length>0){
			this.share_form.on("submit", this.submitShareForm);
		}
		$(document).on("click", "#send_to_another", function(e){
			$("#send_mail_r_form .after_send").hide();
			$("#send_mail_r_form .before_send").show();
		});
		
		$(document).on("click", "#create_share_link", function(e){
			e.preventDefault();
			$.ajax({
				url: `/pdf/createShareLink`,
				type: "POST",
				headers: {
					'X-CSRF-TOKEN': $("#editor_csrf").val()
				},
				dataType: "json",
				data: "&document_title="+encodeURIComponent($(".download-result-link").attr("download"))+"&file_url="+encodeURIComponent($(".download-result-link").attr("href")),
				success: function(data){
					$("#textarea_with_link").show();
					$("#textarea_with_link textarea").val(data.share_url);
					console.log(data);

				},
				error: function(error){
					console.log(error.responseText);
					alert("Error: " + error.message);
					$("#send_mail_r_form .loading_form").remove();
				}
			});
		
		
		});
		
	},
	showShareForm: function(e){
		e.preventDefault();
		$(".like_a_tab", this.share_form).eq(0).show();
		this.share_form.show();
		this.download_result.hide();
	},
	showTab: function(e){
		$(".like_a_tab", this.share_form).removeClass("active");
		$(e.target).addClass("active");
		let tab = $(e.target).data("tab");
		$(".tab_content", this.share_form).hide(); 
		$(`#${tab}_tab`).show();
	},
	submitShareForm: function(e){
		console.log("SUBMIT SHARE FORM", shareForm);
	
		$("#send_mail_r_form").append(`<div class='loading_form'><i class="fa fa-spinner fa-spin" style="font-size:24px"></i></div>`);
		e.preventDefault();
		let $this = shareForm,
			form = $(e.target);
		$.ajax({
			url: `/pdf/sendByEmail`,
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $("#editor_csrf").val()
			},
			dataType: "json",
			data: form.serialize()+"&document_title="+encodeURIComponent($(".download-result-link").attr("download"))+"&file_url="
				+encodeURIComponent($(".download-result-link").attr("href")),
			success: function(data){
				$("#send_mail_r_form .loading_form").remove();
				$("#send_mail_r_form .after_send").show();
				$("#send_mail_r_form .before_send").hide();
			},
			error: function(error){
				console.log(error.responseText);
				alert("Error: " + error.message);
				$("#send_mail_r_form .loading_form").remove();
			}
		});
	}
};
shareForm.init();


var spe = {
	upload_in_progress: false,
	is_asia: false,
	start_blank: 0,
    filename: false,
    size: false,
    fileData: false,
    pdfLinkService: false,
    pdfFindController: false,
    pdfViewer: false,
    loadingTask: false,
    pdfDocument: false,
    container: false,
    current_element_id: false,
    current_editor: false,
    current_form_element: false,
    first_click_after_editing: false,
    current_z_index: 500,
    new_pages: 0,
    deleted_pages: [],
    added_pages: {},
	file_num: 0,
    form_elements_count: 0,
    editors: {
        "text": $(".text-editable-menu"),
        "images": $(".image-editable-menu"),
        "link": $(".link-editable-menu"),
        "links": $(".link-editable-menu"),
        "whiteout": $(".whiteout-editable-menu"),
        "shape": $(".shape-editable-menu"),
        "rectangle": $(".whiteout-editable-menu"),
        "elipse": $(".whiteout-editable-menu"),
        "forms": $(".forms-editable-menu"),
        "annotate": $(".annotate-editable-menu")
    },
    document_broken_fonts: {},
    document_fonts: {},
    history: {},
    deleted_text: {},
    history_redo: {},
    default_text_elements: {},
    default_params: {
        container_selector: false,
        url: false,
        data: false,
    },
    taskParams: {},
    init: function (params) {
    	if(editor_is_init){
    		return
    	}
    	editor_is_init = true;
    

		$(window).bind("beforeunload",function(event) {
			return "Changes you made may not be saved.";
		});
		$(document).unbind("dragover drop");

        var that = this;
        that.default_params = $.extend(that.default_params, params);

        that.container = document.getElementById(that.default_params.container_selector);
        that.pdfLinkService = new pdfjsViewer.PDFLinkService();
        that.pdfFindController = new pdfjsViewer.PDFFindController({linkService: that.pdfLinkService});

        that.pdfViewer = new pdfjsViewer.PDFViewer({
            container: that.container,
            linkService: that.pdfLinkService,
            findController: that.pdfFindController,
        });
        
        //console.log("before bind", that);
        
		try{
			//console.log("set viewer");
	        that.pdfLinkService.setViewer(that.pdfViewer);
		}catch(e){
			console.log(e);
		}
        that.bind();
        $(document).trigger("spe_init", [true]);
        
    },
    dissableAllSelection(){
    	$(".element_selected_now").removeClass("element_selected_now");
    },
    hideAllEditors: function(){
		$.each(spe.editors, function (i, v) {
			$(v).hide();
		});    
    },
    hideAllDropdown: function(){
    	$(".tools-dropdown-menu").removeClass("active");
    },
    DefaultTextElemetns: function () {
        $(".textLayer .text_content_element:not(.edited)").each(function () {
        	var el_content = $(this).html();
			if($(this).find(".highlight, .strike").length>0){
				let cloned_el = $(this).clone();
				$(".highlight, .strike", cloned_el).replaceWith($(".highlight, .strike", cloned_el).html());
				el_content = $(cloned_el).html();
			}
			
//			if($(this).find(".highlight").length>0){
//				let cloned_el = $(this).clone();
//				$(".highlight", cloned_el).replaceWith($(".highlight", cloned_el).html());
//				el_content = $(cloned_el).html();
//			}
			
        	
        	let elem = $(this).clone();
            let params = {
                page_num: $(this).closest(".page").data("page-number"),
                element: $(this),
                is_new: 0,
                element_id: spe.uniq(),
                element_content: el_content
            };
            
            
			var font_object = spe.document_fonts[params.element.attr("original-font-name")];
			
			//font-style
			
            let adds = {
                css: {
                	//weight
                    "font-style":"normal",  
                    
                     // typeof font_object!== 'undefined' && font_object.bold?"bold":"normal",
                    	//params.element.css("font-weight"),
                    "translate": 0,
                    "font-size": px2mm(parseInt(params.element.css("font-size"))) + "mm",
                    "test": params.element.css("font-size") + "px",
                    "font-family": params.element.css("font-family").toLowerCase().replace(" ", ""),
                    "transform": params.element.get(0).style.transform,
                    "letter-spacing": params.element.get(0).style["letter-spacing"], //TODO 
                    "font-weight": "normal", //$(this).hasClass("broken_cff_font") && typeof font_object!== 'undefined' && font_object.bold?"bold":"normal",
                    
                    //"normal", // spe.document_fonts[params.element.attr("original-font-name")].bold?"bold":"normal",
                    "color": params.element.attr("original-color"),//params.element.css("color"),
                    "original-height": 0,
                    "original-width": 0,
                }
            };

            /*if (typeof spe.document_fonts[adds.css['font-family']] != 'undefined') {
                adds.css['font-family'] = spe.document_fonts[adds.css['font-family']].nice_name;
            }*/

            try {
                params.size = params.element.getElementOffset(false, true);
            } catch (err) {
                console.error("Cant add to default text elements: " + err.message);
            }

            params = $.extend(params, adds);

            delete params['element'];
            spe.default_text_elements[params.element_id] = params;

        });
    },
    bind: function () {
        var that = this;
        document.addEventListener('pagesinit', function () {
        	if(typeof PDFTOOLS.override_scale!='undefined'){
        		that.pdfViewer.currentScaleValue = PDFTOOLS.override_scale;
        	}else{
        		if($(window).width()<768){
        			that.pdfViewer.currentScaleValue = 1;
        		}else{
            		that.pdfViewer.currentScaleValue = "fw"; //'page-width'; //TODO скейл тут
            	}
            }
            if (SEARCH_FOR) {
                that.pdfFindController.executeCommand('find', {query: SEARCH_FOR,});
            }
        });
        spe.show_transparent_images = true;
        
        //TODO загрузка файлв
        if (that.default_params.data) {
            that.loadingTask = pdfjsLib.getDocument({
                data: that.default_params.data,
                password: PDF_PASSWORD,
                //url: DEFAULT_URL,
                cMapUrl: CMAP_URL,
                cMapPacked: CMAP_PACKED,
            });
        } else {
            //загузка по урлу
            that.loadingTask = pdfjsLib.getDocument({ //TODO перенести в опции
                url: that.default_params.external_url,
                cMapUrl: CMAP_URL,
                cMapPacked: CMAP_PACKED,
            });
        }
        
//        console.log("time to bind");
//        
//        console.log("loading task is ", that.loadingTask);
//        console.log("and that is ", that);

        
        that.loadingTask.promise.then(that.loadingPromise);
		
		document.addEventListener("documentload", function(){
			blocker.hide();		
		})
		
		document.addEventListener("textlayerrendered", function(tl){
			blocker.hide();
//			$(".text_content_element:not(.fsfs)", parent).each(function(){
//				var el = $(this);
//				if(!main_element){
//				    main_element = el;
//				}
//				main_element.addClass("fsfs");
//				if(main_element.hasClass("broken_cff_font")){
//					var current_font_family = main_element.css("font-family").replace("-fixed", "");
//					main_element.css("font-family", current_font_family+"-fixed");
//					main_element.css("transform", "none");
//				}
//			});
			
			//TODO uncomment
			if(PDFTOOLS.name==false){
				
				$(".text_content_element", $(tl.target)).each(function(i, text_el){
					//console.log(text_el);
					if($(text_el).css("font-family")=='Helvetica'){
						//TODO font
						$(text_el).css("font-family", "Helvetica");
						var temp_top = parseInt($(text_el).css("top"));
						//$(text_el).css("top", (temp_top)+"px");
					}
				});
			
				mergeLetters(tl.target, true);
			}else{
				 $("#simplePDFEditor").addClass("proccessed");
			}
		});

        
        
        //TODO создаем клоны элементов ||| перенести в текст эдитор, надо разгрузить spe. оставить только ебаклик
        $(document).on("click", "[current_editor='text'] .page .textLayer div", function (e) {
            if (e.target.classList.contains("text_content_element")) {
                $.proxy(that.createTextClone($(e.target), true));
            } else {
                $.proxy(that.createTextClone($(e.target.parentNode), true));
            }
        });
        $(document).on("ebaclick", `[current_editor='text'] .page .textLayer, [current_editor='whiteout'] .page .textLayer, [current_editor='links'] .page .textLayer, [current_editor='rectangle'] .page .textLayer, [current_editor='elipse'] .page .textLayer`, that.appendNewEdit); //.children().click(function(e) { return false; });

        $(document).on("click", "#simplePDFEditor .page .editable_element", that.startEditText);
        $(document).on("keyup", "#simplePDFEditor .page .editable_element", function (e) {
            var that = spe, clone = $(this), uniq = clone.attr("element-id");
            var font_family = $(this).css("font-family");
            
            //TODO history text
            spe.toHistory({
                element_id: uniq,
                type: "text",
                element_content: clone.html(),
                element: $(`div[element-id='${uniq}'].editable_element`)
            });

            if(spe.document_fonts.hasOwnProperty(font_family)){
				var font_w = spe.document_fonts[font_family].chars_widths[e.keyCode];
            	if(font_w===0 && !$(this)[0].hasAttribute("dont-show-glyph-missing")){
            		if(confirm("Font family missing chars. Use default font?")){
            			$(this).css("font-family", "sans-serif");
            		}
            		$(this).attr("dont-show-glyph-missing", 1);
            	}
            }

            
            
            //spe.toHistory(uniq, "text", clone.html(), "pagehere");
        });

        $(document).on("click", ".app-tools .tools-menu .tools-menu-item, .app-tools .tools-menu .sub_menu_item", this.changeEditor);

        $(document).on("click", ".change_form_item", this.changeFormEditor);
        $(document).on("click", ".open_undo_modal", that.fillHistory);
        $(document).on("click", ".open_redo_modal", that.fillRedoHistory);
        $(document).on("click", "#time_to_revert", that.revertHistory);
        $(document).on("click", "#time_to_redo", that.redoHistory);
        
        
        
        $(document).on("click", "#start_find_text", (e) => {
            that.searchAndReplace(0, $("#find_text_input").val(), false, true, true);
            return false;
        })
        ;
        $(document).on("click", "#start_replace", (e) => {
        	
            that.searchAndReplace(1, $("#find_text_input").val(), $("#replace_text_input").val(), false);
            return false;
        })
        ;
        $(document).on("click", "#start_replace_and_find_text", (e) => {
            that.searchAndReplace(2, $("#find_text_input").val(), $("#replace_text_input").val(), true);
            return false;
        })
        ;
        $(document).on("click", ".delete-page", this.deletePage);
        $(document).on("click", ".insert-page", function (e) {
        	spe.hideAllEditors();
            that.createPage(e, false)
        });

        $(document).on("click", "ul .change_text_font", function (e) {
            var ff = $(this).data("font-name");
            $(".selected_now").css("font-family", ff);
            var uniq = $(this).attr("element-id");
            spe.toHistory({
                element_id: textEditor.element_id,
                type: "text",
                element_content: $(`.editable_element[element-id='${textEditor.element_id}']`).html(),
                element: $(`div[element-id='${textEditor.element_id}'].editable_element`)
            });

            return false;
        });

        /*
        $(document).on("click", ".apply-btn", function(e){
        	e.preventDefault();
            spe.default_text_elements = {};
            //spe.DefaultTextElemetns();


			$("#apply-popup").addClass("active");
			$(".creating_document").show();
			$(".create_file_box").hide();
			$(".apply_changes_1").html("Wait...");


			var intervalID = setInterval( function() { 
				console.log("upload progress is "+spe.upload_in_progress);
				if(!spe.upload_in_progress){
					clearInterval(intervalID);

					$.when(pdfUploader.saveFile()).then(function (data) {
						console.log(data);
						if(data.success==false){
							$("#apply-popup").removeClass("active")
							swal("Error", data.message, "error");
						}else{
							$(".creating_document").hide();
							$(".create_file_box").show();
							$(".result-top-line .download_file_name").html(spe.filename?spe.filename:"edited_blank.pdf");
							$(".download-result-link").attr({"href": data.url, "download": "edited_" + (spe.filename?spe.filename:"blank.pdf")});
							$("#save-dropbox").attr({'data-url': data.url, 'data-file_name': "edited_" + (spe.filename?spe.filename:"blank.pdf")});
							$("#save-gdrive").attr({'data-src': data.url, 'data-filename': "edited_" + (spe.filename?spe.filename:"blank.pdf")});
					    }
					});
				}
			} , 250);


            return false;
        });
        */
        


        /*$(document).on("click", ".delete-image", function(e){
        	var image_id = $(this).closest("li").data("image-id");
        	$(this).closest("li").remove();
        	e.preventDefault();
			$.ajax({
				url: `/pdf/deleteImage`,
				type: "POST",
				headers: {
					'X-CSRF-TOKEN': $("#editor_csrf").val()
				},
				dataType: "json",
				data: {
					UUID: UUID,
					image_id: image_id
				},
				success: function (data) {
					if(data.success){
						
					}else{
						swal("Error", data.message, "error");
					}
				},
				error: function (error) {
					console.log(error.responseText);
					
					swal("Error", "see browser console", "error");
					//alert("Error: " + error.message);
					
					
				}
			});
        
        });
        */


        //	onclick=""
		if($(".tools-menu").length>0){
		    textEditor.init();
		    imagesEditor.init();
		    linksEditor.init();
		    whiteoutEditor.init();
		    formsEditor.init();
		    annotateEditor.init();
        }
        
        
        console.log("after all binds");
        
    },
    createPage: function (e, empty_doc) {
        e.preventDefault();
        spe.new_pages++;
        var first_page = $("#viewer .page").eq(0),
            pwh = {width: first_page.width(), height: first_page.height()},
            page_div = $("<div>", {'class': 'page'}).attr({"data-page-number": `new_page${spe.new_pages}`}).css($.extend(pwh, {"position": "relative"})),
            canvas = $('<canvas/>', {'class': 'pg_canvas'}).attr(pwh),
            canvas_wrapper = $("<div/>", {"class": "canvasWrapper"}).append(canvas),
            text_layer = $("<div/>", {"class": "textLayer"}).css($.extend(pwh, {"position": "absolute"})),
            delete_template = $(`<div class="page-side-bar"><div data-page-id="new_page${spe.new_pages}" class="page-tools-menu"><a href="#" data-page-id="new_page${spe.new_pages}" class="delete-page"><img src="/img/icon-red-basked.svg" alt=""></a></div></div>`),
            insert_template = $(`<div class="page-between"><a href="#" class="create_new_page insert-page">Insert Page Here</a></div>`);
        
        text_layer.on("click", function (evt) {
            $(this).trigger("ebaclick", [evt, false]);
        })

        page_div.append(canvas_wrapper);
        page_div.append(text_layer);


       	page_div.append(delete_template);

        if ($(e.target).hasClass("insert_first_page")) {
            $("#viewer").prepend(page_div);
        } else {
            if ($(e.target)) {
                page_div.insertAfter($(e.target).closest(".page-between"));
            }
        }
        insert_template.insertAfter(page_div);
        
        spe.toHistory({
            element_id: `data-page-number=${spe.new_pages}`,
            type: "add-page",
            element_content: "Add page",
            element: $(`div[data-page-number=${spe.new_pages}]`).eq(0)
        });
        return false;
    },
    deletePage: function () {
        var deleter = $(this),
            pagenum = deleter.data("page-id"),
            page = $(`.page[data-page-number='${pagenum}']`),
            inserter = page.next(".page-between");

        page.addClass("deleted").hide();
        inserter.hide();
        deleter.hide();
        spe.deleted_pages[pagenum] = pagenum;

        spe.toHistory({
            element_id: `data-page-number=${pagenum}`,
            type: "delete-page",
            element_content: "Delete page",
            element: $(`div[data-page-number=${pagenum}]`).eq(0)
        });
        return false;
    },
    current_founded: 0,
    old_search_text: "",
    searchAndReplace(typ=0, text, need_replace, go_to_next, search_next=false) {

        var total_count = 0,
            founded_elements = [],
            matchCase = $('#find_match_case').get(0).checked;
            
        
        if (spe.old_search_text != text) {
            spe.current_founded =0;
        }else if($(".finded_element_hightlighter").length>0 && spe.old_search_text == text && search_next){
            spe.current_founded++;
        }else{
           // spe.current_founded = 0;
        }



        $(".finded_element").removeClass("finded_element");
        $(".finded_element_hightlighter").each(function () {
            this.outerHTML = this.innerHTML;
        });

        spe.old_search_text = text;
        $(".text_content_element:not(.edited), .text_content_element.editable_element").each(function (i) {
            var temp_el = $(this);
            var content = temp_el.html(); //.replace(/<[^>]+>/ig, "");
            var pos = content.search(new RegExp(`${text}`, matchCase ? "" : "i"));
            if (pos !== -1) {
                var id = `${Date.now().toString(32)}_search_${i}`;
                content = content.slice(0,pos) + `<span id="${id}" class="finded_element_hightlighter">${content.slice(pos,pos+text.length)}</span>` + content.slice(pos+text.length);
                temp_el.html(content);
                founded_elements.push($("#"+id));
                total_count++;
            }
        });
        
        //replace
        //alert(spe.current_founded);
        //if (spe.current_founded > founded_elements.length - 1) {
        //    spe.current_founded = 0;
        //}
        if(typeof founded_elements[0]==='undefined'){
            Swal("Error", `"<b>${text}</b>" not found`, "error");
            return;
        }

        var need_scrool = true;
        if(typ==0){
            //replace
        }else if(typ===1){
            need_scrool = false;
            //replace only
            var par =founded_elements[spe.current_founded].closest(".text_content_element");
			spe.hideElement(founded_elements[spe.current_founded].closest(".text_content_element"));
        	founded_elements[spe.current_founded].html(need_replace);
        	var offset =  founded_elements[spe.current_founded].offset();
        	var replaced_element = spe.createTextClone(par, false);
            par.css({"width": "auto", "overflow": "visible"});
            spe.current_founded = spe.current_founded;


            setTimeout(function(){
            	spe.current_editor = "text";
            	var current_element = replaced_element


				spe.toHistory({
					element_id: current_element.attr("element-id"),
					type: "text",
					element_content: current_element.html(),
					element: $(`div[element-id='`+current_element.attr("element-id")+`']`)
				});
            }, 500);


        }else if(typ===2){
            
            
            var par =founded_elements[spe.current_founded].closest(".text_content_element");
			spe.hideElement(founded_elements[spe.current_founded].closest(".text_content_element"));
        	founded_elements[spe.current_founded].html(need_replace);
        	var offset =  founded_elements[spe.current_founded].offset();
        	var replaced_element = spe.createTextClone(par, false);
            par.css({"width": "auto", "overflow": "visible"});
            spe.searchAndReplace(typ=0, text, false, false, false);

            setTimeout(function(){
            	spe.current_editor = "text";
            	var current_element = replaced_element


				spe.toHistory({
					element_id: current_element.attr("element-id"),
					type: "text",
					element_content: current_element.html(),
					element: $(`div[element-id='`+current_element.attr("element-id")+`']`)
				});
            }, 500);


            return;
            //replace & find next
        }

        $(".found_matches").html(total_count);
		
		console.log(typ);
		console.log(spe.current_founded);        
        console.log(founded_elements);
        
        
        founded_elements[spe.current_founded].addClass("finded_element");
        var offset =  founded_elements[spe.current_founded].offset();
        if(search_next){
           // spe.current_founded++;
        }
        var modal = $("#find-replace-modal");

        modal.css('left',"initial");

        if(offset.left > modal.offset().left - 50){
            modal.css('left',modal.css('right'));
        }
        if(need_scrool){
            $([document.documentElement, document.body]).stop().animate({
                scrollTop: offset.top - 120
            }, 1000);
        }

        
       /*  if (false && go_to_next) {
            $("#start_find_text").click();
        } else {
        } */
    },
    getZIndex: function () {
        spe.current_z_index++;
        return spe.current_z_index;
    },

    removeFromHistoryByID(id) {
        $.each(spe.history, function (i, group) {
            if (typeof group[id] !== 'undefined') {
                delete spe.history[i][id];
            }
        });
    },
    
    redoHistory: function(e){
    	$.each(spe.history_redo, function(type, items){
    		$.each(items, function(id, element){
    			if($(`.revert_item[value='${id}']:checked`).length>0){
					$(`.revert_item[value='${id}']:checked`).closest("tr").remove();
					if(type=='images'){
						element.clone.find("img").attr("src", element.img_base64);
					}
					if($(`div[original-element-id='${id}']`).length>0){
						$(`div[original-element-id='${id}']`).addClass("skipp-pointer-events edited");
					}
					if(type=='annotate'){
						spe.history_redo[type][id]['annotate_parent'].html(spe.history_redo[type][id]['annotate_html']); 
					}else{
						$(`.page[data-page-number='${element.page_num}']`).append(element.clone);
					}
					
					delete element['annotate_parent'];
					spe.history[type][id] = element
					delete spe.history_redo[type][id];
    			}
    		});
    	});
    	return false;
    },
    revertHistory: function (e) {
        var revert_ids = [];
        $(".revert_item:checked").each(function () {
            var type = $(this).attr("element-type"),
                el_id = $(this).val();
                
            if(!spe.history_redo.hasOwnProperty(type)){
            	spe.history_redo[type] = {};
            }
            
            if (typeof spe.history[type][el_id] != 'undefined') {
				var date = new Date(),
				    time = date.getHours() + ":" + (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes()) + ":" + (date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds());

            	spe.history[type][el_id]['time'] = time;
            	spe.history_redo[type][el_id] = spe.history[type][el_id];
                delete spe.history[type][el_id];
            }
            
            if (type == 'delete-page') {
                $("div[" + $(this).attr("value") + "]").removeClass("deleted").show();
                $("div[" + $(this).attr("value") + "]").find(".delete-page").show();
                $("div[" + $(this).attr("value") + "]").next().show();
                $(".delete-page[" + $(this).attr("value") + "]").show();
            } else if(type=='annotate'){
				$(`.new_annotate[element-id='`+$(this).attr("value")+`']`).each(function () {
					var $this = $(this);
					var parent_el = $this.closest(".text_content_element");
					spe.history_redo[type][el_id]['annotate_parent'] = parent_el; //.clone();
					spe.history_redo[type][el_id]['annotate_html'] = parent_el.html(); 
					$this.replaceWith($this.html());
				});
            
            
            } else {
                $(`#simplePDFEditor div[element-id='${el_id}']`).remove();
                $(`[original-element-id='${el_id}']`).removeClass("skipp-pointer-events edited");
                $(`[original-element-id='${el_id}']`).css("color", "");
                $(`[original-element-id='${el_id}']`).css("background", "none");

				$.each(spe.deleted_text, function(page_num, page_elements){
					console.log("sow", page_elements[el_id]);
					if(typeof page_elements[el_id]!='undefined'){
						delete spe.deleted_text[page_num][el_id];
					}

				});
            }
            
            
            $(this).closest("tr").remove();
        });
        if ($(".revert_item").length == 0) {
            $("#undo-modal .alert-info").show();
        }

        //$.fancybox.close($('#undo-modal'));
        return false;
    },
    fillHistory: function (e) {
        var modal = $('#undo-modal'),
            tbody = modal.find("tbody"),
            example_tr = tbody.find(".example_tr").clone(),
            temp_array = [];
        tbody.find(".htr1").remove();
        $.each(spe.history, function (element_type, items) {
            $.each(items, function (element_id, v) {
                if (typeof v.title !== 'undefined') {
                    content = v.title;
                } else if (typeof v.element_content != 'undefined') {
                    var content = (v.element_content.length > 70) ? v.element_content.substring(0, 70) + "..." : v.element_content;
                } else {
                    content = "Unknown element...";
                }
                var tt = {
                    "element_id": v.element_id,
                    "element_type": element_type,
                    "action": content,
                    "time": v.time,
                    "page": "Page 1",
                    "ts": v.ts
                };
                temp_array.push(tt);
            });
        });

        function compare(a, b) {
            if (a.ts < b.ts) {
                return 1;
            }
            if (a.ts > b.ts) {
                return -1;
            }
            return 0;
        }

        temp_array.sort(compare);
        example_tr = tbody.find(".example_tr").clone();
        if (temp_array.length === 0) {
            modal.find(".alert-info").show();
        } else {
            modal.find(".alert-info").hide();
        }
        $.each(temp_array, function (i, item) {
            var tr_html = example_tr.html();
            //example_tr.removeClass("example_tr hidden");
            $.each(item, function (key, v) {
                tr_html = tr_html.replace(`%${key}%`, v);
            });
            tbody.append("<tr class='htr1'>" + tr_html + "</tr>");
        });
        $.fancybox.open(modal);
        return false;
    },
    //TODO redo
    fillRedoHistory: function (e) {
        var modal = $('#redo-modal'),
            tbody = modal.find("tbody"),
            example_tr = tbody.find(".example_tr").clone(),
            temp_array = [];
        tbody.find(".htr1").remove();
        $.each(spe.history_redo, function (element_type, items) {
            $.each(items, function (element_id, v) {
                if (typeof v.title !== 'undefined') {
                    content = v.title;
                } else if (typeof v.element_content != 'undefined') {
                    var content = (v.element_content.length > 70) ? v.element_content.substring(0, 70) + "..." : v.element_content;
                } else {
                    content = "Unknown element...";
                }
                var tt = {
                    "element_id": v.element_id,
                    "element_type": element_type,
                    "action": content,
                    "time": v.time,
                    "page": "Page 1",
                    "ts": v.ts
                };
                temp_array.push(tt);
            });
            
            
            
        });

        function compare(a, b) {
            if (a.ts < b.ts) {
                return 1;
            }
            if (a.ts > b.ts) {
                return -1;
            }
            return 0;
        }

        temp_array.sort(compare);
        example_tr = tbody.find(".example_tr").clone();
        if (temp_array.length === 0) {
            modal.find(".alert-info").show();
        } else {
            modal.find(".alert-info").hide();
        }
        $.each(temp_array, function (i, item) {
            var tr_html = example_tr.html();
            //example_tr.removeClass("example_tr hidden");
            $.each(item, function (key, v) {
                tr_html = tr_html.replace(`%${key}%`, v);
            });
            tbody.append("<tr class='htr1'>" + tr_html + "</tr>");
        });
        $.fancybox.open(modal);
        return false;
    },
    toHistory: function (params, return_it) {
        if (typeof params == 'undefined') {
            return false;
        }
        params.page_num = params.element.closest(".page").data("page-number");
        var date = new Date(),
            time = date.getHours() + ":" + (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes()) + ":" + (date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds());
        if (typeof spe.history[params.type] == 'undefined') {
            spe.history[params.type] = {};
        }
        var adds = {};
        params.is_new = parseInt(params.element.attr("is_new"));
        switch (params.type) {
            case "whiteout":
            case "elipse":
            case "rectangle":
            
            	console.log("wowowow", params.element);
            
                adds = {css: $.extend(whiteoutEditor.getCurrentBorder(params.element.find(".inner_element")), {"background-color": params.element.find(".inner_element").css("background-color")},{"border-w": px2mm(parseInt(params.element.find(".inner_element").css("border-left-width")))})};
                break;
            case 'text':
            	//top
                adds = {
                    css: {
                        "font-style": params.element.css("font-style"),
                        "font-weight": params.element.css("font-weight"),
                        "font-size": px2mm(parseInt(params.element.css("font-size"))) + "mm",
                        "test": params.element.css("font-size") + "px",
                        "font-family": (params.element.css("font-family")).toLowerCase().replace(" ", ""),
                        "transform": params.element.get(0).style.transform,
                        "letter-spacing": params.element.get(0).style["letter-spacing"],
                        "color": params.element.css("color"),
                        "original-font-size": params.element.css("font-size"),
                        "original-height": 0,
                        "original-width": 0,
                        "bs": parseFloat(params.element.css("line-height"))-parseFloat(params.element.css("font-size")),
                    },
                }
                
                
                if (!params.is_new) {
                    if ($("div[original-element-id='" + params.element_id + "']").length > 0) {
                        adds['css']['original-width'] = $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['width'];
                        adds['css']['original-height'] = $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['height'];
                        adds['css']['original-left'] = $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['left'];
                        adds['css']['original-top'] = $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['top'];
                        //adds['css']['original-content'] = $("div[original-element-id='" + params.element_id + "']").html();
                        
                        var page_id = $("div[original-element-id='" + params.element_id + "']").closest(".page").attr("data-page-number");
                        if(typeof spe.deleted_text[page_id]=='undefined'){
                        	spe.deleted_text[page_id] = {};
                        }
                        
                   		spe.deleted_text[page_id][params.element_id] = { 
                   			top:  $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['top'],
                   			left: $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['left'],
                   			height: $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['height'],
                   			width: $("div[original-element-id='" + params.element_id + "']").getElementOffset(false, true)['width'],
                   			font_size: px2mm(parseFloat($("div[original-element-id='" + params.element_id + "']").css("font-size"))),
                   			content: $("div[original-element-id='" + params.element_id + "']").html(),
                   			page: $("div[original-element-id='" + params.element_id + "']").closest(".page").attr("data-page-number"),
                   			base_line: $("div[original-element-id='" + params.element_id + "']").baseline(),
                   			background_color: detectColorOnClick($("div[original-element-id='" + params.element_id + "']"),true)
                   		};
                    }
                }
                break;
            case 'forms': //TODO forms
                //alert("here "+params.element.attr("field_type"));
                var ft = params.element.find("img").attr("field_type");
                if(typeof ft =='undefined'){
                	ft = params.element.find(".new_form_element").attr("element-type");
                }
                
                var adds = {
                    field_params: {
                        "field_name": params.element.attr("field_name"),
                        "field_type": ft,
                        "field_options": params.element.attr("field_options"),
                    }
                };
                break;
            case 'link':
                var adds = {
                    link: {
                        link_type: params.element.attr("external") == 'true' ? "external" : "internal",
                        link: params.element.attr("url"),
                    }
                }
                break;
            case 'images':
            	let image_db_id = 0;
            	if(image_db_id = parseInt($("img", params.element).attr("db_id"))){
            		delete params.img_base64;
            		params.element_content = image_db_id;
            	}
            break;
                
            case 'annotate':
                var annotate_blocks = [],
		            annotate_element = $(`.new_annotate[element-id=${params.element_id}]`);
		            
                $.each(annotate_element, function (i, v) {
                    annotate_blocks.push($(v).getElementOffset(false, true));
                });
                var type = annotate_element.eq(0).hasClass("highlight") ? "Highlight" : "StrikeOut",
                	adds = {
                    annotate: {
                        blocks: annotate_blocks,
                        title: annotate_element.eq(0).attr("annotate_title"),
                        content: annotate_element.eq(0).attr("annotate_content"),
                        color: annotate_element.eq(0).hasClass(".strike")?"#ff0202": annotate_element.eq(0).css("background-color"),
                        type: type,
                    }
                }
                break;


            default:

            	break;
        }
        
        adds['clone'] = params.element;

        try {
            params.size = params.element.getElementOffset(false, true);
        } catch (err) {
        	console.log(params.element);
            console.error("Cant add to history: " + err.message);
        }

        params.time = time;
        params.ts = date.getTime();
        params = $.extend(params, adds);
        delete params['element'];
        
        if(typeof return_it != "undefined" && return_it){
        	return params;
        }
        
        spe.history[params.type][params.element_id] = params;
		return false;
    },
    changeFormEditor: function (e) {
    	e.preventDefault();
        var that = spe;
        that.current_form_element = $(this).attr("form_element_type");
        that.current_editor = "forms";

        $(".new_form_element.follow_the_mouse").remove();
        
        switch (that.current_form_element) {
            case 'textarea':
                that.appendDiv(e, "textarea");
                break;
            case 'input':
                that.appendDiv(e, "input");
                break;
            case 'dropdown':
                that.appendDiv(e, "dropdown");
                break;
            case 'select':
                that.appendDiv(e, "select");
                break;
            //TODO deprecated
            case 'radio':
            case 'checkbox':
                imagesEditor.imageUpload(e, $('img', `.change_form_item[form_element_type='${spe.current_form_element}']`), "form_element", that.current_form_element);
                break;
            default:
                imagesEditor.imageUpload(e, $('img', `.change_form_item[form_element_type='${spe.current_form_element}']`), "form_element");
                break;
        }
		spe.hideAllDropdown();
        return false;
    },
    changeEditor: function (e) {
        e.preventDefault();
        $(".follow_the_mouse").remove();
        $('#viewer .text_content_element.selected_now').removeClass('selected_now');
        $(".new_form_element.follow_the_mouse").remove();
        $(".active_image_moving").removeClass("active_image_moving")
        
        if($(e.target).hasClass("sub_menu_item")){
        	spe.hideAllDropdown();
        }
        spe.dissableAllSelection();

        var that = spe,
            editor = $(this).data("editor-name") === 'skip_it' ? that.current_editor : $(this).data("editor-name");
            
        // Выключить / Включить редактор
        if (false && editor === that.current_editor) {
            if ($(this).closest('ul').hasClass('shapes_dropdown')) {
                $(this).closest('ul').parent('li').removeClass('shapes_active');
            }
            that.current_editor = false;
            $('#simplePDFEditor').attr('current_editor', editor);
            $(this).parent('li').removeClass('active');
        } else {
            $('ul.tools-menu').find('li.shapes_active').removeClass('shapes_active');
            if (editor == 'rectangle') {
                $('ul.shapes_dropdown').find('li').removeClass('active');
                $('ul.shapes_dropdown').find('a[data-editor-name="rectangle"]').parent('li').addClass('active');
            }
            if ($(this).closest('ul').hasClass('shapes_dropdown')) {
                $(this).closest('ul').find('li').removeClass('active');
                $(this).parent('li').addClass('active');
                $(this).closest('ul').parent('li').addClass('shapes_active');
                $(this).closest('ul').parent('li').find('img').first().attr('src', $(this).next().attr('src'));
            }
            that.current_editor = editor;
            $(spe.container).attr("current_editor", editor);
        }
        spe.hideAllEditors();
        spe.first_click_after_editing = false;
    },
    appendDiv: function (e, type) {
        $(".page").addClass("active_image_moving");
        var template = $(`<div class='new_form_element follow_the_mouse ${type}' element-type='${type}'><div class='${type}'></div></div>`),
            css = {};
        switch (type) {
            case 'input':
                var css = {"width": "300px", "height": "30px"}
                break;
            case 'textarea':
                var css = {"width": "300px", "height": "100px"}
                break;
            case 'dropdown':
                var css = {"width": "300px", "height": "30px"}
                break;
            default:
                var css = {"width": "300px", "height": "300px"}
                break;
        }
        template.css(css);
        $("#simplePDFEditor").append(template);
    },
    //TODO добавляем новый редактор - текс/ссылка/прочий
    appendNewEdit: function (evt, e) {
        var page = $(e.target).closest(".page");
        $('#viewer .text_content_element.selected_now').removeClass('selected_now');

		spe.hideAllEditors();
        if (true){ //!spe.first_click_after_editing) {
        	
            switch (spe.current_editor) {
                case 'text':
                	if(spe.first_click_after_editing){
                		spe.first_click_after_editing = false;
                		spe.hideAllEditors();
                		return false;
                	}
                
                    var template = $(`<div is_new="1" element-id="${spe.uniq()}" contenteditable="true" class="text_content_element editable_element selected_now spe_element">Type text here</div>`);
                    var xpos = e.layerX === undefined ? e.offsetX : e.layerX;
                    var ypos = e.layerY === undefined ? e.offsetY : e.layerY;

                    template.css({
                        position: "absolute",
                        "font-size": "20px",
                        "top": ypos - 5,
                        "left": xpos - 50,
                    });
                    page.append(template);
                    setTimeout(() => {
							function selectText() {
								console.log(template);
								if (document.selection) { // IE
									var range = document.body.createTextRange();
									range.moveToElementText(template[0]);
									range.select();
								} else if (window.getSelection) {
									var range = document.createRange();
									range.selectNodeContents(template[0]);
									window.getSelection().removeAllRanges();
									window.getSelection().addRange(range);
								}
							}
                            template.click();
                            template.focus();
                            selectText();
                            template.draggable(dragable_params);
                   },200);
                   break;
                case 'forms':
                    //see append div
                    break;
                case 'links':
                    spe.drawRect("link_rectangle", "links", "new_anotation_link edited_link", page, e);
                    break;
                case 'whiteout':
                    spe.drawRect("whiteout_rectangle", "whiteouts", "new_whiteout", page, e);
                    break;
                case 'rectangle':
                	console.log("draw rect");
                    spe.drawRect("rectangle", "shape", "new_shape", page, e);
                    break;
                case 'elipse':
                    spe.drawRect("elipse", "shape", "new_shape", page, e);
                    break;
                case 'image':
                    break;
                default:
                    console.log("append default");
                    break;
            }
        } else {
            spe.first_click_after_editing = false;
            spe.hideAllEditors();
        }
    },
    drawRect: function (rectangle_type, element_type, classes, page, e) {
        if (true){ //!spe.first_click_after_editing) {
            var template_inner_css = {
                width: "100%",
                height: "100%",
                border: "3px solid white",
                "background-color": "rgba(0,0,0,0)"
            };
            var template = $(`<div rect_type='${rectangle_type}' class='${classes} drawing_rect'><div class='inner_element'></div></div>`),
                x1 = e.layerX, y1 = e.layerY, x2 = 0, x3 = 0, y2 = 0, y3 = 0, x4 = 0, y4 = 0;

            console.log("time to draw");
            $('#app-root').addClass('drawing');

            switch (rectangle_type) {
                case 'rectangle':
                    template_inner_css['border-color'] = "#fb6d6d";
                    break;
                case 'elipse':
                    template_inner_css['border-radius'] = "50%";
                    template_inner_css['border-color'] = "#fb6d6d";
                    break;
                default:

                    break;
            }
            //element_id
            var uniq_id = spe.uniq();
            template.attr("element-id", uniq_id).css({"z-index": spe.getZIndex()});

            page.addClass(`skipp-pointer-events-on-${element_type}`)
            page.append(template);

            switch (element_type) {
                case 'whiteouts':
                    template.css({border: "1px solid white", "background-color": "white"});
                    template.attr({border: "1px", "border-color": "white", "background-color": "white"});
                    template_inner_css['background-color'] = "rgba(255,255,255,1)";
                    break;
                case 'links':
                    delete template_inner_css['border'];
                    break;
                default:
                    template.css("background-color", "rgba(0,0,0,0)");
                    break;
            }

            $(".inner_element", template).css(template_inner_css);
            if(element_type=='whiteout'){
            	
            }
            

            function reCalc() {
                var x3 = Math.min(x1, x2);
                var x4 = Math.max(x1, x2);
                var y3 = Math.min(y1, y2);
                var y4 = Math.max(y1, y2);
                template.css({"left": x3 + 'px', "top": y3 + 'px'});
                var width = (x4 - x3);
                var height = (y4 - y3);
                template.css({"width": width + 'px', "height": height + 'px'});
            }
            function mouseDown(e2) {
                x1, x2 = e2.offsetX;
                y1, y2 = e2.offsetY;
                reCalc();
            }
            function mouseMove(ev2) {
                let parentOffset = $(this).offset();
                x2 = ev2.pageX - parentOffset.left;
                y2 = ev2.pageY - parentOffset.top;
                reCalc();
            }

            function mouseUp() {
            	console.log("mouse upppp");
            	console.log(page);
            
                $('#app-root').removeClass('drawing');
                var cloned = template.clone();
                spe.dissableAllSelection();
                cloned.addClass("element_selected_now").removeClass("drawing_rect");;
                
                cloned.draggable({
                    scroll: false,
                    start: window[`${spe.current_editor}Editor`].startDrag,
                    stop: window[`${spe.current_editor}Editor`].stopDrag
                }); //TODO инициализация двигания ссылок
                cloned.resizable(linksEditor.getResizableParams()); //TODO инициализация двигания ссылок
                page.append(cloned)
                page.removeClass(`skipp-pointer-events-on-${element_type}`);
                template.remove();
                page.off("mousedown", mouseDown).off("mousemove", mouseMove).off("mouseup", mouseUp);
				if(parseInt(cloned.css("width"))<10 || parseInt(cloned.css("height")) <10){
					cloned.remove();
					return false;
				}
                cloned.click();
                spe.first_click_after_editing = true;
            }
            page.on("mousedown", mouseDown).on("mousemove", mouseMove).on("mouseup", mouseUp);
        } else {
            spe.first_click_after_editing = false;
        }
    },
    getElementBgColor: function(element){
    	var canvas = element.closest(".page").find("canvas")[0];
		var ctx = canvas.getContext('2d');
		var element_pos = element.getElementOffset(element.closest(".page"));
		////2112x1632 // 1056x816
		
		
		
		return $.when(ctx.getImageData((element_pos.left)-1, (element_pos.top+(element_pos.height/2)), 1, 1).data);
    },
    //drag
    //TODO клонируем элемент
    
    hideElement: function(element){
        var prom = spe.getElementBgColor(element);
        var color = "";
        prom.then(function(color){
        	if(typeof color == 'object'){
        	//128, 116, 93, 255
        	//9d7bz3
        		color = `${color[0]},${color[1]},${color[2]}`;
        	}else{
				var x = color.split(",");
				color = `{${x[0]},${$x[1]},${x[2]}}`;
			}
	   		element.css({"background": "rgb("+color+")", "width": element.width()+3, "max-height": element.height(), "overflow" : "hidden"});
	   		element.addClass("hh2");  
        });
    },
    
    createTextClone: function (e, open_menu) {
        var is_click = true;
        if (typeof e.preventDefault === 'undefined') {
            is_click = false;
        } else {
            e.preventDefault();
        }
        //this
        var that = spe,
            uniq_id = spe.uniq();

        var element = (is_click) ? $(this) : e,
            page = element.closest(".page"),
            clone = element.clone();
            
        if (element.hasClass("new_annotate")) {
            element = element.closest("text_content_element");
        }
        element.addClass("skipp-pointer-events edited").trigger("unfocus");

		if(clone.css("font-family")=='Helvetica'){
			//TODO font
			clone.css("font-family", "Helvetica");
			var ot = parseInt(clone.css("top"))
			//clone.css("top", (ot-5)+"px");
		}

        if(true){ //element.hasClass("broken_cff_font") || element.hasClass("asian_font")){
	        var prom = spe.getElementBgColor(element);
	        
	        prom.then(function(color){
	        	if(typeof color == 'object'){
	        	//128, 116, 93, 255
	        	//9d7bz3
	        		color = `${color[0]},${color[1]},${color[2]}`;
	        	}else{
					var x = color.split(",");
					
					color = `{${x[0]},${$x[1]},${x[2]}}`;
				}
				//color = "255,255,255";
		   		element.css({"background": "rgb("+color+")", "width": element.width()+3, "max-height": element.height(), "overflow" : "hidden"}); 	        
	        });
        }else{
        	element.css("color", "transparent");
        }
        var original_color = element.attr("original-color");
        var original_hex = rgb2hex(original_color);
        var bg_color = detectColorOnClick(element, true);
        //bg_color = "rgb(255,255,255)";
        console.log("original_hex and bg hex ", original_hex, bg_color);
        if(false){
            if(original_hex==bg_color){
            }
            var text_color = original_color; //detectColorOnClick(element, false);
            //TODO 
            console.log(text_color, bg_color);
            
            if(text_color == bg_color){
                
            }else{
                if(original_color=='rgb(0,0,0)'){
                    original_color = text_color;
                }
            }
        }
        
        var new_css = { //TODO fix
            "color": original_color,
            //"font-family": (typeof spe.document_fonts[element.attr("original-font-name")] !== 'undefined') ? spe.document_fonts[element.attr("original-font-name")].original_name : "sans-serif",
            "font-family": $(clone).css('font-family'),
            "font-weight": (typeof spe.document_fonts[element.attr("original-font-name")] !== 'undefined' && (element.hasClass("broken_cff_font") || element.hasClass("asian_font"))) ? (spe.document_fonts[element.attr("original-font-name")].bold ? "bold" : "normal") : "normal",
            "font-style": (typeof spe.document_fonts[element.attr("original-font-name")] !== 'undefined') ? (spe.document_fonts[element.attr("original-font-name")].italic ? "italic" : "normal") : "normal",
            //"font-size": spe.fixFontSize(clone),
            "transform": element.hasClass("broken_cff_font")?"none": element.get(0).style.transform,
            "letter-spacing": element.get(0).style["letter-spacing"],
            "font-size": element.hasClass("broken_cff_font")?(parseFloat($(element).css('font-size')))+"px":$(element).css('font-size'),
            "line-height": element.css("line-height"),
            //"color": element.css("color"),
            //"min-width": element.css("width")
        }
        
        clone.css(new_css);
        clone.addClass("editable_element")
        clone.attr({"contenteditable": true, "element-id": uniq_id, "is_new": 0});
        
        element.attr("original-element-id", uniq_id);
        //element.css()
		if(clone.hasClass("broken_cff_font")){
			var  font_name = element.css("font-family").replace("-fixed", "");
			if(typeof spe.document_fonts[font_name]!='undefined' && typeof spe.document_fonts[font_name].fixed!='undefined'){
				clone.css("font-family", element.css("font-family").replace("-fixed", "")+"-fixed");
			}
		}
		
        page.append(clone);
        clone.draggable(dragable_params);
        if (open_menu) {
           clone.click();
        }
        clone.focus();
        return clone;
    },
    //TODO открываем редактор текста
    startEditText: function (e) {
        $(".text_content_element").removeClass("selected_now");
        var that = spe,
            clone = $(this),
            element_postion = clone.position(),
            element_height = clone.height(),
            uniq = clone.attr("element-id");
        textEditor.element_id = uniq;

        spe.first_click_after_editing = true;
        clone.css({"z-index": spe.getZIndex()});
        clone.addClass("selected_now");

        //
        that.editors.text.attr("element-id", uniq);
        element_postion.top += element_height + 10;
        
        that.editors.text.css(spe.getEditorPosition($(e.target), false)).show();
        //that.editors.text.show().css(element_postion);
        //TODO history text
        setTimeout(function () {
            spe.toHistory({
                element_id: uniq,
                type: "text",
                element_content: clone.html(),
                element: $(`div[element-id='${uniq}'].editable_element`)
            });
        }, 100);
        return true;
    },
    loadingPromise: function (pdfDocument) {
        //blocker.hide();
        console.log("in loadingPromise");
        console.log("pdfDocument is ",pdfDocument);
        
        blocker.hideUploader();
        blocker.showEditor();
        $(".footer-editor").addClass("active");
        spe.pdfDocument = pdfDocument;
        spe.pdfViewer.setDocument(pdfDocument);

        setTimeout(function () {
            var temp = {};
            $.each(spe.document_fonts, function (i, v) {
                $(".font-family-opts").append(`<li><a class='change_text_font' data-font-name='${i}' href='#'>${v.nice_name}</a> <b>!!!</b></li>`)
            });
//            mergeLetters();
        }, 3000);
        spe.pdfLinkService.setDocument(pdfDocument, null);
    },
    getEditorPosition: function (element, top) { //TODO повесить на все редакторы
        try {
            var whtl = $(element).getElementOffset($("#viewer")),
		        current_editor = spe.current_editor === false ? 'text' : spe.current_editor, // Если editor выключен, то текст
		        editor_height = spe.editors[current_editor].height();
            if (top) {
                return {top: whtl.top - editor_height - 10, left: whtl.left};
            } else {
                return {top: whtl.top + whtl.height + 10, left: whtl.left};
            }
        } catch (e) {
            console.log(e);
            return {top: 0, left: 0};
        }
    },
    fixFontSize: function (element) {
        var font_size = element.css("font-size");
        return Math.ceil((parseFloat(font_size))) + "px";
    },
    uniq: function () {
        return '_' + Math.random().toString(36).substr(2, 9);
    },
    
    uploadFile: async function (file, type, uniq) {
		var formData = new FormData();
		
    	switch(type){
    		case 'Sign':
				formData.append('file', file);
				formData.append("UUID", UUID);
				formData.append("_token", $("#editor_csrf").val());
				formData.append("type", type);    		    		
    		break;
    		default :
				formData.append('file', file);
				formData.append("UUID", UUID);
				formData.append("_token", $("#editor_csrf").val());
				formData.append("type", type);    		
    		break;
    	}
    	if(type=='PDF'){
    		//blocker.show();
    	}
        if(uniq){
        	formData.append("file_id", uniq);
        }
        
        if(window.skip_extract){
        	formData.append('skip_extract', 1);
        }else{
        	formData.append('skip_extract', 0);
        }
        
        if(typeof window.remove_all_texts!='undefined' && window.remove_all_texts==1){
        	formData.append('remove_all_texts', 1);
        }else{
        	formData.append('remove_all_texts', 0);
        }
        if(!PDFTOOLS.name){
        	formData.append('operation_type', 'edit');
        }else{
        	formData.append('operation_type', PDFTOOLS.name);
        }
        if(PDFTOOLS.name=='MixPDF' || PDFTOOLS.name=='pdf2ppt' || PDFTOOLS.name=='PDF2EPUB'){
        	formData.append('multiple_upload', PDFTOOLS.letter);
        }else{
        	formData.append('multiple_upload', 0);
        }
        
        if(PDFTOOLS.name == 'batespdf'){
        	formData.append('multiple_upload', String.fromCharCode(64 + spe.file_num));
        }
        formData.append('operation_id', pdfUploader.operation_id);
        spe.upload_in_progress = true;
        $(".current_file_status").html("File is uploading");
        
        
        
        var uniq_file_id = spe.uniq();
        
        currentUploads.changeStatus(file.name, true);
        
        return $.ajax({
            url: `/pdf/upload${type}`,
            type: "POST",
            data: formData, //{"test": "test", },
            processData: false,
            contentType: false,
            dataType: "json",
			xhr: function(){
				var xhr = new window.XMLHttpRequest();
				
				var started_at = new Date();
				
				xhr.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						var loaded = evt.loaded;
						
		                var seconds_elapsed =   ( new Date().getTime() - started_at.getTime() )/1000;

				        var bytes_per_second =  seconds_elapsed ? loaded / seconds_elapsed : 0 ;
				        var Kbytes_per_second = bytes_per_second / 1000 ;

						currentUploads.changeUploadProgress(file.name, (percentComplete*100).toFixed(0));
						
						if($(`.current_uploads .current_uploaded`).length==0){// && typeof window.is_editor==='undefined'){
							$(`.current_uploads`).append(`<div class='uploading_progress_outer'><div class='file_loading current_uploaded'><span class='tit'>file(s) are uploading: - </span><span class='percent'>0</span>%</div> <div class='progress_inner'></div></div>`);
						}
						
						if(percentComplete>=1){
							console.log("file upload complete")
						}
						
						$(document).trigger("file_upload_percent_update", [currentUploads.getTotalPercent(), bytes_per_second]);
						
						$(`.current_uploads .progress_inner`).css("width", (currentUploads.getTotalPercent())+"%");
						if(currentUploads.getTotalPercent()==100){
							$(`.current_uploads .current_uploaded .tit`).html("File(s) processing...");
							$(`.current_uploads .current_uploaded .percent`).html("100");
						}else{
							$(`.current_uploads .current_uploaded .percent`).html(currentUploads.getTotalPercent());
						}
					}
				}, false);
				xhr.addEventListener("progress", function(evt){
				   if (evt.lengthComputable) {
					   var percentComplete = evt.loaded / evt.total;
					   console.log(file.name, percentComplete, "%");
				   }
				}, false);
				return xhr;
			},
            
            success: function (data) {
				$(document).trigger('after_file_upload', [data]);
            
				if(currentUploads.getTotalPercent()>=100){
					$(".uploading_progress_outer").remove();
				}
				
    	        currentUploads.changeStatus(file.name, false);
            	if(type=='Image'){
            		$("li[data-image-id='new_user_image']").data("image-id", data.image_id);
            		$("img[db_id='wait_db_id']").attr("db_id", data.image_id)
            	}
            	if(type=='Sign'){
            		$("li[data-image-id='new_user_sign']").data("image-id", data.image_id);
            	}
            	window.temp_fonts = data.fonts
	        	if(typeof data.fonts!=='undefined'){
	        		//isEmptyObject
	        		function waitFonts(){
						if($.isEmptyObject(spe.document_fonts)){
							return false;
						}
						var font_loader  = $("<div style='displa: none;'></div>")
			    		$.each(data.fonts, function(i, v){
			    			$.each(spe.document_fonts, function(i2, v2){
			    				if(v2.original_name==i){
			    					spe.document_fonts[i2]['base64'] = v;
			    					spe.document_fonts[i2]['fixed'] = true;

									var str = `@font-face {
									font-family: '${i2}-fixed'; /*shitfont_here*/
										src: url('${spe.document_fonts[i2].base64}');
									}`
									$(`<style>${str}</style>`).appendTo("header");
									var font_n = i2.replace("-fixed", "");
									
									font_loader.append(`<span style='font-family: "${font_n}-fixed"'>test</span>`);
			    				}
			    			});
			    		});
			    		blocker.hide();
			    		
						clearInterval(interval);
	        		}
					var interval = setInterval(waitFonts, 3000);
	        	}

	        	spe.upload_in_progress = currentUploads.isIncomplete();
	        	
	        	if(!spe.upload_in_progress){
    		        $(".current_file_status").html("Your task is processing");
    		        $(".current_speed_and_percent").addClass("hidden");
	        	}

				if(PDFTOOLS.name == 'PDFLOADER'){
					localStorage.setItem('uploadedFileUrl', '/' + data.original_document_url);
					localStorage.setItem('uploadedFileName', data.original_document_name);
				}
	        	
				$(document).trigger("after_upload_file", [data]);
                console.log('after_upload_file data', data);
            },
            error: function (data) {
            	currentUploads.changeStatus(file.name, false);
            
            	spe.upload_in_progress = currentUploads.isIncomplete();
                console.log(data);
	               Swal("Error", ` Maximum file size limit (50 mbs) is exceeded `, "error");
                alert("Cant upload file..");
            }
        });
    },

    renderFile: function () {

    },
};
var elipseEditor, rectangleEditor;
var whiteoutEditor = {
    el: spe.editors.whiteout,
    element_id: false,
    init: function () {
        //TODO может быть баг с айди элемента. надо проверить
        elipseEditor = this,
            rectangleEditor = this;
        this.bind();
    },
    bind: function () {
        var that = this;
        $(document).on("click", ".new_whiteout, .new_shape", this.openEditor);
        $(document).on("click", ".whiteout-editable-menu .set-border, .shapes-editable-menu .set-border", (e) => {
                return that.setBorder(e, "border")
            }
        )
        ;
        $(document).on("click", ".whiteout-editable-menu .change_border_color .color-swatch, .shapes-editable-menu .change_border_color .color-swatch", (e) => {
                return that.setBorder(e, "border-color")
            }
        )
        ;
        $(document).on("click", ".whiteout-editable-menu .change_background_color .color-swatch", (e) => {
                return that.setBorder(e, "background")
            }
        )
        ;
        $(document).on("click", ".delete_whiteout, .delete_shape", this.delete);

    },
    openEditor: function (e) {
        var that = whiteoutEditor,
            element = $(e.target);
        that.element_id = (typeof element.attr("element-id") !== 'undefined' ? element.attr("element-id") : element.closest(".new_whiteout, .new_shape").attr("element-id"));
		
        that.el.css(spe.getEditorPosition($(e.target), true));
        that.el.show();
        if (element.hasClass("new_shape")) {
            //TODO history rectangle
            spe.toHistory({
                element_id: that.element_id,
                type: element.attr("rect_type"),
                element_content: `Shape ${spe.current_editor}`,
                element: $(`div[element-id='${that.element_id}']`)
            });
        } else {
            //TODO history whiteout
            spe.toHistory({
                element_id: that.element_id,
                type: "whiteout",
                element_content: "Whiteout",
                element: $(`div[element-id='${that.element_id}']`)
            });
        }

        spe.dissableAllSelection();
        $(`div[element-id='${that.element_id}']`).addClass("element_selected_now");
        
        $(`div[element-id='${that.element_id}']`).css({"z-index": spe.getZIndex()});
    },
    setBorder: function (e, type) {
        var that = whiteoutEditor,
            border_element = $(e.target),
            editor_element = border_element.closest(".element_editor"),
            new_css = {},
            new_attrs = {},
            whiteout_element = $(`div[element-id='${that.element_id}']`);

        var current_border = that.getCurrentBorder($(".inner_element", whiteout_element));
        switch (type) {
            case 'border':
                current_border['border'] = parseInt($(border_element).css("height"));
                break;
            case 'border-color':
                current_border['border-color'] = $(border_element).css("background-color");
                break;
            case 'background':
                current_border['background-color'] = $(border_element).css("background-color");
                break;
        }
        whiteout_element.attr(current_border);
        $(`.inner_element`, whiteout_element).css({
            "border": `${current_border['border']}px ${current_border['border-color']} ${current_border['border-style']}`,
            "background-color": current_border['background-color']
        });
		var shape_type = "whiteout";
		var sh_t = $(`.new_shape[element-id='${that.element_id}']`).attr("rect_type")
		if(typeof sh_t!='undefined'){
			shape_type = sh_t;
		}
		if(sh_t=='whiteout_rectangle'){
			shape_type = "whiteout";
		}
        spe.toHistory({
            element_id: that.element_id,
            type: shape_type,
            element_content: shape_type,
            element: $(`div[element-id='${that.element_id}']`)
        });

        return false;
    },
    delete: function () {
        var that = whiteoutEditor;
        $(`div[element-id='${that.element_id}'], div[element-id='${that.element_id}']`).remove();
        $(`[element-id='${that.element_id}']`).remove();
        that.el.hide();
        spe.removeFromHistoryByID(that.element_id);
        return false;
    },
    getCurrentBorder: function (element) {
        return {
            "border": parseInt(element.css("border-left-width")),
            "border-style": (element.css("border-left-style") !== 'none') ? element.css("border-left-style") : "solid",
            "border-color": (element.css("border-left-color") != "" ? element.css("border-left-color") : "white"),
            "background": element.css("background-color")
        }
    },
    startDrag: function (e) {
    	spe.dissableAllSelection();
        spe.editors[spe.current_editor].hide();
    },
    stopDrag: function (e) {
		console.log("stop drag rectangle");
        var that = whiteoutEditor;
        that.el.css(spe.getEditorPosition($(e.target), true));
        spe.editors[spe.current_editor].show();
        var elt = "whiteout";

        if ($(e.target).hasClass("new_shape")) {
            elt = $(e.target).attr("rect_type");
        }
        spe.dissableAllSelection();
        $(e.target).addClass("element_selected_now");

        spe.toHistory({
            element_id: whiteoutEditor.element_id,
            type: elt,
            element_content: "Whiteout",
            element: $(`div[element-id='${whiteoutEditor.element_id}']`)
        });
    }
};

//TODO links here
var linksEditor = {
    el: spe.editors.link,
    element_id: false,
    init: function () {
        this.bind();
    },
    bind: function () {
        var that = this;
        $(document).on("click", ".annotationLayer .linkAnnotation", this.createLinkClone);
        $(document).on("click", ".edited_link, .new_anotation_link", this.openLinkEditor);
        $(document).on("keyup", ".external_title, .internal_link, .external_link, .internal_title ", (e) => {
            this.changeLinkValue(e, that)
        })
        ;
        $(document).on("change", ".link_type", (e) => {
            this.changeLinkValue(e, that)
        })
        ;
        $(document).on("click", ".close_link_editor ", () => {
            this.el.hide();
            return false;
        })
        ;
        $(document).on("click", ".link-editable-menu .delete ", that.delete);

    },
    createLinkClone: function (e) {
        e.preventDefault();
        var that = linksEditor,
            uniq_id = spe.uniq();
        that.element_id = uniq_id;

        var element = $(this),
            page = element.closest(".page"),
            link = $(`<div class='edited_link' element-id='${uniq_id}'></div>`);

        element.addClass("skipp-pointer-events edited");
        element.attr("original-element-id", uniq_id);
        var new_css = $.extend({position: "absolute"}, element.getElementOffset(".page")),
            new_attrs = {url: $("a", element).attr("href"), title: $("a", element).attr("title"), external: true};
        if (new_attrs.url.indexOf("http") !== 0) {
            new_attrs.external = false;
        }
        link.css(new_css);
        link.attr(new_attrs)
        page.append(link);
        link.click();
        link.css({"z-index": spe.getZIndex()});
        element.addClass("hidden");
        return false;
    },
    openLinkEditor: function (e) {
        var that = linksEditor,
            link = $(e.target).hasClass("inner_element") ? $(e.target).closest(".edited_link") : $(e.target), //$(e.target).hasClass("new_anotation_link")?$(e.target):$(e.target).closest(".new_anotation_link"),
            is_external = link.attr("external"),
            title = link.attr("title"),
            url = link.attr("url");

        that.element_id = link.attr("element-id");
        $(".external_title", that.el).val(title);
        if (is_external) {
            $(".link_type[value='1']").prop("checked", true);
            $(".external_link", that.el).val(url);
            $(".internal_link", that.el).val("");
        } else {
            $(".link_type[value='2']").prop("checked", true);
            $(".external_link", that.el).val("");
            $(".internal_link", that.el).val(url);
        }
        if (typeof url == 'undefined') {
            url = "#";
        }

        spe.dissableAllSelection();
        link.addClass("element_selected_now");

        spe.toHistory({
            element_id: that.element_id,
            type: "link",
            element_content: `Link ${url}`,
            element: $(`div[element-id='${that.element_id}']`)
        });

        //spe.toHistory(that.element_id, "link", `Link ${url}`, "pagehere");

        that.el.css(spe.getEditorPosition($(e.target), false));
        that.el.show();
        link.css({"z-index": spe.getZIndex()});
    },
    changeLinkValue: function (e, that) {
        var val = $(e.target).val(),
            element = $(e.target),
            te = $(`.edited_link[element-id='${that.element_id}']`);

        te.attr("external", element.val() == "2" ? false : true);
        te.attr("url", $(".external_link", that.el).val());
        te.attr("title", $(".external_link", that.el).val());
        //elem

        if (element.is(":radio")) {
            $(`.edited_link[element-id='${that.element_id}']`).attr("external", (element.val() == "2" ? false : true));
        } else {
            if ($(".link_type:checked").val() == "1") {
                var url = $(".external_link").val();
                te.attr("url", url);
                te.attr("title", $(".external_title").val());
            } else {
                var url = $(".internal_link").val();
                te.attr("url", url);
                te.attr("title", $(".internal_title").val());
            }
            spe.toHistory({
                element_id: that.element_id,
                type: "link",
                element_content: `Link ${url}`,
                element: $(`div[element-id='${that.element_id}']`)
            });
            //spe.toHistory(that.element_id, "link", `Link ${url}`, "pagehere");
        }
    },
//draggable
    startDrag: function (e) {
    	spe.dissableAllSelection();

        spe.editors[spe.current_editor].hide();
    },
    stopDrag: function (e) {
    	console.log("stop drag links");
        var that = whiteoutEditor;
        spe.editors[spe.current_editor].css(spe.getEditorPosition($(e.target), false));
        spe.editors[spe.current_editor].show();
        that.element_id = $(e.target).attr("element-id");

        spe.dissableAllSelection();
        $(e.target).addClass("element_selected_now");


        spe.toHistory({
            element_id: that.element_id,
            type: "link",
            element_content: $(e.target).attr("url"),
            element: $(`div[element-id='${that.element_id}'].edited_link`)
        });
    },
    delete: function () {
        var that = linksEditor;
        $(`div[element-id='${that.element_id}'], div[element-id='${that.element_id}']`).remove();
        $(`[element-id='${that.element_id}']`).remove();
        $(`[original-element-id='${that.element_id}']`).removeClass("skipp-pointer-events edited hide");

        that.el.hide();
        spe.removeFromHistoryByID(that.element_id);
        return false;
    },
    getResizableParams: function () {
        return {
            handles: "n, e, s, w, ne, se, sw, nw",
            stop: function (e) {
                var that = linksEditor,
                    element_id = $(e.target).attr("element-id");
				

                spe.toHistory({
                    element_id: element_id,
                    type: "link",
                    element_content: $(e.target).attr("url"),
                    element: $(e.target) //$(`div[element-id='${element_id}'].edited_link`)
                })
            }
        }
    }

};

var formsEditor = {
    el: spe.editors.forms,
    element_id: false,
    init: function () {
        var that = this;
        $("input[type='text'], textarea", that.el).on("keyup", that.updateValue);
        $("input[type='checkbox']", that.el).on("change", that.updateValue);
        $(".delete_form_element", that.el).on("click", (e) => {
            $(`.element-forms[element-id='${formsEditor.element_id}']`).remove();
            spe.removeFromHistoryByID(formsEditor.element_id);
            spe.hideAllEditors();
            return false;
        })
        ;
    },
    updateValue: function (e) {
        var that = this,
            editor = $(`.element_editor[element-id='${formsEditor.element_id}']`),
            element = $(`.element-forms[element-id='${formsEditor.element_id}']`),
            input = $(e.target);
        element.attr(input.attr("id"), input.val());

		spe.toHistory({
			element_id: formsEditor.element_id,
			type: "forms",
			element_content: element.attr("element-type"),

			element: $(`div[element-id='${formsEditor.element_id}'].element-forms`)
		});
    }
};

//TODO добавление изображений.
var imagesEditor = {
    el: spe.editors.images,
    element_id: false,
    is_blocked: false,
    $sign_canvas: $('#sign_draw_canvas'),
    sign_canvas: document.getElementById('sign_draw_canvas'),
    sign_canvas_cxt: false,
    init: function () {
    	alert("init image");
        this.bind();
    },
    bind: function () {
        var that = this;
        $(document).on("change", "#new_image_uploader", that.imageUpload);
        $(document).on("click", ".user_image img, .image_form_item", function (e) {
            e.preventDefault();
            if($(this).hasClass("image_form_item")){
        		spe.current_editor = "images";
            }
        	$("#simplePDFEditor").attr("current_editor", spe.current_editor);
            that.imageUpload(e, e.target, "old_user_image", $(this).hasClass("image_form_item")?"form_image_item":false);
            spe.hideAllDropdown();
        });
        
        //TODO добавить дебонце.
        $(document).on("mousemove", ".page.active_image_moving", that.moveInsertedImages); //TODO двигаем картинку за мышкой
        $(document).on("click", ".page.active_image_moving", function (e) {
        	if(!$(e.target).is("canvas")){
        		return false;
        	}
        	
            that.appendImageToLayer(e);
        }); //TODO добавляем картинку на страницу
        $(".delete", that.el).click(that.deleteImage);
        $(".rotate", that.el).click(that.rotateImage);

        //TODO рисуем подписи
        that.initSignDraw();
        //$(document).on("click", ".user_images .user_image", that.rotateImage);
        $(document).on("click", ".erase_canvas", that.eraseSignDraw);
        $(document).on("click", "#save_new_sign", (e) => {
        	switch($(".signatore-btn-block.signatore-btn-active").data("type")){
        		case 'text':
        			$(".signaturePreview.sign_preview").eq(0).click();
        		break;
        		
        		default:
					that.imageUpload(e);
					$(".create-signature-modal").hide();
        		break;
        	}
			return false;
        
        })
        ;
        $(document).on("keyup", "#sign_text_input", that.typeTextSign);
        $(document).on("click", "#sign_previews .sign_preview", that.insertTextSign);
    },
    insertTextSign: function (e) {
        var element = e.target, that = imagesEditor;// canvas = document.createElement('canvas');
        //canvas.width = $(e.target).width()+100; canvas.height = 64;
//		rasterizeHTML.drawHTML(element.outerHTML, canvas).then(function(result){
//			that.imageUpload(e, canvas, "text_sign");
//		})
//		var clone = $(e.target).clone();
		$(element).css({"background-color": "none", "background": "none", "color": "black"});
		

		
        html2canvas(element, {backgroundColor: null}).then(canvas => {
            $("body"
            ).append(canvas);
            that.imageUpload(e, canvas, "text_sign");
            $(".create-signature-modal").hide();
        })
        ;


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
            canvas = imagesEditor.sign_canvas;
            $canvas = imagesEditor.$sign_canvas;
            imagesEditor.sign_canvas_cxt = ctx = canvas.getContext("2d");
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
    rotateImage: function (e) {
    	console.log("click");
    	if(imagesEditor.now_rotated){
    		return false;
    	}
    	imagesEditor.now_rotated = 1;
        e.preventDefault();
        var that = imagesEditor,
            element = $(`.outer_image_div[element-id='${that.element_id}']`),
            width = element.width(),
            height = element.height(),
            imgel = element.find(".inserted_image")
        ;

        that.is_blocked = true;
        element.draggable("destroy");//
        if (element.hasClass("ui-resizable")) {
            element.resizable("destroy");
        }

        var image = document.createElement("img");
        image.src = imgel.attr("src");
        var canvas = $(`<canvas id='temp_canvas' width='${image.height}' height='${image.width}'></canvas>`)[0];
        var ctx = canvas.getContext("2d");
        image.onload = function () {
            var img = image;
            var imgWidth = image.width;
            var imgHeight = image.height;
            ctx.width = imgWidth;
            ctx.height = imgHeight;
            
            
            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate(Math.PI / 2);
            
            ctx.drawImage(image, -image.width / 2, -image.height / 2);
            ctx.rotate(-Math.PI / 2);
            ctx.translate(-canvas.width / 2, -canvas.height / 2);
            
            
            //ctx.fillRect(0, 0, 25, 10);
            
            //ctx.fillRect(ctx.width/4, ctx.width/2, ctx.width/2, ctx.height/2);
            
            var new_src = canvas.toDataURL("image/png");
            element.find(".inserted_image").attr("src", new_src);
            
            
            element.find(".inserted_image")[0].onload = function(){
            	imagesEditor.now_rotated = false;
            }
            
            element./*css({width: height, height: width}).*/draggable({
                appendTo: $(image).closest(".page"),
                drag: that.draggableImage,
                stop: that.draggableImage
            });

            imgel.css({width: height, height: width});
            element.css({width: height, height: width});
            if(imgel.hasClass("form_image_item")){
            	
            }else{
	            //imgel.resizable(that.getResizableParams(image.height / image.width, that));
            }
            //resizable

            imgel.closest(".outer_image_div").resizable(that.getResizableParams(image.height / image.width, that));
            that.is_blocked = false;
            //rotate image
            spe.toHistory({
                element_id: that.element_id,
                title: "Image",
                type: "images",
                element_content: imgel.attr("uploaded")=="1"?false: imgel.attr("src"),
                img_base64: imgel.attr("src"),
                element: $(`div[element-id='${that.element_id}'].outer_image_div`)
            });
            imagesEditor.now_rotated = 0;
        }


    },
    //TODO image resize here
    getResizableParams: function (aspect, that) {
        return {
            handles: "n, e, s, w, ne, se, sw, nw",
            aspectRatio: aspect,
            start: function(e){
            	$(e.target).closest(".outer_image_div").removeClass("ui-draggable");
            	$(e.target).closest(".outer_image_div").addClass("is_resized");
            },
            resize: function(e){
            	var pos = $(e.target).getElementOffset(false, false);
            	var el = $(e.target).closest(".outer_image_div");
            },
            stop: function (e) {
            	$(e.target).closest(".outer_image_div").addClass("ui-draggable");
                var img = $(e.target).find("img").attr("src");
                var image_el = $(e.target).find("img");
                //resize image
                spe.toHistory({
                    element_id: that.element_id,
                    title: "Image",
                    type: "images",
                    element_content: image_el.attr("uploaded")=="1"?false: img,
                    img_base64: img,
                    element: $(`div[element-id='${that.element_id}'].outer_image_div`)
                });
            }
        }
    },
    deleteImage: function (e) {
        var that = imagesEditor;
        e.preventDefault();
        $(`.outer_image_div[element-id='${that.element_id}']`).remove();
        spe.removeFromHistoryByID(that.element_id);
        that.el.hide();
    },
    //TODO переделать
    imageUpload: function (e, element, type, form_element_type) {
        $(".inserted_image.follow_the_mouse").remove();
        $('#draw-modal').hide();
        
        var that = spe;
        
        alert(type);
        
        switch (type) {
            case 'text_sign':
                var data = element.toDataURL(),
                    image = $("<img>"),
                    uniq =  spe.uniq();
                image.addClass("inserted_image text_sign is_sign follow_the_mouse").attr({"src": data, "element-id": uniq});
                $("#simplePDFEditor .page").addClass("active_image_moving");
                $("#simplePDFEditor").append(image);
                $(`<li class='sign-entry user_image' data-image-id='new_user_sign'><a class='user_image_outer' href='#'><img src='${data}'></a><a href="#" class="delete-image"><i class="fa fa-trash" ></i></a></li>`).insertBefore(".sign-opts .divider")
                spe.uploadFile(data, "Sign", uniq);
                break;
            case 'old_user_image':
            	var img_el = $(element).is("img") ? $(element): $(element).find("img"),
            		element_parent = img_el.closest("li"),
            		element_db_id = (typeof element_parent.data("image-id")!='undefined'?element_parent.data("image-id"):false),
                	src = img_el.attr("src"),
                	temp_img = $("<img style='width: auto; height: auto'>");
		            temp_img.attr("src", src),
		            data = getBase64Image(temp_img[0]);
                    
                data.then(function(data){
		            if(form_element_type=='form_image_item'){
		                var image = $("<img>").addClass("inserted_image follow_the_mouse add_multiple_times form_image_item").attr("src", data).css({width: "auto"});
		                $("#simplePDFEditor .page").addClass("active_image_moving");
		                $("#simplePDFEditor").append(image);		            	
		            }else{
		                var image = $("<img>").addClass("inserted_image follow_the_mouse").attr({"src": data, "db_id": element_db_id}).css({width: "200px"});
		                $("#simplePDFEditor .page").addClass("active_image_moving");
		                $("#simplePDFEditor").append(image);
		            }
                });
                break;
            //TODO deprecated
            case 'form_element':
                //TODO add form element || добавляет текстовые клоны формы (чекбокс, радиво).
                var temp_img = $("<img style='width: auto; height: auto'>");
                	temp_img.attr("src", $(element).attr("src")),
                    data = getBase64Image(temp_img[0]);

                data.then(function (data) {
                    image = $("<img>").addClass("new_form_element follow_the_mouse").attr("src", data).css({
                        "max-width": "30px",
                        "max-height": "30px"
                    });

                    image.attr("element_type", form_element_type);
                    image.attr("field_type", form_element_type);

                    $("#simplePDFEditor .page").addClass("active_image_moving");
                    $("#simplePDFEditor").append(image);
                });
                
                break;

            default:
            	
                var ell = this;
                ell = e.target;

                if ($(ell).hasClass("user_image")) { //TODO выбор старого файла

                } else if ($(ell).hasClass("save_new_sign")) { //TODO добавление нарисованой сигны
                    var canvas = document.getElementById("sign_draw_canvas"),
                        data = canvas.toDataURL(),
                        temp_img = $("<img style='width: auto; height: auto'>"),
                        image = $("<img>"),
                        uniq = spe.uniq();

                    temp_img.attr({"src": $(ell).find("img").attr("src"), uniq: uniq}),
                        image.addClass("inserted_image is_sign follow_the_mouse").attr("src", data).css({width: "200px"});

                    $("#simplePDFEditor .page").addClass("active_image_moving");
                    $("#simplePDFEditor").append(image);
                    //$("#current_sign").attr("src", data);
                    $.fancybox.close();
                    $(`<li class='sign-entry user_image' data-image-id='new_user_sign'><a class='user_image_outer' href='#'><img src='${data}'></a><a href="#" class="delete-image"><i class="fa fa-trash" ></i></a></li>`).insertBefore(".sign-opts .divider")
                    spe.uploadFile(data, "Sign", uniq);

                } else { //TODO выбор нового файла
                    var file = (ell.files[0]),
                    	uniq = spe.uniq();
                    pdfUploader.getBase64(file).then((data) => {
                        var image = $("<img>").addClass("inserted_image follow_the_mouse").attr({"src": data, "element-id": uniq, "uploaded": 1, "db_id":"wait_db_id"}).css({width: "200px"});
                        
                        $("#simplePDFEditor .page").addClass("active_image_moving");
                        $("#simplePDFEditor").append(image);
                        
                        $(`<li class="tools-default image-entry user_image" data-image-id="new_user_image"><a href="#" class='user_image_outer'><img src='${data}'></a>
                        	<a href="#" class="delete-image"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        	</li>`).insertBefore(".user_images li.divider");
                        spe.uploadFile(file, "Image", uniq);
                    });
                }
                break;
        }
        
        spe.hideAllDropdown();
    },
    moveInsertedImages: function (e) {
        var that = imagesEditor,
            scene = $(".active_image_moving"),
            offsetX = (e.pageX - scene.offset().left),
            offsetY = (e.pageY - scene.offset().top);
            
        if ($(".follow_the_mouse").length > 0) {
            $(".follow_the_mouse").css({top: offsetY, left: offsetX});
        }
    },
    //TODO добавление таскаемого элемента каринки или формы или сингы или..
    appendImageToLayer: function (e) {
        console.log("============= append to image layer ============");
        var that = imagesEditor,
            cloned_image = spe.current_editor == 'forms' ? $(".new_form_element.follow_the_mouse").clone() : $(".inserted_image.follow_the_mouse").clone(),
            target = $(e.target),
            page = target.closest(".page");
            
        that.element_id = spe.uniq();
        
        if(cloned_image.length===0){
        	cloned_image = $(".new_form_element.follow_the_mouse").clone();
        }
        if(cloned_image.length===0){
        	console.log("cloned_image.length === 0");
        	return false;
        }
        
        if(typeof cloned_image.attr('element-id') != 'undefined'){
        	that.element_id = cloned_image.attr('element-id');
        }
        var scene = $(".active_image_moving"),
            element_type = $("#simplePDFEditor").attr("current_editor"),
            offsetX = e.offsetX,//e.pageX - scene.offset().left,
            offsetY = e.offsetY, //e.pageY - scene.offset().top,
            outer_div = $(`<div data-rotate="0" data-w="0" data-h="" element-id="${that.element_id}" class='outer_image_div element-${element_type}'></div>`);
		
        cloned_image.removeClass("follow_the_mouse").css({top: 0, left: 0});
        outer_div.css({top: offsetY + "px", left: offsetX + "px", position: "absolute"})

        var field_type = $(".follow_the_mouse").attr("element-type");
        if(typeof field_type == 'undefined'){
        	field_type = $(".follow_the_mouse").attr("element_type");
        }
    	if(cloned_image.hasClass("form_image_item")){
    		outer_div.addClass("form_image_item_outer");
    		//append
    	}else{
		    $(".follow_the_mouse").remove(); //удаление элемента
		    $("#simplePDFEditor .page").removeClass("active_image_moving");
		}
		
        outer_div.append(cloned_image);
        page.append(outer_div);
        //TODO костыли вы мои костыли...
        if (spe.current_editor == 'forms') {
            var temp = outer_div.find(".new_form_element"); //
            outer_div.css({width: temp.css("width"), "height": temp.css("height")});
            temp.css({width: "100%", "height": "100%"});
            switch (field_type) {
                case 'dropdown':
                case 'textarea':
                case 'input':
                    outer_div.resizable({handles: "n, e, s, w, ne, se, sw, nw"});
                    break;
                default:
                
                break;
            }
            spe.form_elements_count++;
            outer_div.attr({
                "field_type": field_type,
                element_type: "form_field",
                field_name: "Field "+spe.form_elements_count,
                "field_options": "Option 1\r\nOption 2\r\nOption 3",
                "field_allow_multiple": false
            });
            outer_div.draggable({
                appendTo: page,
                start: that.startDrag,
                stop: that.stopDrag
            }).on("click", that.openImageOrFormEditor);
            outer_div.click();
        } else {
            outer_div.attr({element_type: "image"});
            outer_div.draggable({
                appendTo: page,
                start: that.startDrag,
                stop: that.stopDrag
            }).on("click", that.openImageOrFormEditor);
        	if(cloned_image.hasClass("form_image_item")){
        		outer_div.addClass("no_border_no_outline");
                spe.toHistory({
                    element_id: that.element_id,
                    title: "Image",
                    type: "images",
                    element_content: cloned_image.attr("src"),
                    img_base64: cloned_image.attr("src"),
                    element: $(`div[element-id='${that.element_id}'].outer_image_div`)
                });
                //src


				spe.editors[spe.current_editor].attr("element-id", that.element_id);
				spe.editors[spe.current_editor].css(spe.getEditorPosition(outer_div, true));
				spe.editors[spe.current_editor].show();
            	cloned_image.resizable(imagesEditor.getResizableParams($(cloned_image).width() / $(cloned_image).height(), that));
        	}else{

            	cloned_image.closest(".outer_image_div").resizable(imagesEditor.getResizableParams($(cloned_image).width() / $(cloned_image).height(), that));
		        outer_div.click();
            }
        }
        //append history
    },
    startDrag: function (e) {
    	spe.dissableAllSelection();
       	if(spe.editors.hasOwnProperty(spe.current_editor)){
        	spe.editors[spe.current_editor].hide();
        }
    },
    stopDrag: function (e) {
    	console.log("stop drag images");
        var top = true;
        if ($(e.target).hasClass("element-forms")) {
            top = false;
        }
        
        //костыль
        if(spe.current_editor =='sign'){
        	spe.current_editor = 'images';
        }
        
        spe.editors[spe.current_editor].css(spe.getEditorPosition($(e.target), top));
        spe.editors[spe.current_editor].show();

        var form_element = $(e.target).find(".new_form_element");
        if (form_element.length > 0) {
            spe.toHistory({
                element_id: imagesEditor.element_id,
                type: "forms",
                element_content: form_element.attr("element-type"),
                
                element: $(`div[element-id='${imagesEditor.element_id}'].element-forms`)
            });
        } else {
        	var image_el = $(e.target).find("img"),
        		img = image_el.attr("src");
        		
        	//drag image
            spe.toHistory({
                element_id: imagesEditor.element_id,
                type: "images",
                title: "Image",
                element_content: image_el.attr("uploaded")=="1"?false: img,
                img_base64: img,
                //element_content: false, //$(e.target).find("img").attr("src"),
                element: $(`div[element-id='${imagesEditor.element_id}'].outer_image_div`)
            });
        }

        spe.dissableAllSelection();
        $(e.target).addClass("element_selected_now");


        
    },
    draggableImage: function (e) {
        var that = imagesEditor;
        that.element_id = $(e.target).attr("element-id");
        //TODO поменять на функцию получения координат
        //spe.editors[spe.current_editor].show().css({top: parseInt($(e.target).css("top"))-70, left: parseInt($(e.target).css("left"))+20});

    },
    //TODO открытие редактора картинки и форм и...
    openImageOrFormEditor: function (e) {
    	
    	$(".inserted_image.follow_the_mouse.form_image_item").remove();
    
        var element = $(this),
        	that = imagesEditor,
        	top = true;
        
        $(".element_selected_now").removeClass("element_selected_now");
        element.addClass("element_selected_now");
        
        that.element_id = element.attr("element-id");
        if (element.hasClass("element-forms")) {
            spe.current_editor = "forms";
            top = false;
        } else {
            spe.current_editor = "images";
        }
        if(typeof element.attr("field_value")!='undefined'){
        	$("#field_value").val(element.attr("field_value"));
        }else{
        	$("#field_value").val("");
        }
        

        var element_type = element.attr("element_type");
        switch (element_type) {
            case 'form_field':
                var field_type = element.attr("field_type");
                if (typeof field_type == 'undefined') { //скорее всего это чекбокс/радиво картинка
                    field_type = element.find("img").attr("element_type");
                }

                var temp_id = formsEditor.element_id = that.element_id;
                $("#field_name", spe.editors.forms).val(element.attr("field_name"));
                $("#field_options", spe.editors.forms).val(element.attr("field_options"));
                $("#field_allow_multiple", spe.editors.forms).prop("checked", element.attr("field_allow_multiple") == 'true' ? true : false);
                var editor_fields = {};
                
                switch (field_type) {
                    case 'dropdown':
                        editor_fields = ["field_name", "field_options", "field_allow_multiple"];
                        break;
                    default:
                    case 'textarea':
                    case 'input':
                        editor_fields = ["field_name"];
                        break;
                    case 'checkbox':
                    case 'radio':
                        editor_fields = ["field_name", "field_value"];
                        break;
                }
                spe.editors[spe.current_editor].find(".field_row").hide();
                $.each(editor_fields, function (i, v) {
                    spe.editors[spe.current_editor].find(`.${v}_row`).show();
                });

                var form_element = $(e.target).find(".new_form_element");
                spe.toHistory({
                    element_id: formsEditor.element_id,
                    type: "forms",
                    element_content: field_type,
                    element: $(`div[element-id='${imagesEditor.element_id}'].element-forms`)
                });
                //spe.toHistory({element_id: temp_id, type: "forms", element_content: `Form element ${field_type}`, element: $(`div[element-id='${temp_id}']`)});
                break;
            default:
                var temp_id = that.element_id,
                    img = $("img", this).attr("src");
                //TODO history images
                //draggable
                spe.toHistory({
                    element_id: that.element_id,
                    title: "Image",
                    type: "images",
                    element_content: $("img", this).attr("uploaded")=="1"?false: img,
                    img_base64: img,
                    element: $(`div[element-id='${that.element_id}'].outer_image_div`)
                });
                break;
        }
        element.css({"z-index": spe.getZIndex()});

        spe.editors[spe.current_editor].attr("element-id", temp_id);
        spe.editors[spe.current_editor].css(spe.getEditorPosition($(e.target), top));
        spe.editors[spe.current_editor].show();

        //that.el.show().css({top: parseInt($(e.target).css("top"))-70, left: parseInt($(e.target).css("left"))+20});
    }
};

var textEditor = {
    el: spe.editors.text,
    element_id: false,
    init: function () {
        this.bind();
    },
    bind: function () {
        var that = this;
        $(".set_bold", that.el).click(that.setBold);
        $(".set_italic", that.el).click(that.setItalic);
        $(".font-size-number", that.el).change(that.setFontSize);
        $(".color-swatch", that.el).click(that.setColor);
        $(".delete_text", that.el).click(that.deleteText);
    },
    setBold: function () {
        var that = textEditor;
        textEditor.getCurrentID();
        var current_element = $("div.editable_element[element-id='" + that.element_id + "']");
        if (current_element.hasClass("is_bold")) {
            current_element.css("font-weight", "normal");
            current_element.removeClass("is_bold");
        } else {
            current_element.addClass("is_bold");
            current_element.css("font-weight", "bold");
        }
        console.log("set bold/ne bold " + that.element_id);
        spe.toHistory({
            element_id: that.element_id,
            type: "text",
            element_content: current_element.html(),
            element: $(`div[element-id='${that.element_id}'].editable_element`)
        });
    },
    setItalic: function () {
        var that = textEditor;
        textEditor.getCurrentID();
        var current_element = $("div.editable_element[element-id='" + that.element_id + "']");
        if (current_element.hasClass("is_italic")) {
            current_element.css("font-style", "normal");
            current_element.removeClass("is_italic");
        } else {
            current_element.addClass("is_italic");
            current_element.css("font-style", "italic");
        }

        spe.toHistory({
            element_id: that.element_id,
            type: "text",
            element_content: current_element.html(),
            element: $(`div[element-id='${that.element_id}'].editable_element`)
        });

        console.log("set italic/ne italic " + that.element_id);
    },
    setFontSize: function () {
        var that = textEditor;
        textEditor.getCurrentID();
        var current_element = $("div.editable_element[element-id='" + that.element_id + "']");
        current_element.attr("new-font-size", $(this).val() + "px");
        current_element.css("font-size", $(this).val() + "px");

        spe.toHistory({
            element_id: that.element_id,
            type: "text",
            element_content: current_element.html(),
            element: $(`div[element-id='${that.element_id}'].editable_element`)
        });
        console.log("set font size " + that.element_id);
    },
    setColor: function (e) {
        e.preventDefault();
        var el = $(this),
            that = textEditor,
            color = el.css("background-color");

        textEditor.getCurrentID();
        var current_element = $("div.editable_element[element-id='" + that.element_id + "']");
        current_element.attr("new-color", color);
        current_element.css("color", color);
        spe.toHistory({
            element_id: that.element_id,
            type: "text",
            element_content: current_element.html(),
            element: $(`div[element-id='${that.element_id}'].editable_element`)
        });
    },
    deleteText: function (e) {
        e.preventDefault();
        var el = $(this),
            that = textEditor;
        textEditor.getCurrentID();

        var current_element = $("div.editable_element[element-id='" + that.element_id + "']");
        current_element.attr("is_deleted", true);
        current_element.hide();
        spe.editors.text.hide();

        if (current_element.attr("is_new") == '1') {
            spe.removeFromHistoryByID(that.element_id);
        } else {
            spe.toHistory({
                element_id: that.element_id,
                type: "text",
                action: "deleted",
                element_content: "Delete text: " + current_element.html(),
                element: $(`div[element-id='${that.element_id}'].editable_element`)
            });
        }
    },
    getCurrentID: function () {
        var that = textEditor;
        that.element_id = that.el.attr("element-id");
    },
    onDrag: function (e) {
    
        //TODO поменять координаты редактора
        var draged_el = $(e.target),
            editor_position = draged_el.position();
        editor_position.top += draged_el.height() + 10;
        spe.editors.text.show().css(editor_position);
        var uniq = draged_el.attr("element-id");

        spe.toHistory({
            element_id: uniq,
            type: "text",
            element_content: draged_el.html(),
            element: $(`div[element-id='${uniq}'].editable_element`)
        });

    }
};

var annotateEditor = {
    start_element: false,
    selected_elements: [],
    element_id: false,
    init: function () {
        this.bind();
    },
    type: "highlight",
    color: "rgba(153, 220, 250, 0.5);",
    flag: false,
    bind: function () {
        var _this = this;
        $(document).on("click", "div[current_editor='annotate'] .new_annotate", _this.openEditor);
        $(document).on("keyup", ".annotate-editable-menu textarea, .annotate-editable-menu input", _this.setAnnotateValue);
        $(document).on("mouseup", "div[current_editor='annotate'] .text_content_element", _this.getTextRange);


        $(document).on("click", ".delete_annotate", (e) => {

            $(`.new_annotate[element-id='${this.element_id}']`).each(function () {

                var $this = $(this);

                $this.replaceWith($this.html());

            });

            spe.editors[spe.current_editor].hide();
            spe.removeFromHistoryByID(this.element_id);

            return false;
        })
        ;


        $(document).on("click", ".highlite-color", (e) => {

        	$(e.target).closest("ul").find("*").removeClass("active");
			$(e.target).addClass("active");
            this.color = $(e.target).css("background-color");
            this.type = "highlight";
            e.preventDefault();
        })
        ;

        $(document).on("click", ".hl_higlight", (e) => {
			e.preventDefault();
            this.color = "rgba(243,136,112,0.501)";
            this.type = "highlight";
            spe.hideAllDropdown();
        })
        ;
        $(document).on("click", ".hl_strike", (e) => {
        	$(e.target).closest("ul").find("*").removeClass("active");
        	$(e.target).closest("li").addClass("active");
            this.color = "red";
            this.type = "strike";
            e.preventDefault();
            spe.hideAllDropdown();
        })
        ;

    },
    setAnnotateValue: function (e) {
        var that = annotateEditor;
        console.log("set val to ", that.element_id);
        $(`.new_annotate[element-id='${that.element_id}']`).attr($(this).attr("id"), $(this).val());
        spe.toHistory({
            element_id: that.element_id,
            type: "annotate",
            element_content: "Edit annotate",
            element: $(`span[element-id='${that.element_id}'].new_annotate`).eq(0)
        });
    },
    openEditor: function (e) {
        var el_id = $(this).attr("element-id");
        annotateEditor.element_id = el_id;
        var title = $(this).attr("annotate_title");
        var content = $(this).attr("annotate_content");
        spe.editors[spe.current_editor].find("#annotate_title").val(title);
        spe.editors[spe.current_editor].find("#annotate_content").val(content);

        spe.editors[spe.current_editor].attr("element-id", el_id);
        spe.editors[spe.current_editor].css(spe.getEditorPosition($(e.target), false));
        spe.editors[spe.current_editor].show();
    },
    getTextRange: function (e) {
		spe.hideAllEditors();
    
        var that = annotateEditor;
        that.element_id = spe.uniq();

        if (!$(e.target).hasClass("text_content_element")) {
            return false;
        }
        if (window.eba_horosh) {
            return false;
        }
        var that = annotateEditor,
            text = that.getSelectedText(),
            template = $(`<span color='${that.color}' type='${that.type}' element-id='${that.element_id}' class='new_annotate ${that.type}'/>`);

        if (that.type == 'strike') {
            template.addClass("strike");
        } else {
            template.addClass("highlight");
            template.css({"background-color": that.color});
        }


        var selection = window.getSelection(),
            start_element = selection.anchorNode.parentNode,
            end_element = selection.focusNode.parentElement;

        var stop = 10;

        var position = selection.anchorNode.compareDocumentPosition(selection.focusNode),
            backward = false;
        // position == 0 if nodes are the same
        if (!position && selection.anchorOffset > selection.focusOffset || position === Node.DOCUMENT_POSITION_PRECEDING) {
            backward = true;
        }


        if (start_element == end_element) { //ONE STRING
            if (text != '') {
                var current_content = $(this).html();
                template.html(text);
                current_content = current_content.replace(text, template[0].outerHTML);
                $(this).html(current_content);
            }
        } else { //MULTIPLE STRING
            if (backward) {
            	var tt = start_element;
            	start_element = end_element;
            	end_element = tt;
                var temp_it = 0,
                    css = {"background": "green"};
                    

                while (start_element != end_element) {

                    if (start_element == end_element) {

                    } else if (temp_it === 0) {
                        var start_string = start_element.innerHTML.substring(0, selection.focusOffset);
                        var replaced_string = start_element.innerHTML.substring(selection.focusOffset, start_element.innerHTML.length);
                        template.html(replaced_string);
                        var new_html = (start_string + template[0].outerHTML);
                        $(start_element).html(new_html);
                    } else {
                        template.html(start_element.innerHTML);
                        $(start_element).html(template[0].outerHTML);
                    }

                    temp_it++;
                    if (!start_element.nextSibling) {
                        return false;
                    }
                    start_element = start_element.nextSibling;
                }
                
                //focusOffset

                var replaced_string = start_element.innerHTML.substring(0, selection.anchorOffset);
                var end_string = start_element.innerHTML.replace(/<[^>]+>(.*?)<\/[^>]+>/ig, "$1").substring(selection.anchorOffset, start_element.innerHTML.length);
                var st_end_string = start_element.innerHTML.replace(/<[^>]+>(.*?)<\/[^>]+>/ig, "$1").substring(0, selection.anchorOffset);
                
                
                template.html(replaced_string);
                var new_html = (template[0].outerHTML + end_string);
                $(start_element).html(new_html);
                
                
            } else {
                var temp_it = 0,
                    css = {"background": "green"};
                while (start_element != end_element) {
                    if (start_element == end_element) {

                    } else if (temp_it === 0) {
                        var start_string = start_element.innerHTML.substring(0, selection.anchorOffset);
                        var replaced_string = start_element.innerHTML.substring(selection.anchorOffset, start_element.innerHTML.length);
                        template.html(replaced_string);
                        var new_html = (start_string + template[0].outerHTML);
                        $(start_element).html(new_html);

                    } else {

                        template.html(start_element.innerHTML);
                        $(start_element).html(template[0].outerHTML);
                    }

                    temp_it++;
                    if (!start_element.nextSibling) {
                        return false;
                    }
                    start_element = start_element.nextSibling;
                }

                var replaced_string = start_element.innerHTML.substring(0, selection.focusOffset);
                var end_string = start_element.innerHTML.replace(/<[^>]+>(.*?)<\/[^>]+>/ig, "$1").substring(selection.focusOffset, start_element.innerHTML.length);
                var st_end_string = start_element.innerHTML.replace(/<[^>]+>(.*?)<\/[^>]+>/ig, "$1").substring(0, selection.focusOffset);
                
                template.html(replaced_string);
                var new_html = (template[0].outerHTML + end_string);
                $(start_element).html(new_html);
            }
        }
        window.getSelection().empty();
        $(end_element).find(".new_annotate").click();
        $(".new_annotate", end_element).attr("element")
        spe.toHistory({
            element_id: that.element_id,
            type: "annotate",
            element_content: "Add annotate",
            element: $(`span[element-id='${that.element_id}'].new_annotate`).eq(0)
        });
    },
    getSelectedText: function () {
        if (window.getSelection) {
            return window.getSelection().toString();
        } else if (document.selection) {
            return document.selection.createRange().text;
        }
        return '';
    }
};


var blocker = {
	editor_active: false,
    show: function () {
       // $(".pdf_preloader").show();
    },
    hide: function () {
        this.editor_active = false;
        $(".pdf_preloader").hide();
    },
    hideUploader: function () {
        $("#uploader_section").hide("slow");
    },
    showEditor: function () {
        $("#app-root").show();
        this.editor_active = true;
    }
};

var dragable_params = {scroll: false, drag: textEditor.onDrag};


//spe.init('simplePDFEditor');


//$(document).on("click", ".page .textLayer div", function(e){
//	e.preventDefault();
//	var element = $(this),
//		page = element.closest(".page"),
//		clone = element.clone();
//	clone.attr("contenteditable", true);
//	clone.addClass("editable_element");
//	clone.css("font-family", "sans-serif");
//
//	//element.css("background", "white");
//	element.addClass("edited");
//	page.append(clone);
//	clone.focus();
//
//});


//TODO comment
//spe.init({container_selector: "simplePDFEditor", external_url: "/pdf.pdf"});
//spe.init({container_selector: "simplePDFEditor", data: false});


$(document).on("keypress", ".editable_element", function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
    }
});


$(document).on("click", ".page .annotationLayer", function (e) {
	if($(e.target).attr("type")=='checkbox' && $(e.target).closest(".form_element").length>0){
		return true;
	}
	return false;
});


(function ($) {
    $.fn.getStyleObject = function () {
        var dom = this.get(0);
        var style;
        var returns = {};
        if (window.getComputedStyle) {
            var camelize = function (a, b) {
                return b.toUpperCase();
            };
            style = window.getComputedStyle(dom, null);
            for (var i = 0, l = style.length; i < l; i++) {
                var prop = style[i];
                var camel = prop.replace(/\-([a-z])/g, camelize);
                var val = style.getPropertyValue(prop);
                returns[camel] = val;
            }
            ;
            return returns;
        }
        ;
        if (style = dom.currentStyle) {
            for (var prop in style) {
                returns[prop] = style[prop];
            }
            ;
            return returns;
        }
        ;
        return this.css();
    }

    $.fn.copyCSS = function (source) {
        var styles = $(source).getStyleObject();
        this.css(styles);
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

})(jQuery);


jQuery(document).ready(function () {
	$(".page-container").on("click", function(e){
		if (e.target !== this){
			return;
		}
		$(".follow_the_mouse").remove();
        $(".active_image_moving").removeClass("active_image_moving")
	});
	
	$(document).on("click", "[current_editor='forms'], [current_editor='images'], [current_editor='sign']", function(e){
		if($(e.target).hasClass("textLayer")){
			spe.dissableAllSelection();
			spe.hideAllEditors();
		}
	});

	$(document).on("click", ".undo-table-wrap .thr", function(e){
		e.preventDefault();
	})


    $(".open_draw_modal").click(function (e) {
        e.preventDefault();
        $(".create-signature-modal").css("display", "flex");
        $(".tools-dropdown-menu").removeClass("active");
    });

    $('body').on('click', '#undo_table td', function () {
        let $checkbox = $(this).parent('tr').find('input[type="checkbox"]');
        $checkbox.prop("checked", !$checkbox.prop("checked"));
        $checkbox.change();
    });


    popuper.open('.open_search_modal', 'click', '#find-replace-modal');
    popuper.close('.close-replace-modal', 'click', '#find-replace-modal');

    $('body').on('click','#find-replace-modal .close-replace-modal',function () {

        $(".finded_element_hightlighter").each(function () {

            this.outerHTML = this.innerHTML;

        });

    });

    $(".signature-close").click(function (e) {
        e.preventDefault();
        $(".create-signature-modal").hide();
    });

    $(".signatore-btn-block").click(function () {
    	$(".signature-btn").hide();
    	if($(this).hasClass("draw_sign")){
    		$(".signature-btn").show();
    	}
    
        $(".signatore-btn-block").removeClass("signatore-btn-active");
        $(this).addClass("signatore-btn-active");
        $(".create-tab-block").hide();
        $(".create-tab-block").eq($(this).index()).show();
    });
});


function getBase64Image(img){
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.canvas.width = img.width;
    ctx.canvas.height = img.height;
    ctx.drawImage(img, 0, 0);
    var data_url = canvas.toDataURL();
    return $.when(data_url);
}


function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
    options = options || {};
    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }
    value = encodeURIComponent(value);
    var updatedCookie = name + "=" + value;
    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}


function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}


function throttle(func, limit) {
    let inThrottle
    return function () {
        const args = arguments
        const context = this
        if (!inThrottle) {
            func.apply(context, args)
            inThrottle = true
            setTimeout(() => inThrottle = false, limit
            )
        }
    }
}


function debounce(fn, delay) {
    var timer = null;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            fn.apply(context, args);
        }, delay);
    };
}





const typeSizes = {
  "undefined": () => 0,
  "boolean": () => 4,
  "number": () => 8,
  "string": item => 2 * item.length,
  "object": item => !item ? 0 : Object
    .keys(item)
    .reduce((total, key) => sizeOf(key) + sizeOf(item[key]) + total, 0)
};

const sizeOf = value => typeSizes[typeof value](value);



var $, detectBaseline, ref;

//$ = (ref = this.jQuery) != null ? ref : require('jQuery');
detectBaseline = function(el = 'body') {
  var $bigA, $container, $smallA;
  $container = $('<div style="visibility:hidden !important; opacity: 0 !important;"/>');
  $smallA = $('<span style="font-size:0;">A</span>');
  $bigA = $('<span style="font-size:999px;">A</span>');
  $container.append($smallA).append($bigA).appendTo(el);
  setTimeout((function() {
    return $container.remove();
  }), 10);
  return $smallA.position().top / $bigA.height();
};

$.fn.baseline = function() {
  return detectBaseline(this.get(0));
};


async function createFile2(url, filename, accessToken){
	let response = await fetch(url, {
		headers: new Headers({
		'Authorization': 'Bearer ' + accessToken, 
		}), 
	});
	let data = await response.blob();
	let metadata = {  type: 'application/pdf' };
	return new File([data], filename, metadata);
}

async function createFileFromDropbox(url, filename, accessToken=false){
	let response = await fetch(url);
	let data = await response.blob();
	let metadata = {  type: 'application/pdf' };
	return new File([data], filename, metadata);
}




function rgb2hex(orig){
    try{
        var rgb = orig.replace(/\s/g,'').match(/^rgba?\((\d+),(\d+),(\d+)/i);
        return (rgb && rgb.length === 4) ? "#" +
            ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
            ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
            ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : orig;
    }catch(er){
        console.error("cant convert rgb2hex",orig, er);
        return "#ebebeb";
     
    }
}

function detectColorOnClick(text=false, return_bg_color=false){
    var canvas = text.closest(".page").find("canvas")[0],
        ctx = canvas.getContext("2d"),
        canvasImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);


    function parseColors(canvasImgData, text, return_bg_color) {
        var data = canvasImgData.data,
            width = canvasImgData.width,
            imgWidth = width,
            height = canvasImgData.height,
            minVariance = 20;

        //rect = getBoundingClientRect();

        var left = Math.floor(parseInt(text.css("left"))),
            w = Math.round(parseInt(text.css("width"))),
            h = Math.round(parseInt(text.css("height"))),
            bottom = Math.round(parseInt(text.css("height")) + parseInt(text.css("top")) ),
            top = bottom - h,
            start = (left + (top * width)) * 4,
            color = [],
            best = Infinity,
            getRBG = function(x, y){
                return {
                    red: data[(imgWidth * y + x) * 4],
                    green: data[(imgWidth * y + x) * 4 + 1],
                    blue: data[(imgWidth * y + x) * 4 + 2]
                };
            };

        var all_colors = {};
       	var nt = top+(h/3);
       	
       	
        for(let i=left; i<w+left; i++){
            var x = getRBG(i, nt);
            if(typeof x['red']=='undefined'){
            	var hex = rgb2hex(`rgb(255,255,255)`);
            }else{
            	var hex = rgb2hex(`rgb(${x['red']},${x['green']},${x['blue']})`);
            }
            //console.log(hex)
            if(typeof all_colors[hex]=='undefined'){
                all_colors[hex] = 0;
            }
            all_colors[hex] ++;
           // ctx.fillStyle = "#FF0000";
           // ctx.fillRect(i,nt,1,1);
        }
        
        console.log("all colors is ", all_colors);
        
        
        var keys = Object.keys(all_colors);
        if(keys.length===0){
        	if(return_bg_color){
        		color = "rgb(255,255,255)";
        	}else{
            	color = "rgb(0,0,0)";
            }
        }else if(keys.length==1){
            color = keys[0];
        }else{
			var sorted = Object.keys(all_colors).sort((a,b) => all_colors[b]-all_colors[a]) 
			if(return_bg_color){
				color = sorted[0];
			}else{
				color = sorted[1];
			}
        }
        return color;
    }
    return  parseColors(canvasImageData, text, return_bg_color);
}


$(document).on("file_upload_percent_update", function(e, percent, speed){
		
	$(".current_speed_and_percent").html("<b>"+percent+"%</b> - "+bytesToSize(speed)+"/sec");
		

});



function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Byte';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}




jQuery.loadScript = function (url, callback) {
    jQuery.ajax({
        url: url,
        dataType: 'script',
        success: callback,
    });
}

