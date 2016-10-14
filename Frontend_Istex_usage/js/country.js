$(document).ready(function(){
    $(".istex-search-bar-wrapper :submit").click(function(){
    	var query=document.getElementsByClassName('istex-search-input')[0].value
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query
        },
        function(data){
        	console.log(data)
            var parsed = JSON.parse(data);
			var r = []
			var x = 0;
	    	for (var k in parsed) {
	    		if (x<=20) {
         			x++;
	        var occurence=(Object.keys(parsed[k]).length)-1;
	        if (!parsed.hasOwnProperty(k)) 
	            continue
	        $( "#country" ).append('<div class="item"><div class="content"><div class="header">'+k+'('+occurence+')'+'</div> </div> </div>' );
   		  	}
          	else{
           	 break;
          	}
   		 }
        });
   	});
});