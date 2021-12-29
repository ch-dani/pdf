PDFTOOLS.name = "fill_and_sign";
window.show_anyway = false;
window.skip_extract = 0;

var FillAndSign = {
	file: false,
	init: function(file){
		this.file = file;
		$(".fill_and_sign_block_1").removeClass("hidden");
		blocker.hideUploader();
		blocker.hide();
		$(document).on("click", ".im_filling_out", $.proxy(this.showEditor, this));
		$(document).on("click", ".someone_fill_out", $.proxy(this.showShareForm, this));
		$(document).on("spe_init", ()=>{ $(".fill_and_sign_block_1").addClass("hidden"); });
		$(document).on("click", "#get_fill_link", $.proxy(this.createLink, this));
		$(document).on("submit", "#fill_and_sign_email", $.proxy(this.inviteEmail, this));	
	},
	inviteEmail: function(e){
		e.preventDefault();
		var form = $(e.target),
			$this = this;
		$(".before_send", form).addClass("loading");
		$.ajax({
			url: `pdf-fill-and-sign-email`,
			type: "POST",
			headers: { 'X-CSRF-TOKEN': $("#editor_csrf").val() },
			data: {
				uuid: UUID, 
				recipient_email: $("input[name='recipient_email']", form).val(), 
				sender_email: $("input[name='your_email']", form).val(), 
				noty: $("textarea[name='note']", form).val(), 
			},
			dataType: "json",
			success: (data)=>{ $this.emailSended(data, form)  },
			error: function(error){
				console.log(error.responseText);
				alert("Error: " + error.message);
			}
		});
		return false;
	},
	emailSended: function(data, form){
		$(".before_send", form).addClass("hidden");
		$(".after_send", form).removeClass("hidden");
		$(".after_send .recipient_email", form).html($("input[name='recipient_email']", form).val());

	
	},
	speInit: function(){
		that.getBlob(file).then((data) => {
			blocker.show();
			spe = $.extend(spe, {filename: file.name, size: file.size, fileData: data});
			spe.init({container_selector: "simplePDFEditor", data: data});
		});
	},
	showEditor: function(e){
		e.preventDefault();
		var $this = this; 
		$(".fill_and_sign_header").removeClass("hidden");
		pdfUploader.getBlob($this.file).then((data) => {
			blocker.show();
			spe = $.extend(spe, {filename: $this.file.name, size: $this.file.size, fileData: data});
			spe.init({container_selector: "simplePDFEditor", data: data});
		});
	},
	showShareForm: function(e){
		$(".fill_and_sign_header").removeClass("hidden");
		e.preventDefault();
		var $this = this;
		$(".fill_and_sign_block_1").addClass("hidden");
		$(".fill_and_sign_block_2").removeClass("hidden");
	},
	
	createLink: function(e){
		var $this = this,
			email = $("#fill_link_email").val();
		if(!email){ return false; }
		
		$(".fill_sign_link_block .before").addClass("loading");
		
		$.ajax({
			url: `pdf-fill-and-sign-link`,
			type: "POST",
			headers: { 'X-CSRF-TOKEN': $("#editor_csrf").val() },
			data: {uuid: UUID, email: email},
			dataType: "json",
			success: $this.linkCreated,
			error: function(error){
				console.log(error.responseText);
				alert("Error: " + error.message);
			}
		});
		return false;
	},
	linkCreated: function(data){
		$(".fill_sign_link_block .before").addClass("hidden");
		$(".fill_sign_link_block .after").removeClass("hidden");
		$(".fill_sign_link_block textarea").val(data.url);
		console.log(data);
		return false;
	},
};


