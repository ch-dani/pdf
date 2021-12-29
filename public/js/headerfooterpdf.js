window.show_anyway = true;
window.skip_extract = 0;
var HeadFootPDF = {
	name: "headerfooterpdf",
	need_preview: true,
	tool_section: $("#header_footer_section"),
	preview_block: false,
	preview_section: false,
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-header-footer",
	data: { 
		header_type: "hf-pages-arabic",
	},
	load_blob: true,
	file_data: false,
	page_preview_width: 612,
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	page_preview_items_selector: ".resize-margin-block",
	show_only_first_page: true,
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	bind: function(){
		var $this = this;
		$(document).on("click", "#start_task", $.proxy($this.save, this));
		$(document).on("change_pdf_header_type", $.proxy($this.changeHeaderType, this));

		$(document).on("pdftool_file_selected", function(ev, file){

		    pdfUploader.getBlob(file).then((data) => {
		        //var pdfjsLib = window['pdfjs-dist/build/pdf'];
				pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

		        var loadingTask = pdfjsLib.getDocument({data: data});
				$(document).trigger("pdf_loading_task", [loadingTask]);
				loadingTask.promise.catch(function(e){
					console.log("wow doge", e);
				});
			});
		
		
		});
		
	},
	changeHeaderType: function(ev, ht){
		this.data.header_type = ht;
	},
	save: function(e){
		var $this = this;
		var data = $.extend($this.data, {
			"font": $("input[name='format']:checked").val(),
			"location": $("#ft_loacation").val(),
			"font_size": $("#font_size").val(),
			"color": $("#text_color_input").val(),
			"margins":false,
			"text": $(".hf-format-customize:visible input[name='header_text']").val(),
			"text2": $("input[name='header_text2']").val(),
			"only_on_page": $("#display_on_page").val(),
			"start_from_page": $("#start_from_page").val()||1
		});
		
		$this.ajax({uuid: UUID, data: data, file_name: $this.file.name }).then($this.taskComplete);
		return false;
	},
	
	
}
if(HeadFootPDF.tool_section.length>0){
	HeadFootPDF = $.extend(PDFTOOLS, HeadFootPDF);
	HeadFootPDF.main();
}

