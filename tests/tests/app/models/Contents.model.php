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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Contents.model.php';

use \CandyCMS\Core\Models\Contents;

class UnitTestOfContentModel extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array(
        'title'     => 'Title',
        'teaser'    => 'Teaser',
        'content'   => 'Content',
        'keywords'  => 'Keywords',
        'controller'=> 'contents');

    $this->oObject = new Contents($this->aRequest, $this->aSession);
  }

	function tearDown() {
		parent::tearDown();
	}

  function testCreate() {
    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Contents::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getId(1), 'array');
    $this->assertIsA($this->oObject->getOverview(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oObject->update($this->iLastInsertId));
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}