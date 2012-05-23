{strip}
  <div class='page-header'>
    <h1>{$lang.global.logs}</h1>
  </div>
  <table class='table'>
    <thead>
      <tr>
        <th class='column-author'>{$lang.global.author}</th>
        <th class='column-section'>{$lang.global.section}</th>
        <th class='column-action'>{$lang.global.action}</th>
        <th class='column-id center'>{$lang.global.id}</th>
        <th class='column-date headerSortDown'>{$lang.global.date.date}</th>
        <th class='column-actions'></th>
      </tr>
    </thead>
    {foreach $logs as $l}
      {if $l.action_name == 'create' || $l.action_name == 'createfile'}
        <tr class='result-{if $l.result}success{else}error{/if}' style='color:green;'>
      {elseif $l.action_name == 'update' || $l.action_name == 'updatefile'}
        <tr class='result-{if $l.result}success{else}error{/if}' style='color:blue;'>
      {elseif $l.action_name == 'destroy' || $l.action_name == 'destroyfile'}
        <tr class='result-{if $l.result}success{else}error{/if}' style='color:red;'>
      {else}
        <tr class='result-{if $l.result}success{else}error{/if}'>
      {/if}
        <td class='left'>
          <a href='{$l.author.url}'>{$l.author.full_name}</a>
        </td>
        <td>
          {$l.controller_name}
        </td>
        <td>
          {$l.action_name}
        </td>
        <td class='center'>
          {$l.action_id}
        </td>
        <td>
          <time datetime='{$l.time_start.w3c}'>
            {$l.time_start.raw|date_format:$lang.global.time.format.datetime}
          </time>
          {if $l.time_start.raw < $l.time_end.raw - 60}
            &nbsp;-&nbsp;
            <time datetime='{$l.time_end.w3c}'>
              {$l.time_end.raw|date_format:$lang.global.time.format.time}
            </time>
          {/if}
        </td>
        <td class='center'>
          <a href="#" onclick="confirmDestroy('{$l.url_destroy}')">
            <img src='{$_PATH.images}/candy.global/spacer.png'
                class='icon-destroy js-tooltip'
                alt='{$lang.global.destroy.destroy}'
                title='{$lang.global.destroy.destroy}'
                width='16' height='16' />
          </a>
        </td>
      </tr>
    {/foreach}
  </table>
  {$_pages_}
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.tablesorter{$_SYSTEM.compress_files_suffix}.js'></script>
  <script src='{$_PATH.js}/core/jquery.infiniteScroll{$_SYSTEM.compress_files_suffix}.js' type='text/javascript'></script>
  <script type='text/javascript'>
    {if $_AUTOLOAD_.enabled}
      var iCounter = 0;
      $(document).ready(function(){
        $('table').infinitescroll({
          navSelector   : 'div.pagination',
          nextSelector  : 'div.pagination a:first',
          itemSelector  : 'table tbody tr',
          loading       : { msgText : '',
            img         : '{$_PATH.images}/candy.global/loading.gif',
            finishedMsg : '',
            selector    : 'div.js-pagination',
            finished    : function(opts){
              opts.loading.msg.fadeOut(opts.loading.speed);
              iCounter = iCounter + 1;
              if (iCounter % {$_AUTOLOAD_.times} == 0){
                /** if we did load a few times, we want to stop and display a resume button **/
                opts.contentSelector.infinitescroll('pause');
                var a = $('<a alt="{$lang.pages.more}" data-role="button" class="btn">{$lang.pages.more}</a>');
                a.click(function(){
                  $(this).fadeOut( opts.loading.speed, function(){
                    opts.contentSelector.infinitescroll('resume');
                  });
                });
                $(opts.loading.selector).append(a);
              }
              return true;
            }
          },
          animate       : true
        });
      });
    {/if}
    $('table').tablesorter();
  </script>
{/strip}