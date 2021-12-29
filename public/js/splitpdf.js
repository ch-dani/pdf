window.show_anyway = true;
window.skip_extract = 1;
var SplitPDF = {
	name: "SplitPDF",
	need_preview: false,
	tool_section: $(".split_section"),
	preview_section: $(".preview_section"),
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-burst",
	random_colors: [],
	pages_ranges: $("#pages_groups"),
	data: {ranges: false},
	fill_range: false,
	init: function(){
		this.bind();
	},
	bind: function(){
		var $this = this;
		$(document).on("click", ".edit-choose-block", $.proxy(this.splitEditor, this));
		$(document).on("click", "#start_split_by_pages", $.proxy(this.startSplitByPages, this));
		
		$(document).on("input", "#pages_groups", $.proxy(this.manualInput, this));
		
		$(document).on("click", "#extract_every_page", function(e){
			e.preventDefault();
			$this.everyPage();
		});
		$(document).on("click", "#reset_selected_pages", function(e){
			$($this.page_preview_items_selector).each(function(i, v){
				$(v).css("background-color", "inherit");
				$(v).removeClass($this.page_preview_selected_selector);
				$("#pages_groups").val("");
			});
			$this.data.ranges = false;
			return false;
		});
		$("#split_every").keyup(function(){
			if(parseInt($(this).val())>parseInt($(this).attr("max"))){
				return false;
			}
			$this.fillPageRanges();
		});
	},
	
	manualInput: function(e){
		var $this = this,
			val = $("#pages_groups").val();
		
		if(!val){
			return false;
		}
		splited = val.split(",");
//		if(typeof splited[1]=='undefined'){
//			splited[1] = splited[0];
//		}
		ranges = splited.map(function(t){
			return t.trim()
		});
		ranges = ranges.filter(function(t){
			return t!='';
		})
		console.log(ranges);

		$($this.page_preview_items_selector).css("background-color", "inherit"); 

		$($this.page_preview_items_selector).removeClass($this.page_preview_selected_selector);
		$this.fill_range = val; //parseInt($("#split_every").val());
		ranges = ranges.map(function(t){
		  var temp = t.split("-");
		  if(typeof temp[1]=='undefined' || isNaN(temp[1])){
		  	temp[1] = temp[0];
		  }
		  temp[0] = parseInt(temp[0]);
		  temp[1] = parseInt(temp[1]);
		  if(isNaN(temp[1])){
		  	temp[1] = parseInt(temp[0]);
		  }
		  console.log(temp);
		  
		  return Array(parseInt(temp[1])+1-parseInt(temp[0])).fill(parseInt(temp[0])).map((x, y) => x + y);	
		});

		var new_ranges = [];
		if($this.fill_range){
			$("#split_every").val($this.fill_range);
			var selection_groups = [], section_counter=0, ti = 1;
			$.each(ranges, function(ri, r){
				var last_page_in_range = Math.max(...r)-1;
				var last_el = $($this.page_preview_items_selector).eq(last_page_in_range);
			  last_el.addClass($this.page_preview_selected_selector);
				section_counter++;
				$.each(r, function(pi, pn){
				  $($this.page_preview_items_selector).eq(pn-1).css("background-color", $this.random_colors[section_counter]);
				});
			});
			
			$this.data.ranges = val.replace(/,$/i, "");
		}
	},
	startSplitByPages: function(){
		var $this = this;



		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, type: "byPage", ranges: $this.data.ranges, file_name: $this.file.name, "name_patern": $("#filename_patern").val()}).then($this.taskComplete);
			}
		} , 250);	



		return false;
	},
	everyPage: function(){
		var $this = this;


		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, type: "everyPage", file_name: $this.file.name, "name_patern": $("#filename_patern").val()}).then($this.taskComplete);
			}
		} , 250);	
		

	},

	splitEditor: function(e){
		var $this = this,
			element = $(e.currentTarget),
			editor_type = $(element).data("type");
		
		switch(editor_type){
			case 'every-page':
				$this.everyPage();
			break;
			case 'select-pages':
				$this.fill_range = false;
				$(".split_type_selector").addClass("hidden");
				$("#page_range_block").removeClass("hidden");
				$this.startPreview();
			break;
			case 'every-x':
				$(".split_type_selector").addClass("hidden");
				$("#page_every_block").removeClass("hidden");
				$this.fill_range = 4;
				$this.startPreview();
			break;
			case 'every-even':
				//$("#page_every_block").removeClass("hidden");
				$("#apply-popup").addClass("active");
				$(".creating_document").show();
				$(".create_file_box").hide();
				$(".apply_changes_1").html("Wait...");

				var $this = this,
					CMAP_URL = '/libs/pdfjs-dist/cmaps/',
					CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
					

				pdfUploader.getBlob($this.file).then((data)=>{
					var params = {
						data: data,
						//url: DEFAULT_URL,
						cMapUrl: CMAP_URL,
						cMapPacked: CMAP_PACKED,
					};
					return pdfjsLib.getDocument(params).promise.then(function(pdfDoc){ 
						var numPages = pdfDoc.numPages+1;
						var selection_groups = {};
						var group = 0;
						for(let i=1; i!=numPages; i++){
							if(typeof selection_groups[group]=='undefined'){
								selection_groups[group] = [];
							}
							selection_groups[group].push(i);
							if(i%2==1){
								group++;
							}
						}
						var group_text = "";

						$.each(selection_groups, function(i,group){
							var min = Math.min(...group),
								max = Math.max(...group);
							if(min==max){
								group_text += `${max},`;
							}else{
								group_text += `${min}-${max},`;
							}
						});

						$this.pages_ranges.val(group_text.replace(/,$/i, ""));
						$this.data.ranges = group_text.replace(/,$/i, "");


						PDFTOOLS.startTask()
						var intervalID = setInterval( function() { 
							console.log("upload progress is "+spe.upload_in_progress);
							if(!spe.upload_in_progress){
								clearInterval(intervalID);			
								$this.ajax({uuid: UUID, type: "byPage", ranges: $this.data.ranges}).then($this.taskComplete);
							}
						} , 250);	
						
						

					});
				});
				
					

			break;
		}
		return false;
	},
	
	fillPageRanges: function(){
		$this = this;	
		$($this.page_preview_items_selector).removeClass($this.page_preview_selected_selector);
		$this.fill_range = parseInt($("#split_every").val());
		if($this.fill_range){
			$("#split_every").val($this.fill_range);
			var selection_groups = [], section_counter=0,
				ti = 1;
			$($this.page_preview_items_selector).each(function(i, v){
				$(v).css("background-color", $this.random_colors[section_counter]);
				if(typeof selection_groups[section_counter]=='undefined'){
					selection_groups[section_counter] = [];
				}
				selection_groups[section_counter].push(i+1);
				console.log(i);
				if($this.fill_range && ti%$this.fill_range===0){
					section_counter++;
					$(v).addClass($this.page_preview_selected_selector);
				}
				ti++;
			});

			var group_text = "";

			$.each(selection_groups, function(i,group){
				var min = Math.min(...group),
					max = Math.max(...group);
				if(min==max){
					group_text += `${max},`;
				}else{
					group_text += `${min}-${max},`;
				}
			});

			$this.pages_ranges.val(group_text.replace(/,$/i, ""));
			$this.data.ranges = group_text.replace(/,$/i, "");
		}
	},
	renderPagesBlocks: function(pdfDoc, $this){ 
		$this = this;
		blocker.hide();
		$("#split_every").attr("max", pdfDoc.numPages+1);
		for(let i=1; i!=pdfDoc.numPages+1; i++){
			$this.pages_list.append($this.getPagePreviewTemplate({"page_num": i}));
			let tcanvas = $(`#page_canvas_${i}`)[0];
			$this.canvas_list[i] = tcanvas;
			pdfDoc.getPage(i).then(function(page){  $this.renderPageCanvas(page, $this) });
		}
		if($this.fill_range){
			$this.fillPageRanges();
		}
	},
	selectPage: function(e){
	
		$("#page_range_block").removeClass("hidden");
	
		var page = $(e.currentTarget),
			$this = this,
			sl = 0;
		let section_counter = 0;
		$this.data.ranges = false;
		$this.pages_ranges.val("");
		
		$($this.page_preview_items_selector).css("background-color", "initial");
	
		if(page.hasClass($this.page_preview_selected_selector)){
			page.removeClass($this.page_preview_selected_selector); //.css("background-color", "initial");
		}else{
			page.addClass($this.page_preview_selected_selector); //.css("background-color", $this.random_colors[sl]);
		}
		if($(`.${$this.page_preview_selected_selector}`).length==0){
			$($this.page_preview_items_selector).removeClass($this.page_preview_selected_selector).css("background-color", "initial");
			return false;
		}
		var selection_groups = {};
		$($this.page_preview_items_selector).each(function(i, v){
			$(v).css("background-color", $this.random_colors[section_counter]);
			if(typeof selection_groups[section_counter]=='undefined'){
				selection_groups[section_counter] = [];
			}
			selection_groups[section_counter].push(i+1);
			
			if($(v).hasClass($this.page_preview_selected_selector)){
				section_counter++;
			}
		});
	
		var group_text = "";
		
		$.each(selection_groups, function(i,group){
			var min = Math.min(...group),
				max = Math.max(...group);
			if(min==max){
				group_text += `${max},`;
			}else{
				group_text += `${min}-${max},`;
			}
		});
		
		$this.pages_ranges.val(group_text.replace(/,$/i, ""));
		$this.data.ranges = group_text.replace(/,$/i, "");
		return false;
	},

	startPreview: function(){
		var $this = this;
		$this.pages_ranges_block.removeClass("hidden");
		pdfUploader.getBlob($this.file).then((data)=>{
			var params = {filename: $this.file.name, size: $this.file.size, fileData: data};
			$(".footer-editor").addClass("active");
			$this.tool_section.removeClass("hidden");
			$this.preview_section.removeClass("hidden");
			$this.preview(params);
		});
	},
	
}
if(SplitPDF.tool_section.length>0){
	SplitPDF = $.extend(PDFTOOLS, SplitPDF);
	SplitPDF.main();
}

