let credentials = [];
let masterPass = null;
let userToken = null;

$(() => {
    if (getCookie("usertoken") == '') {
        login();
    } else {
        userToken = getCookie("usertoken");
        createMenu();
        loadCreds();
    }



    $('#homeLink').click((event) => {
        event.preventDefault();
        if (getCookie("usertoken") == '') {
            login();
        } else {
            loadCreds();
        }
    });
    $('#helpPageLink').click((event) => {
        event.preventDefault();
        $('#appContent').load("public/help.html");
    });
});

createMenu = () =>
{
    $('navElements > *').remove();
    
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


    var createSettingsmenuitem = $('<li>');
    var link = $('<a>' , {
        text: 'Settings',
        title: 'Settings',
        href: '#'
    });
    link.click(openSettingsView);
    createSettingsmenuitem.append(link);
    $('#navElements').append(createSettingsmenuitem);


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

openSettingsView = () => $('#appContent').load("public/settings.html");

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

login = () => {
    $('#appContent').load("public/login.html");
}

loadCreds = async () => {
    $('#appContent > *').remove();
    credentials = [];
    // haetaan credentiaalit API:sta
    const res = await Credential.fetchAll(userToken);
    if (res.data != null){
        res.data.forEach(cred => {
            let credential = new Credential();
            credential.setFromData(cred)
            credentials.push(credential);
        });
        createCredentialOverview(credentials);
    } else if(res.message == 201) {
        $('#appContent').load("public/credsempty.html");
    } else {
        login();
    }
};


createCredentialOverview = (data) => {
    let element =  $('<div>').addClass('overview').addClass('col');
    let innerRow = $('<div>').addClass('row');
    element.append(innerRow);
    for (let i = 0; i < data.length; i++) {
        innerRow.append(createCredentialOverviewElement(data[i]));
    }
    $('#appContent').append(element);
}

createCredentialOverviewElement = (cred) => {
    let element =  $('<div>').addClass('overviewElement').addClass('col-lg-3');
    element.append($('<h4>').text(cred.credentialDescription));
    element.append($('<p>').addClass('usernameLabel').text('-[ ' + cred.username + ' ]-'));
    
    let link = $('<a>').text(cred.url);
    element.append(link);

    link.click((event) => {
        event.preventDefault();
        if (cred.url.includes("https://") || cred.url.includes("http://")) {
            window.open(cred.url, '_blank');
        } else {
            window.open("https://" + cred.url, '_blank');
        }
        event.stopPropagation();
    });
    
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
        var purettu = AES.decrypt(key, cred.iv, cred.pwd);

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

  
  