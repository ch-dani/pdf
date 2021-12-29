//$(".hint-block").remove()
//detectTables(1)

//var pt_to_mm =  0.3527777777778;
var PDF_PASSWORD = "";

window.show_anyway = true;
window.skip_extract = true;

var ExcelPDF = {
	name: "ExcelPDF",
	need_preview: true,
	tool_section: $(".tool_section"),
	preview_section: false,
	csfr: false,
	pages_list: false,
	pages_ranges_blocs: false,
	page_preview_width: false,
	preview_block: false,
	selectable_pages: false,
	pages_ranges: false,
	toolurl: "/tool/pdf-to-excel",
	random_colors: [],
	data: { tables: {} },
	fill_range: false,
	show_only_first_page: true,
	override_scale: 1.5,
	need_scrool: false,
	init: function(){
		this.bind();
	},
	first_page_rendered: false,
	bind: function(){
		$(document).on("file_selected", function(){
			$(".before_upload").addClass("hidden");
			$(".after_upload").removeClass("hidden");
		})
	
		$(".download-result-link").css("background-image", "url('/img/ext/xls.svg')");
		$(".page-between").addClass("hidden");
		$(".page-side-bar").hide();
		$(".page-side-bar").addClass("hidden");
		$(".delete-page").addClass("hidden");
	
		$(document).on("click", "#start_task", $.proxy(this.save, this));

		$(document).on("click", "#save_csv", $.proxy(this.save, this));
		
		$(document).on("textlayerrendered", function(page){
			if(!ExcelPDF.first_page_rendered){
				ExcelPDF.first_page_rendered = true;
				$(".before_upload").addClass("hidden");
				$(".after_upload").removeClass("hidden");
			}
			detectTables($(page.target));
		});


	},
	save: function(e){
		e.preventDefault();
		var $this = this;
		var type = 'xls';
		if($(e.target).hasClass("csv")){
			type = "csv";
		}
		



		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			

				$this.ajax({uuid: UUID, 
				
				numPages: spe.pdfDocument.pdfInfo.numPages,
				
				UUID: UUID,  type: type, tables: PDFTOOLS.data.tables,
				file_name: $this.file.name,
				basename: $this.file.name.split(".")[0]
				}).then($this.taskComplete);

			}
		} , 250);	
		return false;
	},
};




function Column(t) {
    this.texts = Array.isArray(t) ? t : [t];
    this.updateDimensions = function() {
        this.top = Math.min(...this.texts.map(function(t) { return t.top }))
        this.left = Math.min(...this.texts.map(function(t) { return t.left }))
        this.right = Math.max(...this.texts.map(function(t) { return t.right }))
        this.bottom = Math.max(...this.texts.map(function(t) { return t.bottom }))
    }
    
    this.updateDimensions()

    this.add = function(t) {
        this.texts.push(t)
        this.updateDimensions()
    }

    this.toString = function() {
        var texts = this.texts.map(function(t){ return t.text }).join(' ')
        return [this.top, this.left, this.bottom, this.right, texts].toString()
    }

    this.mergeWith = function(other) {
        var mergedTexts = this.texts.concat(other.texts)
        return new Column(mergedTexts);
    }
    return this;
}



