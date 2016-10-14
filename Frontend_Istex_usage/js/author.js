$(document).ready(function(){
    $(".istex-search-bar-wrapper :submit").click(function(){
      var query=document.getElementsByClassName('istex-search-input')[0].value
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getauthors",
        {
          query: query
        },
        function(data){
            var parsed = JSON.parse(data);
           // console.log(parsed);
			var r = []
      var x = 0;
	    	for (var k in parsed) {
          if (x<=20) {
          x++;
	        var occurence=(parsed[k].length);
	        if (!parsed.hasOwnProperty(k)) 
	            continue
            var res = k.split(",");
	        $( "#authors" ).append('<tbody><tr><td>'+res[0]+'</td><td>'+res[1]+'</td><td>'+occurence+'</td></tr>');     
          }
          else{
            break;
          }
   		 }
        });
   	});
});