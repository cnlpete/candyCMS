<form action='/media/{$smarty.get.action}' method='post' enctype='multipart/form-data'>
  <h1>{$lang.media.title.create}</h1>
  <p>
    <label for='input-file'>{$lang.media.label.choose} *</label>
    <input type='file' name='file' id="input-file" />
    <span class='description'>{$lang.media.info.upload}</span>
  </p>
  <p>
    <label for='input-rename'>{$lang.media.label.rename}</label>
    <input type='text' name='rename' id="input-rename" onkeyup="this.value = candy.system.stripNoAlphaChars(this.value)" />
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='{$smarty.get.action}_file' />
    <input type='submit' value='{$lang.media.title.create}' disabled />
  </p>
</form>
<script type="text/javascript">
  $("input[type='file']").change(function(){
    if ($(this).val()) {
      $("input[type='submit']").attr('disabled',false);
    }
  });

  if(!$.browser.opera) {
    $("input[type='submit']").click(function() {
      $(this).val(LANG_LOADING).attr('disabled',true);
    });
  }
</script>