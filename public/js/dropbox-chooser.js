$(document).ready(function () {
	
    // $('#drpbox-chooser').on('click', function (e) {
    $('.drpbox-chooser').on('click', function (e) {
    	e.preventDefault();
    	// Dropbox.appKey = "kmnvanr1sm5jlg1";
    	Dropbox.appKey = "zovgk9csdcvqrx6";

        var options = {

            // Required. Called when a user selects an item in the Chooser.
            success: function (files) {
            	$(`body`).prepend(`<div class='gloader'></div>`);
                var link = files[0].link;
                link = link.replace("dl=0", "dl=1");

				if(typeof A_TOOL!='undefined' && A_TOOL){
					createFileFromDropbox(link, files[0].name, false).then(function(file){
						// console.log("d1 file is ", file);
						$(".gloader").remove();

						A_TOOL.fileSelected(false, file);
					});
					return false;
				}

				if(typeof PDFTOOLS!='undefined' && PDFTOOLS.name){
					createFileFromDropbox(link, files[0].name, false).then(function(file){
						// console.log("d2 file is ", file);
						$(".gloader").remove();
						pdfUploader.fileSelected(false, [file]);
						
					});
					return false;
				}
                
				createFileFromDropbox(link, files[0].name, false).then(function(file){
					// console.log("d3 file is ", file);
					$(".gloader").remove();
					
					pdfUploader.fileSelected(false, [file]);
				});
        		return false;

//                $.post('/save-file-by-url', {
//                    service: 'dropbox',
//                    link: link,
//                    _token: $('input[name="_token"]').val()
//                })
//                .done(function (response) {
//                	blocker.hide();
//                    console.log(response);
//                    pdfUploader.startDropbox(response.data.link);
//                });
            },

            // Optional. Called when the user closes the dialog without selecting a file
            // and does not include any parameters.
            cancel: function () {
				
            },

            // Optional. "preview" (default) is a preview link to the document for sharing,
            // "direct" is an expiring link to download the contents of the file. For more
            // information about link types, see Link types below.
            linkType: "direct", // or "direct"

            // Optional. A value of false (default) limits selection to a single file, while
            // true enables multiple file selection.
            multiselect: false, // or true

            // Optional. This is a list of file extensions. If specified, the user will
            // only be able to select files with these extensions. You may also specify
            // file types, such as "video" or "images" in the list. For more information,
            // see File types below. By default, all extensions are allowed.
            // extensions: ['.pdf'],
            extensions: typeof ALLOW_FILE_EXT_SIMPLE != 'undefined' ? ALLOW_FILE_EXT_SIMPLE : ['.pdf'],

            // Optional. A value of false (default) limits selection to files,
            // while true allows the user to select both folders and files.
            // You cannot specify `linkType: "direct"` when using `folderselect: true`.
            folderselect: false, // or true

            // Optional. A limit on the size of each file that may be selected, in bytes.
            // If specified, the user will only be able to select files with size
            // less than or equal to this limit.
            // For the purposes of this option, folders have size zero.
            sizeLimit: 50000000 // or any positive number
        };

        Dropbox.choose(options);
    });
});
