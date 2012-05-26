$(document).ready(function() {

	// Kind wechseln
	$("#sid").live( 'change', function() {
		$.ajax({
			type: "POST",
			url: "eltern_lehrer.ajax.php",
			data: "step=kindwechseln&sid="+$("#sid").val(),
			dataType: "text",
			cache: "false",
			success: function(msg){
				$("#info").html(msg);
				reorder();
			}
		});		
	});

	// Lehrer hinzufügen / entfernen
	$(".lehrer").live( 'change', function() {
		$.ajax({
			type: "POST",
			url: "eltern_lehrer.ajax.php",
			data: "step=check&id="+$(this).attr("id")+"&checked="+$(this).attr("checked"),
			dataType: "text",
			cache: "false",
			success: function(msg){
				$("#info").html(msg);
				reorder();
			}
		});
	});

	$(".xterminlink").live( 'click', function() {
		$.ajax({
			type: "POST",
			url: "eltern_lehrer.ajax.php",
			data: "step=mein_lehrer&id="+$(this).attr("ref"),
			dataType: "text",
			cache: "false",
			success: function(msg){
				window.location.href = "eltern_termin.php";
			}
		});
	});

	$(".terminlink").live( 'click', function() {
		window.location.href = "eltern_termin.php";
	});

});

function reorder() {
	$.ajax({
		type: "POST",
		url: "eltern_lehrer.ajax.php",
		data: "step=meine",
		dataType: "text",
		cache: "false",
		success: function(msg){
			$("#meine").html(msg);
		}
	});
	$.ajax({
		type: "POST",
		url: "eltern_lehrer.ajax.php",
		data: "step=andere",
		dataType: "text",
		cache: "false",
		success: function(msg){
			$("#andere").html(msg);
		}
	});
}
