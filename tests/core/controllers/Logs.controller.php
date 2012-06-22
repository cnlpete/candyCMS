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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Logs.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Logs.model.php';

use \CandyCMS\Core\Controllers\Logs;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfLogController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'logs';
		$this->oObject = new Logs($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

  function testCreate() {
    # there is no create
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assert404();
  }

  function testUpdate() {
    # there is no update
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }

}

class UnitTestOfLogController extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array('controller' => 'logs');

    $this->oObject = new Logs($this->aRequest, $this->aSession);
  }

  function testInsert() {
    $iTime = time() - 100;
    $this->assertTrue(Logs::insert('test', 'create', 1, 0, $iTime, $iTime, true));

    $this->iLastInsertId = (int) \CandyCMS\Core\Models\Logs::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testUpdateEndTime() {
    $iTime = time() + 100;
    $this->assertTrue($this->oObject->updateEndTime($this->iLastInsertId, $iTime));

  }

  function testUpdateResultFlag() {
    $this->assertTrue(Logs::insert('test', 'resultflag', 1, 0, '', '', true));

    $this->iLastInsertId = (int) \CandyCMS\Core\Models\Logs::getLastInsertId();
    $this->assertTrue($this->oObject->updateResultFlag($this->iLastInsertId, false));

  }

}