//code js d'apparition des différents modal
$(document).ready(function(){
	$('.bubbleauthors').click(function(){
	$('.bubbleviewauthors')
	  .modal('show');
	});

	$('.bubblelaboratorys').click(function(){
	$('.bubbleviewlaboratory')
	  .modal('show');
	});

	

$('.leaflet').click(function(){
	$('.leafletview').modal('show');
		mymap.invalidateSize();
		mymap.fitBounds(bounds);

	});

});