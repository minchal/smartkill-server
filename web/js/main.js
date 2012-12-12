$(function(){
	$('*[rel="tooltip"]').tooltip();
	
	
});

function GMAPGuessZoom(r) {
	if (r <= 500) {
		return 14;
	}
	if (r <= 1500) {
		return 13;
	}
	if (r <= 4000) {
		return 12;
	}
	
	return 11;
}
