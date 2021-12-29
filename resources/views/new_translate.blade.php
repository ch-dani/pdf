<?php 
$lang_markers = ["en"=>"Hello",
"cs"=>"Ahoj",
"de"=>"Hallo",
"es"=>"Hola",
"fr"=>"Salut",
"it"=>"ciao",
"ja"=>"こんにちは",
"pt"=>"boa tarde",
"ru"=>"Добрый день!",
"tr"=>"iyi günler",
"zh-CN"=>"下午好",
"zh-TW"=>"下午好"];
$marker = 'hello';
if(isset($_GET['from'])){
	//$marker = $lang_markers[isset($_GET['from'])?$_GET['from']:"en"];
	$marker = "en";
}
?>

<style>
body{
	line-height: 0px;

}
span{
	display: inline-block;
	font-size: 1px;
	height: 1px;
}
span:empty{
	display: none;
}
.page{
	display: inline-block;
	overflow: visible;
	width: 1px;

}

</style>
@if($type=='docx')	
<div class="start_marker">{{ $marker }} </div>
@foreach($texts as $pn=>$paragraph)
@foreach($paragraph['text'] as $tn=>$text)
<i class="page_text text_{{$pn}}_{{$tn}}" data-page="{{ $pn }}" data-tn="{{ $tn }}">{{ ($text) }}</i>
@endforeach
@endforeach
<div class="last_text">{{ $marker }}</div>

@elseif($type=='pdf')
<div class="start_marker">{{ $marker }}</div>
	@foreach($texts as $pn=>$page)
		<div class="page">
		@foreach($page as $tn=>$text)
			@foreach($text as $ln=>$line)
			<span class="page_text text_{{$pn}}_{{$tn}}" data-page="{{ $pn }}" data-tn="{{ $tn }}" data-ln="{{ $ln }}">{{ ($line) }}</span>
			@endforeach
		@endforeach
		</div>
	@endforeach
<div class="last_text">{{ $marker }}</div>

@else
@foreach($texts as $t)
<span>{{ $t }}</span>
@endforeach

@endif

<script src="https://deftpdf.com/assets/jquery-1.11.2.js"></script>
@if($type=='lang_detect')
	<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
	<script>
	function googleTranslateElementInit() {
		new google.translate.TranslateElement({
				pageLanguage : 'auto', 
				layout : google.translate.TranslateElement.InlineLayout.SIMPLE,
				autoDisplay : true
			},
		'google_translate_element');
	}
	(function(XHR) {
    "use strict";
    var open = XHR.prototype.open;
    var send = XHR.prototype.send;
    var hasSentOnlyRequest = false;
    XHR.prototype.open = function(method, url, async, user, pass) {
        this._url = url;
        this._method = method
        open.call(this, method, url, async, user, pass);
    };
    XHR.prototype.send = function(data) {
        var self = this;
        var oldOnReadyStateChange;
        var url = this._url;

        function onReadyStateChange() {
            if (self.readyState == 4) {
                if (this._method.toLowerCase() == 'post' && isGoogleTranslateURL(this._url)) {
                    informParentDetectedLanguage(self.responseText);
                }
            }
            if (oldOnReadyStateChange) {
                oldOnReadyStateChange();
            }
        }
        if (isGoogleTranslateURL(this._url)) {
            if (hasSentOnlyRequest) {
                return;
            } else {
                hasSentOnlyRequest = true;
            }
        }
        if (!this.noIntercept) {
            if (this.addEventListener) {
                this.addEventListener("readystatechange", onReadyStateChange, false);
            } else {
                oldOnReadyStateChange = this.onreadystatechange;
                this.onreadystatechange = onReadyStateChange;
            }
        }
        send.call(this, data);
    }
	})(XMLHttpRequest);
	var GOOGLE_URL = 'translate.googleapis.com/translate_a/t';

	function isGoogleTranslateURL(url) {
		return (url.indexOf(GOOGLE_URL) > -1);
	}

	function informParentDetectedLanguage(jsonStr) {
		var jsonObj = window.JSON.parse(jsonStr);
		var l0 = jsonObj.length;
		var l1 = jsonObj[0].length;
		var l2 = jsonObj[0][0].length;
		if (typeof jsonObj[0] === "string") {
			informParentDetectedLanguageSingleEntry(jsonObj);
		} else {
			informParentDetectedLanguageArray(jsonObj);
		}
	}

	function informParentDetectedLanguageArray(jsonObj) {
		var langCounter = [];
		for (var i = 0; i < jsonObj.length; i++) {
			var string = jsonObj[i][0];
			var stringLen = string.length;
			var detectedLang = jsonObj[i][1];
			if (langCounter[detectedLang]) {
				langCounter[detectedLang] += stringLen;
			} else {
				langCounter[detectedLang] = stringLen;
			}
		}
		var maxCharsDetectdForLang = 0;
		var detectedLang = '';
		for (key in langCounter) {
			if (maxCharsDetectdForLang < langCounter[key]) {
				detectedLang = key;
				maxCharsDetectdForLang = langCounter[key];
			}
		};
		console.log(jsonObj);
		console.log("language is ", detectedLang);
		parent.$('body').trigger('lang_detected', [detectedLang]);
	}

	function informParentDetectedLanguageSingleEntry(jsonObj) {
		parent.$('body').trigger('lang_detected', [jsonObj[1]]);
	}

