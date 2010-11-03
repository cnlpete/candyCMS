<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This plugin loads a template with your adsense code. Copy your code from Google and
# paste it into "public/skins/_plugins/adsense.tpl".
# You can include your plugin via "{$_plugin_adsense_}".
# This does only work at the main template ("app/views/layouts/application.tpl").

class Adsense {

  public function show() {
    $oSmarty = new Smarty();
    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = 'public/skins/_plugins';
    return $oSmarty->fetch('adsense.tpl');
  }
}