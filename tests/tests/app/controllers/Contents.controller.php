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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Contents.controller.php';

use \CandyCMS\Core\Controllers\Contents;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfContentController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'contents';
		$this->oObject = new Contents($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('18855f87f2');
    $this->assertNoText('8f7fb844b0');

    # Short ID
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertResponse(200);

    # Long ID
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/18855f87f2'));
		$this->assertResponse(200);
	}

	function testShowEntryUnpublished() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2'));
		$this->assert404();
	}

	function testCreate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testUpdate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}
}