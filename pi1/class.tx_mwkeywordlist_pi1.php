<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2004-2005 mehrwert (typo3@mehrwert.de)
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
 *   53: class tx_mwkeywordlist_pi1 extends tslib_pibase
 *  100:     function getRecursivePagelist($uid, $maxlevels = 3, $level = 0, $enableFields, $sys_language_uid = 0)
 *  151:     function main($content, $conf)
 *  348:     function userFriendlySort($b, $a)
 *  368:     function simplifyString($str)
 *  391:     function getJumpMenu()
 *  426:     function microtimeFloat()
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

@require_once PATH_tslib.'class.tslib_pibase.php';

/**
 * Plugin 'A-Z keyword list with pages linked for the 'mw_keywordlist' extension.
 *
 * @version		$Rev$
 * @id			$Id$
 * @author		mehrwert <typo3@mehrwert.de>
 * @license		GPL
 */
class tx_mwkeywordlist_pi1 extends tslib_pibase {

	// Same as class name
	var $prefixId = 'tx_mwkeywordlist_pi1';

	// Path to this script relative to the extension dir.
	var $scriptRelPath = 'pi1/class.tx_mwkeywordlist_pi1.php';

	// The extension key.
	var $extKey = 'mw_keywordlist';

	// Global configuration
	var $conf;

	// Pages read from the pagetree
	var $pages = Array();

	// Pages in other languages - also read from the pagetree
	var $pagesLanguageOverlay = Array();

	// Available chars of active items
	var $alphabetNav = Array();

	// Number of iterations
	var $iter_num = 0;

	// Th pagelist - used in getRecursivePagelist()
	var $pageList;

