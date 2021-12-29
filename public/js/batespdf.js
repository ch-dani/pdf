window.skip_extract = 0;
window.show_anyway = true;
var BatesPDF = {
	name: "batespdf",
	need_preview: true,
	tool_section: $("#bates_section"),
	preview_block: false,
	preview_section: false,
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-bates-numbering",
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
		$(document).on("input", ".exhb_input", $.proxy($this.inputExhb, this));
		$(document).on("input", ".bates_inp", $.proxy($this.inputBates, this));
		$(document).on("input", "#exhb_custom", $.proxy($this.customExhb, this));
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
		
		
		$(document).on("change", "select[name='bates_type']", function(){
			var template = $this.exhbPreviewTemplates($(this).val());
			var preview = $(".bates-preview");
			preview.html(template);
		});
		
	},
	customExhb: function(e){
		var inp = $(e.target),
			val = inp.val();
		
		var t1 = "Preview: <br>File 1, Page 1: "+val.replace("[FILE_NUMBER]", "1").replace("[BATES_NUMBER]", "000001");
		var t2 = "<br>File 1, Page 2: "+val.replace("[FILE_NUMBER]", "1").replace("[BATES_NUMBER]", "000002")+"<br>...";
		var t3 = "<br>File 10, Page 1: "+val.replace("[FILE_NUMBER]", "10").replace("[BATES_NUMBER]", "000123")+"";
		var new_preview = t1+t2+t3
		
		$(".bates-preview").html(new_preview)
	},
	inputBates: function(e){
		var inp = $(e.target);
		$(".bates_val").html(inp.val());
	},	
	inputExhb: function(e){
		var inp = $(e.target);
		$(".exhb_val").html(inp.val());
	},
	changeHeaderType: function(ev, ht){
		this.data.header_type = ht;
	},
	
	exhbPreviewTemplates: function(type){
		var templates = {
			"bates-with-exhibit": `
				Preview:<br>
				File 1, Page 1: <span class='exhb_val'>Exhibit</span> 1 <span class='bates_val'>Case XYZ</span> 000001<br>
				File1, Page 2:  <span class='exhb_val'>Exhibit</span> 1 <span class='bates_val'>Case XYZ</span> 000002<br>
				...<br>
				File 10, Page 1: <span class='exhb_val'>Exhibit</span> 10 <span class='bates_val'>Case XYZ</span> 000123
			`,
			"full-bates": `
				Preview:<br>
				File 1, Page 1: <span class='bates_val'>Case XYZ</span> 000001<br>
				File1, Page 2: <span class='bates_val'>Case XYZ</span> 000002<br>
				...<br>
				File 10, Page 1: <span class='bates_val'>Case XYZ</span> 000123			
			`,
			"bates-with-exhibit-3-digits": `
				Preview:<br>
				File 1, Page 1: <span class='exhb_val'>Exhibit</span> 1 <span class='bates_val'>Case XYZ</span> 001<br>
				File1, Page 2:  <span class='exhb_val'>Exhibit</span> 1 <span class='bates_val'>Case XYZ</span> 002<br>
				...<br>
				File 10, Page 1: <span class='exhb_val'>Exhibit</span> 10 <span class='bates_val'>Case XYZ</span> 123
			`,
			"full-bates-3-digits": `
				Preview:<br>
				File 1, Page 1: <span class='bates_val'>Case XYZ</span> 001<br>
				File1, Page 2: <span class='bates_val'>Case XYZ</span> 002<br>
				...<br>
				File 10, Page 1: <span class='bates_val'>Case XYZ</span> 123			
			`,
			
			"just-number": `
				Preview:<br>File 1, Page 1: 000001<br>File1, Page 2: 000002<br>...<br>File 10, Page 1: 000123
			`,
			"just-number-3-digits":`
				Preview:<br>File 1, Page 1: 001<br>File1, Page 2: 002<br>...<br>File 10, Page 1: 123
			`,
			"bates-custom":`
				Preview:<br>File 1, Page 1: Exh1ibit 1 Case XYZ 000001<br>File1, Page 2: Exh1ibit 1 Case XYZ 000002<br>...<br>File 10, Page 1: Exh1ibit 10 Case XYZ 000123			
			`
			
		};
	
		return templates[type];
	
	},
	save: function(e){
		var $this = this;
		var exhb_type = $("select[name='bates_type']").val();
		
		
		var data = {
			exhb: $("select[name='bates_type']").val(),
			margins: $("[name=addMargins]:checked").val(),
			user_inp_1: $(".exhb_input:visible").val(),
			user_inp_2: $(".bates_inp:visible").val(),
			user_inp_3: $("#exhb_custom").val(),
			location: $("select[name='pageLocation']").val(),
			bates_start_from: $("input[name='bates_start_from']").val()||1,
			file_start_from: $("input[name='file_start_from']").val()||1,
			font: $("input[name='format']:checked").val()||"courier2",
			font_size: $("#font_size").val()||10,
			color: $("#text_color_input").val(),
			file_patern: $("input[name='outputFilenamePattern']").val()
			
		
		};


		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
			$this.ajax({uuid: UUID, data: data, file_name: $this.file.name }).then($this.taskComplete);

			}
		} , 250);	
		
		//$this.ajax({uuid: UUID, data: data, file_name: $this.file.name }).then($this.taskComplete);
		return false;
	},
	
	
}
if(BatesPDF.tool_section.length>0){
	BatesPDF = $.extend(PDFTOOLS, BatesPDF);
	BatesPDF.main();
}

