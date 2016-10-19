//code js d'apparition des différents modal
$(document).ready(function(){
	$('.bubbleauthors').click(function(){
		console.log('debug')
	$('.bubbleviewauthors')
	  .modal('show');
	});

	$('.bubblelaboratorys').click(function(){
		console.log('debug')
	$('.bubbleviewlaboratory')
	  .modal('show');
	});

	

$('.leaflet').click(function(){
	$('.leafletview').modal('show');
		mymap.invalidateSize();
		mymap.fitBounds(bounds);

	});

});