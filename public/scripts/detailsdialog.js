$('#showPasswordBtn').click(function()
{
  if ($('#detailsDialogPasswordInput').attr('type') == 'text')
  {
    $('#detailsDialogPasswordInput').prop('type', 'password');
    $(this).text('Show password');
  }
  else
  {
    $('#detailsDialogPasswordInput').prop('type', 'text');
    $(this).text('Hide password');
  }
});

$('#deleteCredentialsBtn').click(async function()
{
    $('#saveCredentialsBtn').prop('disabled', true);
    $('#showPasswordBtn').prop('disabled', true);
    $('#deleteCredentialsBtn').prop('disabled', true);
    var c = new Credential();
    c.id = $('#detailsDialogIdInput').val();
    await c.delete(userToken);
    loadCreds();
    $("#detailsDialog").modal('hide');
    $('#saveCredentialsBtn').prop('disabled', false);
    $('#showPasswordBtn').prop('disabled', false);
    $('#deleteCredentialsBtn').prop('disabled', false);
});

$('#saveCredentialsBtn').click(async function()
{
  var id = $('#detailsDialogIdInput').val();

  $('#saveCredentialsBtn').prop('disabled', true);
  $('#showPasswordBtn').prop('disabled', true);
  $('#deleteCredentialsBtn').prop('disabled', true);
  
  
  if (id == null || id == "") // Uusi credential
  {
    console.log("Uus cred");
    var c = new Credential();
    c.credentialDescription = $('#detailsDialogDescriptionInput').val();
    c.username = $('#detailsDialogUsernameInput').val();
    c.salt = AES.generateSalt();
    c.iv = AES.generateIv();
    var key = AES.generateKey (c.salt, masterPass);
    c.url = $('#detailsDialogUrlInput').val();

    var pwd = $('#detailsDialogPasswordInput').val();

    c.pwd = AES.encrypt(key, c.iv, pwd);
    
    await c.create(userToken);
    loadCreds();
  }
  else // Muokataan vanhaa
  {
    console.log("Vanha cred");
    var c = new Credential();
    c.id = $('#detailsDialogIdInput').val();
    c.credentialDescription = $('#detailsDialogDescriptionInput').val();
    c.username = $('#detailsDialogUsernameInput').val();
    c.salt = AES.generateSalt();
    c.iv = AES.generateIv();
    var key = AES.generateKey (c.salt, masterPass);
    c.url = $('#detailsDialogUrlInput').val();

    var pwd = $('#detailsDialogPasswordInput').val();

    c.pwd = AES.encrypt(key, c.iv, pwd);
    await c.update(userToken);
    loadCreds();
  }
  $("#detailsDialog").modal('hide');
  $('#saveCredentialsBtn').prop('disabled', false);
  $('#showPasswordBtn').prop('disabled', false);
  $('#deleteCredentialsBtn').prop('disabled', false);
});