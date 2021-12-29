$(document).on("keyup", "#search_tool_input", function(e){
    e.preventDefault();
    var s = $(this).val().toLowerCase();
    if(s){
        $("#megaMenu li:not(.header) a").each(function(){
            var cont = $(this).html().replace(/<(?:.|\n)*?>/gm, '').toLowerCase();
            if(cont.search(s)==-1){
                $(this).addClass("not_finded");
            }else{
                $(this).removeClass("not_finded")
            }

        });
    }else{
        $(".not_finded").removeClass("not_finded");
    }
});

$(document).on("click", ".start-over-button", function(){
    $(window).unbind('beforeunload');
    window.location.reload();
    return false;
});

var testTitle = document.getElementById('test-title') || false;

if (testTitle) {
    testTitle.addEventListener('click', function(e) {
        e.preventDefault();

    });
}

$(document).on("pdf_loading_task", function(ev, loadingTask){
    loadingTask.onPassword = function(updatePassword, reason){
        user_updatePassword = updatePassword;
        showPasswordPrompt(updatePassword, reason);
    }
});



var PDF_PASSWORD = false

function showPasswordPrompt(upd, reason){
    console.log("reason is ", reason);
    if(reason==2){
        html = "Incorect password";
    }else{
        html = "Please, enter password";
    }
    return Swal.fire({
        title: 'Document is protected',
        input: 'password',
        html: html,
        inputAttributes: {
            autocapitalize: 'off'
        },
        type: "error",
        showCancelButton: true,
        confirmButtonText: 'Open',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {

        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if(typeof result.dismiss!=='undefined'){
            $(".before_upload").show().removeClass("hidden");
            $(".after_upload").addClass("hidden");
            $(".fixed-bottom-panel").addClass("hidden");
            if(typeof PDFTOOLS!='undefined'){
                PDFTOOLS.data = {};
                $("#files_list").html("");
            }
        }else{
            PDF_PASSWORD = result.value;
            upd(result.value)
        }
    });
}

jQuery(document).ajaxSend(function(evt, request, settings){
    jQuery(".start-over-button").hide();

    if(typeof settings.data!='undefined'){
        if(typeof settings.data=='string'){
            settings.data = settings.data + "&pdf_password="+PDF_PASSWORD;
        }else{
            settings.data.append("pdf_password", PDF_PASSWORD);
        }
    }

});

$( document ).ajaxComplete(function( event, xhr, settings ) {
    jQuery(".start-over-button").show();
});

jQuery(document).on("after_save_file", function(){
    jQuery(".start-over-button").show();
    console.log("after file save");
});



$("html").on("dragover dragover", function(e){
    e.preventDefault();
    $("#drop_zone").addClass("file_over"); //, {duration:500});
    $(".app-welcome form").addClass("file_over");
}).on("dragleave dragend", function(e){
    $("#drop_zone").removeClass("file_over");
    $(".app-welcome form").removeClass("file_over");
}).on("drop", dropFile);

function dropFile(e){
    $("#drop_zone input[type='file'], .drop_zone_2 input[type='file']").prop("files", e.originalEvent.dataTransfer.files).change();
    return false;
}

if(typeof setCookie == 'undefined'){
    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function setCookie(name, value, options) {
        options = options || {};
        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }
        value = encodeURIComponent(value);
        var updatedCookie = name + "=" + value;
        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }
        document.cookie = updatedCookie;
    }
}

$(document).on("click", "#rename_file", function(e){
    e.preventDefault();
    var new_file_name = false;
    if(new_file_name = prompt("Rename to", $(".download-result-link").attr("download"))){

        var new_href = $(".download-result-link").attr("href").split("?")[0];
        new_href = new_href+"?rename="+new_file_name;
        $(".download-result-link").attr("href", new_href);
        $(".download-result-link").attr("download", new_file_name);
        $(".download-result-link .download_file_name").html(new_file_name);
    }
    return false;


});


var show_ext = getCookie("show_ext");
var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);
if(typeof show_ext=='undefined' && isChrome){

}







