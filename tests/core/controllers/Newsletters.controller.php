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
require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Newsletters.controller.php';

use \CandyCMS\Core\Controllers\Newsletters;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfNewsletterController extends CandyWebTest {

  function setUp() {
    $this->aRequest['controller'] = 'newsletters';
    $this->oObject = new Newsletters($this->aRequest, $this->aSession);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testCreate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('newsletters.title.subscribe'));
    $this->assertField('newsletters[name]', '');
    $this->assertField('newsletters[surname]', '');
    $this->assertField('newsletters[email]', '');
  }

  function testShow() {
    #should redirect to create
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller']);
    $this->assertResponse(302);
  }

  function testSubscribe() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertTrue($this->setField('newsletters[name]', md5($this->aSession['user']['name'] . time())));
    $this->assertTrue($this->setField('newsletters[surname]', md5($this->aSession['user']['surname'] . time())));
    $this->assertTrue($this->setField('newsletters[email]', time() . '_' . WEBSITE_MAIL_NOREPLY));

    $this->click(I18n::get('newsletters.title.subscribe'));
    $this->assertText(I18n::get('success.newsletter.create'));
    $this->assertResponse(200);

    # Wrong email address
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertTrue($this->setField('newsletters[name]', md5($this->aSession['user']['name'] . time())));
    $this->assertTrue($this->setField('newsletters[surname]', md5($this->aSession['user']['surname'] . time())));
    $this->assertTrue($this->setField('newsletters[email]', str_replace('@', '', WEBSITE_MAIL_NOREPLY)));

    $this->click(I18n::get('newsletters.title.subscribe'));
    $this->assertText(I18n::get('error.standard'));
    $this->assertResponse(200);
  }

  function testUpdate() {
    # there is no update
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }

  function testDestroy() {
    # there is no destroy
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assert404();
  }
}