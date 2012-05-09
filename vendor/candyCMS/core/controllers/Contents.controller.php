<?php

/**
 * CRUD action of content entries.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Core\Controllers;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;

class Contents extends Main {

  /**
   * Show content entry or content overview (depends on a given ID or not).
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if ($this->_iId) {
      $sTemplateDir  = Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile = Helper::getTemplateType($sTemplateDir, 'show');

      if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
        $aData = $this->_oModel->getData($this->_iId);

        if (!isset($aData) || !$aData[$this->_iId]['id'])
          return Helper::redirectTo('/errors/404');

        $this->setDescription($aData[$this->_iId]['teaser']);
        $this->setKeywords($aData[$this->_iId]['keywords']);
        $this->setTitle($this->_removeHighlight($aData[$this->_iId]['title']));

        $this->oSmarty->assign('contents', $aData);
      }

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
    else {
      $sTemplateDir  = Helper::getTemplateDir($this->_aRequest['controller'], 'overview');
      $sTemplateFile = Helper::getTemplateType($sTemplateDir, 'overview');

      $this->setTitle(I18n::get('global.manager.content'));

      $this->oSmarty->assign('contents', $this->_oModel->getData($this->_iId));

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

  /**
   * Build form template to create or update a content entry.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    $sTemplateDir  = Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile = Helper::getTemplateType($sTemplateDir, '_form');

    # Update
    if ($this->_iId) {
      $aData = $this->_oModel->getData($this->_iId, true);
      $this->setTitle($aData['title']);

      foreach ($aData as $sColumn => $sData)
        $this->oSmarty->assign($sColumn, $sData);
    }

    # Create
    else {
      foreach ($this->_aRequest[$this->_sController] as $sInput => $sData)
        $this->oSmarty->assign($sInput, $sData);
    }

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a content entry.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('content');

    return parent::_create(array('searches', 'sitemaps'));
  }

  /**
   * Update a content entry.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _update() {
    $this->_setError('content');

    return parent::_update(array('searches', 'sitemaps'));
  }

  /**
   * Destroy a content entry.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    return parent::_destroy(array('searches', 'sitemaps'));
  }
}