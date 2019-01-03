$(document).ready(function(){
	$("#container #btn_get_digest").bind("click", get_digest);
	
	function get_digest(){		
		var open_message = $("#container textarea").val();
		
		$.ajax({
			url: "php/handler.php",
			type: "POST",
			data: ({"open_message": open_message}),
			success: function (success_hashing){
				$("#container #digest").empty();
				$("#container #digest").append(success_hashing);
			}
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/*$("#container #btn_get_digest").bind("click", get_digest);
	
	function get_digest(){		
		var open_message = $("#container textarea").val();
		
		$.ajax({
			url: "php/handler.php",
			type: "POST",
			data: ({"open_message": open_message}),
			success: function (success_hashing){
				$("#container #div_digest").empty();
				$("#container #div_digest").append("<span><b>Digest: </b></span>");
				$("#container #div_digest").append("<span>" + success_hashing + "</span>");
			}
		});
	}*/
});