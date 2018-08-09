function plx_portal_background_call($attr) {
	
	$.ajax({
		method: "POST",
		url: "",
		data: { apikeyKeyId: valueKeyId }
	})
	.done(function( msg ) {
		
	})
	.fail(function( msg ) {
		
	});
	
}