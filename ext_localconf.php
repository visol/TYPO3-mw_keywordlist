<?php

/**
 * TYPO3 Extension configuration for the mw_keywordlist Extension
 *
 * @package TYPO3
 * @subpackage tx_mwkeywordlist
 * @author mehrwert intermediale kommunikation GmbH <typo3@mehrwert.de>
 * @license GPL
 */

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Add new menu type
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
	$_EXTKEY,
	'Classes/ListView.php',
	'_pi1',
	'CType',
	1
);
