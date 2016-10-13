$(document).ready(function(){
    $("button").click(function(){
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getlaboratorys",
        {
          query: '"environmental impact"AND"rare earth element"'
        },
        function(data){
            var parsed = JSON.parse(data);
			var r = []
      console.log(parsed);
	    	for (var k in parsed) {
	        var occurence=(Object.keys(parsed[k]).length)-1;
	        if (!parsed.hasOwnProperty(k)) 
	            continue
	        $( "#laboratorys" ).append('<div class="item"><div class="content"><div class="header">'+k+'('+occurence+')'+'</div> </div> </div>' );
   		 }
        });
   	});
});