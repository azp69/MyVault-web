class Credential {
    id;
    credentialDescription;
    username;
    pwd;
    salt;
    iv;
    url;
    
    constructor() {
        
    }

    set(id, credentialDescription, username, pwd, salt, iv, url) {
        this.id = id;
        this.credentialDescription = credentialDescription;
        this.username = username;
        this.pwd = pwd;
        this.salt = salt;
        this.iv = iv;
        this.url = url;
    }

    setFromData(data) {
        this.id = data.id;
        this.credentialDescription = data.credentialDescription;
        this.username = data.username;
        this.pwd = data.pwd;
        this.salt = data.salt;
        this.iv = data.iv;
        this.url = data.url;
    }

    /**
     * Pyytää kaikki credentiaalit API:lta usertokenin perusteella
     * @param {string} usertoken 
     * @param {function} callback 
     */
    static async fetchAll(usertoken, callback) {
        const reqData = JSON.stringify({
            requestType:"READ", 
            usertoken: usertoken
        });
        try {
            const res = await callCredApi(reqData);
            return res;
            //callback(res);
        } catch(ex) {
            return {message:ex, data:null};
        }
    }

    /**
     * Lähettää päivitetyn credentiaalin API:lle
     * @param {string} usertoken 
     */
    async update(usertoken) {
        let reqData = {
            requestType:"UPDATE",
            usertoken: usertoken
        };
        for (const prop in this) {
            reqData[prop] = this[prop];
        }
        try {
            const res = await callCredApi(JSON.stringify(reqData));
        } catch(ex) {
            console.error(ex);
        }
    }

    /**
     * Lähettää uuden credentiaalin API:lle
     * @param {string} usertoken 
     */
    async create(usertoken) {
        let reqData = {
            requestType:"CREATE",
            usertoken: usertoken
        };
        for (const prop in this) {
            reqData[prop] = this[prop];
        }
        try {
            const res = await callCredApi(JSON.stringify(reqData));
        } catch(ex) {
            console.error(ex);
        }
    }

    /**
     * Lähettää API:lle pyynnön poistaa credentiaali
     * @param {string} usertoken 
     */
    async delete(usertoken) {
        let reqData = {
            requestType:"DELETE",
            usertoken: usertoken,
            id: this.id
        };
        try {
            const res = await callCredApi(JSON.stringify(reqData));
        } catch(ex) {
            console.error(ex);
        }
    }


}