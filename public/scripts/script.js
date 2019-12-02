let credentials = [];
let masterPass = null;
let userToken = null;

$(() => {
    if (getCookie("usertoken") == '')
    {
        login();
    }
    else
    {
        userToken = getCookie("usertoken");
        
        createMenu();
        loadCreds();
    }    
});

createMenu = () =>
{
    $('#detailsPlaceholder').load("public/detailsdialog.html");
    var createCredmenuitem = $('<li>');
    var link = $('<a>' , {
        text: 'Create new credential',
        title: 'Create new credential',
        href: '#'
    });

    link.click(openNewCredentialView);

    createCredmenuitem.append(link);
    $('#navElements').append(createCredmenuitem);

    var signoutmenuitem = $('<li>');
    link = $('<a>', {
        text: 'Log out',
        title: 'Log out',
        href: '?'
    });


    link.click(function(){
        document.cookie = "usertoken=; path=/;";
        masterPass = null;
    });

    signoutmenuitem.append(link);
    $('#navElements').append(signoutmenuitem);
}

openNewCredentialView = () => {
    if (masterPass == null || masterPass == "")
    {
        masterPass = prompt("Please enter your master password", "");

        if (masterPass == null || masterPass == "") {
            return;
        } 
    }

    $('#detailsDialogUsernameInput').val("");
    $('#detailsDialogDescriptionInput').val("");
    $('#detailsDialogUrlInput').val("");
    $('#detailsDialogIdInput').val("");
    $('#detailsDialogPasswordInput').val("");
    $("#detailsDialog").modal('toggle');
}

login = () =>
{
    $('#appContent').load("public/login.html");
}

loadCreds = () =>
{
    $('#appContent > *').remove();
    credentials = [];
    
    console.log("USERTOKEN:" + userToken);
    Credential.fetchAll(userToken, (data) => {
        // let returnArray = [];
        if (data.data != null){
            data.data.forEach(cred => {
                let credential = new Credential();
                credential.setFromData(cred)
                // returnArray.push(credential);
                credentials.push(credential);
            });
            
            createCredentialOverview(credentials);
        } else if(data.message == 'No Credentials Found') {
            $('#appContent').load("public/credsempty.html");
        }
    }); 
};


createCredentialOverview = (data) => {
    let element =  $('<div>').addClass('overview');
    for (let i = 0; i < data.length; i++) {
        element.append(createCredentialOverviewElement(data[i]));
    }
    element.css('padding-left', '5em');
    $('#appContent').append(element);
}

createCredentialOverviewElement = (cred) => {
    let element =  $('<div>').addClass('overviewElement');
    element.append($('<h4>').text(cred.credentialDescription));
    element.append($('<p>').addClass('usernameLabel').text('-[ ' + cred.username + ' ]-'));
    
    element.click(function()
    {
        if (masterPass == null || masterPass == "")
        {
            masterPass = prompt("Please enter your master password", "");

            if (masterPass == null || masterPass == "") {
                return;
            } 
        }

        var key = AES.generateKey(cred.salt, masterPass);
        var purettu = AES.decrypt(key, cred.iv, cred.password);

        if (purettu == "" || purettu == null)
        {
            alert("Wrong master password?");
            masterPass = null;
            return;
        }
        
        $('#detailsDialogUsernameInput').val(cred.username);
        $('#detailsDialogDescriptionInput').val(cred.credentialDescription);
        $('#detailsDialogUrlInput').val(cred.url);
        $('#detailsDialogIdInput').val(cred.id);
        $('#detailsDialogPasswordInput').val(purettu);

        $("#detailsDialog").modal('toggle');
    });

    return element;
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  
  