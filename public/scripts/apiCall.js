callCredApi = (reqData) => {
    return new Promise((resolve, reject) => {
        $.ajax('api/credentials/', {
            type: 'POST',
            data: reqData,
            success: (res) => {
                if (res.message > 299) {
                    reject(res.message);
                } else {
                    resolve(res);
                }
            },
            error: (jqXhr, textStatus, errorMessage) => {
                /* console.log(jqXhr);
                console.log(textStatus);
                console.log(errorMessage);
                console.log("Bad usertoken?"); */
                reject(errorMessage);
            }
        });
    });
}