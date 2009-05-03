<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2009 mehrwert (typo3@mehrwert.de)
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_mwkeywordlist_pi1 extends tslib_pibase
 *
 *              SECTION: Main control and dispatcher functions
 *  170:     function getRecursivePagelist($uid, $maxlevels = 3, $level = 0, $enableFields, $sys_language_uid = 0)
 *  295:     function main($content, $conf)
 *  304:     function mw_arraySort(&$value, $key)
 *
 *              SECTION: Various helper functions
 *  463:     function simplifyString($str)
 *  496:     function renderJumpMenu()
 *  529:     function microtimeFloat()
 *  542:     function setContentPageTypes()
 *  560:     function setContentPageTypesWhereClause()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

@require_once (PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin A-Z keyword list with pages linked for the 'mw_keywordlist' extension.
 * Provides facilities to retrieve keywords from the pages and generate a link list
 *
 * @package		TYPO3
 * @subpackage	tx_mwkeywordlist
 * @version		$Id$
 * @author		mehrwert <typo3@mehrwert.de>
 * @license		GPL
 */
class tx_mwkeywordlist_pi1 extends tslib_pibase {

	/**
	 * Same as class name
	 * @var	String
	 */
	var $prefixId = 'tx_mwkeywordlist_pi1';

	/**
	 * Path to this script relative to the extension dir.
	 * @var	String
	 */
	var $scriptRelPath = 'pi1/class.tx_mwkeywordlist_pi1.php';

	/**
	 * The extension key.
	 * @var	String
	 */
	var $extKey = 'mw_keywordlist';

	/**
	 * Wether or not to check the cHash.
	 * @var	Boolean
	 */
	var $pi_checkCHash = TRUE;

	/**
	 * Global configuration.
	 * @var	array
	 */
	var $conf;

	/**
	 * Pages read from the pagetree.
	 * @var	array
	 */
	var $pages = array();

	/**
	 * Pages in other languages - also read from the pagetree
	 * @var	array
	 */
	var $pagesLanguageOverlay = array();

	/**
	 * Available chars of active items
	 * @var	array
	 */
	var $existingKeys = array();

	/**
	 * Available chars for jump menu
	 * @var	array
	 */
	var $jumpMenuIndexKeys = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

	/**
	 * Number of iterations
	 * @var	Integer
	 */
	var $iter_num = 0;

	/**
	 * The pagelist - used in getRecursivePagelist()
	 * @var	Mixed
	 */
	var $pageList;

	/**
	 * If set, workspaces are supported
	 * @var	Boolean
	 */
	var $enableWorkspaces = false;

	/**
	 * Additional option for the where clause
	 * @var	String
	 */
	var $contentDoktypesWhereClause = '';

	/**
	 * Additional option for the where clause
	 * @var	String
	 */
	var $contentDoktypes = '';

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
	 * @param	mixed		$uid The uid or a comma separated list of uids of the page
	 * @param	integer		$maxlevels maximum number of levels to search; defaults to 3
	 * @param	integer		$level starting level to search; defaults to 0
	 * @param	string		$enableFields TYPO3 enable fields (the additional part of the
	 * @param	integer		$sys_language_uid The current sys_language_uid
	 * @return	array		List of all pages
	 * @todo 				Add support for TYPO3 workspaces
	 */
	function getRecursivePagelist($uid, $maxlevels = 3, $level = 0, $enableFields, $sys_language_uid = 0)	{

		// Returns an array with pagerows for subpages with pid=$uid (which is pid here!). This is used for menus
		if ($this->pageList == null) {
			$this->pageList = array();
		}

		// Retrieve the pagetree and JOIN relevant translations from table
		// pages_language_overlay (plo) only if necessary - improves performance
		if ($sys_language_uid != 0) {
			$select_fields	= '	pages.uid AS page_uid,
								pages.pid AS page_pid,
								plo.uid AS uid,
								plo.pid AS pid,
								plo.title AS title,
								plo.nav_title AS nav_title,
								plo.subtitle AS subtitle,
								plo.keywords AS keywords';

			$from_table		= 'pages AS pages
							   LEFT JOIN pages_language_overlay AS plo ON pages.uid=plo.pid AND plo.sys_language_uid = '. $sys_language_uid;
		} else {
			$select_fields	= '	uid,
								pid,
								title,
								nav_title,
								subtitle,
								keywords';

			$from_table		= 'pages';
		}

		// Build the database query: If the user has not set a starting point start
		// at top level (website root, pid = 0). Query for the pid of the pages table
		if ($uid == 0) {
			$where_clause	= 'pages.pid = ' . $uid . $enableFields;
		} else {
			$where_clause	= ($level == 0) ? 'pages.uid IN (' . $uid . ')' . $enableFields : 'pages.pid = ' . $uid . $enableFields;
		}
		// Do not add doktypes to the query if querying the website root, pid = 0
		// otherwise query will probably fail to retrieve any results
		$where_clause  .= ( $uid == 0 ? '' : $this->contentDoktypesWhereClause);
		$groupBy		= '';
		$orderBy		= 'sorting';
		$limit			= '';

		// If SQL query succeeds, proceed
		if ($result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit)) {

			// If the query returns at least on row, proceed
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($result) > 0) {

				// For each results row process results
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {

					// Store the original values for
					$originalRow = $row;

					// If workscape support is activated
					if ($this->enableWorkspaces == true) {

						// Set table name depending on the sys_language_uid
						$versionOLQueryTable = $sys_language_uid != 0 ? 'pages' : 'pages_language_overlay';

						// Check for version overlay
						$GLOBALS['TSFE']->sys_page->versionOL($versionOLQueryTable, $row);
					}

					// Proceed, if the result is an array
					if (is_array($row)) {

						// If the sys_language_uid is not the default language (0)
						// set the UID of the row to the overlay's PID (which is the
						// UID of the page in the default sys language
						if ($sys_language_uid != 0) {
							$row['uid'] = $row['page_uid'];
						}

						$this->pageList[$row['uid']] = array(
							'i' => $this->iter_num++,
							'uid' => $row['uid'],
							'pid' => $row['pid'],
							'title' => $row['title'],
							'nav_title' => $row['nav_title'],
							'subtitle' => $row['subtitle'],
							'keywords' => $row['keywords']
						);

						if ($level < $maxlevels) {
							//$row['uid'] = $originalRow['page_uid'];
							$this->getRecursivePagelist($row['uid'], $maxlevels, ($level + 1), $enableFields, $sys_language_uid);
						}
					} else {
						// If the result of the version overlay check was negative
						// e.g. no result row from sys_page->versionOL(), restore
						// the original row values

						$row = array();

						// If the sys_language_uid is not the default language (0)
						// set the UID of the row to the overlay's PID (which is the
						// UID of the page in the default sys language
						if ($sys_language_uid != 0) {
							$row['uid'] = $originalRow['page_uid'];
						} else {
							$row['uid'] = $originalRow['uid'];
						}

						if ($level < $maxlevels) {
							$this->getRecursivePagelist($row['uid'], $maxlevels, ($level + 1), $enableFields, $sys_language_uid);
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
	 * @param	string		Page content
	 * @param	array		configuration options
	 * @return	string		HTML Keyword list
	 */
	function main($content, $conf) {

		/**
		 * Callback function for sorting
		 *
		 * @param	string		$value of the array
		 * @param	array		$key of the array
		 * @return	void
		 */
		function mw_arraySort(&$value, $key) {
			@ksort($value);
		}

		// get settings for levels, defaults to 4 if not set
		$levels = (isset($conf['levels'])) ? intval($conf['levels']) : 4;

		// Show debugging information?
		$displayParseTimeInfo = ( $conf['displayParseTimeInfo'] == '1' ? true : false );

		// start performance test
		if ($displayParseTimeInfo) {
			$time_start = $this->microtimeFloat();
		}

		// Set config
		$this->conf = $conf;

		// Compatiblity with older releases of TYPO3 ( > 4.0.0)
		// that have no support for workspaces
		// Currently disabled!
		if (t3lib_div::int_from_ver(TYPO3_version) > 4000000) {
			$this->enableWorkspaces = false;
		} else {
			$this->enableWorkspaces = false;
		}

		$this->setContentPageTypes();
		$this->setContentPageTypesWhereClause();

		// Get the PID from which to make the menu.
		// If a page is set as reference in the 'Startingpoint' field, use that
		// Otherwise use the pages id-number from TSFE
		$pageUids = t3lib_div::intExplode(',', $this->cObj->data['pages']);
		$pageUidList = is_array($pageUids) ? implode(',', $pageUids) : intval($GLOBALS['TSFE']->id);

		// get the page list
		$this->pages = $this->getRecursivePagelist($pageUidList, $levels, '', $this->cObj->enableFields('pages'), $GLOBALS['TSFE']->sys_page->sys_language_uid);

		// Only proceed if pages is an array and contains more than one entry
		if (is_array($this->pages) && sizeof($this->pages) > 1) {

			// Create array
			$index = array();

			// Set the pointer to first element
			reset($this->pages);

			// loop through all selected pages
			while (list($uid, $pages_row) = each ($this->pages)) {
				$keywords = preg_split('/[\s]*,[\s]*/', $pages_row['keywords'], -1, PREG_SPLIT_NO_EMPTY);
				if (sizeof($keywords) > 0) {
					foreach ($keywords AS $keyword) {
						$simplifiedKeyword = $this->simplifyString($keyword);
						$key = strtoupper(substr($simplifiedKeyword, 0, 1));
						if (in_array($key, $this->jumpMenuIndexKeys)) {
							$index[$key][$simplifiedKeyword][$pages_row['title']] = $uid;
							$originalKeywords[$simplifiedKeyword] = $keyword;
						} else {
							$index['0-9'][$simplifiedKeyword][$pages_row['title']] = $uid;
							$originalKeywords[$simplifiedKeyword] = $keyword;
						}
					}
				}
			}

			// Sort the index
			@ksort($index);

			// Sort array, first level
			array_walk($index, mw_arraySort);

			// last alphabetic character
			$lastchar = '';

			// content of keyword section
			$keywordSection = '';

			// pages related to keyword
			$keywordRelationList = '';

			// Loop through the keys
			foreach ( (array) $index AS $key => $items) {

				// header for the current section
				$sectionHeader = '';

				// get first character and capitalize it
				$firstchar = $key;

				// If a new char appears, create new section
				if ($firstchar != $lastchar) {

					// add anchor
					$sectionHeader .= "\n\t" . '<a name="'. ($firstchar == '0-9' ? 'general' : strtolower($firstchar)) .'" id="' . ($firstchar == '0-9' ? 'general' : strtolower($firstchar)) .'"></a>' . "\n";

					// add sectionHeader (capital letter)
					$sectionHeader .= "\t" . $this->cObj->wrap($firstchar, $conf['sectionHeaderWrap']);

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
							$keywordRelationList .= "\n\t\t" . $this->cObj->wrap(htmlspecialchars($originalKeywords[$keyword]), $conf['keywordWrap']) . "\n";
						}
						$keywordRelationList .= "\n\t\t\t" . $this->cObj->wrap($this->pi_linkToPage($conf['bullet'] . $property, $value), $conf['keywordRelationListItemWrap']);
						$prevKeyword = $keyword;
					}

				}

				$keywordRelationList = "\t\t" . $this->cObj->wrap($keywordRelationList, $conf['keywordRelationListWrap']) . "\n\t";
				$keywordSection =  "\n\t" . $this->cObj->wrap($keywordRelationList, $conf['keywordSectionWrap']);
				$content .= $sectionHeader . $keywordSection;

				if ($conf['showSectionTopLinks']) {
					$content .= $this->cObj->cObjGetSingle($conf['sectionTopLink'], $conf['sectionTopLink.']);
				}
			}

			// finish performance tests
			if ($displayParseTimeInfo) {
				$time_end = $this->microtimeFloat();
				$time = $time_end - $time_start;
				$content = '<p style="color: #f00;">Parsetime: ' . $time . ' seconds.</p>' . $content;
			}
		}

		// Concat the jumpmenu and the content (the keywordlist)
		$content = $this->renderJumpMenu() . $this->cObj->wrap($content, $conf['contentWrap']);

		// wrap final output and return
		return $this->pi_wrapInBaseClass($content);
	}


	/********************************************
	 *
	 * Various helper functions
	 *
	 ********************************************/

	/**
	 * Convert umlauts in a string for proper sorting in the list
	 * utilizing TYPO3 csConvObj in TYPO3 version 3.7.0 or higher
	 *
	 * @param	string		HTML content for the login form
	 * @return	string		Converted string (i.e. ö => oe)
	 */
	function simplifyString($str) {

		// Charset detection
		if (isset($GLOBALS['TSFE']->renderCharset) && $GLOBALS['TSFE']->renderCharset != '') {
			$charset = $GLOBALS['TSFE']->renderCharset;
		} else {
			$charset = 'iso-8859-1';
		}

		// Remove leading and trailing whitespace
		$str = trim($str);

		// Compatiblity with older releases of TYPO3 ( < 3.7.0)
		if (t3lib_div::int_from_ver(TYPO3_version) < 3007000) {
			// Convert special chars using old method
			$str = t3lib_div::convUmlauts($str);
			$str = strtolower($str);
		} else {
			// Convert special chars using csConvObj
			$str = $GLOBALS['TSFE']->csConvObj->conv_case($charset, $str, 'toLower');
			$str = $GLOBALS['TSFE']->csConvObj->specCharsToASCII($charset, $str);
		}
		return $str;
	}


	/**
	 * Render the A-Z jump menu respecting the available keys
	 * while building the alphabet navigation. Only valid keys
	 * are linked.
	 *
	 * @return	string		HTML code of the jump menu
	 */
	function renderJumpMenu() {

		// Jump menu
		$jumpMenu = array();

		// Special treatment for special chars and data
		if (in_array('0-9', $this->existingKeys)) {
			$jumpMenu[] = '<a' . $this->pi_classParam('activeLink') . ' href="' . $this->pi_getPageLink($GLOBALS['TSFE']->id, '', '') . '#general' . $this->elUid . '">0-9</a>';
		} else {
			$jumpMenu[] = '<span' . $this->pi_classParam('inactiveLink') . '>0-9</span>';
		}

		// Loop through all chars and fill the jump menu array
		foreach ($this->jumpMenuIndexKeys AS $jumpMenuIndexKey) {
			if (in_array($jumpMenuIndexKey, $this->existingKeys)) {
				$jumpMenu[] = '<a' . $this->pi_classParam('activeLink') . ' href="' . $this->pi_getPageLink($GLOBALS['TSFE']->id, '', '') . '#' . $this->elUid . strtolower($jumpMenuIndexKey) . '">' . $jumpMenuIndexKey . '</a>';
			} else {
				$jumpMenu[] = '<span' . $this->pi_classParam('inactiveLink') . '>' . $jumpMenuIndexKey . '</span>';
			}
		}

		$jumpMenu = implode(' ' . $this->conf['jumpMenuSeperator'] . ' ', $jumpMenu);
		$jumpMenu = '<div' . $this->pi_classParam('jumpmenu') . '>' . $jumpMenu . '</div>';

		return $jumpMenu;
	}


	/**
	 * Create timestamps to debug parse- and querytime of the extension
	 *
	 * @return	integer		seconds
	 */
	function microtimeFloat() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}


	/**
	 * Set allowed doktypes based on the user settings. Default value
	 * is $GLOBALS['TYPO3_CONF_VARS']['FE']['content_doktypes']
	 * if the user has set respectContentDoktypes = 1
	 *
	 * @return	void
	 */
	function setContentPageTypes() {
		if (intval($this->conf['respectContentDoktypes'])) {
			if (!empty($this->conf['setCustomContentDoktypes'])) {
				$this->contentDoktypes = implode(',', t3lib_div::intExplode(',', $this->conf['setCustomContentDoktypes']));
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
	 * @return	void
	 */
	function setContentPageTypesWhereClause() {
		if (!empty($this->contentDoktypes)) {
			$this->contentDoktypesWhereClause = ' AND pages.doktype IN ('. $this->contentDoktypes .')';
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mw_keywordlist/pi1/class.tx_mwkeywordlist_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mw_keywordlist/pi1/class.tx_mwkeywordlist_pi1.php']);
}

?>