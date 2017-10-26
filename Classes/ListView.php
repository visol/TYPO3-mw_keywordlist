<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2002-2016 mehrwert (typo3@mehrwert.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Plugin A-Z keyword list with pages linked for the 'mw_keywordlist'
 * extension. Provides facilities to retrieve keywords from the pages and
 * generate a link list.
 *
 * @package TYPO3
 * @subpackage tx_mwkeywordlist
 * @author mehrwert <typo3@mehrwert.de>
 * @license GPL
 * @see pi1/class.tx_mwkeywordlist_pi1.php
 */
class tx_mwkeywordlist_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{

    /**
     * Same as class name
     * @var    String
     */
    public $prefixId = 'tx_mwkeywordlist_pi1';

    /**
     * Path to this script relative to the extension dir.
     * @var    String
     */
    public $scriptRelPath = 'Classes/KeyWordList.php';

    /**
     * The extension key.
     * @var    String
     */
    public $extKey = 'mw_keywordlist';

    /**
     * Wether or not to check the cHash.
     * @var    Boolean
     */
    public $pi_checkCHash = true;

    /**
     * Global configuration.
     * @var    array
     */
    public $conf;

    /**
     * Pages read from the pagetree.
     * @var    array
     */
    public $pages = array();

    /**
     * Pages in other languages - also read from the pagetree
     * @var    array
     */
    public $pagesLanguageOverlay = array();

    /**
     * Available chars of active items
     * @var    array
     */
    public $existingKeys = array();

    /**
     * Available chars for jump menu
     * @var    array
     */
    public $jumpMenuIndexKeys = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z'
    );

    /**
     * Number of iterations
     * @var    Integer
     */
    public $numIterations = 0;

    /**
     * The pagelist - used in getRecursivePagelist()
     * @var    Mixed
     */
    public $pageList;

    /**
     * If set, workspaces are supported
     * @var    Boolean
     */
    public $enableWorkspaces = false;

    /**
     * Additional option for the where clause
     * @var    String
     */
    public $contentDoktypesWhereClause = '';

    /**
     * Additional option for the where clause
     * @var    String
     */
    public $contentDoktypes = '';

    /**
     * Flag if cipher index (0-9) should be displayed at the end.
     * @var    Boolean
     */
    public $showCipherIndexAtTheEnd = false;

    /********************************************
     *
     * Main control and dispatcher functions
     *
     ********************************************/

    /**
     * This function recursively selects pages underneath a certain page uid and
     * returns the selected values as an array.
     *
     * Note that this behaviour might cause performance issues under certain
     * circumstances, such as
     * - too frequent use of this function
     * - slow hardware (database server)
     * - large page tree
     *
     * @param mixed $uid The uid or a comma separated list of uids of the page
     * @param integer $maxlevels maximum number of levels to search; defaults to 3
     * @param integer $level starting level to search; defaults to 0
     * @param string $enableFields TYPO3 enable fields (the additional part of the
     * @param integer $sysLanguageUid The current sys_language_uid
     * @return array   List of all pages
     * @todo           Add support for TYPO3 workspaces
     */
    private function getRecursivePagelist($uid, $maxlevels = 3, $level = 0, $enableFields = '', $sysLanguageUid = 0)
    {

        // Returns an array with pagerows for subpages with pid=$uid
        // (which is pid here!). This is used for menus
        if ($this->pageList == null) {
            $this->pageList = array();
        }

        // If option is set, exclude pages that have the
        // "do not show in menu" option activated
        if ($this->conf['excludeNotInMenu'] == 1) {
            $enableFields .= ' AND nav_hide = 0';
        }

        // Retrieve the pagetree and JOIN relevant translations from table
        // pages_language_overlay (plo) only if necessary - improves performance
        if ($sysLanguageUid != 0) {
            $selectFields = '	pages.uid AS page_uid,
								pages.pid AS page_pid,
								plo.uid AS uid,
								plo.pid AS pid,
								plo.title AS title,
								plo.nav_title AS nav_title,
								plo.subtitle AS subtitle,
								plo.keywords AS keywords';

            $fromTable = 'pages AS pages' .
                ' LEFT JOIN pages_language_overlay AS plo ON pages.uid=plo.pid' .
                ' AND plo.sys_language_uid = ' . $sysLanguageUid;
        } else {
            $selectFields = '	uid,
								pid,
								title,
								nav_title,
								subtitle,
								keywords';

            $fromTable = 'pages';
        }

        // Build the database query: If the user has not set a starting point start
        // at top level (website root, pid = 0). Query for the pid of the pages table
        if ($uid == 0) {
            $whereClause = 'pages.pid = ' . $uid . $enableFields;
        } else {
            $whereClause = ($level == 0) ? 'pages.uid IN (' . $uid . ')' .
                $enableFields : 'pages.pid = ' . $uid . $enableFields;
        }

        // Do not add doktypes to the query if querying the website root, pid = 0
        // otherwise query will probably fail to retrieve any results
        $whereClause .= ($uid == 0 ? '' : $this->contentDoktypesWhereClause);
        $groupBy = '';
        $orderBy = 'sorting';
        $limit = '';

        // If SQL query succeeds, proceed
        if ($result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            $selectFields,
            $fromTable,
            $whereClause,
            $groupBy,
            $orderBy,
            $limit)
        ) {

            // If the query returns at least on row, proceed
            if ($GLOBALS['TYPO3_DB']->sql_num_rows($result) > 0) {

                // For each results row process results
                while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {

                    // Store the original values for
                    $originalRow = $row;

                    // If workscape support is activated
                    if ($this->enableWorkspaces == true) {

                        // Set table name depending on the sysLanguageUid
                        $versionOlqueryTable = $sysLanguageUid != 0 ? 'pages' : 'pages_language_overlay';

                        // Check for version overlay
                        $GLOBALS['TSFE']->sys_page->versionOL($versionOlqueryTable, $row);
                    }

                    // Proceed, if the result is an array
                    if (is_array($row)) {

                        // If the sys_language_uid is not the default language (0)
                        // set the UID of the row to the overlay's PID (which is the
                        // UID of the page in the default sys language
                        if ($sysLanguageUid != 0) {
                            $row['uid'] = $row['page_uid'];
                        }

                        $this->pageList[$row['uid']] = array(
                            'i' => $this->numIterations++,
                            'uid' => $row['uid'],
                            'pid' => $row['pid'],
                            'title' => $row['title'],
                            'nav_title' => $row['nav_title'],
                            'subtitle' => $row['subtitle'],
                            'keywords' => $row['keywords']
                        );

                        if ($level < $maxlevels) {
                            // $row['uid'] = $originalRow['page_uid'];
                            $this->getRecursivePagelist($row['uid'], $maxlevels, ((int) $level + 1), $enableFields,
                                $sysLanguageUid);
                        }
                    } else {
                        // If the result of the version overlay check was negative
                        // e.g. no result row from sys_page->versionOL(), restore
                        // the original row values
                        $row = array();

                        // If the sys_language_uid is not the default language (0)
                        // set the UID of the row to the overlay's PID (which is the
                        // UID of the page in the default sys language
                        if ($sysLanguageUid != 0) {
                            $row['uid'] = $originalRow['page_uid'];
                        } else {
                            $row['uid'] = $originalRow['uid'];
                        }

                        if ($level < $maxlevels) {
                            $this->getRecursivePagelist($row['uid'], $maxlevels, ((int) $level + 1), $enableFields,
                                $sysLanguageUid);
                        }
                    }
                }
            }
        }
        return $this->pageList;
    }

    /**
     * The main function. Collect keyword data and prepare HTML output.
     *
     * @param   string $content Page content
     * @param   array $conf configuration options
     * @return  string   HTML Keyword list
     */
    public function main($content, $conf)
    {

        // get settings for levels, defaults to 4 if not set
        $levels = (isset($conf['levels'])) ? intval($conf['levels']) : 4;

        // Show debugging information?
        $displayParseTimeInfo = ($conf['displayParseTimeInfo'] == '1' ? true : false);

        // start performance test
        if ($displayParseTimeInfo) {
            $timeStart = $this->microtimeFloat();
        }

        // Set config
        $this->conf = $conf;

        // Compatiblity with older releases of TYPO3 ( > 4.0.0)
        // that have no support for workspaces
        // Currently disabled!
        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) > 4000000) {
            $this->enableWorkspaces = false;
        } else {
            $this->enableWorkspaces = false;
        }

        // If activated in backed, set showCipherIndexAtTheEnd flag to true.
        // Field "filelink_size" is used here.
        if (intval($this->cObj->data['filelink_size']) == 1) {
            $this->showCipherIndexAtTheEnd = true;
        }

        $this->setContentPageTypes();
        $this->setContentPageTypesWhereClause();

        // Get the PID from which to make the menu.
        // If a page is set as reference in the 'Startingpoint' field, use that
        // Otherwise use the pages id-number from TSFE
        $pageUids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->cObj->data['pages']);
        $pageUidList = is_array($pageUids) ? implode(',', $pageUids) : intval($GLOBALS['TSFE']->id);

        // get the page list
        $this->pages = $this->getRecursivePagelist(
            $pageUidList,
            $levels,
            '',
            $this->cObj->enableFields('pages'),
            $GLOBALS['TSFE']->sys_page->sys_language_uid);

        // Only proceed if pages is an array and contains more than one entry
        if (is_array($this->pages) && sizeof($this->pages) > 1) {

            // Create array
            $index = array();

            // Set the pointer to first element
            reset($this->pages);

            // loop through all selected pages
            while (list($uid, $pagesRow) = each($this->pages)) {
                $keywords = preg_split('/[\s]*,[\s]*/', $pagesRow['keywords'], -1, PREG_SPLIT_NO_EMPTY);
                if (sizeof($keywords) > 0) {
                    foreach ($keywords AS $keyword) {
                        // Define field that is used as linktext, see issue #6048
                        if ($this->conf['linktext'] != '') {
                            $linktext = $this->cObj->getData($this->conf['linktext'], $pagesRow);
                        } else {
                            $linktext = $pagesRow['title'];
                        }
                        $simplifiedKeyword = $this->simplifyString($keyword);
                        $key = strtoupper(substr($simplifiedKeyword, 0, 1));
                        if (in_array($key, $this->jumpMenuIndexKeys)) {
                            $index[$key][$simplifiedKeyword][$linktext] = $uid;
                            $originalKeywords[$simplifiedKeyword] = $keyword;
                        } else {
                            $index['0-9'][$simplifiedKeyword][$linktext] = $uid;
                            $originalKeywords[$simplifiedKeyword] = $keyword;
                        }
                    }
                }
            }

            // Sort the index
            ksort($index);

            // Sort array, first level
            array_walk($index, array($this, 'mwArraySort'));

            // Set 0-9 index to last position if enabled in backend
            if ($this->showCipherIndexAtTheEnd === true) {
                $index = $this->moveCipherIndexToLastPosition($index);
            }

            // last alphabetic character
            $lastchar = '';

            // content of keyword section
            $keywordSection = '';

            // pages related to keyword
            $keywordRelationList = '';

            // Loop through the keys
            foreach ((array)$index AS $key => $items) {

                // header for the current section
                $sectionHeader = '';

                // get first character and capitalize it
                $firstchar = $key;

                // If a new char appears, create new section
                if ($firstchar != $lastchar) {

                    // add anchor
                    $sectionHeader .= chr(10) . chr(9) .
                        '<a name="' . ($firstchar == '0-9' ? 'general' : strtolower($firstchar)) .
                        '" id="' . ($firstchar == '0-9' ? 'general' : strtolower($firstchar)) . '"></a>' . chr(10);

                    // add sectionHeader (capital letter)
                    $sectionHeader .= chr(9) . $this->cObj->wrap($firstchar, $conf['sectionHeaderWrap']);

                    // add character to existing char list
                    $this->existingKeys[] = $firstchar;

                    // set vars for next loop
                    $lastchar = $firstchar;
                }

                $keywordRelationList = '';
                $prevKeyword = '';

                foreach ($items AS $keyword => $properties) {
                    ksort($properties);
                    foreach ($properties AS $property => $value) {
                        if ($keyword != $prevKeyword) {
                            $keywordRelationList .= chr(10) . chr(9) . chr(9) . chr(9) . $this->cObj->wrap(
                                    htmlspecialchars($originalKeywords[$keyword]),
                                    $conf['keywordWrap']) . chr(10);
                        }
                        $keywordRelationList .= chr(10) . chr(9) . chr(9) . chr(9) . $this->cObj->wrap(
                                $this->pi_linkToPage($conf['bullet'] . $property, $value),
                                $conf['keywordRelationListItemWrap']);
                        $prevKeyword = $keyword;
                    }

                }

                $keywordRelationList = chr(9) . chr(9) . $this->cObj->wrap(
                        $keywordRelationList,
                        $conf['keywordRelationListWrap']) . chr(10) . chr(9);
                $keywordSection = chr(10) . chr(9) . $this->cObj->wrap(
                        $keywordRelationList,
                        $conf['keywordSectionWrap']);
                $content .= $sectionHeader . $keywordSection;

                if ($conf['showSectionTopLinks']) {
                    $content .= $this->cObj->cObjGetSingle(
                        $conf['sectionTopLink'],
                        $conf['sectionTopLink.']);
                }
            }

            // finish performance tests
            if ($displayParseTimeInfo) {
                $timeEnd = $this->microtimeFloat();
                $time = $timeEnd - $timeStart;
                $content = '<p style="color: #f00;">Parsetime: ' . $time . ' seconds.</p>' . $content;
            }
        }

        // Concat the jumpmenu and the content (the keywordlist)
        $content = '<a name="tx-mwkeywordlist-top"></a>' . $this->renderJumpMenu() . $this->cObj->wrap($content, $conf['contentWrap']);

        // wrap final output and return
        return $this->pi_wrapInBaseClass($content);
    }

    /********************************************
     *
     * Various helper functions
     *
     ********************************************/

    /**
     * Callback function for sorting
     *
     * @param  string &$value of the array
     * @param  array $key of the array
     * @return void
     */
    public function mwArraySort(&$value, $key)
    {
        ksort($value);
    }

    /**
     * Convert umlauts in a string for proper sorting in the list
     * utilizing TYPO3 csConvObj in TYPO3 version 3.7.0 or higher
     *
     * @param   string $str HTML content for the login form
     * @return  string  Converted string (i.e. � => oe)
     */
    private function simplifyString($str)
    {

        // Charset detection
        if (isset($GLOBALS['TSFE']->renderCharset) && $GLOBALS['TSFE']->renderCharset != '') {
            $charset = $GLOBALS['TSFE']->renderCharset;
        } else {
            $charset = 'iso-8859-1';
        }

        // Remove leading and trailing whitespace
        $str = trim($str);

        // Convert special chars using csConvObj
        $str = $GLOBALS['TSFE']->csConvObj->conv_case($charset, $str, 'toLower');
        $str = $GLOBALS['TSFE']->csConvObj->specCharsToASCII($charset, $str);

        return $str;
    }


    /**
     * Render the A-Z jump menu respecting the available keys
     * while building the alphabet navigation. Only valid keys
     * are linked.
     *
     * @return string  HTML code of the jump menu
     */
    private function renderJumpMenu()
    {

        // Jump menu
        $jumpMenu = array();

        // Special treatment for special chars and data
        if (in_array('0-9', $this->existingKeys)) {
            $jumpMenu['0-9'] = '<a' . $this->pi_classParam('activeLink') . ' href="' .
                $this->pi_getPageLink($GLOBALS['TSFE']->id, '',
                    '') . '#general' . $this->elUid . '" rel="general">0-9</a>';
        } else {
            $jumpMenu['0-9'] = '<span' . $this->pi_classParam('inactiveLink') . '>0-9</span>';
        }

        // Loop through all chars and fill the jump menu array
        foreach ($this->jumpMenuIndexKeys AS $jumpMenuIndexKey) {
            if (in_array($jumpMenuIndexKey, $this->existingKeys)) {
                $jumpMenu[] = '<a' . $this->pi_classParam('activeLink') . ' href="' .
                    $this->pi_getPageLink($GLOBALS['TSFE']->id, '', '') . '#' .
                    $this->elUid . strtolower($jumpMenuIndexKey) . '" rel="' . strtolower($jumpMenuIndexKey) . '">' .
                    $jumpMenuIndexKey . '</a>';
            } else {
                $jumpMenu[] = '<span' . $this->pi_classParam('inactiveLink') . '>' . $jumpMenuIndexKey . '</span>';
            }
        }

        // Set 0-9 index to last position if enabled in backend
        if ($this->showCipherIndexAtTheEnd === true) {
            $jumpMenu = $this->moveCipherIndexToLastPosition($jumpMenu);
        }

        $jumpMenu = implode($this->conf['jumpMenuSeperator'], $jumpMenu);
        $jumpMenu = '<div' . $this->pi_classParam('jumpmenu') . '>' . $jumpMenu . '</div>';

        return $jumpMenu;
    }


    /**
     * Create timestamps to debug parse- and querytime of the extension
     *
     * @return  integer  seconds
     */
    private function microtimeFloat()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }


    /**
     * Set allowed doktypes based on the user settings. Default value
     * is $GLOBALS['TYPO3_CONF_VARS']['FE']['content_doktypes']
     * if the user has set respectContentDoktypes = 1
     *
     * @return void
     */
    private function setContentPageTypes()
    {
        if (intval($this->conf['respectContentDoktypes'])) {
            if (!empty($this->conf['setCustomContentDoktypes'])) {
                $this->contentDoktypes = implode(',',
                    \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->conf['setCustomContentDoktypes']));
            } else {
                $this->contentDoktypes = $GLOBALS['TYPO3_CONF_VARS']['FE']['content_doktypes'];
            }
        } else {
            $this->contentDoktypes = '';
        }
    }


    /**
     * Sets the SQL-Statement used later in the WHERE-clause
     *
     * @return void
     */
    private function setContentPageTypesWhereClause()
    {
        if (!empty($this->contentDoktypes)) {
            $this->contentDoktypesWhereClause = ' AND pages.doktype IN (' . $this->contentDoktypes . ')';
        }
    }


    /**
     * Look for an array entry with key "0-9" and move it to the last
     * position of the array. Return the modified array.
     *
     * @param array $dataArray Array with data of the jump menu or the index entries.
     * @return array The modified data array.
     */
    private function moveCipherIndexToLastPosition($dataArray)
    {
        if (array_key_exists('0-9', $dataArray)) {
            $ciphers = $dataArray['0-9'];
            unset($dataArray['0-9']);
            $dataArray['0-9'] = $ciphers;
        }

        return $dataArray;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mw_keywordlist/Classes/ListView.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mw_keywordlist/Classes/ListView.php']);
}
