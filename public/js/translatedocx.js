window.show_anyway = false;
window.skip_extract = 0;
window.remove_all_texts = 1;

var epg = false; //empty page generator

var tpp = {};
var current_range = false;

var lastScrollTop = 0;
var scroll_direction = "down";
$(window).scroll(function(event){
	var st = $(this).scrollTop();
	if(st >lastScrollTop){
		scroll_direction = "down";
	}else{
		scroll_direction = "up";
	}
	lastScrollTop = st;
});

var translated_list = [];

var def_lang = $(".source_language").val();
if(def_lang=='auto'){
	$(".buttons_part").addClass("hidden");
}


window.total_chars = 0;
window.total_chars_on_page = {};

var TranslateDOCX = {
	name: "translatedocx",
	need_preview: false,
	preview: function(params){ //TODO запускаем обработку документа
	
		var $this = this,
			CMAP_URL = '/libs/pdfjs-dist/cmaps/',
			CMAP_PACKED = true, pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
		var params = {
			url: params.url,
			cMapUrl: CMAP_URL,
			cMapPacked: CMAP_PACKED,
			password: PDF_PASSWORD
		};
		$(document).trigger("before_start_preview_pages", [params]);

		var loadingTask = pdfjsLib.getDocument(params);
		PDFTOOLS.loadingTask = loadingTask;

		loadingTask.promise.then(function(pdfDoc_){
			$(document).trigger("before_render_pages_blocks", [pdfDoc_]);
			PDFTOOLS.pages_list.html("");
			$this.renderPagesBlocks(pdfDoc_, $this)
		}).catch(function(err){
			alert("Get document error: "+err.message);
		});
		return loadingTask;

	},
	
	updatePreview: function(params){
		
		$this = PDFTOOLS;
		$this.preview({url: params.url});
		
//		var params = {fileData: data, password: PDF_PASSWORD};
//		$this.preview(params);
	},
	
	
	translate_docx: function(){
		var $this = this;
	},
	
	bind: function(){
		var $this = this;


		$(document).on("click", "#download_file", async function(e){
			e.preventDefault();
			downloadURI($(this).attr("href"), "translated_"+PDFTOOLS.file_name);
			return false;
		
//			e.preventDefault();
//			showLoading1($(".download_outer"));
//			var resp = await savePages(range(1, PDFTOOLS.total_pages+1),0);
//			hideLoading()
//			window.location = resp.url
			
		});


		$(document).on('after_file_upload', function(event, data){

			$('.before_upload').addClass('hidden');
			$('.after_upload').removeClass('hidden');
			$this.preview({url: 'uploads/docx/' + data.file_path});
		});


		$(document).on("submit", "#stripe_form", function(e){
			e.preventDefault();
			
			
			showLoading1($("#stripe_form"))
			
			var form = $(e.target);
			if(!$("#email").is(":valid")){
				$(".loading").remove();
				$(".stripe_errors").removeClass("hidden");
				$(".stripe_errors").html("Please, enter valid email");
				return false;
			}
			
			var extraDetails = {
		//		name: form.find('input[name=cardholder-name]').value,
				email: $("#email").val()
			};
			stripe.createToken(card, extraDetails).then(function(data){
				$(".stripe_errors").addClass("hidden");
				
				if(typeof data.error!='undefined'){
					Swal.fire({
						type: 'error',
						title: 'Error',
						text: data.error.message,
					})
					hideLoading()
					return false;
				}
				
				data.token.price = current_range;
				data.token.email = $("#email").val();
				
				PDFTOOLS.getCSFR();
				
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': PDFTOOLS.csfr
					},
					method: "POST",
					url: "/createStripeCharge",
					data: data.token,
					dataType: "json",
					success: function(data){
						hideLoading();
						if(data.success){
						
						
							if(PDFTOOLS.name=='translatedocx'){
								$('.credit_card_popup_wrp.one').removeClass("active");
								$('body').removeClass("credit_card_popup_opens");							
								$("#translate_all").click();
							}else{
								PDFTOOLS.charge_id = data.payment_id;
								TranslatePDF.startTranslate();
								$('.credit_card_popup_wrp.one').removeClass("active");
								$('body').removeClass("credit_card_popup_opens");
							}
						}else{
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: data.message,
							});		
							hideLoading();
												
						}
					},
					error: function(error){
						hideLoading();
						console.log(error);
						alert(error.responseJSON.message);
					}
				});
			}).catch(function(error){
				console.log(error);
				hideLoading();
				alert("error");
				
			});
		});



		$(document).on("click", ".translate_page, #translate_all", function(e){
			e.preventDefault();
			var price = PDFTOOLS.getPriceRange(window.total_chars);

			if($(".source_language").val()==$(".target_language").val()){
				swal("Error", "Please, select different languages", "error");
				return false;
			}


//			if(price>0 && PDFTOOLS.paid_pages!='all'){
//				Swal.fire({
//					title: `Translation cost will be $${price}`,
//					html: `<div class='stripe_outer'><div class='form_title'>Your document consists of <b>${window.total_chars}</b> symbols.</div>`+stripe_form+"</div>",
//					type: 'warning',
//					showCancelButton: true,
//					confirmButtonColor: '#3085d6',
//					cancelButtonColor: '#d33',
//					confirmButtonText: 'Pay',
//					preConfirm: function(){
//						$(".swal2-popup").append("<div class='loading'></div>");
//						PDFTOOLS.pay_for = "all";
//						$(".stripe_outer form").submit();
//						return false;
//					}
//				}).then((result)=>{
//					return false;
//				});
//				setTimeout(function(){
//					card.mount('#card-element');;
//				},500)
//				return false;
//			}

			$(".before_translate").addClass("hidden");
			$(".translate_proggress").removeClass("hidden");


			var formData = new FormData();
			formData.append("UUID", UUID);
			formData.append("_token", $("#editor_csrf").val());
			formData.append("lang_from", $(".source_language").eq(0).val());
			formData.append("lang_to", $(".target_language").eq(0).val());
			formData.append("operation_id", pdfUploader.operation_id);
			
			
			$(".hide_after_start").addClass("hidden");
			var last_response_len=false;
			var last_response = false;
			$.ajax({
				url: `/trans-docx`,
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "text",
				xhrFields: {
					onprogress: function(e){
						
						var this_response, response = e.currentTarget.response;
						if(last_response_len === false){
							this_response = response;
							last_response_len = response.length;
						}else{
							this_response = response.substring(last_response_len);
							last_response_len = response.length;
						}
						last_response = this_response;
						var percent = last_response.replace(/\|\|\|\|+$/,'').split("||||");
						if(!percent[percent.length-1]){
							$(".total_translated").html(total_chars);
							$(".translated-in-process p").html("Update preview");
						}else{
							var prg = parseInt(percent[percent.length-1]*total_chars/100);
							if(prg==total_chars){
								$(".translated-in-process p").html("Update preview");
							}
							$(".total_translated").html(prg);
						}
					},
				
				},
				success: function (data) {
					$(".preview_title").html("Translate preview");
					try{
						var data = JSON.parse(last_response);
					}catch(exc){
						Swal("Error", `Unknown error 1`, "error");
					}
					
					
					if(data.file_path.length){
					
					
						$(".after_translate").removeClass("hidden");
						$(".translate_proggress").addClass("hidden");
						$(document).trigger("after_translate_docx");
						hideLoading();
						$("#download_file").attr({"href": data.docx_file, "target": "_blank"});
						
						


						$(".upload_another_pdf").removeClass("hidden");
						$(".t-upload-another-pdf").css("border", "none");
						
						
						
						$this.updatePreview({url: data.file_path, });
						
					}else{
					
						alert("error");
					}
				},
				error: function (data) {
					console.log(data);
					Swal("Error", `Unknown error `, "error");
				}
			});
			return;
		});


	},