function MultiLineBlock(l) {
    this.lines = [l]
    this.columns = []

    this.addLine = function(l) {
        this.lines.push(l)
    }
    
    this.top = function() {
        return Math.min(...this.columns.map(function(c) { return c.top }))
    }

    this.left = function() {
        return Math.min(...this.columns.map(function(c) { return c.left }))
    }
    this.right = function(){
        return Math.max(...this.columns.map(function(c) { return c.right }))
    }
    this.bottom = function() {
        return Math.max(...this.columns.map(function(c) { return c.bottom }))
    }
    
    this.getBlockSize = ()=>{
        $.each(this.lines, (i, v)=>{
            this.lines[i].right = Math.max(...this.lines[i].texts.map(function(t) { return t.left+t.width }));
            this.lines[i].left = Math.min(...this.lines[i].texts.map(function(t) { return t.left }));
            this.lines[i].top = Math.min(...this.lines[i].texts.map(function(t) { return t.top }));
            this.lines[i].bottom = Math.max(...this.lines[i].texts.map(function(t) { return t.bottom }));
        });
        
        this.left = Math.min(...this.lines.map(function(t) { return t.left }));
        this.right = Math.max(...this.lines.map(function(t) { return t.right; }));
        this.top = Math.min(...this.lines.map(function(t) { return t.top; }));
        this.bottom = Math.max(...this.lines.map(function(t) { return t.bottom; }));;
        return {left: this.left, right: this.right, top: this.top, bottom: this.bottom};
    }
    
    this.toString = function() {
        return '[\n' + this.lines.join('\n') + '\n(' + this.top() +', '+ this.left() +')(' + this.bottom() +', '+ this.right() +')]'
    }
    this.findColumn = function(t) {
        return this.columns.find(function(c){
            var noOverlap = t.right < c.left || t.left > c.right
            if(!noOverlap) {
                return c
            }
        })
    }

    this.addColumn = function(c) {
        this.columns.push(c)
    }
    var self = this
    this.decomposeColumns = function() {
        var it = 0;
        this.lines.forEach(function(line) {
            line.texts.forEach(function(t) {
                var col = self.findColumn(t)
                if(!col) {
                    var new_c = new Column(t);
                    self.addColumn(new_c)
                } else {
                    col.add(t)
                }
            });
            it++;
        })
        this.columns = this.columns.filter(function(c){
            var existsAnotherThatIncludesC = self.columns.find(function(other){
                var same = other.left == c.left && other.right == c.right
                var otherIncludesC = other.left <= c.left && other.right >= c.right
                return !same && otherIncludesC
            })
            return !existsAnotherThatIncludesC
        });
        
        this.columns.sort(function(a, b){
            return a.left - b.left
        })

        var mergedColumns = [];
        var threshold = 5;

        for(var i = 0; i < this.columns.length; i++) {
            var c = this.columns[i];
            var nextColumn = this.columns[i + 1];
            if(nextColumn) {
            }
            if(nextColumn && (nextColumn.left - c.right < threshold)) {
                mergedColumns.push(c.mergeWith(nextColumn));
                i += 1
            } else {
                mergedColumns.push(c);
            }
        }

        if(mergedColumns.length != this.columns.length) {
            this.columns = mergedColumns;
        }
    }

    return this;
}



function Line(t) {
    this.texts = [t]
   
    this.left = Math.min(...this.texts.map(function(t) {  return t.left }));
    this.right =  Math.max(...this.texts.map(function(t) { return t.left+t.width }));
    this.top = Math.min(...this.texts.map(function(t) {  return t.top }))
    this.bottom = Math.max(...this.texts.map(function(t) { return t.top+t.height; }))
    
    
    this.getMaxBottom = ()=>{
        return Math.max(...this.texts.map(function(t){
            return t.bottom;
        }));        
    }

    this.getMinTop = ()=>{
        return Math.min(...this.texts.map(function(t){
            return t.top;
        }));        
    }    

    this.add = function(t) { this.texts.push(t) }
    this.toString = function() { return this.texts.map(function(t) { return t.text }).join(',') }
    this.toStringEx = function() { return '[top:'+ this.top +', bottom:'+ this.bottom +', texts:[' + this.texts.map(function(t) { return t.text }).join(',') + ']]' }
    this.multiline = function() { return this.texts.length > 1 }
    this.sort = ()=>{
        this.texts = this.texts.sort(function(a, b){
            return a.left - b.left
        })
    }

    return this;
}


