$(document).ready(function () {
    // $('#gdrive-chooser').on('click', function (e) {
    $('.gdrive-chooser').on('click', function (e) {
    	e.preventDefault();
        loadPicker();
    });

    // The Browser API key obtained from the Google API Console.
    // Replace with your own Browser API key, or your own key.
    // var developerKey = 'AIzaSyDlPCZbbC5APwJU792R6WL4bOuwuM_PQsM';
    // var clientId = "692731351743-kocna8ov0o54it98es2hvlsrhg3avb6i.apps.googleusercontent.com";
    // var appId = "692731351743";

    var developerKey = 'AIzaSyDftkIjrhqfHZVZR6FDPqd1yxmoVjpdQBE';
    var clientId = "909973002003-q304nsj9uieafhn0b7ke274bo7l0n8ot.apps.googleusercontent.com";
    var appId = "909973002003";
    


//    var developerKey = 'AIzaSyAawW_4yft1xLYQWpQ5-mRewgOVbbmDc4Y';
//	var clientId = "708318393409-pmq3bb6bk5bcbcgh91e824sduvaq3976.apps.googleusercontent.com";
//    var appId = "708318393409";


    var scope = ['https://www.googleapis.com/auth/drive'];
	var scope = ['https://www.googleapis.com/auth/drive.file'];

    var pickerApiLoaded = false;
    var oauthToken;

    // Use the Google API Loader script to load the google.picker script.
    function loadPicker() {
        gapi.load('auth', {'callback': onAuthApiLoad});
        gapi.load('picker', {'callback': onPickerApiLoad});
    }

    function onAuthApiLoad() {
        window.gapi.auth.authorize(
            {
                'client_id': clientId,
                'scope': scope,
                'immediate': false
            },
            handleAuthResult);
    }

    function onPickerApiLoad() {
        pickerApiLoaded = true;
        createPicker();
    }

    function handleAuthResult(authResult) {
        if (authResult && !authResult.error) {
            oauthToken = authResult.access_token;
            createPicker();
        }
    }

    // Create and render a Picker object for searching images.
    function createPicker() {
        if (pickerApiLoaded && oauthToken) {
            var view = new google.picker.View(google.picker.ViewId.DOCS);
            
            if(typeof ALLOW_FILE_EXT!='undefined'){
            	view.setMimeTypes(ALLOW_FILE_EXT);
            }else{
            	view.setMimeTypes("application/pdf");
            }
            var picker = new google.picker.PickerBuilder()
                .enableFeature(google.picker.Feature.NAV_HIDDEN)
                .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                .setAppId(appId)
                .setOAuthToken(oauthToken)
                .addView(view)
                .addView(new google.picker.DocsUploadView())
                //.setDeveloperKey(developerKey)
                .setCallback(pickerCallback)
                .build();
            picker.setVisible(true);
        }
    }
    



    

    // A simple callback implementation.
	/*
    function pickerCallback(data) {
        if (data.action == google.picker.Action.PICKED) {
			$(`body`).prepend(`<div class='gloader'></div>`);

            gapi.client.load("https://content.googleapis.com/discovery/v1/apis/drive/v2/rest")
                .then(function() {
                	if(typeof blocker !=='undefined'){
                		blocker.show();
                	}
                	

                    console.log("GAPI client loaded for API");

                        gapi.client.drive.files.get({
                            "fileId": data.docs[0].id
                        })
                        .then(function(response) {
                                    // Handle the results here (response.result has the parsed body).
                                    // pdfUploader.startDropbox(response.result.webContentLink);

                                    if (response.result.downloadUrl) {
                                    
                                        var accessToken = gapi.auth.getToken().access_token;
                                    	var file_name = (response.result.originalFilename);

                                    	if(typeof A_TOOL!='undefined' && A_TOOL){
											createFile2(response.result.downloadUrl, file_name, accessToken).then(function(file){
												console.log("file is ", file);
												A_TOOL.fileSelected(false, file);
												$(".gloader").remove();
											});
                                    		return false;
                                    	}
                                    	
                                    	if(PDFTOOLS.name){
											createFile2(response.result.downloadUrl, file_name, accessToken).then(function(file){
												console.log("file is ", file);
												pdfUploader.fileSelected(false, [file]);
												$(".gloader").remove();
											});
                                    		return false;
                                    	}
										$(`body`).prepend(`<div class='gloader'></div>`);
										createFile2(response.result.downloadUrl, file_name, accessToken).then(function(file){
										
											pdfUploader.fileSelected(false, [file]);
											$(".gloader").remove();
										});
										

										return false;

                                    } else {
                                        console.log(null);
                                    }
                        },
                        function(err) { 
                        
                        $(".gloader").remove();
                        console.error("Execute error", err); });

                    },
                    function(err) { console.error("Error loading GAPI client for API", err); });
        }
    }
    */
	function pickerCallback(data) {


		if (data.action == google.picker.Action.PICKED) {
			$(`body`).prepend(`<div class='gloader'></div>`);

			gapi.client.load("https://content.googleapis.com/discovery/v1/apis/drive/v2/rest")
				.then(function() {
						if(typeof blocker !=='undefined'){
							blocker.show();
						}


						gapi.client.drive.files.get({
							"fileId": data.docs[0].id
						})
							.then(function(response) {
									if (response.result.downloadUrl) {


										var fileId = data.docs[0].id;
										var accessToken = gapi.auth.getToken().access_token;
										var file_name = data.docs[0].name;
										var downloadUrl = "https://www.googleapis.com/drive/v3/files/"+fileId+"?alt=media"

										var accessToken = gapi.auth.getToken().access_token;
										var file_name = (response.result.originalFilename);
										// console.log("file_name", file_name);

										if(typeof A_TOOL!='undefined' && A_TOOL){
											createFile2(downloadUrl, file_name, accessToken).then(function(file){
												// console.log("g1 file is ", file);
												A_TOOL.fileSelected(false, file);
												$(".gloader").remove();
											});
											return false;
										}

										if(typeof PDFTOOLS!='undefined' && PDFTOOLS.name){
											createFile2(downloadUrl, file_name, accessToken).then(function(file){
												// console.log("g2 file is ", file);
												pdfUploader.fileSelected(false, [file]);
												$(".gloader").remove();
											});
											return false;
										}
										$(`body`).prepend(`<div class='gloader'></div>`);
										createFile2(downloadUrl, file_name, accessToken).then(function(file){
											// console.log("g3 file is ", file);
											pdfUploader.fileSelected(false, [file]);
											$(".gloader").remove();
										});


										return false;

									} else {
										console.log(null);
									}
								},
								function(err) {

									$(".gloader").remove();
									console.error("Execute error", err); });

					},
					function(err) { console.error("Error loading GAPI client for API", err); });
		}
	}
});
