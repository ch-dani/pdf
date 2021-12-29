$(document).ready(function () {

    $(document).on("after_save_file", function(e, data){
    	if(typeof gapi!='undefined'){
    	
    		if(typeof data.file_name=='undefined'){
	    		data.file_name = data.new_file_name;
    		}
    		
    		
		    gapi.savetodrive.render('savetodrive-div', {
		        src: '//'+window.location.hostname+'/'+data.url,
		        filename: data.file_name,
		        sitename: 'PDF'
		    });
        }
    });
});
