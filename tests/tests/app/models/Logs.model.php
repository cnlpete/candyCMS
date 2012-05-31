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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Logs.model.php';

use \CandyCMS\Core\Models\Logs;
use \CandyCMS\Core\Helpers\Helper;

class UnitTestOfLogModel extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array('controller' => 'logs');

    $this->oObject = new Logs($this->aRequest, $this->aSession);
  }

  function testCreate() {
    $this->assertTrue(Logs::insert('test', 'create', 1, 0, time(), time()));

    $this->iLastInsertId = (int) Logs::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetOverview() {
    $this->assertIsA($this->oObject->getOverview(), 'array');
  }

  function testSetEndTime() {
    $iTime = time();
    $this->assertTrue($this->oObject->setEndTime($this->iLastInsertId, $iTime));
    $aLogs = $this->oObject->getOverview(1);
    foreach ($aLogs as $aLog)
      $this->assertEqual($aLog['time_end']['raw'], $iTime);
  }

  function testSetResultFlag() {
    $this->assertTrue($this->oObject->setResultFlag($this->iLastInsertId, false));
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}