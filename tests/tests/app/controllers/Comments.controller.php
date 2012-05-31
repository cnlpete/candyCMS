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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Comments.controller.php';

use \CandyCMS\Core\Controllers\Comments;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfCommentController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'blogs';
		$this->oObject = new Comments($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertText(I18n::get('global.comments'));
    $this->assertResponse(200);
	}

	function testCreate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
    $this->assertField('blogs[name]', '');
    $this->assertField('blogs[email]', '');
    $this->assertField('blogs[content]', '');

    #empty submit
    $this->click(I18n::get('comments.title.create'));
		$this->assertText(I18n::get('error.form.missing.name'));
		$this->assertText(I18n::get('error.form.missing.content'));

    #create with wrong email
    $this->assertTrue($this->setField('blogs[name]', 'Name'));
    $this->assertTrue($this->setField('blogs[email]', 'notAnEmailAdress'));
    $this->assertTrue($this->setField('blogs[content]', 'hello'));
    $this->click(I18n::get('comments.title.create'));
		$this->assertText(I18n::get('error.mail.format'));

    #create with empty email
    $this->assertTrue($this->setField('blogs[name]', 'Name'));
    $this->assertTrue($this->setField('blogs[email]', ''));
    $this->assertTrue($this->setField('blogs[content]', 'hello without email adress'));
    $this->click(I18n::get('comments.title.create'));
		$this->assertText(I18n::get('success.create'));
	}

	function testDestroy() {
    $this->assertTrue($this->get(WEBSITE_URL . '/comments/1/destroy'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }

  function testUpdate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/comments/1/update'));
    $this->assert404();
  }

}