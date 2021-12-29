window.show_anyway = true;
window.skip_extract = 1;
PDF_PASSWORD = '';
var EncryptPDF = {
	name: "EncryptPDF",
	maxPages: 2,
	need_preview: false,
	tool_section: $("#encrypt_section"),
	preview_section: false,
	csfr: false,
	selectable_pages: false,
	toolurl: "/pdf-encrypt",
	data: { pages: {} },
	load_blob: false,
	file_data: false,
	page_preview_width: 172,
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	bind: function(){
		$(document).on("click", "#start_task", $.proxy(this.save, this));

		$(document).on("pdftool_file_selected", function(ev, file){

			pdfUploader.getBlob(file).then((data) => {
				pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';
				var loadingTask = pdfjsLib.getDocument({data: data});
				loadingTask.onPassword = function(){
					swal("Error", "Document already encrypted");
					setTimeout(function(){
						window.location.reload();
					}, 3000);
				}
			});
		});
	},
	save: function(e){
		var $this = this;
		
		if(!$("input[name='password_open']").val()){
			//swal("Error", "Please, enter password", "error");
			//return false;
		}
		var form_data = $("#encrypt_form").serializeForm();

		PDFTOOLS.startTask();
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, options: form_data, file_name: $this.file.name }).then($this.taskComplete);
			}
		} , 250);
		return false;
	},
	fileSelected: function(prom, file){
		EncryptPDF.file_name = file.name;
		$this = EncryptPDF;
		$this.file = file;
		var params = {filename: $this.file.name, size: $this.file.size, fileData: $this.file_data};

		pdfUploader.getBlob(file).then((data) => {
			var params = {filename: file.name, size: file.size, fileData: data};

			EncryptPDF.preview(params);
			$(".r_upload_section").hide();
			$("#rotate_section").removeClass("hidden");
			$("#zoom_section").removeClass("hidden");
			$(".footer-editor").addClass("active");
			$(".file_name_here").html($this.file.name);

		});
	},
	getPagePreviewTemplate: function(params){
		return `
			<div id="page_preview_${params['page_num']}" class="crop-section__page crop-section__page-small" data-pagenum="${params['page_num']}">
				<div class="split-main-num"></div>
				<div class="split-main-photo">
					<div class="canvas_padding_outer">
						<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
					</div>
				</div>
			</div>
		`;
	},
}

if(EncryptPDF.tool_section.length>0){
	converterTool = EncryptPDF = $.extend(PDFTOOLS, EncryptPDF);
	EncryptPDF.main();
}

$.fn.serializeForm = function(){
	var ret = {};
	$("input", this).each(function(i, v){
		if($(v).is(":radio") || $(v).is(":checkbox")){
			if($(v).is(":checked")){
				var val = $(v).val();
			}else{
				var val = -1;
			}
		}else{
			var val = $(v).val()
		}
		let el_name = $(v).attr("name");
		let ke = el_name.replace("[]", '');
		if(typeof ret[ke]==='undefined'){
			ret[ke] = [];
		}
		if(val!=-1){
			if(el_name.search(/\[\]/)!=-1){
				ret[ke].push(val);
			}else{
				ret[el_name] = val;
			}
		}
	});
	return ret;
}


