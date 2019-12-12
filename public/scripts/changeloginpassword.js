$('#changePasswordForm').submit(function( event )
{
    event.preventDefault();
    var oldpass = $('#oldPasswordInput').val();
    var newpass = $('#newpasswordInput').val();
    var newpassAgain = $('#newpasswordAgainInput').val();

    if (newpass == newpassAgain)
    {
        updateUserPassword(userToken, oldpass, newpass);
    }
});

async function updateUserPassword(usertoken, oldpass, newpass) {
    let reqData = {
        requestType:"PWDUPDATE",
        usertoken: usertoken,
        oldpassword : oldpass,
        newpassword : newpass
    };
    
    try {
        const res = await callLoginApi(JSON.stringify(reqData));
        $('#oldPasswordInput').val('');
        $('#newpasswordInput').val('');
        $('#newpasswordAgainInput').val('');
        $('#pwdChangeStatusMessage').text('Password changed!');
    } catch(ex) {
        console.log(ex);
        $('#pwdChangeStatusMessage').text('Couldn\'t change your password!');
    }
}