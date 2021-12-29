window.skip_extract = true;
var SplitBySizePDF = {
	name: "SplitBySizePDF",
	need_preview: false,
	tool_section: $("#split_by_size_section"),
	preview_section: false,
	csfr: false,
	pages_list: false,
	pages_ranges_blocs: false,
	preview_block: false,
	selectable_pages: true,
	toolurl: "/pdf-split-by-size",
	random_colors: [],
	pages_ranges: false,
	data: {ranges: false},
	fill_range: false,
	init: function(){
		this.bind();
	},
	bind: function(){
		var $this = this;
		
		$(document).on("click", "#start_task", $.proxy($this.save, this));


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

		
//		$(document).on("click", ".edit-choose-block", $.proxy(this.splitEditor, this));
//		$(document).on("click", "#start_split_by_pages", $.proxy(this.startSplitByPages, this));
//		$(document).on("click", "#extract_every_page", function(e){
//			e.preventDefault();
//			$this.everyPage();
//		});
//		$(document).on("click", "#reset_selected_pages", function(e){
//			$($this.page_preview_items_selector).each(function(i, v){
//				$(v).css("background-color", "inherit");
//				$(v).removeClass($this.page_preview_selected_selector);
//				$("#pages_groups").val("");
//			});
//			$this.data.ranges = false;
//			return false;
//		});
//		$("#split_every").keyup(function(){
//			if(parseInt($(this).val())>parseInt($(this).attr("max"))){
//				return false;
//			}
//			$this.fillPageRanges();
//		});
	},
	save: function(e){
		e.preventDefault();
		var $this = this;

		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, unit: $("input[name='size_unit']:checked").val(), split_size: $("#split_size").val(),  file_name: $this.file.name}).then($this.taskComplete);
			}
		} , 250);	



		return false;
	},
	
	
}
if(SplitBySizePDF.tool_section.length>0){
	SplitBySizePDF = $.extend(PDFTOOLS, SplitBySizePDF);
	SplitBySizePDF.main();
}

