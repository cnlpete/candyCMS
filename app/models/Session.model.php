<?php

/**
 * Handle all blog SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Page as Page;
use PDO;

class Session extends Main {

  /**
   * Fetch all user data of active session
   *
   * @static
   * @access public
   * @return array $aResult user data
   * @see app/controllers/Index.controller.php
   */
  public static function getSessionData() {
    if (empty(parent::$_oDbStatic))
      parent::_connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT
                                                *
                                              FROM
                                                " . SQL_PREFIX . "users
                                              WHERE
                                                session = :session_id
                                              AND
                                                ip = :ip
                                              LIMIT
                                                1");

      $oQuery->bindParam('session_id', session_id(), PDO::PARAM_STR);
      $oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $bReturn = $oQuery->execute();

      if ($bReturn === false)
        $this->destroy();

      return $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      parent::$_oDbStatic->rollBack();
    }
  }

  /**
   * Update session for current user.
   *
   * @static
   * @access public
   * @param integer $iId ID of user
   * @return boolean $bResult status of query
   */
  public static function update($iId) {
    try {
      $oQuery = parent::$_oDbStatic->prepare("UPDATE
                                                " . SQL_PREFIX . "users
                                              SET
                                                session = :session,
                                                ip = :ip,
                                                last_login = :last_login
                                              WHERE
                                                id = :id");

      $oQuery->bindParam('session', session_id(), PDO::PARAM_STR);
      $oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $oQuery->bindParam('last_login', time(), PDO::PARAM_INT);
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      parent::$_oDbStatic->rollBack();
    }
  }

	/**
	 * Return aggregated data
	 *
	 * @access public
	 * @return array $this->_aData
	 */
  public function getData() {
    return $this->_aData;
  }

  /**
   * Create a user session.
   *
   * @access public
   * @return boolean status of query
   *
   */
  public function create() {
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        id, verification_code
                                      FROM
                                        " . SQL_PREFIX . "users
                                      WHERE
                                        email = :email
                                      AND
                                        password = :password
                                      LIMIT
                                        1");

      $sPassword = md5(RANDOM_HASH . Helper::formatInput($this->_aRequest['password']));
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
      $oQuery->bindParam('password', $sPassword, PDO::PARAM_STR);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    # User did verify his and has id, so log in!
    if (isset($aResult['id']) && !empty($aResult['id']) && empty($aResult['verification_code']))
      return Session::update($aResult['id']);
  }

  /**
   * Resend password of verification code.
   *
   * @access public
   * @param string $sNewPasswordSecure
   * @return boolean status of query
   */
  public function createResendActions($sNewPasswordSecure = '') {
    require_once 'app/controllers/Mail.controller.php';
    $bResult = false;

    if ($this->_aRequest['action'] == 'resendpassword') {
      try {
        $oQuery = $this->_oDb->prepare("SELECT name FROM " . SQL_PREFIX . "users WHERE email = :email");
        $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
        $bResult = $oQuery->execute();

        $this->_aData = $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      if (empty($this->_aData['name']) || $bResult == false)
        return false;

      else {
        # Set new password
        try {
          $oQuery = $this->_oDb->prepare("UPDATE " . SQL_PREFIX . "users SET password = :password WHERE email = :email");
          $oQuery->bindParam(':password', $sNewPasswordSecure, PDO::PARAM_STR);
          $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);

          return $oQuery->execute();
        }
        catch (AdvancedException $e) {
          $this->_oDb->rollBack();
        }
      }
    }
    elseif ($this->_aRequest['action'] == 'resendverification') {
      try {
        $oQuery = $this->_oDb->prepare("SELECT name, verification_code FROM " . SQL_PREFIX . "users WHERE email = :email");
        $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
        $bResult = $oQuery->execute();

        $this->_aData = $oQuery->fetch(PDO::FETCH_ASSOC);

        if (empty($this->_aData['verification_code']) || $bResult == false)
          return false;
        else
          return $bResult;
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
  }

  /**
   * Destroy a user session and logout.
   *
   * @access public
   * @return boolean status of query
   *
   */
  public function destroy() {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "users
                                      SET
                                        session = :session_null
                                      WHERE
                                        session = :session_id");

      $sNull = 'NULL';
      $iSessionId = session_id();
      $oQuery->bindParam('session_null', $sNull, PDO::PARAM_NULL);
      $oQuery->bindParam('session_id', $iSessionId, PDO::PARAM_STR);
      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }
}