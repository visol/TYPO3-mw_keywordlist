<?php

/**
 * TYPO3 Extension configuration for the mw_keywordlist Extension
 *
 * @package		TYPO3
 * @subpackage	tx_mwkeywordlist
 * @version		$Id$
 * @author		mehrwert <typo3@mehrwert.de>
 * @license		GPL
 */

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPlugin(array('LLL:EXT:mw_keywordlist/locallang_db.xml:tt_content.menu_type_pi1', $_EXTKEY.'_pi1'), 'menu_type');

	// Add checkbox to menu palette.
$TCA['tt_content']['palettes']['menu']['showitem'] .= ',--linebreak--,filelink_size;LLL:EXT:mw_keywordlist/locallang_db.xml:label_filelink_size';

	// Add TypoScript configuration files
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'A-Z Keywordlist');

?>