</script>
@else
<script type="text/javascript">


	// $.extend({
	// 	debounce : function(fn, timeout, invokeAsap, ctx) {
	// 		if(arguments.length == 3 && typeof invokeAsap != 'boolean') {
	// 			ctx = invokeAsap;
	// 			invokeAsap = false;
	// 		}
	// 		var timer;
	// 		return function() {
	// 			var args = arguments;
	// 			ctx = ctx || this;
	// 			invokeAsap && !timer && fn.apply(ctx, args);
	// 			clearTimeout(timer);
	// 			timer = setTimeout(function() {
	// 				invokeAsap || fn.apply(ctx, args);
	// 				timer = null;
	// 			}, timeout);
	// 		};
	// 	},
	// 	throttle : function(fn, timeout, ctx) {
	// 		var timer, args, needInvoke;
	// 		return function() {

	// 			args = arguments;
	// 			needInvoke = true;
	// 			ctx = ctx || this;

	// 			timer || (function() {
	// 				if(needInvoke) {
	// 					fn.apply(ctx, args);
	// 					needInvoke = false;
	// 					timer = setTimeout(arguments.callee, timeout);
	// 				}
	// 				else {
	// 					timer = null;
	// 				}
	// 			})();
	// 		};
	// 	}
	// });


	function googleTranslateElementInit() {
		new google.translate.TranslateElement({
				pageLanguage : 'auto', 
				layout : google.translate.TranslateElement.InlineLayout.SIMPLE,
				autoDisplay : true
			},
		'google_translate_element');
	}
	var last_changed_block_ts = new Date().getTime()+5000;
	var interval = false;

	var translate_trigged = false;
	var translated_texts = {};
	var total_texts = $(".page_text:not(:empty)").length;
	var percent_translated = 0;
	var percent = 0 ;

 
	function sendObjects(){

		elements = document.getElementsByClassName("page_text"); 

		for (var v of elements) {

		//$(".page_text").each(function(i,v){
			var pn = $(v).data("page");
			var tn = $(v).data("tn");
			var ln = $(v).data("ln");
			if(typeof translated_texts[pn]=='undefined'){
				translated_texts[pn] = {};
			}
			if(typeof translated_texts[pn][tn]=='undefined'){
				translated_texts[pn][tn] = [];
			}
			//console.log(translated_texts);
			//console.log(pn, tn, $(v).text());
			<?php 
			if($type=='docx'){ 
				?>
				translated_texts[pn][tn].push($(v).text());
				<?php }else{ ?>
					translated_texts[pn][tn].push($(v).text());
				<?php
			}
			?>
		};

		clearInterval(mod_interval);
		console.log(translated_texts);
		parent.$('body').trigger('after_google_translate', [translated_texts]);
	}

	var mod_checker_active = false;
	var mod_interval = false;
	var mod_date = 0;
	function modChecker(){
		mod_checker_active = true;
		mod_interval = setInterval(() => {
			var current_ts = new Date().getTime()/1000;
			if(current_ts>mod_date){
				sendObjects();
				console.log("send interval");
				clearInterval(mod_interval);
			}
		}, 1000);
	};

	var tflag = false;
	var percent = 0;

	var total_texts = $(".page_text:not(:empty)").length;
	var iteration_count = 0;

	var last_dom_changed =  new Date().getTime()/1000;


	var int1 = setInterval(() => {
		$(".page_text:not(.translated) font").each(function(){
			var parent = $(this).closest(".page_text");
			parent.addClass("translated");
			parent.css({"font-size": "0px", "width": 0, height: 0});
		});

		var total_translated = $(".page_text.translated").length;
		percent = (total_translated*100)/total_texts;
		if(percent>100){ percent = 100; };
		parent.$('body').trigger('translate_proggress', [parseInt(percent), total_translated]);

		iteration_count++;
		if(iteration_count>1500){
			clearInterval(int1);
			sendObjects();
			return false;
		}
		
		if($(".page_text:not(.translated):not(:empty)").length==0){
			clearInterval(int1);
			sendObjects();
			return false;
		}

		var ts = new Date().getTime()/1000;
		if(ts-last_dom_changed>10){
			clearInterval(int1);
			sendObjects();
			return false;
		}
	}, 1000);
	
	$(document).on("DOMSubtreeModified", function(){
		last_dom_changed =  new Date().getTime()/1000;
	});

</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
@endif


