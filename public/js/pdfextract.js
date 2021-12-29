window.show_anyway = true;
window.skip_extract = true;

var ExtractPDF = {
	name: "ExtractPDF",
	need_preview: true,
	tool_section: $(".split_section"),
	preview_section: $(".preview_section"),
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-extract-pages",
	random_colors: [],
	pages_ranges: $("#pages_groups"),
	data: {ranges: false},
	fill_range: false,
	page_preview_selected_selector: "selected_page_extract",
	page_preview_width: 200,
	init: function(){
		this.bind();
	},
	bind: function(){
		var $this = this;
		$(document).on("before_render_pages_blocks", $.proxy($this.showBottomPanel, this));
		$(document).on("click", "input[name='selection_type']", $.proxy($this.changeSelection, this));
		
		$(document).on("click", "#reset_selected_pages", function(e){
			$($this.page_preview_items_selector).each(function(i, v){
				$(v).css("background-color", "inherit");
				$(v).removeClass($this.page_preview_selected_selector);
				$("#pages_groups").val("");
			});
			$this.data.ranges = false;
			return false;
		});
		
		$(document).on("keyup", "#pages_groups", function(){
			var val = $(this).val()
			PDFTOOLS.data.ranges = val;
			PDFTOOLS.setRanges();
		});
		
		$(document).on("click", "#start_task", $.proxy($this.save, this));
		
//		$("#split_every").keyup(function(){
//			if(parseInt($(this).val())>parseInt($(this).attr("max"))){
//				return false;
//			}
//			
//		});
	},
	setRanges: function(){
		var ranges = $("#pages_groups").val();
		var temp_range = ranges.split(','),
		range_arr = [];

		$.each(temp_range, function(i,v){
			sb = v.split("-");
			if(sb.length>1 && typeof sb[0]!=='undefined' && typeof sb[1]!=='undefined' && sb[1]){
				//console.log("sb is", sb[0], sb[1]);
				range_arr = range_arr.concat(range(parseInt(sb[0]),parseInt(sb[1]),1));
			}else{
				if(v){
					range_arr.push(parseInt(v))
				}
			}
		});
		
		console.log(range_arr);

		$("#pages_previews_here .preview_page_block").each(function(i,v){
			var it = i+1;
			if(range_arr.indexOf(it)!=-1){
				$(this).addClass("selected_page").addClass("selected_page_extract")
			}else{
				$(this).removeClass("selected_page_extract").removeClass("selected_page")
			}
		});
	},
	save: function(e){
		e.preventDefault();
		var $this = this;
		if(!$this.data.ranges){
			swal("Error", "Please, select pages", "error");
			return false;
		}


		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, "discard_bookmarks": $("#discard_bookmarks").is(":checked")?1:0,  ranges: $this.data.ranges, file_name: $this.file.name}).then($this.taskComplete);
			}
		} , 250);	

		return false;
	},
	changeSelection: function(e){
		var $this = this,
			el = $(e.target),
			val = el.val();
		switch(val){
			default:
			case 'manual':
				
			break;
			case 'odd':
				$(".extract_block_not_split").removeClass("selected_page selected_page_extract");
				$(".extract_block_not_split:even").addClass("selected_page selected_page_extract");
			break;
			case 'even':
				$(".extract_block_not_split").removeClass("selected_page selected_page_extract");
				$(".extract_block_not_split:odd").addClass("selected_page selected_page_extract");
			break;
		}
		$this.getRanges();
	},
	showBottomPanel: function(doc){
		var $this = this;
		$this.fill_range = false;
		$("#pages_ranges").removeClass("hidden");
		$(".split_type_selector").addClass("hidden");
		$("#page_range_block").removeClass("hidden");
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
		
		$("input[name='selection_type'][value='manual']").prop("checked", true);
	
		var page = $(e.currentTarget),
			$this = this,
			sl = 0;
		let section_counter = 0;
		$this.data.ranges = false;
		$this.pages_ranges.val("");
		
		page.addClass("selected_page");

		if(page.hasClass($this.page_preview_selected_selector)){
			page.removeClass($this.page_preview_selected_selector); //.css("background-color", "initial");
		}else{
			page.addClass($this.page_preview_selected_selector); //.css("background-color", $this.random_colors[sl]);
		}
		
		if($(`.${$this.page_preview_selected_selector}`).length==0){
			//$($this.page_preview_items_selector).removeClass($this.page_preview_selected_selector).css("background-color", "initial");
			return false;
		}
		$this.getRanges();

		return false;
	},
	
	getRanges: function(){
		var $this = this;
		section_counter = 0;
		selection_groups = {};
		var prev_element = false;
		$($this.page_preview_items_selector).each(function(i, v){
			if($(v).hasClass($this.page_preview_selected_selector)){
				if(prev_element && !prev_element.hasClass($this.page_preview_selected_selector) ){					
					section_counter++;
				}
				if(typeof selection_groups[section_counter]=='undefined'){
					selection_groups[section_counter] = [];
				}
				selection_groups[section_counter].push(i+1);
			}
			prev_element = $(v);
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
	},
	startPreview: function(){
		alert();
		var $this = this;
		$this.pages_ranges_block.removeClass("hidden");
		pdfUploader.getBlob($this.file).then((data)=>{
			var params = {filename: $this.file.name, size: $this.file.size, fileData: data};
			$(".footer-editor").addClass("active");
			$this.tool_section.removeClass("hidden")
			$this.preview_section.removeClass("hidden");
			$this.preview(params);
		});
	},

	getPagePreviewTemplate(params){
		return `
			<div class="preview_page_block split-main-block extract_block_not_split page_${params['page_num']}" data-page-id='${params['page_num']}'>
				<div class="split-main-num">${params['page_num']}</div>
				<div class="split-main-photo">
					<canvas data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
				</div>
			</div>
		`;
	},

	
}
if(ExtractPDF.tool_section.length>0){
	ExtractPDF = $.extend(PDFTOOLS, ExtractPDF);
	ExtractPDF.main();
}

const range = (start, stop, step = 1) =>
  Array(Math.ceil((stop+1 - start) / step)).fill(start).map((x, y) => x + y * step)

