<?php

/**
 * CRUD actions for gallery overview and gallery albums.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Gallery as Model;

require_once 'app/models/Gallery.model.php';
require_once 'app/helpers/Page.helper.php';
require_once 'app/helpers/Upload.helper.php';
require_once 'app/helpers/Image.helper.php';

class Gallery extends Main {

  /**
   * Include the gallery model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new Model($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  /**
   * Show gallery album or album overview (depends on a given ID or not).
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    # Language
    $this->oSmarty->assign('lang_create_file_headline', LANG_GALLERY_FILE_CREATE_TITLE);
    $this->oSmarty->assign('lang_no_files_uploaded', LANG_ERROR_GALLERY_NO_FILES_UPLOADED);

    # Album images
    if (!empty($this->_iId)) {
      # collect data array
      $sAlbumName = Model::getAlbumName($this->_iId);
      $sAlbumDescription = Model::getAlbumContent($this->_iId);

      # Get data and count afterwards
      $this->_aData = $this->_oModel->getThumbs($this->_iId);

      $this->oSmarty->assign('files', $this->_aData);
      $this->oSmarty->assign('file_no', count($this->_aData));
      $this->oSmarty->assign('gallery_name', $sAlbumName);
      $this->oSmarty->assign('gallery_content', $sAlbumDescription);

      $this->_setDescription($sAlbumDescription);
      $this->_setTitle($this->_removeHighlight(LANG_GLOBAL_GALLERY . ': ' . $sAlbumName));

      $this->oSmarty->template_dir = Helper::getTemplateDir('galleries' ,'files');
      return $this->oSmarty->fetch('files.tpl');
    }
    # Album overview
    else {
      $this->_setDescription(LANG_GLOBAL_GALLERY);
      $this->_setTitle(LANG_GLOBAL_GALLERY);

      $this->oSmarty->assign('albums', $this->_oModel->getData());
      $this->oSmarty->assign('_pages_', $this->_oModel->oPage->showPages('/gallery'));

      # Language
      $this->oSmarty->assign('lang_create_album_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $this->oSmarty->assign('lang_headline', LANG_GLOBAL_GALLERY);

      $this->oSmarty->template_dir = Helper::getTemplateDir('galleries' ,'albums');
      return $this->oSmarty->fetch('albums.tpl');
    }
  }

  /**
   * Build form template to create or update a gallery album.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    $this->_aData = $this->_oModel->getData($this->_iId, true);

    if (!empty($this->_iId)) {
      # Language
      $this->oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $this->oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    else {
      $this->_aData['title']        = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $this->_aData['description']  = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';

      # Language
      $this->oSmarty->assign('lang_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $this->oSmarty->assign('lang_submit', LANG_GALLERY_ALBUM_CREATE_TITLE);
    }

    foreach ($this->_aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->oSmarty->template_dir = Helper::getTemplateDir('galleries', '_form_album');
    return $this->oSmarty->fetch('_form_album.tpl');
  }

  /**
   * Create a gallery album.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('title');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->create() === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('gallery_albums'));
      return Helper::successMessage(LANG_SUCCESS_CREATE, '/gallery');
    }

    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/gallery');
  }

  /**
   * Update a gallery album.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _update() {
    $this->_setError('title');

    $sRedirect = '/gallery/' . (int) $this->_aRequest['id'];

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return Helper::successMessage(LANG_SUCCESS_UPDATE, $sRedirect);
    }

    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
  }

  /**
   * Destroy a gallery album.
   *
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    if($this->_oModel->destroy($this->_iId) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
      return Helper::successMessage(LANG_SUCCESS_DESTROY, '/gallery');
    }

    else {
      unset($this->_iId);
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/gallery');
    }
  }

  /**
   * Build form template to upload or update a file.
   * NOTE: We need to get the request action because we already have an gallery album ID.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormFileTemplate() {
    # Update
    if ($this->_aRequest['action'] == 'updatefile') {
      $this->oSmarty->assign('content', Model::getFileContent($this->_iId));

      # Language
      $this->oSmarty->assign('lang_headline', LANG_GALLERY_FILE_UPDATE_TITLE);
    }
    # Create
    else {
      # See helper/Image.helper.php for details!
      # r = resize, c = cut
      $sDefault = isset($this->_aRequest['cut']) ? Helper::formatInput($this->_aRequest['cut']) : 'c';

      $this->oSmarty->assign('default', $sDefault);
      $this->oSmarty->assign('content', isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '');

      # Language
      $this->oSmarty->assign('lang_create_file_cut', LANG_GALLERY_FILE_CREATE_LABEL_CUT);
      $this->oSmarty->assign('lang_create_file_resize', LANG_GALLERY_FILE_CREATE_LABEL_RESIZE);
      $this->oSmarty->assign('lang_file_choose', LANG_GALLERY_FILE_CREATE_LABEL_CHOOSE);
      $this->oSmarty->assign('lang_headline', LANG_GALLERY_FILE_CREATE_TITLE);
    }

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->oSmarty->template_dir = Helper::getTemplateDir('galleries', '_form_file');
    return $this->oSmarty->fetch('_form_file.tpl');
  }

  /**
   * Create a gallery entry.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function createFile() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      if (isset($this->_aRequest['createfile_gallery'])) {
        if ($this->_createFile() === true) {
          # Log uploaded image. Request ID = album id
          Log::insert($this->_aRequest['section'], 'createfile', (int) $this->_aRequest['id']);
          return Helper::successMessage(LANG_GALLERY_FILE_CREATE_SUCCESS, '/gallery/' . $this->_iId);
        }
        else
          return Helper::errorMessage(LANG_ERROR_UPLOAD_CREATE, '/gallery/' . $this->_iId . '/createfile');
      }
      else
        return $this->_showFormFileTemplate();
    }
  }

  /**
   * Upload each selected file.
   *
   * @access private
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  private function _createFile() {
    if (isset($this->_aFile['file']) && !empty($this->_aFile['file']['name'][0])) {

      for ($iI = 0; $iI < count($this->_aFile['file']['name']); $iI++) {
        $aFile['name'] = $this->_aFile['file']['name'][$iI];
        $aFile['type'] = $this->_aFile['file']['type'][$iI];
        $aFile['tmp_name'] = $this->_aFile['file']['tmp_name'][$iI];
        $aFile['error'] = $this->_aFile['file']['error'][$iI];
        $aFile['size'] = $this->_aFile['file']['size'][$iI];

        $this->_oModel->createFile($aFile);
      }

      return true;
    }
    else {
      $this->_aError['file'] = LANG_ERROR_FORM_MISSING_FILE;
      return $this->_showFormFileTemplate();
    }
  }

  /**
   * Update a gallery entry.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function updateFile() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/gallery');

    else {
      if( isset($this->_aRequest['updatefile_gallery']) ) {
        if( $this->_oModel->updateFile($this->_iId) === true) {
          Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
          return Helper::successMessage(LANG_SUCCESS_UPDATE, '/gallery');
        }
        else
          return Helper::errorMessage(LANG_ERROR_GLOBAL, '/gallery');
      }
      else
        return $this->_showFormFileTemplate();
    }
  }

  /**
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function destroyFile() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/gallery');

    else {
      if($this->_oModel->destroyFile($this->_iId) === true) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
        unset($this->_iId);
        return Helper::successMessage(LANG_SUCCESS_DESTROY, '/gallery');
      }
      else
        return Helper::errorMessage(LANG_ERROR_GLOBAL_FILE_COULD_NOT_BE_DESTROYED, '/gallery');
    }
  }
}