<?php

/**
 * Handle all blog SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Core\Models;

use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\Pagination;
use PDO;

require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/Pagination.helper.php';

class Blogs extends Main {

  /**
   * Get count of visible blog entries
   *
   * @access public
   * @return integer the total count
   *
   */
  public function getCount() {
    # Show unpublished items and entries with diffent languages to moderators or administrators only
    $sWhere = isset($this->_aSession['user']['role']) && $this->_aSession['user']['role'] >= 3 ?
            'WHERE 1' :
            "WHERE published = '1' AND language = '" . WEBSITE_LANGUAGE . "'";

    try {
      $oQuery  = $this->_oDb->query("SELECT COUNT(*) FROM " . SQL_PREFIX . "blogs " . $sWhere);
      return $oQuery->fetchColumn();
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0043 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Get blog overview data by tagname.
   *
   * @access public
   * @param integer $iLimit blog post limit, 0 for infinite
   * @param string $sTagname the tagname to query for.
   * @return array data from _setData
   *
   */
  public function getOverviewByTag($iLimit = LIMIT_BLOG, $sTagname = '') {
    $iResult = $this->getCount();

    if ($iLimit != 0)
      $this->oPagination = new Pagination($this->_aRequest, (int) $iResult, $iLimit);

    else
      $this->oPagination = new Pagination($this->_aRequest, (int) $iResult, $iResult);

    try {
      # Show unpublished items and entries with diffent languages to moderators or administrators only
      $sWhere = isset($this->_aSession['user']['role']) && $this->_aSession['user']['role'] >= 3 ?
              'WHERE 1' :
              "WHERE published = '1' AND language = '" . WEBSITE_LANGUAGE . "'";

      $sLimit = $iLimit != 0 ?
              ' LIMIT ' . $this->oPagination->getOffset() . ', ' . $this->oPagination->getLimit() :
              '';

      if (empty($sTagname)) {
        $sTagname = str_replace('%20', ' ', Helper::formatInput($this->_aRequest['search'], false));
        # Remove all characters that might harm us, only allow digits, normal letters and whitespaces
        $sTagname = trim( preg_replace('/[^\d\s\w]/', '', $sTagname) );
      }

      $oQuery = $this->_oDb->prepare("SELECT
                                        b.*,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email,
                                        u.use_gravatar,
                                        COUNT(c.id) AS comment_sum
                                      FROM
                                        " . SQL_PREFIX . "blogs b
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        b.author_id=u.id
                                      LEFT JOIN
                                        " . SQL_PREFIX . "comments c
                                      ON
                                        c.parent_id=b.id
                                      " . $sWhere . "
                                        AND (tags LIKE :CommaTagnameComma
                                          OR tags LIKE :CommaTagname
                                          OR tags LIKE :tagnameComma
                                          OR tags   =  :tagname
                                          OR tags LIKE :CommaSpaceTagname
                                          OR tags LIKE :CommaSpaceTagnameComma)
                                      GROUP BY
                                        b.id
                                      ORDER BY
                                        b.date DESC"
                                      . $sLimit);
      $oQuery->bindValue(':CommaTagnameComma', '%,' . $sTagname . ',%', PDO::PARAM_STR);
      $oQuery->bindValue(':CommaTagname', '%,' . $sTagname, PDO::PARAM_STR);
      $oQuery->bindValue(':tagnameComma', $sTagname . ',%', PDO::PARAM_STR);
      $oQuery->bindValue(':tagname', $sTagname, PDO::PARAM_STR);
      # These last two lines are for compatibility, since there might be blog-entries done before 2.1 with a space
      $oQuery->bindValue(':CommaSpaceTagname', '%, ' . $sTagname, PDO::PARAM_STR);
      $oQuery->bindValue(':CommaSpaceTagnameComma', '%, ' . $sTagname . ',%', PDO::PARAM_STR);
      $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0001 - ' . $p->getMessage());
      exit('SQL error.');
    }

    foreach ($aResult as $aRow) {
      # We use the date as identifier to give plugins the possibility to patch into the system.
      $iDate = $aRow['date'];

      # We need to specify 'blogs' because this might also be called for rss
      $this->_aData[$iDate] = $this->_formatForOutput($aRow,
              array('id', 'uid', 'author_id', 'comment_sum'),
              array('published', 'use_gravatar'),
              'blogs');

      # @todo trim spaces, redundant, if those get removed at blog creation/editing
      $this->_aData[$iDate]['tags_raw'] = $aRow['tags'];
      # Explode using ',' and filter empty items (since explode always gives at least one item)
      $this->_aData[$iDate]['tags'] = array_filter(array_map('trim', explode(',', $aRow['tags'])));
      $this->_formatDates($this->_aData[$iDate], 'date_modified');
    }

    return $this->_aData;
  }

  /**
   * Get blog overview data.
   *
   * @access public
   * @param integer $iLimit blog post limit, 0 for infinite
   * @return array data from _setData
   *
   */
  public function getOverview($iLimit = LIMIT_BLOG) {
    if (WEBSITE_MODE == 'test' && $iLimit != 0)
      $iLimit = 2;

    $iResult = $this->getCount();

    if ($iLimit != 0)
      $this->oPagination = new Pagination($this->_aRequest, (int) $iResult, $iLimit);

    else
      $this->oPagination = new Pagination($this->_aRequest, (int) $iResult, $iResult);

    try {
      # Show unpublished items and entries with diffent languages to moderators or administrators only
      $sWhere = isset($this->_aSession['user']['role']) && $this->_aSession['user']['role'] >= 3 ?
              '' :
              "WHERE published = '1' AND language = '" . WEBSITE_LANGUAGE . "'";

      $sLimit = $iLimit != 0 ?
              ' LIMIT ' . $this->oPagination->getOffset() . ', ' . $this->oPagination->getLimit() :
              '';

      $oQuery = $this->_oDb->prepare("SELECT
                                        b.*,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email,
                                        u.use_gravatar,
                                        COUNT(c.id) AS comment_sum
                                      FROM
                                        " . SQL_PREFIX . "blogs b
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        b.author_id=u.id
                                      LEFT JOIN
                                        " . SQL_PREFIX . "comments c
                                      ON
                                        c.parent_id=b.id
                                      " . $sWhere . "
                                      GROUP BY
                                        b.id
                                      ORDER BY
                                        b.date DESC"
                                      . $sLimit);

      $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0001 - ' . $p->getMessage());
      exit('SQL error.');
    }

    foreach ($aResult as $aRow) {
      # We use the date as identifier to give plugins the possibility to patch into the system.
      $iDate = $aRow['date'];

      # We need to specify 'blogs' because this might also be called for rss
      $this->_aData[$iDate] = $this->_formatForOutput($aRow,
              array('id', 'uid', 'author_id', 'comment_sum'),
              array('published', 'use_gravatar'),
              'blogs');

      # @todo trim spaces, redundant, if those get removed at blog creation/editing
      $this->_aData[$iDate]['tags_raw'] = $aRow['tags'];
      # Explode using ',' and filter empty items (since explode always gives at least one item)
      $this->_aData[$iDate]['tags'] = array_filter(array_map('trim', explode(',', $aRow['tags'])));
      $this->_formatDates($this->_aData[$iDate], 'date_modified');
    }

    return $this->_aData;
 }


  /**
   * Get blog entry data.
   *
   * @access public
   * @param integer $iId ID to load data from
   * @param boolean $bUpdate prepare data for update
   * @return array data from _setData
   *
   */
  public function getId($iId, $bUpdate = false) {
    # Show unpublished items to moderators or administrators only
    $iPublished = $this->_aSession['user']['role'] >= 3 ? 0 : 1;

    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        b.*,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email,
                                        u.use_gravatar,
                                        COUNT(c.id) AS comment_sum
                                      FROM
                                        " . SQL_PREFIX . "blogs b
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        b.author_id=u.id
                                      LEFT JOIN
                                        " . SQL_PREFIX . "comments c
                                      ON
                                        c.parent_id=b.id
                                      WHERE
                                        b.id = :id
                                      AND
                                        b.published >= :published
                                      LIMIT 1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);
      $oQuery->execute();

      $aRow = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0002 - ' . $p->getMessage());
      exit('SQL error.');
    }

    if ($bUpdate === true)
      $this->_aData = $this->_formatForUpdate($aRow);

    else {
      $this->_aData[1] = $this->_formatForOutput($aRow,
              array('id', 'uid', 'author_id', 'comment_sum'),
              array('published', 'use_gravatar'));
      $this->_aData[1]['tags_raw']      = $aRow['tags'];
      $this->_aData[1]['tags']          = array_filter( array_map('trim', explode(',', $aRow['tags'])) );
      $this->_formatDates($this->_aData[1], 'date_modified');
    }

    return $this->_aData;
  }

  /**
   * Create a blog entry.
   *
   * @access public
   * @return boolean status of query
   *
   */
  public function create() {
    $iPublished = isset($this->_aRequest[$this->_sController]['published']) &&
            $this->_aRequest[$this->_sController]['published'] == true ?
            1 :
            0;

    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "blogs
                                        ( author_id,
                                          title,
                                          tags,
                                          teaser,
                                          keywords,
                                          content,
                                          language,
                                          date,
                                          published)
                                      VALUES
                                        ( :author_id,
                                          :title,
                                          :tags,
                                          :teaser,
                                          :keywords,
                                          :content,
                                          :language,
                                          :date,
                                          :published )");

      $sTags = $this->_aRequest[$this->_sController]['tags'];
      $sTags = Helper::formatInput(implode(',', array_filter(array_map('trim', explode(',', $sTags)))));
      $oQuery->bindParam('tags', $sTags, PDO::PARAM_STR);
      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);

      foreach (array('title', 'teaser', 'content') as $sInput)
        $oQuery->bindParam(
                $sInput,
                Helper::formatInput($this->_aRequest[$this->_sController][$sInput], false),
                PDO::PARAM_STR);

      foreach (array('keywords', 'language') as $sInput)
        $oQuery->bindParam(
                $sInput,
                Helper::formatInput($this->_aRequest[$this->_sController][$sInput]),
                PDO::PARAM_STR);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = Helper::getLastEntry($this->_sController);

      return $bReturn;
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0003 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0004 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Update a blog entry.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
  public function update($iId) {
    $iDateModified = isset($this->_aRequest[$this->_sController]['show_update']) &&
            $this->_aRequest[$this->_sController]['show_update'] == true ?
            time() :
            0;

    $iPublished = isset($this->_aRequest[$this->_sController]['published']) &&
            $this->_aRequest[$this->_sController]['published'] == true ?
            1 :
            0;

    $iUpdateAuthor = isset($this->_aRequest[$this->_sController]['show_update']) &&
            $this->_aRequest[$this->_sController]['show_update'] == true ?
            $this->_aSession['user']['id'] :
            (int) $this->_aRequest[$this->_sController]['author_id'];

    $iDate = isset($this->_aRequest[$this->_sController]['update_date']) &&
            $this->_aRequest[$this->_sController]['update_date'] == true ?
            time() :
            (int) $this->_aRequest[$this->_sController]['date'];

    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "blogs
                                      SET
                                        author_id = :author_id,
                                        title = :title,
                                        tags = :tags,
                                        teaser = :teaser,
                                        keywords = :keywords,
                                        content = :content,
                                        language = :language,
                                        date = :date,
                                        date_modified = :date_modified,
                                        published = :published
                                      WHERE
                                        id = :id");

      $sTags = $this->_aRequest[$this->_sController]['tags'];
      $sTags = Helper::formatInput(implode(',', array_filter( array_map('trim', explode(',', $sTags)))));
      $oQuery->bindParam('tags', $sTags, PDO::PARAM_STR);
      $oQuery->bindParam('author_id', $iUpdateAuthor, PDO::PARAM_INT);
      $oQuery->bindParam('date', $iDate, PDO::PARAM_INT);
      $oQuery->bindParam('date_modified', $iDateModified, PDO::PARAM_INT);
      $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      foreach (array('title', 'teaser', 'content') as $sInput)
        $oQuery->bindParam(
                $sInput,
                Helper::formatInput($this->_aRequest[$this->_sController][$sInput], false),
                PDO::PARAM_STR);

      foreach (array('keywords', 'language') as $sInput)
        $oQuery->bindParam(
                $sInput,
                Helper::formatInput($this->_aRequest[$this->_sController][$sInput]),
                PDO::PARAM_STR);

      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0005 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0006 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Delete a blog entry and also delete its comments.
   *
   * @access public
   * @param integer $iId ID to delete
   * @return boolean status of query
   *
   */
  public function destroy($iId) {
    $bResult = parent::destroy($iId);

    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "comments
                                      WHERE
                                        parent_id = :parent_id");

      $oQuery->bindParam('parent_id', $iId, PDO::PARAM_INT);
      $bResult = $bResult && $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0009 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0010 - ' . $p->getMessage());
      exit('SQL error.');
    }

    return $bResult;
  }
}
