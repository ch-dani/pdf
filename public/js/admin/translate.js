
var Translate = function(){
	this.name = "Translate Pricing";
	this.table = $("#translate_price_table");
	this.tbody = $("#translate_price_table tbody");
	this.num = 100;
	
	
	this.doc = $(document);
	this.bind = () =>{
		this.doc.on("click", "#add_new_range", (e)=>{
			e.preventDefault();
			var clone = this.tbody.find(".example_tr").html();
			//clone.removeClass("hidden").removeClass("example_tr");
			clone = "<tr>"+clone.replace(/%num%/g, this.num)+"</tr>";
			
			this.tbody.append(clone);
			this.num++;
		});

		this.doc.on("click", ".remove_range", (e)=>{
			e.preventDefault();
			var tr = $(e.target).closest("tr");
			tr.remove();
		});
		this.doc.on("submit", "#transPriceForm", $.proxy(this.save, this));
	};

	this.save =(e)=>{
		e.preventDefault();
		var fd = $(e.target).serialize();
		$.ajax({
			method: "POST",
			url: "/admin/setting/translate-pricing",
			data: fd,
			dataType: "json",
			success: function(data){
				swal("Success", "", "success");
				console.log(data);
			},
			error: function(error){
				console.log(error);
			}
		});		
	};
	
	
	this.bind();
	return this;
}

var trans = new Translate();


