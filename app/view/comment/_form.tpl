<a name='create'></a>
<div id="create_comment">
  <form action='{$action}' method='post'>
    <fieldset>
      <legend>{$lang_headline}</legend>
      <table>
        <tr class='row1'>
          <td class='td_left'>
            <label for="name">{$lang_name}</label>
          </td>
          <td class='td_right'>
            {if $USER_RIGHT > 0}
              {$USER_NAME} {$USER_SURNAME}
            {else}
              <div class="input">
                <input type="text" value="{$name}" name="name" id="name" />
              </div>
            {/if}
          </td>
        </tr>
        <tr class='row2'>
          <td class='td_left'>
            <label for='js-create_commment_text'>{$lang_content}</label>
          </td>
          <td class='td_right'>
            <div class="textarea">
              <textarea name='content' id='js-create_commment_text' rows='10' cols='50'>{$content}</textarea>
            </div>
            <div class='description'>
              <img src="%PATH_IMAGES%/spacer.gif" class="icon-redirect" alt="" />
              <a href='/Help/BB-Code' target='_blank'>{$lang_bb_help}</a>
            </div>
          </td>
        </tr>
      </table>
    </fieldset>
    <center>
      {literal}
        <script type="text/javascript">
          var RecaptchaOptions = {
             lang : 'de'
          };
        </script>
      {/literal}
      {$_captcha_}
    </center>
    <div class="submit">
      <input type='submit' value='{$lang_submit}' />
    </div>
    <div class="button">
      <input type='button' value='{$lang_reset}'
             onclick="destroyContent('createCommentText')" />
    </div>
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$parentID}' name='parentID' />
  </form>
</div>