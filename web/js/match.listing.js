$(function(){
	var $modal = $('#mapModal');
	var map;
	var circle;
	
	$modal.modal({
		show: false
	});
	
	$('.mapModalBtn').click(function(){
		var $this = $(this);
		var lat = $this.data('lat');
		var lng = $this.data('lng');
		var rad = $this.data('radius');
		var pointLat = $this.data('point-lat');
		var pointLng = $this.data('point-lng');
		
		var map;
		var circle;
		var marker;
		
		$modal.modal('show');
		
		$('#mapModalDesc').html($this.parents('tr').find('td:first-child').html());
		
		$modal.on('shown', function () {
			var center = new google.maps.LatLng(lat, lng, true);
			
			if (!map) {
				map = new google.maps.Map(document.getElementById('map-modal'), {
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				circle = new google.maps.Circle({
					map: map,
					strokeWeight: 1
				});
			}
			
			if (pointLat && pointLng) {
				var p = new google.maps.LatLng(pointLat, pointLng, true);
				
				if (!marker) {
					marker = new google.maps.Marker({map: map});
				}
				
				marker.setPosition(p);
				map.setCenter(p);
				map.setZoom(GMAPGuessZoom(rad)+2);
			} else {
				map.setCenter(center);
				map.setZoom(GMAPGuessZoom(rad));
			}
			
			circle.setRadius(rad);
			circle.setCenter(center);
		});
	});
});
