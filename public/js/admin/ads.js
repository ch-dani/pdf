$(document).ready(function () {

	$(document).on("click", ".remove_existed_ad", function(){
		$(this).closest(".ad_outer").remove();
		return false;
	});
	$(document).on("click", "#add_new_ad", function(){
		var textareaCount = 0;
		let adTextarea = `
			<div class="ad_outer">
				<textarea name="page_ads[]" class="code_textarea  m-sm form-control" cols="50" rows="8" placeholder="Insert ads code..."></textarea>
				<div class="form-group">
					<br>
					<button type="button" class="btn btn-danger btn_remove remove_existed_ad" >Remove</button>
				</div>
			</div>
		`;
		
		$(".code_textarea_wrapper").append(adTextarea);
	});
	$(document).on("click", "#save_page_ads", async function(e){
		e.preventDefault();
		
		var ads = {};

		$(".code_textarea").each(function(i,v){
			ads[i] = $(v).val();
		});
		


		Swal.fire({
			title: 'Saving...',
			onBeforeOpen: () => {Swal.showLoading() }
		})
		
		
		
		$.ajax({
			url: save_url,
			method: "post",
			dataType: "JSON",
			data: {
				_token: $('meta[name="csrf-token"]').attr('content'),
				page_id: page_id,
				ads: ads
			},
			success: function(data){
				console.log(data);
				if(data.success){
					Swal.fire({
						type: 'success',
						title: 'Success'
					});
				}else{
					Swal.fire({
						type: 'error',
						title: 'error'
					});
				}
			},
			error: function(error){
					Swal.fire({
						type: 'error',
						title: 'error'
					});
			}
		});
		
		

	});


    let options = {
        'paging': true,
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'pageLength': 15,
        'order': [[0, "asc"]]
    };


    $('#ads').DataTable(options)



});
