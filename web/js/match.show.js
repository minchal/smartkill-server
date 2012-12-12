$(function(){
	// formularz z hasłem
	$form  = $('#pass-form');
	$input = $('#pass-input');
	$error = $('#pass-error');
	
	$form.ajaxForm({
		beforeSubmit: function() {
			if (!$input.is(':visible') && $form.data('pass')) {
				$input.slideDown();
				return false;
			}
			return true;
		},
		success: function(rsp) {
			if (rsp.status == 'success') {
				location.reload();
			} else if (rsp.status == 'error') {
				$error.stop().text(rsp.msg).fadeIn().delay(5000).fadeOut();
			}
		}
	});
	
	// zarządzanie graczami
	if ($('#players-manage').length) {
		function onDrop(event, ui) {
			var $item = ui.draggable;
			$(this).append($item.css({top:0, left:0}));
			
			$.ajax({
				url: $item.data('switch')
			});
		}
		
		$('#hunters .user, #preys .user').draggable({
			revert: 'invalid',
			zIndex: 100,
			start: function() {
				$('#delete').show();
			},
			stop: function() {
				$('#delete').hide();
			}
		});
		
		$('#hunters').droppable({
			accept: '#preys .user',
			drop: onDrop
		});
		
		$('#preys').droppable({
			accept: '#hunters .user',
			drop: onDrop
		});
		
		$('#delete').droppable({
			accept: '#hunters .user, #preys .user',
			drop: function(event, ui){
				$(this).hide();
				
				var $item = ui.draggable;
				$item.remove();
				
				$.ajax({
					url: $item.data('delete')
				});
			}
		});
	}
	
	// mapy
	var $map = $('#map');
	var $modal = $('#mapModal');
	
	function showCircleMap(id, lat, lng, radius) {
		var center = new google.maps.LatLng(lat, lng, true);
		var map    = new google.maps.Map(document.getElementById(id), {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			zoom: 12
		});
		
		map.setCenter(center);
		
		var circle = new google.maps.Circle({
			map: map,
			center: center,
			radius: radius,
			strokeWeight: 1
		});
		
		return map;
	}
	
	try {
		// mapa obszaru
		var m = showCircleMap('map', $map.data('lat'), $map.data('lng'), $map.data('size'));
		
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng($map.data('lat2'), $map.data('lng2'), true), 
			map: m
		});
		
		// mapa zdarzenia
		$modal.modal({
			show: false
		});
		
		$('.mapModalBtn').click(function(){
			var $this = $(this);
			
			$modal.modal('show');
			
			$('#mapModalDesc').html($this.parents('tr').find('td:first-child').html());
			
			$modal.on('shown', function () {
				var map = showCircleMap('map_event', $map.data('lat'), $map.data('lng'), $map.data('size'));
				
				var point = new google.maps.LatLng($this.data('lat'), $this.data('lng'), true);
				
				var marker = new google.maps.Marker({
					position: point, 
					map: map
				});
				
				map.setCenter(point);
			});
		});
	} catch (e) {
		console.log(e);
	}
});
