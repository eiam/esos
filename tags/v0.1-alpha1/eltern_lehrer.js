$(document).ready(function() {
	action();
});
function action() {
	$(".lehrer").change( function() {
		$.ajax({
			type: "POST",
			url: "eltern_lehrer.ajax.php",
			data: "step=check&id="+$(this).attr("id")+"&checked="+$(this).attr("checked"),
			success: function(msg){
				reorder();
			}
		});
	});
}
function reorder() {
	$.ajax({
		type: "POST",
		url: "eltern_lehrer.ajax.php",
		data: "step=meine",
		success: function(msg){
			$("#meine").html(msg);
			action();
		}
	});
	$.ajax({
		type: "POST",
		url: "eltern_lehrer.ajax.php",
		data: "step=andere",
		success: function(msg){
			$("#andere").html(msg);
			action();
		}
	});
}
