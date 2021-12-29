
window.show_anyway = false;
window.skip_extract = 1;

var PDFINVOICE = {
	name: "PDFInvoice",
	need_preview: true,
	tool_section: $(".invoice-section"),
	preview_block: false, //$(".pages_preview_block"),
	preview_section: false,
	csfr: false,
	selectable_pages: true,
	toolurl: "/invoice-generator",
	data: { },
	load_blob: true,
	file_data: false,
	page_preview_width: 1065,
	one_canvas_for_all_pages: false,
	hide_before_upload: true,
	page_preview_items_selector: false,
	show_only_first_page: false,
	init: function(){
		window.show_anyway = true;
		this.bind();
	},
	bind: function(){
		var $this = this;
		$(document).on("click", "#generate_invoice", $.proxy($this.save, this));
	},

	// taskComplete: function(){

	// },
	save: function(e){
		var $this = this;

		function generateInvoice() {
			var texts = [];
			$(".border_bottom_elements").each(function () {
				texts.push({
					"type": "border_bottom",
					"text": "",
					"size": $(this).getElementOffset(false, true),
					"css": {
						"border-color": $(this).css("border-bottom-color")
					}
				});
			});	
			$(".border_elements").each(function () {
				texts.push({
					"type": "border",
					"text": "",
					"size": $(this).getElementOffset(false, true),
					"css": {
						"border-color": $(this).css("border-left-color")
					}
				});
			});
	
	
			$(".grab_img").each(function () {
				texts.push({
					"type": "image",
					"text": "",
					"size": $(this).getElementOffset(false, true),
					"src": $(this).attr("src"),
					"css": {
						"color": "red"
					}
				});
			});
			$(".bg_elements").each(function () {
				texts.push({
					"type": "bg",
					"text": "",
					"size": $(this).getElementOffset(false, true),
					"css": {
						"border-radius": px2mm(parseInt($(this).css("border-top-left-radius"))) + "mm",
						background: $(this).css("background-color")
					}
				});
			});
			$(".grab_it").each(function () {
				var lsp = px2mm(parseFloat($(this).css("letter-spacing")))
				texts.push({
					"text": $(this).is("input")?$(this).val():$(this).html(),
					"type": "text",
					"size": $(this).getElementOffset(false, true),
					"css": {
						"letter-spacing": (lsp?lsp:0.1) + "mm",
						"color": $(this).css("color"),
						"text-align": $(this).css("text-align"),
						//"text-align": "left",
						"font-size": px2mm(parseInt($(this).css("font-size"))) + "mm",
						"font-family": "Montserrat", //$(this).css("font-family"),
						"font-weight": $(this).css("font-weight") == 700 ? "bold" : "normal",
						//"border-radius": px2mm(parseInt($(this).css("border-top-left-radius")))+"mm",
						//background: "red", //$(this).css("background-color")
					}
				});
			});
	
			return texts;
		}
		texts = generateInvoice();
	
		total_tr = $(".invoice-table tbody tr").length;
		tr_height = px2mm($(".invoice-table-wrapper tbody tr").eq(0).height());
		add_page_size = total_tr*tr_height;
		
		add_page_size += px2mm($("#textarea-main").height())



		PDFTOOLS.startTask()
		var intervalID = setInterval( function() { 
			console.log("upload progress is "+spe.upload_in_progress);
			if(!spe.upload_in_progress){
				clearInterval(intervalID);			
				$this.ajax({
					uuid: UUID, 
					texts: texts,
					add_page_size: add_page_size
					//file_name: $this.file.name 
				}
				).then($this.taskComplete);

			}
		} , 250);	

		return false;
	},
	
}
if(PDFINVOICE.tool_section.length>0){
	PDFINVOICE = $.extend(PDFTOOLS, PDFINVOICE);
	PDFINVOICE.main();
}


$(document).on("click", ".logo_outer", function(e){
	e.preventDefault();
	$("#inputfile").trigger('click');	
});


$(document).on("keypress", ".disable_enter", function(e){
	var keyCode = e.keyCode || e.which;
	if (keyCode === 13) { 
		e.preventDefault();
		return false;
	}
});

$(document).on("keypress", ".float_only", function(event) {
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});


$(document).on("click", "#generate_invoice1", function (e) {
	var tables = {};

	


	e.preventDefault();
	$.ajax({
		method: "POST",
		url: "invoice-generator",
		data: {
			texts: texts,
			add_page_size: page_add_size
		},
		dataType: "json",
		headers: {
			'X-CSRF-TOKEN': $("#editor_csrf").val()
		},
		success: function (data) {

			console.log(data);
		},
		error: function (error) {
			console.log(error);
		}
	});

});

