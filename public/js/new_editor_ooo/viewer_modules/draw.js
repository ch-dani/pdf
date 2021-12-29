var DrawMode = {
    selector: "#drawing-mode-options",
    drawingLineWidth: 30,
    drawingColor: "#ebebeb",
    init: function(){
        $(document).on("change", "#drawing-color", (e)=>{
            var val = $(e.target).val()
            this.drawingColor = hex2rgb(val,0.5);
            this.enterDrawMode();
        });
        $(document).on("change", "#drawing-line-width", (e)=>{
            var val = $(e.target).val()
            this.drawingLineWidth = val;
            this.enterDrawMode();
        });
        this.drawingColor = hex2rgb($("#drawing-color").val(), 0.5)
        $(document).on("change", "#drawing-mode-selector", (e)=>{
            var val = $(e.target).val()
        });
    },
    enterDrawMode: function(){
        $.each(viewer.pages, (pn, page)=>{
            page.fcanvas.isDrawingMode = true
            page.fcanvas.freeDrawingBrush.color = this.drawingColor;
            page.fcanvas.freeDrawingBrush.width =  this.drawingLineWidth;;
        });
        //$(this.selector).show();
    },
    leaveDrawMode: function(){
        $.each(viewer.pages, (pn, page)=>{
            page.fcanvas.isDrawingMode = false
        });
        //$(this.selector).hide();
    }
};

DrawMode.init();

