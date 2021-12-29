(function() {

	var remote = require('electron').remote;
	var dialog = remote.dialog



	function init() {

		document.getElementById("minimize_app").addEventListener("click", function(e) {
			remote = require('electron').remote;
			remote.BrowserWindow.getFocusedWindow().minimize();
		});

		document.getElementById("maximize_app").addEventListener("click", function(e) {
			remote = require('electron').remote;
			
			var win = remote.BrowserWindow.getFocusedWindow();
			
			if(!win.isMaximized()) {
				win.maximize();
			} else {
				win.unmaximize();
			}
		});
		document.getElementById("close_app").addEventListener("click", function(e) {
			window = remote.getCurrentWindow();
			window.close();
		});
	};

	document.onreadystatechange = function() {
		if(document.readyState == "complete") {
			init();
		}
	};



})();
