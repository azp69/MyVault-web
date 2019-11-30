
$('#loginForm').submit(function( event )
{
    event.preventDefault();
    
    var o = new Object();
    o.requestType = "LOGIN";
    o.username = $('#usernameInput').val();
    o.password = $('#passwordInput').val();
    

    // alert(JSON.stringify(o));

    apiCall(o, function(result) 
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
            createMenu();
            $(".loginform").hide();
        }
        
    });
});

$('#linkToRegistering').click((event) => {
    event.preventDefault();
    $('#appContent > *').remove();
    $('#appContent').load("public/register.html");
});

$('#registerForm').submit(function( event )
{
    event.preventDefault();

    if ($('#emailInput').val().trim() == '' || $('#usernameInput').val().trim() == '' || 
        $('#passwordInput').val().trim() == '' || $('#serialInput').val().trim() == '') {
        $('#regAlert').show().text('All fields are required!');
        return;
    }

    if ($('#passwordInput').val() != $('#confirmPasswordInput').val()) {
        $('#regAlert').show().text('Passwords did not match!');
        $('#passwordInput').val('');
        $('#confirmPasswordInput').val('');
        return;
    }

    let o = new Object();
    o.requestType = "REGISTER";
    o.username = $('#emailInput').val();
    o.username = $('#usernameInput').val();
    o.password = $('#passwordInput').val();
    o.serialkey = $('#serialInput').val();
    
    apiCall(o, (result) => {
        if (result.message) {
            alert(result.message);
        } else if(result.usertoken) {
            document.cookie = "usertoken=" + result.usertoken + "; path=/;";
            userToken = result.usertoken;
            console.log(userToken);
            loadCreds();
            createMenu();
            $('#appContent > *').remove();
        }
    });
});

apiCall = (requestObject, callback) => {
    $.ajax({
        url : "api/login/index.php",
        data : JSON.stringify(requestObject),
        dataType : 'json',
        contentType : 'application/json',
        type : 'POST'
        }).done(callback)
        .fail(function(result) {
            alert(result);
        });
}


$('#usernameInput').focusout(() => {
    if ($('#registerForm').length)
    {
        let ob = new Object();
        ob.requestType = "CHECKIFUSEREXISTS";
        ob.username = $('#usernameInput').val();
        apiCall(ob, (result) => {
            if (result.message) {
                alert("Username already taken, please choose another one.");
            }
        });
    }
});