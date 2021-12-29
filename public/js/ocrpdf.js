window.show_anyway = true;
window.skip_extract = 0;
var OcrPDF = {
	name: "ocrpdf",
	need_preview: true,
	tool_section: $("#ocr_section"),
	preview_block: $(".pages_preview_block"),
	preview_section: $("#ocr_section"),
	csfr: false,
	selectable_pages: true,
	toolurl: "/pdf-ocr",
	data: { },
	load_blob: true,
	file_data: false,
	page_preview_width: 1065,
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	page_preview_items_selector: ".resize-margin-block",
	show_only_first_page: false,
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	bind: function(){
		var $this = this;
		$(document).on("click", "#start_task", $.proxy($this.save, this));
		
		$(document).on("change", ".lang_select", function(){
			var lang = $(this).val();
			var ss = $(this);
			
			$(".lang_select").each(function(){
				if($(this)!=ss){
					$(this).find(`option[value='${lang}']`).eq(0).prop("selected", "selected");
				}
			})
		
		});
		
		$(document).on("file_selected", function(){
			$(".after_upload").removeClass("hidden");
		});
		
		$(document).on("click", ".recognize_text", $.proxy($this.startRecognize, this));
		$(document).on("after_set_canvas_size", function(e, pn, w, h){
			$(`#canvas_overlay_${pn}`)[0].width = w;
			$(`#canvas_overlay_${pn}`)[0].height = h;
		});
		$(document).on("click", ".save_as_file", (e)=>{ e.preventDefault(); $this.savePageAsFile($(e.target)); })
		$(document).on("click", ".copy_to_clipboard", (e)=>{ e.preventDefault(); 
			$this.copyToClipboard($(e.target).closest(".ocr_result").find("pre")); })		
	},
	copyToClipboard: function(element){
		element = element[0];
		console.log(element);
		if (document.selection) { 
			var range = document.body.createTextRange();
			range.moveToElementText(element);
			range.select().createTextRange();
			document.execCommand("copy"); 
		} else if (window.getSelection){
			var range = document.createRange();
			range.selectNode(element);
			window.getSelection().addRange(range);
			document.execCommand("copy");
		}
		swal("", "Text copied")
	},
	savePageAsFile: function(el){
		var blob = new Blob([el.closest(".ocr_result").find("pre").text()], {type: 'text/plain'});
		var downloadUrl = URL.createObjectURL(blob);
		var a = document.createElement("a");
		a.href = downloadUrl;
		a.download = PDFTOOLS.file_name+".txt";
		document.body.appendChild(a);
		a.click();
	},
	startRecognize: function(e){
		e.preventDefault();

        var $this = this,
			page_item = $(e.target).closest(".page_item"),
			textFrom = $(".page_canvas", page_item)[0],
			overlay =  $(".input_overlay", page_item)[0],
			log = $(".ocr_log", page_item)[0],
            result_block = $(".ocr_result", page_item);

        window.start_task_popup = false;

        $(".before_ocr", page_item).addClass("hidden");
        $(".ocr_progress", page_item).removeClass("hidden");
        var progressbar = $(".progressbarc_c", page_item),
            status = $(".current_task_text", page_item),
            result_block = $(".ocr_result", page_item);

        var progressbar = $(".progressbarc_c", page_item),
            status = $(".current_task_text", page_item),
            result_block = $(".ocr_result", page_item);

        $(document).trigger("update_ocr_progress", [progressbar, 0]);
        progressbar.find('#count').text("0%");

        let c = 1;
        var cc = setInterval( () => {
        	if (c > 8)
        		clearInterval(cc);
            	progressbar.find('#count').text(Math.ceil(c * 10) + "%");
            	$(document).trigger("update_ocr_progress", [progressbar, c * 10]);
        	c++;
        }, 1500);

        var intervalID = setInterval( () => {
            console.log("upload progress is "+spe.upload_in_progress);
            if(!spe.upload_in_progress){
                clearInterval(intervalID);
                PDFTOOLS.ajax({uuid: UUID, type: 'text', page_item: page_item.index(), lang: $(".lang_select").val(), file_name: $this.file.name }).then(function (result) {
                    $(".ocr_progress", page_item).addClass("hidden");
                    result_block.removeClass("hidden");
                    $this.showResult(result, overlay, result_block);
                });

            }
        } , 250);
		
		/*$(".before_ocr", page_item).addClass("hidden");
		$(".ocr_progress", page_item).removeClass("hidden");
		var current_action = $(".ocr_current_action", page_item);
		
		
		var progressbar = $(".progressbarc_c", page_item),
			status = $(".current_task_text", page_item),
			result_block = $(".ocr_result", page_item);
			
			

		window.Tesseract = Tesseract.create({
			workerPath: "https://cdn.jsdelivr.net/gh/naptha/tesseract.js@1.0.9/dist/worker.min.js",
			langPath: "https://cdn.jsdelivr.net/gh/naptha/tessdata@d111fa78396c2b897079b45cfcf527fb1c5f2120/3.02/",
			corePath: "https://cdn.jsdelivr.net/gh/naptha/tesseract.js-core@0.1.0/index.js"
		});
		var lang = $(".lang_select", page_item).val();
		Tesseract.recognize(textFrom, lang)
		.progress((packet)=> {  
			$this.progressUpdate(packet, log, progressbar, status, current_action)
		})
		.then((result)=> {
			$(".ocr_progress", page_item).addClass("hidden");
			result_block.removeClass("hidden");
			$this.showResult(result, overlay, result_block); 
		})*/
	},
	
	showResult: function(result, result_canvas, result_block){

		if (typeof result.text !== 'undefined')
			$(".ocr_result_content", result_block).html("<pre>"+result.text+"</pre>");

		var input_overlay = result_canvas;
		var ioctx = input_overlay.getContext('2d')

		/*result.words.forEach(function(w){
			var b = w.bbox;
			ioctx.strokeWidth = 2
			ioctx.strokeStyle = 'red'
			ioctx.strokeRect(b.x0, b.y0, b.x1-b.x0, b.y1-b.y0)
			ioctx.beginPath()
			ioctx.moveTo(w.baseline.x0, w.baseline.y0)
			ioctx.lineTo(w.baseline.x1, w.baseline.y1)
			ioctx.strokeStyle = 'green'
			ioctx.stroke()
		});*/
	
	},
	progressUpdate: function(packet, log, progressbar, status_block, action_block){
		if(false){ //log.firstChild && log.firstChild.status === packet.status){
			//progressbar.animate(0);
			console.log("if ");
			if('progress' in packet){
				var progress = log.firstChild.querySelector('progress')
				progress.value = packet.progress
			}
			
		}else{
			var line = document.createElement('div');
			line.status = packet.status;
			var status = document.createElement('div')
			status.className = 'status'
			//status.appendChild(document.createTextNode(packet.status))
//			line.appendChild(status)
//			if('progress' in packet){
//				var progress = document.createElement('progress')
//				progress.value = packet.progress
//				progress.max = 1
//				line.appendChild(progress)
//			}
			status_block.html(packet.status);
			progressbar.find('#count').text(Math.ceil(packet.progress * 100) + "%");
			
			
			$(document).trigger("update_ocr_progress", [progressbar, packet.progress * 100])
			
//		console.log("packet.progress", packet.progress);

//			progressbar.animate(packet.progress);

//			if(packet.status == 'done'){
//				var pre = document.createElement('pre')
//				pre.appendChild(document.createTextNode(packet.data.text))
//				line.innerHTML = ''
//				line.appendChild(pre)
//			}

			//log.insertBefore(line, log.firstChild)
		}
		
		action_block.html(packet.status);
		
	},

	save: function(e){
		var $this = this;
//		var exhb_type = $("select[name='bates_type']").val();
//		
//		
//		var data = {
//			exhb: $("select[name='bates_type']").val(),
//			user_inp_1: $(".exhb_input:visible").val(),
//			user_inp_2: $(".bates_inp:visible").val(),
//			user_inp_3: $("#exhb_custom").val(),
//			location: $("select[name='pageLocation']").val(),
//			bates_start_from: $("input[name='bates_start_from']").val()||1,
//			file_start_from: $("input[name='file_start_from']").val()||1,
//			font: $("input[name='format']:checked").val()||"courier2",
//			font_size: $("#font_size").val()||10,
//			color: $("#text_color_input").val(),
//			file_patern: $("input[name='outputFilenamePattern']").val()
//			
//		
//		};




		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({uuid: UUID, type: $(".output-btn-active").data("val"), lang: $(".lang_select").val(), file_name: $this.file.name }).then($this.taskComplete);

			}
		} , 250);	

		return false;
	},


	getPagePreviewTemplate(params){
		return `
    		<div class="page_item" style="margin-bottom: 50px;">
				<div class="recognize-left-pdf">
					<div class='canvases_outers'>
						<canvas class='input_overlay' id="canvas_overlay_${params['page_num']}"></canvas>
						<canvas class='page_canvas'	data-rotate="0" data-page-id="${params['page_num']}" id="page_canvas_${params['page_num']}"></canvas>
					</div>
				</div>
				<div class="recognize-midle">
				    <span>${params['page_num']}</span>
				    <img src="img/arow-next.svg" alt="Alternate Text" />
				</div>
				<div class="recognize-right-info" style="justify-content: center;">
					<div class="before_ocr" style="justify-content: center;">
						<div class="recognize-info-block">
							<p>Quick single page mode</p>
							<div class="recognize-btns-block">
								<select class="select lang_select">
								<option value="eng" selected="selected">English</option>	
                                <option value="spa">Spanish</option>
                                <option value="ita">Italian</option>
                                <option value="ara">Arabic</option>
                                <option value="bul">Bulgarian</option>
                                <option value="chs">Chinese</option>
                                <option value="hrv">Croatian</option>
                                <option value="ces">Czech</option>
                                <option value="dan">Danish</option>
                                <option value="nld">Dutch</option>
                                <option value="fin">Finnish</option>
                                <option value="fra">French</option>
                                <option value="deu">German</option>
                                <option value="ell">Greek</option>
                                <option value="ita">Italian</option>
                                <option value="jpn">Japanese</option>
                                <option value="kor">Korean</option>
                                <option value="nor">Norwegian</option>
                                <option value="pol">Polish</option>
                                <option value="por">Portuguese</option>
                                <option value="rus">Russian</option>
                                <option value="slv">Slovenian</option>
                                <option value="swe">Swedish</option>
                                <option value="tur">Turkish</option>
								</select>
								<a href="#" class="recognize_text button-green">Recognize text on this page</a>
							</div>
						</div>
					</div>
					<div class="ocr_progress hidden" style="justify-content: center;">
						${PDFTOOLS.percentageCircle()}
						<div class='ocr_current_action' style='text-transform: capitalize'></div>
					</div>
					<div class="ocr_result hidden">
						<div class='ocr_result_content' contenteditable="true"></div>
						<div class='ocr_footer'>
							<a class='copy_to_clipboard' href="#">Copy to clipboard</a>
							<a class='save_as_file' href="#">Download</a>
						</div>
					</div>
				</div>
			</div>
		`;
	},
	
	createProgressBar: function(element){


		return ;


	},
	percentageCircle: function(){
		return `
			<div class='progressbarc_c'>
				<svg id="animated" viewbox="0 0 100 100" width="100px" height="100px">
					<circle cx="50" cy="50" r="45" fill="#7750DD"/>
					<path fill="none" stroke-linecap="round" stroke-width="5" stroke="#fff"
							stroke-dasharray="251.2,0"
							d="M50 10
							a 40 40 0 0 1 0 80
							a 40 40 0 0 1 0 -80">
					</path>
					<text id="count" x="50" y="50" text-anchor="middle" dy="7" font-size="20" fill="#fff">100%</text>
				</svg>
			</div>
		`;
	}
	
	
}
if(OcrPDF.tool_section.length>0){
	OcrPDF = $.extend(PDFTOOLS, OcrPDF);
	OcrPDF.main();
}

$(document).on('update_ocr_progress', function (e, bar, progr) {

	progr = (progr.toFixed(1)) / 100;
	var profressPath = bar.find('path');
	var x = 251.2 * progr;
	var y = 251.2 - x;
	profressPath.attr('stroke-dasharray', x+', '+ y);

});





