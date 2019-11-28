class Credential {
    id;
    ownerId;
    credentialDescription;
    username;
    password;
    salt;
    iv;
    url;
    
    constructor() {
        
    }

    set(id, ownerId, credentialDescription, username, password, iv, url) {
        this.id = id;
        this.ownerId = ownerId;
        this.credentialDescription = credentialDescription;
        this.username = username;
        this.password = password;
        this.iv = iv;
        this.url = url;
    }

    setFromData(data) {
        this.id = data.id;
        this.ownerId = data.ownerId;
        this.credentialDescription = data.credentialDescription;
        this.username = data.username;
        this.password = data.pwd;
        this.salt = data.salt;
        this.iv = data.iv;
        this.url = data.url;
    }

    static fetchAll(usertoken, callback) {
        let reqData = JSON.stringify({
            requestType:"read", 
            usertoken: usertoken
        });

        $.ajax('api/credentials/', {
            async: 'false',
            type: 'POST',
            data: reqData,
            success: callback,
            error: (jqXhr, textStatus, errorMessage) => {
                console.log(textStatus);
                console.log(errorMessage);
                console.log("Bad usertoken?");
                login();
                return null;
            }
        });
    }
}