$(document).ready(function () {
	// Select File

    window.onbeforeunload = function(){
        return "You have attempted to leave this page. Are you sure?";
    };

	

	$(".file-btn").on('click', function (e) {
		e.preventDefault();
		$("#inputfile").trigger('click');
	});

	$("#inputfile").change(function (e) {
		$(".without_image").removeClass("without_image");
		var fileName = '';
		var input = $(this)[0];
		fileName = e.target.value.split('\\').pop();

		var reader = new FileReader();
		reader.onload = function (e) {
		   $('.pdf_logo').attr('src', e.target.result);
		}
	    reader.readAsDataURL(input.files[0]);		
		if ($('#inputfile').get(0).files.length !== 0) {
			$(".input-file-box .file-name span").text(fileName);
			$(".delete-file").fadeIn();
			$(".file-btn").text("Choose another file");
		}
	});

	$(".delete-file").on('click', function (e) {
		e.preventDefault();
		$("#inputfile").val("");
		if ($('#inputfile').get(0).files.length === 0) {
			$(".input-file-box .file-name span").text("No file");
			$(".delete-file").fadeOut();
			$(".file-btn").text("Choose a file");
		}
	});

	// Add row
	$("#add-row").click(function (e) {
		e.preventDefault();
		var countTr = $('table.invoice-table tbody tr').length;
		countTr++;
		
		$("table.invoice-table tbody").append(
		`<tr class="border_elements">
				<td class="number"><span class="grab_it">${countTr}</span></td>
				<td class="desc">
					<span class="grab_it" onfoc1us="document.execCommand(\'selectAll\',false,null)" contenteditable="true" type="text" value="">Supporting of in-house project (hours worked)</span>
				</td>
				<td class="quantity">
					<span class="grab_it float_only" contenteditable="true" value="1">1</span>
				</td>
				<td class="price">
					<span class="grab_it float_only" contenteditable="true" value="125.00">125.00</span>
				</td>
				<td class="total"><span class="grab_it val">125.00</span></td>
		</tr>`);
		
		subtotalCalc();
		totalCalc();
		$(".active_color").click();
	});

	// Delete row
	$("#delete-row").click(function (e) {
		e.preventDefault();
		if($('table.invoice-table tbody tr.del').length!=0){
			$('table.invoice-table tbody tr.del').remove();
			$('table.invoice-table > tbody  > tr').each(function (index) {
				$(this).find("td.number").text(index + 1);
			});
		}else{
			$('table.invoice-table tbody tr').last().remove()
		}
		subtotalCalc();
		totalCalc();
	});

	$('.invoice-table').on('click', 'tbody tr td.number', function () {
		$(this).parent().toggleClass("del");
	});

	// Subtotal
	function subtotalCalc() {
		var subtotalCount = 0;
		$('table.invoice-table > tbody  > tr .val').each(function (index) {
			subtotalCount = subtotalCount + parseFloat($(this).html())  //parseFloat($(this).find("td.total .val").text());
		});


		$(".item-price.subtotal-item > .price").text(parseFloat(subtotalCount).toFixed(2));
		var salesCountValTotal = parseFloat($("#salex-tax-input").html()) / 100;
		$(".item-price.sales-item > .sales-price").html(parseFloat(subtotalCount * salesCountValTotal).toFixed(2));
	}

	// Subtotal
	function totalCalc() {
		var totalCount = 0;
		var subtotalCountVal = parseFloat($(".item-price.subtotal-item > .price").text());
		var salesCountVal = parseFloat($(".item-price.sales-item > .sales-price").text());

		$(".item-price.total-item .total-count").text(parseFloat(subtotalCountVal + salesCountVal).toFixed(2));

	}


	// change Quantity or Unit price
	$(document).on('input', 'tbody tr td.quantity span, tbody tr td.price span', function () {
		var quantity = parseInt($(this).closest("tr").find("td.quantity span").html());
		var price = parseFloat($(this).closest("tr").find("td.price span").html());

		var totalCountItem = quantity * price;
		
		if(isNaN(totalCountItem)){
			totalCountItem = 0;
		}
		
		$(this).closest("tr").find("td.total .val").html(parseFloat(totalCountItem).toFixed(2));
		
		subtotalCalc();
		totalCalc();
	});

	//Salex Tax
	$("#salex-tax-input").on("input", function () {
		var sale = parseFloat($(this).html()) / 100;
		var subtotalVal = parseFloat($(".item-price.subtotal-item > .price").text());
		var saleCount = subtotalVal * sale;
		if(isNaN(saleCount)){
			saleCount= 0 ;
		}
		
		$(".item-price.sales-item > .sales-price").text(saleCount.toFixed(2));
		totalCalc();
	});


	var textarea = document.querySelector('textarea');
	textarea.addEventListener('keydown', autosize);

	function autosize() {
		var el = this;
		setTimeout(function () {
			el.style.cssText = 'height:auto; padding:0';
			// for box-sizing other than "content-box" use:
			// el.style.cssText = '-moz-box-sizing:content-box';
			el.style.cssText = 'height:' + el.scrollHeight + 'px';
		}, 0);
	}
	$(document).ready(function () {
		$("#datepicker").datepicker({
			dateFormat: "dd-M-yy"
		});
	});
	$(".chose-color a").on("click", function (e) {
		$(".active_color").removeClass("active_color");
		$(this).addClass("active_color");
		e.preventDefault();
		var bgColor = table_border_color = $(this).attr('data-bgcolor');
		var textColor = table_text_color = $(this).attr('data-color');
		$('.header-invoice').css("border-bottom", "1px solid " + bgColor);
		$('.invoice-table thead tr').css("background-color", bgColor);
		$('.invoice-table thead tr th').css("color", textColor);
		$('.invoice-table tbody tr').css("border", "1px solid " + bgColor);
		$('.body-invoice .item-price').css({
			"background-color": bgColor,
			"color": textColor
		});
		$('.item-price.sales-item .sales-input input[type="text"]').css("color", textColor);
		$('.invoice-table thead').css("border", "1px solid " + bgColor);
	});
	
	subtotalCalc();
	totalCalc();
	

});

var table_border_color = "#F1F2F5";
var table_text_color = "rgb(51, 51, 51);";

