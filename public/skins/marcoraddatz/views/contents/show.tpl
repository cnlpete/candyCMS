{if !$c}
  <div class='error' id='js-error' title='{$lang_missing_entry}' onclick="hideDiv('js-error')">
    <p>{$lang_missing_entry}</p>
  </div>
{else}
  <div id='c{$c.id}' class='element'>
    <div class='date' title="{$c.datetime}">
      {$c.date}
    </div>
    <div class='header'>
      <h2>
        {$c.title}
        {if $USER_RIGHT > 3}
          <a href='/Content/{$c.id}/update'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                 title='{$lang_update}' />
          </a>
        {/if}
      </h2>
    </div>
    {$c.content}
    <div class='footer'>
      {$lang_share}:
      <a href='http://www.facebook.com/share.php?u={$c.url}&amp;t={$c.eTitle}'
         class='js-tooltip' title='{$lang_add_bookmark}::http://www.facebook.com'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-facebook" alt='Facebook' width='16' height='16' />
      </a>
      <a href='http://del.icio.us/post?url={$c.url}&amp;title={$c.eTitle}'
         class='js-tooltip' title='{$lang_add_bookmark}::http://del.icio.us'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
      </a>
      <a href='http://technorati.com/cosmos/search.html?url={$c.url}'
         class='js-tooltip' title='{$lang_add_bookmark}::http://technorati.com'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-technorati" alt='Technorati' width='16' height='16' />
      </a>
      <a href='http://digg.com/submit?phase=2&amp;url={$c.url}&amp;title={$c.eTitle}'
         class='js-tooltip' title='{$lang_add_bookmark}::http://digg.com'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-digg" alt='Digg' width='16' height='16' />
      </a>
      <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$c.url}&amp;bm_description={$c.eTitle}'
         class='js-tooltip' title='{$lang_add_bookmark}::http://www.mister-wong.de'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-mrwong" alt='MrWong' width='16' height='16' />
      </a>
    </div>
  </div>
{/if}