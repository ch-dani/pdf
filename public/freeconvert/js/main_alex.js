jQuery(document).ready(function($){
	columnConform('.module__our-blog .article__title');

	$(document).on("click", ".editable-btn", function(e){
		e.preventDefault();

		var th = $(this);
		var currentBtn = th.next();

		$(".editable-btn").parent().removeClass("active");
		th.parent().addClass("active");

		if(currentBtn.hasClass("active")){
			$(".tools-dropdown-menu").removeClass("active");
			$(".editable-btn").parent().removeClass("active");
		}
		else{
			$(".tools-dropdown-menu").removeClass("active");
			currentBtn.addClass("active");
		}
	});


	$(document).on("click", ".editable-btn.set_bold", function(){
		let element = $(this).closest(".document_add_element_wrpr").find('.text_content_element');
		
		let current_state = 1;
		
		if(typeof element.attr("bold")!=='undefined'){
			current_state = parseInt(element.attr("bold"))? parseInt(element.attr("bold")) : 0;
		
		}
		let value = false;
		if(!current_state){
			element.css({"font-weight": "bold"});
			element.attr("bold", 1);		
			value = true;
		}else{
			element.css({"font-weight": "normal"});
			element.attr("bold", 0);		
		}

		let outer = $(this).closest(".document_add_element_wrpr");

		$(document).trigger("whistory", {
			id: outer.attr("id"), 
			page: outer.closest('.page_block_p').data('pagenum'), 
			type: "change_bold",
			value: !value
		});

		
	});

	$(document).on("click", ".editable-btn.set_underline", function(){
		let element = $(this).closest(".document_add_element_wrpr").find('.text_content_element');
		let current_state = element.attr("underline")? parseInt(element.attr("underline")) : 0;
		let value = false;
		if(current_state){
			value = false;
			element.css({"text-decoration": "none"});
		}else{
			value = true;
			element.css({"text-decoration": "underline"});
		}
		element.attr("underline", !current_state?1:0);		

		let outer = $(this).closest(".document_add_element_wrpr");

		$(document).trigger("whistory", {
			id: outer.attr("id"), 
			page: outer.closest('.page_block_p').data('pagenum'), 
			type: "change_underline",
			value: !value
		});
		
	});
	
	
	$(document).on("click", ".editable-btn.set_italic", function(){
		let element = $(this).closest(".document_add_element_wrpr").find('.text_content_element');
		let current_state = element.attr("italic")? parseInt(element.attr("italic")) : 0;
		if(current_state){
			element.css({"font-style": "normal"});
			value = false;
		}else{
			element.css({"font-style": "italic"});
			value = true;
		}
		element.attr("italic", !current_state?1:0);		

		let outer = $(this).closest(".document_add_element_wrpr");


		$(document).trigger("whistory", {
			id: outer.attr("id"), 
			page: outer.closest('.page_block_p').data('pagenum'), 
			type: "change_italic",
			value: !value
		});
		
	});

	$('.choose_more_trigger').click(function(e){
		e.preventDefault();

		$(this).closest('.choose_more_wrpr').toggleClass('active');
		$(this).closest('.choose_popup').toggleClass('more_menu_open');
	});

	loadUploadedFile();

	async function loadUploadedFile() {
		if((typeof converterTool !== "undefined" && converterTool.fileSelected) || (typeof pdfUploader !== "undefined" && pdfUploader.fileSelected)){
			let url = localStorage.getItem('uploadedFileUrl');
			let name = localStorage.getItem('uploadedFileName');

			if(url){
				localStorage.removeItem('uploadedFileUrl');
				localStorage.removeItem('uploadedFileName');

				let file_obj = await createFile(url, name);
				let ev = new Event('build');

				if(typeof converterTool !== "undefined" && converterTool.fileSelected){
					converterTool.fileSelected(ev, file_obj);
				}else if(typeof pdfUploader !== "undefined" && pdfUploader.fileSelected){
					pdfUploader.fileSelected(ev, [file_obj]);
				}

				$(".after_upload").removeClass("hidden");
			}
		}
	}

	async function createFile(url, name){
		let response = await fetch(url);
		let data = await response.blob();
		let metadata = {
   			// type: 'image/jpeg'
		};
		let file = new File([data], name, metadata);

		return file;
	}

	$(document).on('click', '.link_convert .remove', function(e){
		e.preventDefault();

		window.location.reload();
	});
}); /* jQuery(document).ready() */