//	taskComplete: function(data){
//		if(!data.success){
//			$("#apply-popup").removeClass("active");
//			swal("Error_1", data.message, "error");
//			return false;
//		}

//		var $this = this;
//		if(typeof data.new_file_name!= 'undefined'){
//			$this.new_file_name = data.new_file_name;
//		}
//		$("#apply-popup .modal-header").removeClass("hidden");
//		$(".creating_document").hide();
//		$(".create_file_box").show();
//		$(".result-top-line span").html($this.new_file_name);
//		$(".download-result-link").attr({"href": data.url, "download": $this.new_file_name });
//		$("#save-dropbox").attr({'data-url': data.url, 'data-file_name': $this.new_file_name});
//		$("#save-gdrive").attr({'data-src': data.url, 'data-filename': $this.new_file_name});

//		if(typeof data.redirect!='undefined' && data.redirect){
//			$(".download-result-link").attr("_target", "blank");

//			var win = window.open(data.edit_link, '_blank');
//			win.focus();
//		}else{
//		}
//	},
//	save: function(e){
//		console.log('save');
//		var $this = this;
//		PDFTOOLS.startTask();
//		$(".download-result-link").css("background-image", "url(/img/docx-img.svg)");
//		
//		var intervalID = setInterval( function() {
//			console.log("upload progress is "+spe.upload_in_progress);
//			if(!spe.upload_in_progress){
//				clearInterval(intervalID);
//				$this.ajax({UUID: UUID, type: $(".output-btn-active").data("val"), lang: $(".lang_select").val(), file_name: $this.file.name, download: true }).then($this.taskComplete);
//			}
//		} , 250);

//		return false;
//	},
	
}

