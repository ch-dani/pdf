window.skip_extract = true;
window.show_anyway = true;

var SplitOutlinePDF = {
	name: "SplitOutlinePDF",
	need_preview: false,
	tool_section: $(".split_by_outline_section"),
	preview_section: $(".preview_section"),
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-split-by-bookmarks",
	random_colors: [],
	pages_ranges: $("#pages_groups"),
	data: {ranges: false},
	fill_range: false,
	page_preview_selected_selector: "selected_page_extract",
	page_preview_width: 200,
	outline_levels: [],
	hide_before_upload: true,
	init: function(){
		this.bind();
	},
	bind: function(){
		var $this = this;
		$(document).on("change", "input[name='change_bookmark_level']", $.proxy($this.changeBookmarkLevel, this));
		$(document).on("input", "input[name='bookmarks_contain']", $.proxy($this.filterBookmarks, this));
		$(document).on("click", "#start_task", $.proxy(this.save, this));
		
	},
	filterBookmarks: function(e){
		var val = $(e.target).val();
		$("input[name='change_bookmark_level'][value='1']").prop("checked", true);
		if(val==''){
			$("#outline_list li").removeClass("hidden");
		}
		$("#outline_list li").each(function(i,v){
			let tit = $(v).data("bookmark-title");
			let x = new RegExp(val, "i");
			if(tit.search(x)==-1){
				$(v).addClass("hidden");
			}else{
				$(v).removeClass("hidden");
			}
		});
	},
	changeBookmarkLevel: function(e){
		var element = $(e.target),
			level = parseInt(element.val());
		
		$("#outline_list li").each(function(i, v){
			var x = parseInt($(v).data("level-id"));
			if(x>=level){
				$(v).removeClass("hidden");
				$(v).find(".nbsp_bl").removeClass("hidden");
			}else{
				$(v).find(".nbsp_bl").removeClass("hidden");
				$(v).addClass("hidden");
			}
		});
	},
	fileSelected: function(prom, file){
		var $this = PDFTOOLS;
		$this.file = file;
		console.log(`PDFTOOL: ${$this.name} file selected`);
		
		$("span[data-value='filename']").html(file.name);
		if($this.hide_before_upload){
			$(".before_upload").addClass("hidden");
		}
		
		$this.scroolToSectionBegin();
		blocker.hide();
		$this.file_name = file.name;
		$(".current_filename", $this.tool_section).html(file.name);

		pdfUploader.getBlob(file).then((data)=>{
			var $this = PDFTOOLS;
			var params = {filename: file.name, size: file.size, fileData: data, password: PDF_PASSWORD};
//			$(".footer-editor").addClass("active");
			$this.tool_section.removeClass("hidden");
//			($this.preview_section.length>0)?$this.preview_section.removeClass("hidden"):"";

			var $this = this,
				CMAP_URL = '/libs/pdfjs-dist/cmaps/',
				CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
			var params = {
				data: params.fileData,
				//url: DEFAULT_URL,
				cMapUrl: CMAP_URL,
				cMapPacked: CMAP_PACKED,
				password: PDF_PASSWORD
			};

			$(document).trigger("before_start_preview_pages", [params]);
			loadingTask = pdfjsLib.getDocument(params);
			//$(document).trigger("pdf_loading_task", [loadingTask]);
			
			loadingTask.promise.then(function(pdfDoc_){ 
				console.log("pdfDoc_ is: ",pdfDoc_);
				$this.pdfDoc = pdfDoc_;
				pdfDoc_.getOutline().then((outline)=>{
					$this.outline = outline;
					if(outline !== null){
						$this.drawOutlines(outline, 0);
					}else{
						$("#outline_list").append("<li> No bookmarks found </li>");
						$("#start_task").attr("disabled", "disabled")
						swal("Error", "No bookmarks found", "error");
						return false;
					}
					 $(".bookmarks_levels").html("");
					$this.drawOutlineLevels();
				});
			});
		});
		$this.uploader_section.hide();
	},
	drawOutlines: function(outlines, level=0){
		$this = PDFTOOLS;
		$.each(outlines, function(i,v){
		
			var empty_str = "<span class='nbsp_bl'>&nbsp;&nbsp;&nbsp;&nbsp;</span>";  
			var li = $(`<li data-dest='${JSON.stringify(v.dest[0])}' data-bookmark-title="${v.title}" data-level-id="${level+1}">${empty_str.repeat(level)}${v.title}</li>`);
			$.when($this.pdfDoc.getPageIndex(v.dest[0])).then(function(pageIndex){
				pageIndex.then(function(pn){
					$(li).data("page-number", pn+1);
				})
			});
			$("#outline_list").append(li);
			$this.outline_levels[level] = {level: level+1};
			if(v.items!=null){
				level++;
				$this.drawOutlines(v.items, level);
				level--;
			}
		});
	},
	drawOutlineLevels: function(){
		$.each($this.outline_levels, function(i,v){
		   let is_checked = (v.level==1)?"checked":"";
		   let t = `<label class="resolution-item">
		        <input type="radio" name="change_bookmark_level" value="${v['level']}" ${is_checked}>
		        <span class="resolution-item-checkmark">
		            ${v['level']}
		        </span>
		    </label>`;		
		    $(".bookmarks_levels").append(t);
		})
	},
	save: function(e){
		e.preventDefault();
		var $this = this;
		var bookmarks = {};
		var file_n = 1;
		$("#outline_list li:not(.hidden)").each(function(i,v){
			bookmarks[file_n] = {"title": $(v).data("bookmark-title"), page_num: $(v).data("pageNumber")}
			file_n++;
		});
				
		if($.isEmptyObject(bookmarks)){
			swal("Error", "No bookmarks found", "error");
			return false;
		}
		$this.ajax({uuid: UUID, name_patern: $("input[name='filename_pattern']").val(), pages: bookmarks, file_name: $this.file.name}).then($this.taskComplete);
		return false;
	},

	showBottomPanel: function(doc){
		var $this = this;
		$this.fill_range = false;
		$("#pages_ranges").removeClass("hidden");
		$(".split_type_selector").addClass("hidden");
		$("#page_range_block").removeClass("hidden");
	},
	
}
if(SplitOutlinePDF.tool_section.length>0){
	SplitOutlinePDF = $.extend(PDFTOOLS, SplitOutlinePDF);
	SplitOutlinePDF.main();
}

