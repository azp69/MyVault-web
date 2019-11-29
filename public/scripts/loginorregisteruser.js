
$('#registerForm').submit(function( event )
{
    event.preventDefault();
    
    var o = new Object();
    o.requestType = "LOGIN";
    o.username = $('#usernameInput').val();
    o.password = $('#passwordInput').val();
    

    // alert(JSON.stringify(o));

    $.ajax({
        url : "api/login/index.php",
        data : JSON.stringify(o),
        dataType : 'json',
        contentType : 'application/json',
        type : 'POST'
        }).done(function(result) 
        {
            // var obj = JSON.parse(result);
            if (result.message)
            {
                alert(result.message);
            }
            else if(result.usertoken)
            {
                document.cookie = "usertoken=" + result.usertoken + "; path=/;";
                userToken = result.usertoken;
                console.log(userToken);
                
                
                loadCreds();
                $(".loginform").remove();
            }
            
        }).fail(function( result )
        {
            alert(result);
        });

});