	/**
	 * This function recursively selects pages underneath a certain page uid and
	 * returns the selected values as an array.
	 *
	 * Note that this behaviour might cause performance issues under certain circumstances,
	 * such as
	 * - too frequent use of this function
	 * - slow hardware (database server)
	 * - large page tree
	 *
	 * @param	integer		uid of the page to search underneath. This is not included in the result!
	 * @param	integer		maximum number of levels to search; defaults to 3
	 * @param	integer		starting level to search; defaults to 0
	 * @param	string		the additional part of the WHERE statement object to build queries
	 * @param	integer		the current sys_language_uid
	 * @return	array		list of all pages
	 * @author	mehrwert <typo3@mehrwert.de>
	 */
	function getRecursivePagelist($uid, $maxlevels = 3, $level = 0, $enableFields, $sys_language_uid = 0)	{

		// Returns an array with pagerows for subpages with pid=$uid (which is pid here!). This is used for menus
		if ($this->pageList == null) {
			$this->pageList = Array();
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, pid, title, nav_title, subtitle, keywords', 'pages', '(pid IN (' . $uid . ') OR uid IN (' . $uid . '))' . $enableFields, '', 'sorting');

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {

			// If the current language is not default (0) only dump the page uids
			// to an array for later implosion
			if($sys_language_uid != 0) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$this->pageList[$row['uid']] = $row['uid'];
					if ($level < $maxlevels) {
						$this->getRecursivePagelist($row['uid'], $maxlevels, ($level + 1), $enableFields, $sys_language_uid);
					}
				}
			} else {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
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
						$this->getRecursivePagelist($row['uid'], $maxlevels, ($level + 1), $enableFields, $sys_language_uid);
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
	 * @author	mehrwert <typo3@mehrwert.de>
	 */
	function main($content, $conf) {

		// get settings for levels, defaults to 4 if not set
		$levels = (isset($conf['levels'])) ? intval($conf['levels']) : 4;

		// DEBUG
		$performanceTest = false;

		// start performance test
		if ($performanceTest) {
			$time_start = $this->microtimeFloat();
		}

		// Set config
		$this->conf = $conf;

		// Get the PID from which to make the menu.
		// If a page is set as reference in the 'Startingpoint' field, use that
		// Otherwise use the page's id-number from TSFE
		$pageUids = t3lib_div::intExplode(',', $this->cObj->data['pages']);
		$pageUidList = is_array($pageUids) ? implode(',', $pageUids) : intval($GLOBALS['TSFE']->id);

		// get the page list
		$this->pages = $this->getRecursivePagelist($pageUidList, $levels, '', $this->cObj->enableFields('pages'), $GLOBALS['TSFE']->sys_page->sys_language_uid);

		// We have to merge the translations from the system in that ugly way
		// because TYPO3/DBAL does not support JOINs yet.

		if ($GLOBALS['TSFE']->sys_page->sys_language_uid != 0 && sizeof($this->pages > 0)) {

			$pagesLanguageOverlayPidList = Array();
			foreach($this->pages AS $page) {
				$pagesLanguageOverlayPidList[] = $page;
			}

			// get all related language overlay entries
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid, title, nav_title, subtitle, keywords', 'pages_language_overlay', '(pid IN (' . implode(',', $pagesLanguageOverlayPidList) . ') AND sys_language_uid = '. $GLOBALS['TSFE']->sys_page->sys_language_uid . ')' . $this->cObj->enableFields('pages_language_overlay'));

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$this->pagesLanguageOverlay[$row['pid']] = Array(
						'i' => $this->iter_num++,
						'uid' => $row['pid'],
						'pid' => $row['pid'],
						'title' => $row['title'],
						'nav_title' => $row['nav_title'],
						'subtitle' => $row['subtitle'],
						'keywords' => $row['keywords']
					);
				}
			}
			$this->pages = $this->pagesLanguageOverlay;
		}

		// loop through all selected pages
		reset ($this->pages);

		$allKeys = Array();
		$keywords = Array();

		while (list($uid, $pages_row) = each ($this->pages)) {
			$keywords = preg_split ('/[\s]*,[\s]*/', $pages_row['keywords'], -1, PREG_SPLIT_NO_EMPTY);
			if (!empty($keywords)) {
				array_walk ($keywords, trim);
				foreach ($keywords AS $keyword) {
					if (!empty($keyword)) {
						$allKeys[$keyword][$uid] = 1;
					}
				}
			}
		}

		$sortedKeys = array_keys($allKeys);
		uasort ($sortedKeys, Array($this, 'userFriendlySort'));

		// last alphabetic character
		$lastchar = '';

		// list of keywords
		$keywordList = '';

		// content of keyword section
		$keywordSection = '';

		// pages related to keyword
		$keywordRelationList = '';

		// keyword sections
		$sections = Array();

		// items in a section
		$sectionListItems = '';

		// simple counter
		$i = 0;

		// Loop through the keys
		foreach ($sortedKeys AS $key) {

			// header for the current section
			$sectionHeader = '';

			// get first character and capitalize it
			$firstchar = strtoupper(substr($this->simplifyString($key), 0, 1));

			// start a new section?
			$newSection = false;

			// If a new char appears, create new section
			if ($firstchar != $lastchar) {

				// Start a new section
				$newSection = true;

				// add anchor
				$sectionHeader .= "\n\t" . '<a name="'. strtolower($firstchar) .'" id="'. strtolower($firstchar) .'"></a>' . "\n";

				// add sectionHeader (capital letter)
				$sectionHeader .= "\t" . $this->cObj->wrap($firstchar, $conf['sectionHeaderWrap']);

				// add character to existing char list
				$this->alphabetNav[] = $firstchar;

				// set vars for next loop
				$lastchar = $firstchar;
			}

			$keyword = "\n\t\t" . $this->cObj->wrap(htmlspecialchars($key), $conf['keywordWrap']) . "\n";
			$keywordRelationList = '';
			$keywordRelationListItems = Array();

			// @fixme: calling $this->pi_linkToPage() for every keyword/page
			// will cause performance problems with large keyword lists. Would
			// be better to generate the links with a custom function in this class.
			//
			// Add all entries to an array with the page titel as key for sorting
			foreach ($allKeys[$key] AS $puid => $foo) {
				$keywordRelationListItems[$this->pages[$puid]['title']] = "\n\t\t\t" . $this->cObj->wrap($this->pi_linkToPage($conf['bullet'] . $this->pages[$puid]['title'], $this->pages[$puid]['uid']), $conf['keywordRelationListItemWrap']);
			}

			// sort the item list
			ksort ($keywordRelationListItems);

			// add the sorted items to the link list
			foreach ($keywordRelationListItems AS $keywordRelationListItem) {
				$keywordRelationList .= $keywordRelationListItem;
			}

			// delete the array
			unset ($keywordRelationListItems);

			if ($newSection && $i != 0) {
				$sections[] = $sectionListItems;
				$sectionListItems = '';
			}

			$keywordRelationList = $keywordRelationList . "\n\t\t";
			$keywordRelationList = "\t\t" . $this->cObj->wrap($keywordRelationList, $conf['keywordRelationListWrap']) . "\n\t";
			$keywordSection =  "\n\t" . $this->cObj->wrap($keyword . $keywordRelationList, $conf['keywordSectionWrap']);
			$sectionListItems .= $sectionHeader . $keywordSection;

			$i++;
		}

		// @fixme: because the loop above will not add the last section,
		// we've to add the last section here.
		$sections[] = $sectionListItems;

		foreach ($sections AS $section) {
			$content .= $this->cObj->wrap($section . "\n", $conf['sectionWrap']) . "\n";
			if ($conf['showSectionTopLinks']) {
				$content .= $this->cObj->wrap($conf['sectionTopLink'], $conf['sectionTopLinkWrap']);
			}
		}

		// finish performance tests
		if ($performanceTest) {
			$time_end = $this->microtimeFloat();
			$time = $time_end - $time_start;
			$content = '<p style="color: #f00;">Parsetime: ' . $time . ' seconds.</p>' . $content;
		}

		$content = $this->getJumpMenu() . $this->cObj->wrap($content, $conf['contentWrap']);

		// wrap final output and return
		return $this->pi_wrapInBaseClass($content);
	}


