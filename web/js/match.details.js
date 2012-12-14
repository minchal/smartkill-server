$(function(){
	// formularz z hasłem
	$form  = $('#pass-form');
	$input = $('#pass-input');
	$error = $('#pass-error');
	
	if ($form.length && $form.ajaxForm) {
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
	}
	
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
	
	function showCircleMap(id, lat, lng, radius) {
		var center = new google.maps.LatLng(lat, lng, true);
		var map    = new google.maps.Map(document.getElementById(id), {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			zoom: GMAPGuessZoom(radius)
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
		showCircleMap('map', $map.data('lat'), $map.data('lng'), $map.data('size'));
	} catch (e) {
		console.log(e);
	}
});
