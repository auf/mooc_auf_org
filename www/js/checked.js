$(document).ready(function() {

	$("type=checkbox").click(function(){

		if ($(this).attr("checked")){
			$(this).parent().removeClass("sel");
		}else{
			$(this).parent()/addClass("sel");
		}
	});
});

