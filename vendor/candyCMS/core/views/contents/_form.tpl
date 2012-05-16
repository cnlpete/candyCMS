{strip}
  <div class='page-header'>
    <h1>
      {if $_REQUEST.action == 'create'}
        {$lang.contents.title.create}
      {else}
        {$lang.contents.title.update|replace:'%p':$title}
      {/if}
    </h1>
  </div>
  <form method='post' class='form-horizontal'
        action='/{$_REQUEST.controller}/{if isset($_REQUEST.id)}{$_REQUEST.id}/{/if}{$_REQUEST.action}'>
    <div class='control-group{if isset($error.title)} alert alert-error{/if}'>
      <label for='input-title' class='control-label'>
        {$lang.global.title} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input type='text' name='{$_REQUEST.controller}[title]' class='span4 required focused'
              value="{$title}" id='input-title' autofocus required />
        <span class='help-inline'>
          {if isset($error.title)}
            {$error.title}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-teaser' class='control-label'>
        {$lang.global.teaser}
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[teaser]' value="{$teaser}" type='text' class='span4'
              id='input-teaser' />
        <span class='help-inline'></span>
        <p class='help-block'>
          {$lang.blogs.info.teaser}
        </p>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-keywords' class='control-label'>
        {$lang.global.keywords}
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[keywords]' value="{$keywords}" type='text'
              class='span4' id='input-keywords' />
        <p class='help-block'>
          {$lang.contents.info.keywords}
        </p>
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.content} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <textarea name='{$_REQUEST.controller}[content]' class='js-tinymce required span4' id='input-content'>
          {$content}
        </textarea>
        {if isset($error.content)}
          <span class='help-inline'>
            {$error.content}
          </span>
        {/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-published' class='control-label'>
        {$lang.global.published}
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[published]' value='1' type='checkbox' class='checkbox'
              id='input-published' {if $published == true}checked{/if} />
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary'
            value="{if $_REQUEST.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
      {if $_REQUEST.action == 'update'}
        <input type='button' class='btn btn-danger' value='{$lang.contents.title.destroy}'
              onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroy')" />
        <input type='reset' class='btn' value='{$lang.global.reset}' />
        <input type='hidden' value='{$_REQUEST.id}' name='{$_REQUEST.controller}[id]' />
      {/if}
    </div>
  </form>
  <script type='text/javascript' src='/vendor/tiny_mce/jquery.tinymce.js'></script>
  <script type='text/javascript'>
    $(document).ready(function(){
      $('textarea.js-tinymce').tinymce({
        script_url : '/vendor/tiny_mce/tiny_mce.js',
        theme : "advanced",
        plugins : "autosave,safari,style,advimage,advlink,inlinepopups,insertdatetime,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen",
        theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
        theme_advanced_buttons3 : "hr,|,link,unlink,anchor,|,image,|,cleanup,removeformat,|,code,|,insertdate,inserttime,|,outdent,indent,|,sub,sup,|,charmap",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        language : "{$WEBSITE_LANGUAGE}",
        remove_script_host : false,
        document_base_url : "{$WEBSITE_URL}",
        entity_encoding : "raw",
        height : "300px",
        content_css : "{$_PATH.css}/core/tinymce{$_SYSTEM.compress_files_suffix}.css"
      });
    });

    $('#input-title').bind('keyup', function() {
      countCharLength(this, 128);
    });

    $('#input-teaser').bind('keyup', function() {
      countCharLength(this, 180);
    });
  </script>
{/strip}