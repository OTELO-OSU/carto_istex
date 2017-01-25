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

	$('.helpme').click(function(){
		$('.aide')
		  .modal('show');
		});

	 

$('.leafletmap').click(function(){
	$('.leafletview').modal('show');
		mymap.invalidateSize();  //Resize de la map hidden div
		mymap.fitBounds(bounds); // zoom sur la partie qui des poi qui nous interessent
	});

});


