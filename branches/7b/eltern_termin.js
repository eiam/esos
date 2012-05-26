$(document).ready(function() {
	konfigurieren();
});

function reloadLehrer() {
	$.ajax({
		type: "POST",
		url: "eltern_termin.ajax.php",
		data: "reloadLehrer=true"+"&sid="+$("#sid").val(),
		dataType: "text",
		cache: "false",
		success: function(msg){
			$("#lehrerauswahl").html(msg);     
		}
	});
}

function reloadTable(tid) {
	$.ajax({
		type: "POST",
		url: "eltern_termin.ajax.php",
		data: "reload=true"+"&sid="+$("#sid").val(),
		dataType: "text",
		cache: "false",
		success: function(msg){
			$("#termine").html(msg);
			if (tid>0) {
				$("#success"+tid).removeClass('hidden');
				setTimeout("$('#success'+"+tid+").fadeOut()", 2000);
			}
		}
	});
}

function weitere_lehrer() {
	if ($('#lehrer').val()=="other") {
		window.location.href = "eltern_lehrer.php";
   	}
}

function konfigurieren() {

	$("#sid").live( 'change', function() {
		$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "kindwechseln=true&sid="+$("#sid").val(),
			dataType: "text",
			cache: "false",
			success: function(msg){
				reloadTable();
				reloadLehrer();
				$("#info").html(msg);
			}
		});		
	});

    // Termin mit Lehrer hinzufügen
    $("#terminhinzufuegen").live( 'click', function() {
		weitere_lehrer();
		$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "insert=true&sid="+$("#sid").val()+"&Lid="+$("#lehrer :selected").val(),
			dataType: "text",
			cache: "false",
			success: function(msg){
				reloadTable();
				reloadLehrer();
			}
		});
    });
	
	// Bestätigen-Button
    $("#weiter").click( function() {
		window.location.href = "eltern_ausgabe.php";
    });
    
    // Zeit des Termins ändern
    $("select.zeit").live( 'change', function() {
        var tid=$(this).attr('id').slice(4);
        $("#working"+tid).fadeIn('fast');
		$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "terminspeichern=true&tid="+tid+"&zeit="+$(this).val(),
			dataType: "text",
			cache: "false",
			success: function(msg){
				reloadTable(tid);
				reloadLehrer();
				$("#info").html(msg);
			}
		});
    });
    
    // Termin löschen
    $(".delete").live( 'click', function() {
		var tid=$(this).attr('id').slice(6);
    	$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "loeschen=true&tid="+tid,
			dataType: "text",
			success: function(msg) {
				reloadTable();
				reloadLehrer();
				$("#info").html(msg);
			}
		});
    });

    // Weitere Lehrer
    $('#lehrer').live( 'change', function() {
    	weitere_lehrer();
    });
    
    $('#other').live( 'click', function() {
    	window.location.href = "eltern_lehrer.php";
    });
    
}



