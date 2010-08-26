<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_User extends Model_Main {

  # Get user name and surname
  public static final function getUserNamesAndEmail($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT name, surname, email FROM user WHERE id = :id LIMIT 1");

      $oQuery->bindParam('id', $iId);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      return $aResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }

  public static function getExistingUser($sEmail) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT email FROM user WHERE email = :email LIMIT 1");

      $oQuery->bindParam('email', $sEmail);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      if (isset($aResult['email']) && !empty($aResult['email']))
        return false;
      else
        return true;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }

  private function _setData() {
    if (empty($this->_iID)) {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->query(" SELECT
                                    id,
                                    name,
                                    email,
                                    surname,
                                    last_login,
                                    regdate,
                                    use_gravatar
                                  FROM
                                    user
                                  ORDER BY
                                    id ASC");

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($aResult as $aRow) {
          $iId = $aRow['id'];
          $aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);

          $this->_aData[$iId] = array(
              'name'          => Helper::formatOutput($aRow['name']),
              'surname'       => Helper::formatOutput($aRow['surname']),
              'last_login'    => Helper::formatTimestamp($aRow['last_login']),
              'regdate'       => Helper::formatTimestamp($aRow['regdate']),
              'id'            => $aRow['id'],
              'use_gravatar'  => $aRow['use_gravatar'],
              'avatar_32'     => Helper::getAvatar('user', 32, $aRow['id'], $aGravatar)
          );
        }
      } catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }
    } else {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("	SELECT
                                    name,
                                    surname,
                                    last_login,
                                    email,
                                    regdate,
                                    description,
                                    userright,
                                    newsletter_default,
                                    use_gravatar
                                  FROM
                                    user
                                  WHERE
                                    id = :id
                                  LIMIT 1");

        $oQuery->bindParam('id', $this->_iID);
        $oQuery->execute();

        $this->_aData = $oQuery->fetch(PDO::FETCH_ASSOC);

      } catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }
    }
  }

  public function getData($iId = '') {
    if (!empty($iId))
      $this->_iID = (int) $iId;

    $this->_setData();
    return $this->_aData;
  }

  public function create($iVerificationCode) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  user (name, surname, password, email, regdate, verification_code)
                                VALUES
                                  ( :name, :surname, :password, :email, :regdate, :verification_code )");

      $oQuery->bindParam('name', Helper::formatInput($this->_aRequest['name']));
      $oQuery->bindParam('surname', Helper::formatInput($this->_aRequest['surname']));
      $oQuery->bindParam('password', md5(RANDOM_HASH . $this->_aRequest['password']));
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']));
      $oQuery->bindParam('regdate', time());
      $oQuery->bindParam('verification_code', $iVerificationCode);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }

  public function update($iId) {
    $iNewsletterDefault = isset($this->_aRequest['newsletter_default']) ? 1 : 0;
    $iUseGravatar = isset($this->_aRequest['use_gravatar']) ? 1 : 0;

    if (($iId !== USER_ID) && USER_RIGHT == 4)
      $iUserRight = isset($this->_aRequest['userright']) && !empty($this->_aRequest['userright']) ?
              (int) $this->_aRequest['userright'] :
              0;
    else
      $iUserRight = USER_RIGHT;

    # Make sure the password is set and override session due to saving problems
    if (isset($this->_aRequest['newpw']) && !empty($this->_aRequest['newpw']) &&
            isset($this->_aRequest['oldpw']) && !empty($this->_aRequest['oldpw']))
      $this->_aSession['userdata']['password'] = md5(RANDOM_HASH . $this->_aRequest['newpw']);
    $sPassword = $this->_aSession['userdata']['password'];

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  user
                                SET
                                  name = :name,
                                  surname = :surname,
                                  email = :email,
                                  description = :description,
                                  newsletter_default = :newsletter_default,
                                  use_gravatar = :use_gravatar,
                                  password = :password,
                                  userright = :userright
                                WHERE
                                  id = :id");

      $oQuery->bindParam('name', Helper::formatInput($this->_aRequest['name']));
      $oQuery->bindParam('surname', Helper::formatInput($this->_aRequest['surname']));
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']));
      $oQuery->bindParam('description', Helper::formatInput($this->_aRequest['description']));
      $oQuery->bindParam('newsletter_default', $iNewsletterDefault);
      $oQuery->bindParam('use_gravatar', $iUseGravatar);
      $oQuery->bindParam('password', $sPassword);
      $oQuery->bindParam('userright', $iUserRight);
      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }

  public function destroy($iId) {
    # Delete avatars
    @unlink(PATH_UPLOAD . '/user/32/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/64/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/100/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/200/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/' . POPUP_DEFAULT_X . '/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/original/' . (int) $iId . '.jpg');

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	DELETE FROM
                                  user
                                WHERE
                                  id = :id
                                LIMIT
                                  1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }

	public function verifyEmail($iVerificationCode) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	SELECT
																	id
																FROM
																	user
																WHERE
																	verification_code = :verification_code
																LIMIT 1");

      $oQuery->bindParam('verification_code', $iVerificationCode);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }

    if (!empty($aResult['id'])) {
      try {
        $oQuery = $oDb->prepare("	UPDATE
																		user
																	SET
																		verification_code = ''
																	WHERE
																		id = :id");

        $oQuery->bindParam('id', $aResult['id']);
        $bResult = $oQuery->execute();

        $oDb = null;

        if ($bResult == true)
          return Model_Session::setActiveSession($aResult['id']) . Helper::redirectTo('/Start');
        else
          return false;
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }
    }
    else {
      $oDb = null;
      return false;
    }
  }
}