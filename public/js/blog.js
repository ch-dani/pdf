var blog_page = 1;
var stop_it = false;
var loading_now = false;


document.addEventListener('DOMContentLoaded', function(){ 



	(function($) {
		$.fn.donetyping = function(callback){
			var _this = $(this);
			var x_timer;    
			_this.keyup(function (){
				clearTimeout(x_timer);
				x_timer = setTimeout(clear_timer, 1000);
			}); 

			function clear_timer(){
				clearTimeout(x_timer);
				callback.call(_this);
			}
		}
	})(jQuery);


	var win = $(window);
	var fh = $("footer").height();

	win.scroll(function() {
		if(stop_it || loading_now){
			return false;
		}
		
		if ($(document).height() - win.height() <= win.scrollTop()+fh+100) {
			blog_page++;
			if(blog_page>total_pages){
				stop_it = true;
			}
			loadPosts();
		}
		
	});
	
	function block(){
		$(".blog-container").addClass("blocked");
	}
	
	function unblock(){
		$(".blog-container").removeClass("blocked");
	}
	
	function loadPosts(search=false){
		search = $("input[name='s']").val();
		
		if(stop_it || loading_now){
			return false;
		}
		console.log(blog_page);
		var category_id = $(".change_blog_category.active").data("id");
		loading_now = true;
		block();
		$.ajax({
			method: "GET",
			url: window.location.pathname,
			data: {category: category_id, page: blog_page, is_ajax: true, search: search},
			dataType: "json",
			success: function(data){
				loading_now = false;
				$("#posts_not_found").hide();

				if(search && $(data.html).find(".posts article").length==0){
					$('#blog_loading').hide();
					$(".posts").html("");
					$("#posts_not_found").show();
					unblock();
					return false;
				}else{
				}
				total_pages = data.total_pages;
				if(blog_page==1 || search!=""){
					$(".posts").html("");
				}
				$(".posts").append($(data.html).find(".posts").html());
//				$('#blog_loading').hide();
				unblock();
			},
			error: function(error){
				loading_now = false;
				$('#blog_loading').html("ERROR");
				console.log(error);
				unblock();

			}
		});
	
	}
	$("input[name='s']").on("keyup", function(){
		if($(this).val() && $(this).val().length>=3){
			$(".blog_search").removeClass("hidden");
		}else{
			$(".blog_search").addClass("hidden");
			
		}
	});
	
	$("#search_input").donetyping(function(){
		console.log("done");
//		if($(this).val().length>=3){
//		}else{
//			
//		}
		loadPosts();
	});
	
	$(document).on("submit", ".search", function(e){
		e.preventDefault();
		loadPosts();
	});
	
	$(document).on("click", ".blog_search", function(){
		stop_it = false;		
		loadPosts();
	});
	
	
	$(document).on("click", ".change_blog_category", function(e){
		stop_it = false;
		//e.preventDefault();
		$(".change_blog_category").removeClass("active");
		$(this).addClass("active");
		$("input[name='s']").val("");
		blog_page = 1;
		loadPosts();
	});


	$(document).ready(function () {
		if($(document).innerWidth() > 991) {
			var pb_width = $(".posts-bar").width();
			var listWidth = $('#list-cat').innerWidth()-75;
			var search_width = pb_width-listWidth;
			$(".search").css("max-width", search_width);
			var loopWidth = 0;
			var flag = false
			$('#list-cat ul li:not(.mcl)').each(function (index) {
				elv = $(this).innerWidth();
				loopWidth = loopWidth + $(this).innerWidth();
				if (listWidth < loopWidth) {
					flag = true;
					console.log("apend");
					$('.dropdown-cat').append('<li>' + $(this).html() + '</li>');
					$(this).remove();
				}
			});
			if(!flag){
				$(".mcl").remove();
			}
		}
		$("#list-cat").on('click', '.more-cat', function(e) {
			e.preventDefault();
			$(this).next().toggleClass('active');
		});
	});


	
	
});