function detectTables($textLayer) {
    var $pageWrap = $textLayer.closest(".page");
    var pageNum = $pageWrap.data("page-number");
    var texts = [];
    texts = $textLayer.find('div:not(.eba_div)').map(function(i, elem){
        var $this = $(elem)
        var position = $this.position()
        var height = $this.height()
        var width = $this.width()

        return new Text({
            text: $this.text(),
            node: $this,
            top: position.top,
            bottom: position.top + height,
            left: position.left,
            right: position.left + width,
            height: height,
            width: width,
        })
    }).toArray();
    
    console.log("test is ", texts);
    
    if(texts.length==0 || texts.length==1){
    	$(".fixed-bottom-panel").addClass("hidden");
    	swal("Error", "We're sorry, image-only scanned documents are not supported.", "error");
    	return false;
    }
    
    
    texts = texts.filter(function(t){
        return t.text != '';
    });

    texts = texts.sort(function(a, b) {
        return a.top - b.top
    })

    if(texts.length == 0) return;

    var lines = []
    var perci = 0;
    texts.forEach(function(t) {
        var last = lines[lines.length - 1]
        t.top = parseInt(t.top.toFixed(0));
        //TODO костыль для некоторых блоков. если в них текст налазит или еще чего.
        t.bottom = parseInt(t.bottom.toFixed(0))-3;
        if(last){
            last.bottom = parseInt(last.getMaxBottom().toFixed(1));
            last.top = parseInt(last.getMinTop().toFixed(1));
            //console.log(t.text, " last.top ", last.top, "t.top", t.top, " last.bottom", last.bottom);
        }
        if(last && ((last.top <= t.top+perci && t.top <= last.bottom) || (last.top <= t.bottom+perci && t.bottom <= last.bottom+perci)   ) ) {
            last.add(t)
        } else {
            nl = new Line(t);            
            lines.push(nl)
        }
    });
    
    //TODO mb uncomment
//    lines.forEach(function(l){
//        l.sort()
//    })

        
    
    function round3(x) {
        return Math.ceil(x/3)*3;
    }

    function mode(array) {
        if (array.length == 0)
            return null;
        var modeMap = {};
        var maxEl = array[0], maxCount = 1;
        for (var i = 0; i < array.length; i++) {
            var el = array[i];
            if (modeMap[el] == null)
                modeMap[el] = 1;
            else
                modeMap[el]++;
            if (modeMap[el] > maxCount) {
                maxEl = el;
                maxCount = modeMap[el];
            }
        }
        return maxEl;
    }

    
    function distanceOf(i) {
        var l = lines[i]
        var prev = lines[i-1]
        if(l && prev) {
            return Math.round(l.top - prev.bottom)
        }
    }
    
    var dist = []
    lines.forEach(function(l, i){
        var d = distanceOf(i)
       // console.log(d, "on line ",i, l)
        if(d) dist.push(d)
    })

    dist = dist.sort(function(a, b){
        return a - b
    });
    
    var variance = 20;
    if(dist.length > 0) {
        var std = math.std(...dist), 
            mean = math.mean(...dist);
        variance = mean + std / 2;
    }
    
    var blocks = []
    var multiMode = false;

    function areFollowingSingleLines(start, upTo) {
        if(start + upTo >= lines.length) return false
        var i = 0
        while(i < upTo) {
            var l = lines[start + i]
            if(l.multiline()) return false
            i++
        }
        return true
    }
    
    
    for(var i=0; i< lines.length; i++){
        var line = lines[i]
        var d = distanceOf(i)
        var seemsBlockBreak = d && d > variance;
        //console.log("line is", d, "is variance", variance);
        
        if(!line.multiline() ){
            continue;
        }
        
        if(seemsBlockBreak || blocks.length==0){
            block = new MultiLineBlock(line);
            blocks.push(block)
        }else{
            var mlb = blocks[blocks.length - 1]
            mlb.addLine(line)
        }
        //TODO хуерга с сейджи
        if(false){
            if(line.multiline()) {
                if(seemsBlockBreak) {
                    multiMode = false
                }
                if(!multiMode) {
                    block = new MultiLineBlock(line);
                    blocks.push(block)
                    multiMode = true
                } else {
                    var mlb = blocks[blocks.length - 1]
                    mlb.addLine(line)
                }
            } else {
                var fewSingleBlocks = areFollowingSingleLines(i, 2)

                if(fewSingleBlocks && !seemsBlockBreak) {
                    i++ 
                } else {
                    multiMode = false
                }
            }
        }
    }
    
    var table_iterator = 0;
    blocks.forEach(function(b) {
        b.getBlockSize();
        b.decomposeColumns()
        
        var bHeight = b.bottom-b.top;
        var bWidth = b.right-b.left; //-left_is;

        var padding = 7
        var scale = window.current_fw_scale;

        var $hint = $('<div class="hint-block"></div>').css({
            top: b.top - padding, 
            left: b.left - padding,
            width: bWidth + 2 * padding, 
            height: bHeight + 2 * padding
        });
        

        
        
        if(typeof ExcelPDF.data.tables[pageNum] == 'undefined'){
        	ExcelPDF.data.tables[pageNum] = {};
        }
        ExcelPDF.data.tables[pageNum][table_iterator] = {
        	top: parseInt(px2mm(b.top-padding)/pt_to_mm),
        	left: parseInt(px2mm(b.left)/pt_to_mm),
        	width: parseInt(px2mm(bWidth + 2 * padding)/pt_to_mm),
        	height: parseInt(px2mm(bHeight + 2 * padding)/pt_to_mm)
        };
        

        var $hintInner = $('<div class="hint-block-inner"></div>')

        $hint.append($hintInner)
        $pageWrap.append($hint)

        $textLayer.css({ opacity: 1 })
        $pageWrap.find('canvas').css({ opacity: '0.3' })

        var bPos = $hint.position();
        var height = $hint.height();

        var table = {
            rows: [], columns: [], pageNum: pageNum
        }
       	if(true){
		    b.columns.forEach(function(c, i) {
		        // text in tables should be visible
		        c.texts.forEach(function(t) {
		            //t.node.css({ color: 'red' });
		           // t.node.remove()
		        })

		        var top = padding, left = c.left - bPos.left, width = (c.right - c.left);
		        var $column = $('<div class="hint-block-column"></div>').css({
		            top: top, left: left,
		            width: width, height: height,
		            position: "absolute",
		        });
		        
		        $hintInner.append($column)

		        var column = {
		            left: c.left/scale,
		            top: b.top/scale,
		            width: width/scale,
		            height: bHeight/scale,
		        }
		        table.columns.push(column)
		    })
        }

        
        // draw rows
        if(true){
        	var cels = { };
        	
        	var ri = -1;
		    b.lines.forEach(function(l){
		    	ri++;
		    	
		        var top = l.top - bPos.top
		        var width = $hint.width()
		        var height = (l.bottom - l.top)
				
				var cel_n = -1;
				if(typeof cels[ri]=='undefined'){ cels[ri] = {}; }
				
				
		        l.texts.forEach(function(t) {
		        	cel_n++;
		        	//console.log(t);
		        	cels[ri][cel_n] = t.text;
		           //t.node.css({ color: 'red' });
		           // t.node.remove()
		        });

		        var $row = $('<div  class="hint-block-row"></div>').css({
		            top: top,
		            left: 0,
		            width: width, 
		            height: height,
		            position: "absolute",
		            
		        })
		        $hintInner.append($row)

		        var row = {
		            x :  px2mm(b.left-padding)/pt_to_mm, //new Big(b.left.div(scale).round().toFixed(),
		            y : px2mm(l.top-padding)/pt_to_mm, //new Big(l.top).div(scale).round().toFixed(),
		            w: px2mm(bWidth+padding)/pt_to_mm, //new Big(bWidth).div(scale).round().toFixed(),
		            h: px2mm(height+padding)/pt_to_mm //new Big(height).div(scale).round().toFixed()
		        }
		        table.rows.push(row)
		    })
        }
        
        table_iterator++;
        ExcelPDF.data.cels = cels;
        ExcelPDF.data.table= table;
        
        //console.log("table is ", table);
    })
    
    
}


function Text(data) {
    $.extend(this, data)
    return this;
}



function px2mm(px, scale=false){
	if(scale){
		
	}else{
		scale = typeof spe.pdfViewer.currentScale=='undefined'?1:spe.pdfViewer.currentScale;
	}
    return px / pixel_ratio / scale;
}


if(ExcelPDF.tool_section.length>0){
	ExcelPDF = $.extend(PDFTOOLS, ExcelPDF);
	ExcelPDF.main();
}


