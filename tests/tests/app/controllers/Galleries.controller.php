<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

require_once PATH_STANDARD . '/app/controllers/Galleries.controller.php';

use \CandyCMS\Controller\Galleries as Galleries;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfGalleryController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'galleries';
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
    # Show overview
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('6dffc4c552');

    # Show album
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertResponse(200);
		$this->assertText('982e960e18');

		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/6dffc4c552'));
		$this->assertResponse(200);
		$this->assertText('982e960e18');

    # Show image
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/image/1'));
		$this->assertResponse(200);
		$this->assertText('782c660e17');
	}

	function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/' . $this->aRequest['controller'] . '/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile));
		$this->assertTrue(unlink($sFile));
	}

  /**
   * @todo create / update / destroy tests
   */
}