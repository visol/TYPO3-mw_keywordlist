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

	// Check TYPO3 version and add TypoScript configuration files
if (version_compare(TYPO3_version, '6.0.0') < 0) {
	t3lib_extMgm::addPlugin(
		array(
			'LLL:EXT:mw_keywordlist/locallang_db.xml:tt_content.menu_type_pi1',
			$_EXTKEY . '_pi1'
		),
		'menu_type'
	);
	t3lib_extMgm::addStaticFile(
		$_EXTKEY,
		'static/',
		'A-Z Keywordlist'
	);
} else {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
		array(
			'LLL:EXT:mw_keywordlist/locallang_db.xml:tt_content.menu_type_pi1',
			$_EXTKEY . '_pi1'
		),
		'menu_type'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		$_EXTKEY,
		'static/',
		'A-Z Keywordlist'
	);
}

	// Add checkbox to menu palette.
$TCA['tt_content']['palettes']['menu']['showitem'] .= ',--linebreak--,filelink_size;LLL:EXT:mw_keywordlist/locallang_db.xml:label_filelink_size';

?>