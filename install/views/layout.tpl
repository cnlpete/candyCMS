<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <link href='/public/favicon.ico' rel='shortcut icon' type='image/x-icon' />
    <link href='/public/css/core/application.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1{$_compress_files_suffix_}.js"></script>
    <script type="text/javascript">
      if (typeof jQuery == 'undefined')
        document.write(unescape("%3Cscript src='%PATH_PUBLIC%/js/core/jquery.1.7.1{$_compress_files_suffix_}.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <title>{$title}</title>
  </head>
  <body>
    <nav class="navbar navbar-fixed-top">
      <div class='navbar-inner'>
        <div class='container'>
          <span class="brand">
            CandyCMS - Installation and migration assistant
          </span>
        </div>
      </div>
    </nav>
    <div class="container">
      <div class='page-header'>
        <h1>
          {$title}
        </h1>
      </div>
      {$content}
    </div>
  </body>
</html>