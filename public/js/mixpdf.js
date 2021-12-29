var MixPDF = {
	name: "MixPDF",
	need_preview: false,
	tool_section: $("#mix_pdf_section"),
	files_list: $("#files_list"),
	preview_section: false,
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-mix",
	data: { files: {} },
	file_data: false,
	current_letter: 1,
	letter: "A",
	session: "",
	multiple_upload: true,
	init: function(){
		console.log("init mix pdf");
		this.bind();
	},
	
	getFilesSettings: function(){
		var ret = {};
		var fn = 1;
		$(".file_block").each(function(){
			ret[fn]	= {
				name: $(".alt-mix-title .fnm", $(this)).html(),
				document_switch: $(".switch_pages_after", $(this)).val(),
				document_switch: $(".switch_pages_after", $(this)).val(),
				pages_count: $(this).attr("pages-count"),
				pages_ordering: $("input[type=radio]:checked", $(this)).val()
			}
			fn++;
		});
		return ret;
	},
	bind: function(){
		var $this = this;
		$(document).on("before_upload_file", (file) => {
			var $this = this;
			$this.letter = String.fromCharCode(64 + $this.current_letter);
			$this.current_letter++;
		});
		$(document).on("click", ".order-btn input", (e)=>{
			var block = $(e.target).closest(".switch_pages_b"),
				file_n = block.closest(".file_block").data("file-n");
			block.find(".order-btn-active").removeClass("order-btn-active");
			$(e.target).closest(".order-btn").addClass("order-btn-active");
			$this.data.files[file_n]['pages_ordering'] =  $(e.target).val();
			
		});
		
		$(document).on("input", ".switch_pages_after", function(e){
			var val = $(this).val(),
				block = $(this).closest(".file_block"),
				file_n = block.data("file-n");
			if(/^-?\d*$/.test(val)){
				val = val;
			}else{
				val = 1;
				$(this).val(1);
			}
			
			$this.data.files[file_n].document_switch = val;
			
		});
		$(document).on("click", ".change_sort", $.proxy(this.changeSort, this));
		$(document).on("click", ".save_file", $.proxy(this.save, this));
		$(document).on("click", ".delete_file", $.proxy(this.deleteFile, this));
		
		
	},
	deleteFile: function(e){
		var element = $(e.target),
			$this = this,
			block = element.closest(".file_block"),
			file_num = block.data("file-n");
		block.remove();
		delete $this.data.files[file_num];
		return false;
	},
	changeSort: function(e){
		var $this = this,
			element = $(e.target),
			sort = $(element).data("sort");
		var files = $(".file_block", $this.files_list);
		$this.data.files = {};
		files.sort(function(a, b){
			if(sort=='asc'){
				return $(".fnm", a).html().localeCompare($(".fnm", b).html())
			}else{
				return $(".fnm", b).html().localeCompare($(".fnm", a).html())
			}
		});
		
		let it = 1;
		$.each(files, function(i, v){
			$(files).eq(i).attr("data-file-n", it);
			$this.data.files[it] = {
				name: $(".fnm", v).html(), 
				document_switch: $(".switch_pages_after", v).val(), 
				pages_ordering: $("input:checked", v).val(),
				pages_count: $(".switch_pages_after", $(v)).attr("max")
			};
			it++;
		});
		
		$this.files_list.html(files);
		
		return false;
	},
	fileSelected: function(prom, file){
		var $this = this;
		$this.file = file;
		console.log(`PDFTOOL: ${$this.name} file selected`);
		
		$this.data.files[$this.current_letter-1] = {name: file.name, document_switch: 1, pages_ordering: "regular", pages_count: 0};
		
		$this.scroolToSectionBegin();
		blocker.hide();
		$this.file_name = file.name;
		$this.tool_section.removeClass("hidden");
		$("#start_mix_panel").removeClass("hidden");
		$this.uploader_section.hide();
		$this.files_list.append($this.createNewFileBlock(file));

		var file_num = $this.current_letter-1;
		console.log("file selected");
		
		pdfUploader.getBlob(file).then((data)=>{
			var params = {filename: file.name, size: file.size, fileData: data};
			var $this = this,
				CMAP_URL = '/libs/pdfjs-dist/cmaps/',
				CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
			var params = {
			    data: params.fileData,
			    //url: DEFAULT_URL,
			    cMapUrl: CMAP_URL,
			    cMapPacked: CMAP_PACKED,
			    file_num: file_num,
			    password: PDF_PASSWORD
			};
			
			var loadingTask = pdfjsLib.getDocument(params)

			//$(document).trigger("pdf_loading_task", [loadingTask]);
			
			
			loadingTask.promise.then(function(pdfDoc){
				var fn = pdfDoc.loadingTask.file_num;
				//alert(`#file_block_${$this.current_letter-1} .pages_count`+ "__" + pdfDoc.pdfInfo.numPages);
				
				$(`#file_block_${fn}`).attr("pages-count", pdfDoc.pdfInfo.numPages);
				
				$(`#file_block_${fn} .pages_count`).html(`(Total pages: ${pdfDoc.pdfInfo.numPages})`);
				$(`#file_block_${fn} .switch_pages_after`).attr("max", pdfDoc.pdfInfo.numPages).val(1);
				$this.data.files[fn]['pages_count'] = pdfDoc.pdfInfo.numPages;
			});

		});
		
		
		$("#files_list").sortable({
			connectedSortable: "#files_list",
			stop: function(){
			
			}
		}).disableSelection()
		

	},
	createNewFileBlock: function(file){
		var $this = this;
		return `
			<div class="alt-mix-block file_block" id="file_block_${this.current_letter-1}" data-file-n='${this.current_letter-1}' data-file-id="${$this.letter}"  >
				<span class="alt-close-block">
					<a href='#' class='delete_file'>+</a>
					<span class='file_num'>${this.current_letter-1}</span>
				</span>
				<div class="alt-mix-title">
					<img src="img/document-edit-icc-1.svg" alt="Alternate Text">
					<span class='fnm'>${file.name}</span> <span class='pages_count' style='font-size: 10px;'>(Total pages: 0)</span>
				</div>
				<div class="pick-pages-btn switch_pages_b">
					<p>Pick pages in: </p>
					<label class='order-edit-btn regular-order-btn order-btn order-btn-active'><input checked type='radio' name='porder[${this.letter}]' value='regular'>Regular Order</label>
					<label class='order-edit-btn reverse-order-btn order-btn'><input type='radio' name='porder[${this.letter}]' value='reverse'>Reverse Order</label>
				</div>		

				<div class="switch-alt-mix">
					<p>Switch document after reading</p>
						<input type="number" max="" class="switch_pages_after" pattern="[\\d+]" value="1" />
					<p>pages</p>
					
				</div>
			</div>`;
	},
	
	save: function(e){
		e.preventDefault();
		var $this = this;
		if(!$("#files_list_form").is(":valid")){
			swal("Error", "Switch document after reading pages > document pages count", "error")
			return false;
		}
		
		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);
				setTimeout(function(){
					$this.data.files = PDFTOOLS.getFilesSettings();
					$this.ajax({uuid: UUID, files: $this.data.files, file_name: $this.file.name}).then($this.taskComplete);
				},400)			

			}
		} , 250);	


		return false;
	},
	
}
if(MixPDF.tool_section.length>0){
	window.skip_extract = 1;
	MixPDF = $.extend(PDFTOOLS, MixPDF);
	MixPDF.main();
}

