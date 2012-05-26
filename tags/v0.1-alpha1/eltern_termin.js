$(document).ready(function() {
	konfigurieren();
});

function reloadLehrer() {
	$.ajax({
		type: "POST",
		url: "eltern_termin.ajax.php",
		data: "reloadLehrer=true"+"&sid="+$("#sid").val(),
		success: function(msg){
			$("#lehrer").html(msg);       
		}
	});
}

function reloadTable() {
	$.ajax({
		type: "POST",
		url: "eltern_termin.ajax.php",
		data: "reload=true"+"&sid="+$("#sid").val(),
		success: function(msg){
			$("#termine").html(msg);
		}
	});
}

function konfigurieren() {
    
    // Termin mit Lehrer hinzufügen
    $("#terminhinzufuegen").click( function() {
		$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "zeitabfrage=true&sid="+$("#sid").val()+"&Lid="+$("#lehrer :selected").val(),
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
		$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "terminspeichern=true&tid="+$(this).attr('id').slice(4)+"&zeit="+this.value,
			success: function(msg){
				reloadTable();
				reloadLehrer();
			}
		});
    });
    
    // Termin löschen
    $(".delete").live( 'click', function() {
    	$.ajax({
			type: "POST",
			url: "eltern_termin.ajax.php",
			data: "loeschen=true&tid="+$(this).attr('id').slice(6),
			success: function(msg) {
				reloadTable();
				reloadLehrer();
			}
		});
    });    
    
    // Weitere Lehrer
    $('#lehrer').change( function() {
    	if ($(this).val()=="other") {
    		window.location.href = "eltern_lehrer.php";
    	}
    });
    
}



