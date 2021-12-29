function ImagesEditor(){
	this.cleaned = {opacity: 100, width: 0, height: 0, scaleToWidth: 0, scaleToHeight: 0 };
	
	this.bindEv = () =>{
		
	};
	
	
	this.elementClick = (e)=>{
		var pn = $(e.target.canvas.lowerCanvasEl).data("pn");

		viewer.current_page = pn;
		if(editorMenu.editor!='edit_image'){
			return false;
		}
		
		this.parseStyles(e.target, true);
	};

	
	this.parseStyles = (object, all)=>{
		console.log(object);	
	};
	
	
	
	this.bindEv();
	return this;
}
 
var imagesEditor = new ImagesEditor();
