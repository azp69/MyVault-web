
$('#loginForm').submit(function( event )
{
    event.preventDefault();
    
    var o = new Object();
    o.requestType = "LOGIN";
    o.username = $('#usernameInput').val();
    o.password = $('#passwordInput').val();
    
    apiCall(o, function(result) 
    {
        // var obj = JSON.parse(result);
        if (result.message)
        {
            console.log(result.message);
            // alert(result.message);
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

/**
 * Tekee joitain perus-validointeja syötetyille tiedoille, suorittaa pyynnön API:lle, ja mikäli kaikki
 * menee oikein, päästää käyttäjän jatkamaan sovellukseen
 */
$('#registerForm').submit(function( event )
{
    event.preventDefault();

    let pwd = $('#passwordInput').val();

    // tarkistetaan, että kaikki tiedot on annettu
    if ($('#emailInput').val().trim() == '' || $('#usernameInput').val().trim() == '' || 
        pwd.trim() == '' || $('#confirmPasswordInput').val().trim() == '' || $('#serialInput').val().trim() == '') {
        $('#regAlert').show().text('All fields are required!');
        $('input').each(function() {
            if (this.value.trim() == '') {
                $(this).toggleClass('is-valid', false);
                $(this).toggleClass('is-invalid', true);
            }
        });
        return;
    }

    

    // tarkistetaan, että salasanan pituus on ainakin 8 merkkiä
    if (!testPwd(pwd)) {
        let pwdInput = $('#passwordInput');
        //$('#regAlert').show().text('Password length must be at least 8 characters long and it must not contain spaces!');
        removeAlertDiv('pwdAlert');
        pwdInput.parent().append(createAlertDiv(
            'pwdAlert', 
            'Password length must be at least 8 characters long and it must not contain spaces!'));
        pwdInput.toggleClass('is-valid', false);
        pwdInput.toggleClass('is-invalid', true);
        return;
    } else {
        let pwdInput = $('#passwordInput');
        pwdInput.toggleClass('is-valid', true);
        pwdInput.toggleClass('is-invalid', false);
        removeAlertDiv('pwdAlert');
    }
    
    // tarkistetaan, että käyttäjä ei ole tehnyt kirjoitusvirhettä salasanassa, vaan on kirjoittanut saman salasanan
    // molempiin salasanakenttiin
    if (!testPwdConfirm(pwd, $('#confirmPasswordInput').val())) {
        $('#regAlert').show().text('Passwords did not match!');
        $('#passwordInput').val('');
        $('#confirmPasswordInput').val('');
        let pwdConfPwdInput = $('#confirmPasswordInput');
        pwdConfPwdInput.toggleClass('is-valid', false);
        pwdConfPwdInput.toggleClass('is-invalid', true);
        removeAlertDiv('confPwdAlert');
        pwdConfPwdInput.parent().append(createAlertDiv(
            'confPwdAlert',
            'Passwords do not match!'));
        let pwdInput = $('#passwordInput');
        pwdInput.toggleClass('is-valid', false);
        pwdInput.toggleClass('is-invalid', true);
        return;
    } else {
        let pwdInput = $('#passwordInput');
        pwdInput.toggleClass('is-valid', true);
        pwdInput.toggleClass('is-invalid', false);
        let pwdConfPwdInput = $('#confirmPasswordInput');
        pwdConfPwdInput.toggleClass('is-valid', true);
        pwdConfPwdInput.toggleClass('is-invalid', false);
        removeAlertDiv('confPwdAlert');
    }

    // luodaan objekti, joka lähetetään API:lle
    let o = new Object();
    o.requestType = "REGISTER";
    o.username = $('#emailInput').val();
    o.username = $('#usernameInput').val();
    o.password = pwd;
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

$('#emailInput').focus(function(event) {
    event.preventDefault();
    if ($('#registerForm').length)
        $(this).toggleClass('is-invalid', false);
});

/**
 * Testaa, että eihän salasana sisällä whitespacea ja että 
 * onhan salasanan pituus vähintään 8 merkkiä
 */
testPwd = (pwd) => {
    if (/\s/.test(pwd)) return false; 
    if (pwd.length < 8) return false;
    return true;
}

/**
 * testaa, että käyttäjä ei ole tehnyt kirjoitusvirhettä salasanassa, 
 * vaan on syöttänyt molempiin salasanakenttiin saman salasanan
 */
testPwdConfirm = (pwd, confirmpwd) => {
    if (pwd != confirmpwd) return false;
    return true;
}

apiCall = (requestObject, callback) => {
    $.ajax({
        url : "api/login/index.php",
        data : JSON.stringify(requestObject),
        dataType : 'json',
        contentType : 'application/json',
        type : 'POST'
        }).done(callback)
        .fail(function(result) {
            alert("Failed");
        });
}

// poistuttaessa username-kentästä, kysytään API:lta, onko käyttäjätunnus jo olemassa
$('#usernameInput').focusout(function(event) {
    if ($('#registerForm').length) {
        event.preventDefault();
        // testataan, että salasana on riittävän pitkä, eikä sisällä välilyöntejä
        if (/\s/.test($(this).val()) || $(this).val().length < 3) {
            $(this).toggleClass('is-valid', false);
            $(this).toggleClass('is-invalid', true);
            // poistetaan (mahdollinen) vanha alert-div
            removeAlertDiv('usernameAlert');
            // lisätään alert-div
            $(this).parent().append(createAlertDiv(
                'usernameAlert', 
                'Username too short, must be at least 3 characters long and it must not contain spaces!'));
            return;
        } else {
            // poistetaan (mahdollinen) alert-div
            removeAlertDiv('usernameAlert');
        }
        // luodaan objekti, joka lähetetään API:lle
        let o = new Object();
        o.requestType = "CHECKIFUSEREXISTS";
        o.username = $('#usernameInput').val();
        // kysytään apilta, onko käyttäjä jo olemassa
        apiCall(o, (result) => {
            if (result) {
                $(this).toggleClass('is-valid', false);
                $(this).toggleClass('is-invalid', true);
                // poistetaan (mahdollinen) vanha alert-div
                removeAlertDiv('usernameAlert');
                // lisätään alert-div
                $(this).parent().append(createAlertDiv(
                    'usernameAlert', 
                    'Username already taken, please choose another one.'));
            } else {
                $(this).toggleClass('is-invalid', false);
                $(this).toggleClass('is-valid', true);
                // poistetaan (mahdollinen) alert-div
                removeAlertDiv('usernameAlert');
            }
        });
    }
});

/**
 * Luo ja palauttaa parametreina annettujen id:n ja viestin perusteella alert-divin
 */
createAlertDiv = (id, message) => {
    let alertDiv = $('<div>')
        .addClass('invalid-feedback')
        .attr('id', id)
        .text(message);
    return alertDiv;
}

/**
 * Poistaa parametrina annetun id:n mukaisen alert-divin, jos sellainen on olemassa
 */
removeAlertDiv = (id) => {
    if ($(`#${id}`).length) {
        $(`#${id}`).remove();
    }
}

// salasanakentästä poistuttaessa tarkistetaan, että salasana täyttää minimivaatimukset
$('#passwordInput').focusout(function(event) {
    if ($('#registerForm').length) {
        event.preventDefault();
        let pwd = $(this).val();
        if (!testPwd(pwd)) {
            $(this).toggleClass('is-invalid', true);
            $(this).toggleClass('is-valid', false);
            // poistetaan (mahdollinen) vanha alert-div
            removeAlertDiv('pwdAlert');
            // lisätään alert-div
            $(this).parent().append(createAlertDiv(
                'pwdAlert', 
                'Password length must be at least 8 characters long and it must not contain spaces!'));
        } else {
            $(this).toggleClass('is-invalid', false);
            $(this).toggleClass('is-valid', true);
            // poistetaan (mahdollinen) alert-div
            removeAlertDiv('#pwdAlert');
        }
    }
});

// kun siirretään focus inputtiin, poistetaan virhe-ilmoitukset
$('#passwordInput').focus(function(event) {
    event.preventDefault();
    if ($('#registerForm').length)
        $(this).toggleClass('is-invalid', false);
});

// salasanan vahvistuskentästä poistuttaessa tarkistetaan, että salasanat mätsäävät
$('#confirmPasswordInput').focusout(function(event) {
    if ($('#registerForm').length) {
        event.preventDefault();
        let pwd = $('#passwordInput').val();
        let confirmpwd = $(this).val();
        if (!testPwdConfirm(pwd, confirmpwd)) {
            $(this).toggleClass('is-invalid', true);
            $(this).toggleClass('is-valid', false);
            // poistetaan (mahdollinen) vanha alert-div
            removeAlertDiv('confPwdAlert');
            // lisätään alert-div
            $(this).parent().append(createAlertDiv(
                'confPwdAlert',
                'Passwords do not match!'));
        } else {
            $(this).toggleClass('is-invalid', false);
            $(this).toggleClass('is-valid', true);
            // poistetaan (mahdollinen) alert-div
            removeAlertDiv('confPwdAlert');
        }
    }
});

// kun mennään kentään, poistetaan alertit
$('#confirmPasswordInput').focus(function(event) {
    if ($('#registerForm').length){
        event.preventDefault();
        $(this).toggleClass('is-invalid', false);
        // poistetaan (mahdollinen) alert-div
        removeAlertDiv('confPwdAlert');
    }
});
// kun mennään kentään, poistetaan alertit
$('#serialInput').focus(function(event) {
    if ($('#registerForm').length){
        event.preventDefault();
        $(this).toggleClass('is-invalid', false);
    }
});