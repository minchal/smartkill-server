$(function(){
	// mapa
	var $map   = $('#map');
	var center = new google.maps.LatLng($map.data('lat'), $map.data('lng'), true);
	var map    = new google.maps.Map(document.getElementById('map'), {
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		zoom: 11
	});
	
	map.setCenter(center);
	
	var circle = new google.maps.Circle({
		map: map,
		center: center,
		radius: $map.data('size'),
		strokeWeight: 1
	});
	
	// formualrz z hasłem
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
			var $item = ui.draggable;
			$item.remove();
			
			$.ajax({
				url: $item.data('delete')
			});
		}
	});
});
