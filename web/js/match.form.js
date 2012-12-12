$(function(){
	var $rad = $('#match_size');
	var $lat = $('#match_lat');
	var $lng = $('#match_lng');
	
	var center = new google.maps.LatLng($lat.val(), $lng.val(), true);
	
	var map = new google.maps.Map(document.getElementById('map'), {
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		zoom: 12
	});
	
	map.setCenter(center);
	
	var circle = new google.maps.Circle({
		map: map,
		center: center,
		strokeWeight: 1,
		radius: parseInt($rad.val())
	});
	
	var marker = new google.maps.Marker({
		position: center, 
		map: map,
		draggable: true
	});
	
	google.maps.event.addListener(marker, 'position_changed', function() {
		var p = marker.getPosition();
		circle.setCenter(p);
		$lat.val(p.lat());
		$lng.val(p.lng());
	});
	
	$rad.change(function(){
		circle.setRadius(parseInt($rad.val()));
	});
});
