{if $USER_ROLE >= 3}
  <p class='center'>
    <a href='/blog/create'>
      <img src='%PATH_IMAGES%/spacer.png' class='icon-create' alt='' width='16' height='16' />
      {$lang.global.create.entry}
    </a>
  </p>
{/if}
{if !$blog}
  <div class='alert alert-warning'>
    <h4>{$lang.error.missing.entries}</h4>
  </div>
{else}
  {foreach $blog as $b}
    <article class='blogs'>
      <header class='page-header'>
        <h2>
          {if $b.published == false}
            {$lang.global.not_published}:
          {/if}
          <a href='/blog/{$b.id}/{$b.encoded_title}'>{$b.title}</a>
          {if $USER_ROLE >= 3}
            <a href='/blog/{$b.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class='icon-update' alt='{$lang.global.update.update}'
                   title='{$lang.global.update.update}' width='16' height='16' />
            </a>
          {/if}
        </h2>
        <p>
          <time datetime='{$b.date_w3c}'>
            {$b.datetime}
          </time>
          {$lang.global.by}
          <a href='/user/{$b.author_id}/{$b.encoded_full_name}' rel='author'>{$b.full_name}</a>
          {if $b.date_modified != ''}
            - {$lang.global.last_update}: {$b.date_modified}
          {/if}
        </p>
      </header>
      {if $b.teaser !== ''}
        <p class='summary'>
          {$b.teaser}
        </p>
      {/if}
      {$b.content}
      <footer class='row'>
        <div class='span4 tags'>
          {if $b.tags[0] !== ''}
            {foreach from=$b.tags item=t name=tags}
              <a class='js-tooltip' title='{$lang.global.tags.info}: {$t}' href='/blog/{$t}'>
                {$t}
              </a>{if !$t@last}, {/if}
            {/foreach}
          {/if}
        </div>
        <div class='span4 comments right'>
          <a href='/blog/{$b.id}/{$b.encoded_title}#comments'>
            {$b.comment_sum} {$lang.global.comments}
          </a>
        </div>
        {if $_request_id_ && (!isset($smarty.get.action) || $smarty.get.action !== 'page')}
          <div class='span8'>
            <hr />
            <div id='socialshareprivacy'></div>
            <script src='%PATH_JS%/core/jquery.socialshareprivacy{$_compress_files_suffix_}.js' type='text/javascript'></script>
          </div>
        {/if}
      </footer>
    </article>
  {/foreach}
  {* Show comments only if we got a entry *}
  {if isset($b.id)}
    {$_blog_footer_}
  {/if}
{/if}
<script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script src='%PATH_JS%/core/jquery.capty{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.js-fancybox').fancybox();
    $('.js-image').capty({ height: 35 });

    if($('#socialshareprivacy').length > 0){
      $('#socialshareprivacy').socialSharePrivacy({
        services : {
          facebook : {
            'language' : '{$WEBSITE_LOCALE}',
            'dummy_img' : '%PATH_IMAGES%/jquery.socialshareprivacy/dummy_facebook.png'
          },
          twitter : {
            'dummy_img' : '%PATH_IMAGES%/jquery.socialshareprivacy/dummy_twitter.png'
          },
          gplus : {
            'dummy_img' : '%PATH_IMAGES%/jquery.socialshareprivacy/dummy_gplus.png',
            'display_name' : 'Google Plus'
          }
        },
        css_path : ''
      });
    };
  });

  $('.js-media').each(function(e) {
    var $this = $(this);
    $.getJSON(this.title, function(data) {
      $this.html(data['html']);
    });
  });
</script>