	/**
	 * Sort and simplify strings uses {@link simplifyString()}
	 *
	 * @param	string		$b		Text
	 * @param	string		$a		Text
	 * @return	string
	 * @author		mehrwert <typo3@mehrwert.de>
	 */
	function userFriendlySort($b, $a) {

		$a = $this->simplifyString($a);
		$b = $this->simplifyString($b);

		if ($a == $b) {
			return 0;
		}

		return ($a > $b) ? -1 : 1;
	}


	/**
	 * Convert umlauts in a string for proper sorting in the list
	 *
	 * @param	string		HTML content for the login form
	 * @return	string		Converted string (i.e. ö => oe)
	 * @author		mehrwert <typo3@mehrwert.de>
	 */
	function simplifyString($str) {

		// Compatiblity with older releases of TYPO3 ( < 3.7.0)
		if (t3lib_div::int_from_ver(TYPO3_version) < 3007000) {
			// Convert special chars using old method
			$str = t3lib_div::convUmlauts($str);
			$str = strtolower(trim($str));
		}
		else {
			// Convert special chars using new csConvObj
			$str = $GLOBALS['TSFE']->csConvObj->conv_case('iso-8859-1', $str, 'toLower');
			$str = $GLOBALS['TSFE']->csConvObj->specCharsToASCII('iso-8859-1', $str);
		}
		return $str;
	}


	/**
	 * Create the A-Z jump menu
	 *
	 * @return	string		HTML code of the jump menu
	 * @author		mehrwert <typo3@mehrwert.de>
	 */
	function getJumpMenu() {

		// Chars to display in the jum menu menu
		$abc = Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

		// Jump menu
		$jumpMenu = Array();

		// The items to link
		$actLinks = $this->alphabetNav;

		// unique item id if available
		$elUid = $this->elUid;

		// Loop through all chars and fill the jump menu array
		foreach ($abc AS $v) {
			if (in_array($v, $actLinks)) {
				$jumpMenu[] = '<a' . $this->pi_classParam('activeLink') . ' href="' . $this->pi_getPageLink($GLOBALS['TSFE']->id, '', '') . '#' . $elUid . strtolower($v) . '">' . $v . '</a>';
			} else {
				$jumpMenu[] = '<span' . $this->pi_classParam('inactiveLink') . '>' . $v . '</span>';
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
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mw_keywordlist/pi1/class.tx_mwkeywordlist_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mw_keywordlist/pi1/class.tx_mwkeywordlist_pi1.php']);
}

?>