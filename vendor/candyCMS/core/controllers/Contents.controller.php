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
      $this->oSmarty->setTemplateDir($sTemplateDir);

      if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
        $aData = $this->_oModel->getId($this->_iId);

        if (!isset($aData) || !$aData[$this->_iId]['id'])
          return Helper::redirectTo('/errors/404');

        $this->setDescription($aData[$this->_iId]['teaser']);
        $this->setKeywords($aData[$this->_iId]['keywords']);
        $this->setTitle($this->_removeHighlight($aData[$this->_iId]['title']));

        $this->oSmarty->assign('contents', $aData);
      }

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
    else {
      $sTemplateDir  = Helper::getTemplateDir($this->_aRequest['controller'], 'overview');
      $sTemplateFile = Helper::getTemplateType($sTemplateDir, 'overview');
      $this->oSmarty->setTemplateDir($sTemplateDir);

      $this->setTitle(I18n::get('global.manager.content'));

      $this->oSmarty->assign('contents', $this->_oModel->getOverview());

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
    $this->oSmarty->setTemplateDir($sTemplateDir);

    if ($this->_iId) {
      $aData = $this->_oModel->getId($this->_iId, true);
      $this->setTitle($aData['title']);
    }
    else {
      $aData['title']     = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $aData['teaser']    = isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
      $aData['keywords']  = isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
      $aData['content']   = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $aData['published'] = isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';
    }

    foreach($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

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