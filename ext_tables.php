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

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    array(
        'LLL:EXT:mw_keywordlist/locallang_db.xml:tt_content.menu_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'CType'
);

// Add checkbox to menu palette.
$GLOBALS['TCA']['tt_content']['palettes']['menu']['showitem'] .= ',--linebreak--,
	filelink_size;LLL:EXT:mw_keywordlist/locallang_db.xml:label_filelink_size';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'static/',
    'A-Z Keywordlist'
);
