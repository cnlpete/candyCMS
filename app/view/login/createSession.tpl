<div id="login">
  <form method='post' action='/Login'>
    <fieldset>
      <legend>{$lang_login}</legend>
      <div class="input">
        <label for='email'>{$lang_email}:</label>
        &nbsp;
        <input name='email' type='text' id='email' value='' />
      </div>
      &nbsp;
      <div class="input">
        <label for='password'>{$lang_password}:</label>
        &nbsp;
        <input name='password' type='password' id='password' value='' />
      </div>
      <div class="submit">
        <input type='submit' value='{$lang_login}' />
      </div>
    </fieldset>
    <input type='hidden' value='formdata' name='create_session' />
    <p><a href='/Login/createnewpassword'>Passwort vergessen?</a></p>
  </form>
</div>