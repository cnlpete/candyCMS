<?php

/**
 * System tests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 2.0
 *
 */

define('PATH_STANDARD', dirname(__FILE__) . '/..');

require_once PATH_STANDARD . '/vendor/autoload.php';
require_once PATH_STANDARD . '/tests/simpletest/autorun.php';
require_once PATH_STANDARD . '/tests/simpletest/web_tester.php';

require_once PATH_STANDARD . '/tests/candy/Candy.unit.php';
require_once PATH_STANDARD . '/tests/candy/Candy.web.php';

require_once PATH_STANDARD . '/app/config/Candy.inc.php';
require_once PATH_STANDARD . '/app/config/Plugins.inc.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/AdvancedException.helper.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/SmartySingleton.helper.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/I18n.helper.php';

define('CLEAR_CACHE', true);
define('CURRENT_URL', 'http://localhost/');
define('MOBILE', false);
define('MOBILE_DEVICE', false);
define('UNIQUE_ID', 'tests');
define('VERSION', '0');
define('TESTFILE', '/private/var/tmp/test'.md5(time()));
define('WEBSITE_LOCALE', 'en_US');
define('WEBSITE_LANGUAGE', DEFAULT_LANGUAGE);
define('EXTENSION_CHECK', ALLOW_EXTENSIONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test');

setlocale(LC_ALL, WEBSITE_LOCALE);

class AllFileTests extends TestSuite {

	function __construct() {
		parent::__construct();
		$this->TestSuite('All tests');

    if (WEBSITE_MODE !== 'test')
      die('not in testing mode');

    else if (DEFAULT_LANGUAGE !== 'en')
      die('language not set to "en"');

    else {
      new \CandyCMS\Core\Helpers\I18n(WEBSITE_LANGUAGE, $_SESSION);

      # All Tests
      $aTests = array(
          # @todo AdvancedException
          # @todo Dispatcher
          'Helper.helper'   => PATH_STANDARD . '/tests/tests/app/helpers/Helper.helper.php',
          'I18n.helper'     => PATH_STANDARD . '/tests/tests/app/helpers/I18n.helper.php',

          'Image.helper'    => PATH_STANDARD . '/tests/tests/app/helpers/Image.helper.php',
          # @todo pagination
          'SmartySingleton' => PATH_STANDARD . '/tests/tests/app/helpers/SmartySingleton.helper.php',
          'Upload.helper'   => PATH_STANDARD . '/tests/tests/app/helpers/Upload.helper.php',

          'blogs'     => array(
                          PATH_STANDARD . '/tests/tests/app/models/Blogs.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Blogs.controller.php'),

          'calendars' => array(
                          PATH_STANDARD . '/tests/tests/app/models/Calendars.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Calendars.controller.php'),

          'comments'  => array(
                          PATH_STANDARD . '/tests/tests/app/models/Comments.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Comments.controller.php'),

          'contents'  => array(
                          PATH_STANDARD . '/tests/tests/app/models/Contents.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Contents.controller.php'),

          'downloads' => array(
                          PATH_STANDARD . '/tests/tests/app/models/Downloads.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Downloads.controller.php'),

          'errors'    => PATH_STANDARD . '/tests/tests/app/controllers/Errors.controller.php',

          'galleries' => array(
                          PATH_STANDARD . '/tests/tests/app/models/Galleries.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Galleries.controller.php'),

          'index'     =>  PATH_STANDARD . '/tests/tests/app/controllers/Index.controller.php',


          'logs'      => array(
                          PATH_STANDARD . '/tests/tests/app/models/Logs.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Logs.controller.php'),

          'mails'     => PATH_STANDARD . '/tests/tests/app/controllers/Mails.controller.php',

          'main'      => array(
                          PATH_STANDARD . '/tests/tests/app/models/Main.model.php'),
          # @todo controller

          'medias'    => PATH_STANDARD . '/tests/tests/app/controllers/Medias.controller.php',

          'newsletters' => PATH_STANDARD . '/tests/tests/app/controllers/Newsletters.controller.php',

          'rss'       => PATH_STANDARD . '/tests/tests/app/controllers/Rss.controller.php',

          'searches'  => array(
                          PATH_STANDARD . '/tests/tests/app/models/Searches.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Searches.controller.php'),

          'sessions'  => array(
                          PATH_STANDARD . '/tests/tests/app/models/Sessions.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Sessions.controller.php'),

          'sitemaps'  => PATH_STANDARD . '/tests/tests/app/controllers/Sitemaps.controller.php',

          'sites'     => PATH_STANDARD . '/tests/tests/app/controllers/Sites.controller.php',

          'users'     => array(
                          PATH_STANDARD . '/tests/tests/app/models/Users.model.php',
                          PATH_STANDARD . '/tests/tests/app/controllers/Users.controller.php'),
      );

      $argv = $_SERVER['argv'];
      $iNumberOfArgs = count($argv);
      # are there specific tests given?
      if ($iNumberOfArgs > 1) {
        array_shift($argv);
        foreach ($argv as $sArg)
          if ($aTests[$sArg]) {
            $this->_addFiles($aTests[$sArg]);
          }
          else
            die($sArg . ' not found');
      }

      # no specific test given, run all of them
      else {
        # add all tests
        $this->_addFiles($aTests);
      }
    }
	}

  private function _addFiles($mFile) {
    if (is_array($mFile))
      foreach ($mFile as $sTest)
        $this->_addFiles($sTest);
    else
      $this->addFile($mFile);
  }
}

?>