$(function(){
	$modal = $('#mapModal');
	
	$modal.modal({
		show: false
	});

	$('.mapModalBtn').click(function(){
		var $this = $(this);
		var lat = $this.data('lat');
		var lng = $this.data('lng');
		var rad = $this.data('radius');
		
		$modal.modal('show');
		
		$('#mapModalDesc').html($this.parents('tr').find('td:first-child').html());
		
		$modal.on('shown', function () {
			var center = new google.maps.LatLng(lat, lng, true);
			
			var map = new google.maps.Map(document.getElementById("map"), {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				zoom: 11
			});
			
			var circle = new google.maps.Circle({
				map: map,
				center: center,
				radius: rad,
				strokeWeight: 1
			});
			
			map.setCenter(center);
		});
	});
});

