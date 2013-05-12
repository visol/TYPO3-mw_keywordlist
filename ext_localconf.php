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

	// Check TYPO3 version and add new menu type
if (version_compare(TYPO3_version, '6.0.0', '>=') == 1) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
		$_EXTKEY,
		'Classes/ListView.php',
		'_ListView',
		'menu_type',
		1
	);
} else {
	t3lib_extMgm::addPItoST43(
		$_EXTKEY,
		'pi1/class.tx_mwkeywordlist_pi1.php',
		'_pi1',
		'menu_type',
		1
	);
}

?>