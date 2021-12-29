function mergeLetters(parent) {
	parent = $(parent);
    var words = [];
    var i = 0;
    var canvas = document.createElement('canvas');
    canvas.mozOpaque = true;
    var ctx = canvas.getContext('2d', {alpha: false});


	space_prec = 70;
	if(window.innerWidth<=1600){
		space_prec = 40;
	}
	if(window.innerWidth<=1300){
		space_prec = 8;
	}else{
	
	}



    $('.text_content_element', parent).each(function () {
        var word = {
            comp: {
                top: this.style.top,
                fontFamily: this.style.fontFamily,
                fontName: this.getAttribute('original-font-name'),
                color: this.getAttribute('original-color')
            },
            transform:this.getAttribute('transform'),
            height: parseFloat(window.getComputedStyle(this, null)["height"]),
            width: parseFloat(window.getComputedStyle(this, null)["width"]),
            fontSize: this.style.fontSize,
            left: parseFloat(this.style.left),
            html: this.innerHTML,
            el: this
        };

        if (
            typeof words[i] === "undefined" ||
            ( word.html.length  === 1 && compare(words[i][0].comp, word.comp) && ( word.left - words[i][words[i].length - 1].left ) < space_prec )
        ) {
            words[i] = typeof words[i] === "undefined" ? [] : words[i];
            words[i].push(word);
            words[i][0].height = words[i][0].height > word.height ? words[i][0].height : word.height;
        } else {
            i++;
            words[i] = [];
            words[i].push(word);
        }

    });

    words.forEach(function (letters) {

        if(letters.length <= 1){
            return;
        }

        var div = letters[0];
        var prevLeft = div.left + div.width;
        var width = 0;

        div.el.style.overflow = 'initial';
        div.el.style.height = div.height + "px";
        div.el.style.width = div.width + "px";
        div.el.innerHTML = div.html;
        //div.el.innerHTML = `<span style="top:0;left:0;position:relative;">${div.html}</span>`;

        letters.shift();

        letters.forEach(function (letter,i) {
            let shift = letter.left - div.left;
            let margin = letter.left - prevLeft;
            letter.el.className = "";
            letter.el.style.top = 0;
            letter.el.style.left = 0;
            letter.el.style.marginLeft = `${margin}px`;
            letter.el.style.position = 'relative';

            div.el.innerHTML += (margin > 3 ? " " : "") + letter.el.innerHTML;
            //div.el.innerHTML += letter.el.outerHTML.replace('div','span');

            letter.el.remove();

            prevLeft = letter.left + letter.width;

            width = width > (shift + letter.width + margin) ? width : (shift + letter.width + margin);

        });

        div.el.style.width = "initial";

        

        if(width > 0 && letters.length > 1){

            var style = window.getComputedStyle(div.el);
            var textWidth = getTextWidth({
                text:div.el.innerHTML,
                size:style.fontSize,
                font:style.fontFamily,
            });

            if(div.el.innerHTML === "Page 1"){
                console.log(width);
                console.log(textWidth);
            }
            div.el.style.letterSpacing = (width - textWidth)/letters.length + "px";
        }

    });
    
    var temp = [];
    var main_element = false;
    var next = false;

    $(".text_content_element:not(.fsfs)", parent).each(function(){
    	var el = $(this);
        if(!main_element){
            main_element = el;
        }
        main_element.addClass("fsfs");

        next = main_element.next().eq(0);
        
        let ml_right_top_corner = (parseFloat(main_element.css("left"))+main_element.width()),
            next_left_top_corner = (parseFloat(next.css("left"))),
            space_width = Math.ceil(parseFloat($(main_element).css('font-size'))) //$("<div id='space_width_test' style='position: absolute; left: -10000px; font-size: "+$(el).css("font-size")+"; font-family: \""+$(el).css("font-family")+"\", serif;'>&nbsp;</div>").appendTo($("body")).width();                                   
        
        $("#space_width_test").remove();
        
    	var el_l = parseFloat(main_element.css("left"))+parseFloat(main_element.css("width"));
    	var n_l = parseFloat(next.css("left"));
        var sp = n_l-el_l;
    	if(next){
    		if(main_element.css("font-family") == next.css("font-family")
    			&& main_element.css("font-size") == next.css("font-size")
    			&& main_element.css("top")==next.css("top")
    			&& main_element.css("font-weight")==next.css("font-weight")               
                && next_left_top_corner-ml_right_top_corner<=(space_width*1.5)
                && parseFloat(next.css("left"))>parseFloat(main_element.css("left"))
    			){
    			
    			var sp_count = Math.ceil(space_width/(next_left_top_corner-ml_right_top_corner));
    			
    			
    			//alert(main_element.html()+"  "+next_left_top_corner+" - "+ml_right_top_corner+"="+sp_count+" "+next.html()+"  space width: "+space_width+" sp_count "+sp_count);
    			//alert(main_element.html()+" \\ "+sp_count+" spw: "+space_width);
    			main_element.html(main_element.html()+(sp<1?"":" ".repeat(1))+next.html());
    			
//    			var w1 = parseFloat(main_element.css("left"))
//    			var w2 = parseFloat(next.css("left")) + parseFloat(next.css("width"));
//    			main_element.css("min-width", (w2-w1)+"px");

    			
    			next.remove();
    		}else{
    			main_element = el;
    		}
    	}
    });
    $(parent).addClass("merged_letter");
    $("#simplePDFEditor").addClass("proccessed");


    
    

    function getTextWidth(obj) {

        ctx.font = obj.size + ' ' + obj.font;

        return ctx.measureText(obj.text).width;

    }

    function compare(o1, o2) {

        for (const name in o1) {

            if (o1.hasOwnProperty(name)) {

                if (
                    !o2.hasOwnProperty(name) ||
                    o1[name] !== o2[name]
                ) {

                    return false;

                }

            }

        }

        return true;

    }

    return words;

}

