let credentials = [];
let userToken = null;

$(() => {
    // const usertoken = "9a8b6d59d6ba5ad7d0b6572603faa3f331225bfa6a069e666a908c85f604e52a2736d3caf217507ee0804d2a03d14d790ff968fc1cd15992fe28ea2fd129c549";
    if(userToken === null)
    {
        login();
    }
    else
    {
        loadCreds();
    }
});

login = () =>
{
    $('#appContent').load("public/login.html");
}

loadCreds = () =>
{
    console.log("USERTOKEN:" + userToken);
    Credential.fetchAll(userToken, (data) => {
        let returnArray = [];
        data.data.forEach(cred => {
            let credential = new Credential();
            credential.setFromData(cred)
            returnArray.push(credential);
            credentials.push(credential);
        });
        createCredentialOverview(returnArray);
    }); 
};


createCredentialOverview = (data) => {
    let element =  $('<div>').addClass('overview');
    for (let i = 0; i < data.length; i++) {
        element.append(createCredentialOverviewElement(data[i]));
    }
    $('#appContent').append(element);
}

createCredentialOverviewElement = (cred) => {
    let element =  $('<div>').addClass('overviewElement');
    element.append($('<h4>').text(cred.credentialDescription));
    element.append($('<p>').addClass('usernameLabel').text('-[ ' + cred.username + ' ]-'));
    return element;
}