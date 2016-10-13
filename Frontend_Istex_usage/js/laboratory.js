$(document).ready(function(){
    $("button").click(function(){
        query=$("input").val;
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getlaboratorys",
        {
          query: query
        },
        function(data){
            var parsed = JSON.parse(data);
			var r = []
      var x = 0;
	    	for (var k in parsed) {
          if (x<=20) {
          x++;
	        var occurence=(parsed[k].length);
	        if (!parsed.hasOwnProperty(k)) 
	            continue
	        $( "#laboratorys" ).append('<div class="item"><div class="content"><div class="header">'+k+'('+occurence+')'+'</div> </div> </div>' );     
          }
          else{
            break;
          }
   		 }
        });
   	});
});