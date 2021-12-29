'use strict';

window.show_anyway = false;
window.skip_extract = true;
PDFTOOLS.name = "compresspdf"; 

var Compress = {
	original_size: 0,
	compressed_size: 0,
	data: {
		'UUID': UUID,
		'image_quality': 'medium',
		'image_resolution': "72",
		'image_conversion': "none",
		'compression_speed': "normal"
	},
	init: function(){
		window.skip_extract = 1;
	
		$(document).on("file_selected", this.fileSelected);
		this.bind();
	},
	bind: function(){
	
		$("#compress_section input").on("change", function(){
			console.log("==click==");
			$(this).closest(".btn-group").find(".active").removeClass("active");
			$(this).closest("label").addClass("active");
			Compress.data[$(this).attr('name')] = $(this).val();
		});
		var ff = false;
		$("#more_compress_options").click(function(){
			$(".compress_options").toggleClass("hidden");
			if(ff){
				$("#more_compress_options").html("More options");
			}else{
				$("#more_compress_options").html("Fewer options");
			}
			ff = !ff;
		});
		
		$("#compress_button").click(function(e){
			
			Compress.compress();
		});
	},
	
	fileSelected: function(prom, file){
		blocker.hide();
		
		Compress.original_size = file.size;

//        pdfUploader.getBlob(file).then((data) => {
//            var pdfjsLib = window['pdfjs-dist/build/pdf'];
//			pdfjsLib.GlobalWorkerOptions.workerSrc = '/libs/pdfjs-dist/build/pdf.worker.js';

//            var loadingTask = pdfjsLib.getDocument({data: data});
//			$(document).trigger("pdf_loading_task", [loadingTask]);
//			loadingTask.promise.catch(function(e){
//				console.log("wow doge", e);
//			});
//		});
		
		Compress.file_name = file.name;
		$(".current_filename").html(file.name);
		$("#c_upload_section").addClass("hidden");
		$("#compress_section").removeClass("hidden");
	},
	compress: function(){
		$("#apply-popup").addClass("active");
		$(".creating_document").show();
		$(".create_file_box").hide();

		
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);

			if(!spe.upload_in_progress){
				clearInterval(intervalID);
				
				
				Compress.data['image_quality'] = $("[name='image_quality']:checked").val();
				Compress.data['image_resolution'] = $("[name='image_resolution']:checked").val()
				Compress.data['image_conversion'] = $("[name='image_conversion']:checked").val()
				Compress.data['compression_speed'] = $("[name='compression_speed']:checked").val()


				$.ajax({
					method: "POST",
					url: "/pdf-compress",
					headers: {
						'X-CSRF-TOKEN': $("#editor_csrf").val()
					},
					data: Compress.data,
					dataType: "json",
					success: function(data){
					
					

						
						
					
						$("#apply-popup .modal-header").removeClass("hidden");
						if(data.success==false){
							$("#apply-popup").removeClass("active");
							swal("Error", data.message, "error");
							return false;
						}

						Compress.compressed_size = data.s2;
						
						if(Compress.compressed_size>=Compress.original_size){
							$("#apply-popup").removeClass("active");
							swal("Error", "Sorry, the file can not be compressed any further", "error");
							return false;
						}
						
						var perc = (100-(Compress.compressed_size*100/Compress.original_size)).toFixed(1);

						$("#file_size_changes .before").html(bytesToSize(Compress.original_size));
						$("#file_size_changes .after").html(bytesToSize(Compress.compressed_size));
						$("#file_size_changes .compressed_percent").html(perc);
						$("#file_size_changes").removeClass("hidden");
						
					    $(".creating_document").hide();
					    $(".create_file_box").show();
					    $(".result-top-line .download_file_name").html(""+data.new_file_name+"");
					    $(".download-result-link").attr({"href": "/"+data.url, "download": "" + data.new_file_name});
					    $("#save-dropbox").attr({'data-url': "/"+data.url, 'data-file_name': "" + data.new_file_name});
					    $("#save-gdrive").attr({'data-src': "/"+data.url, 'data-filename': "" + data.new_file_name});

					},
					error: function(error){
						console.log(error.responseText);
						console.log(error);
						alert("error...");
						$("#apply-popup").removeClass("active");
						blocker.hide()
						
					}
				});

			}
			
			
		} , 250);

	}
};

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    if (i == 0) return bytes + ' ' + sizes[i];
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};


if($(".compress_form").length>0){
	Compress.init();
}

