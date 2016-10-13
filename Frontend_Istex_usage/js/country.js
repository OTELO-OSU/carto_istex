$(document).ready(function(){
    $("button").click(function(){
        $.post("demo_test_post.asp",
        {
          query: ""
        },
        function(data,status){
            alert("Data: " + data + "\nStatus: " + status);
        });
    });
});