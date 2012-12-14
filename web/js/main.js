$(function(){
	$('*[rel="tooltip"]').tooltip();
	
	if(!Modernizr.inputtypes['datetime-local']){
    	$('input[type=datetime-local]').datepicker({
			format: 'yyyy-mm-ddThh:ii:ss',
			weekStart: 1,
			days: ["nd","pn","wt","śr","cz","pt","so"],
			months: ["styczeń","luty","marzec","kwiecień","maj","czerwiec","lipiec","sierpień","wrzesień","październi","listopad","grudzień"]
		});
	};
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
