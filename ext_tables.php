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

// Determine current TYPO3 version and set class names
if (version_compare(TYPO3_branch, '6.2', '<')) {
    $extensionMamagementUtility = 't3lib_extMgm';
    $generalUtility = 't3lib_div';
}
else {
    $extensionMamagementUtility = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';
    $generalUtility = '\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility';
}

$extensionMamagementUtility::addPlugin(
    array(
        'LLL:EXT:mw_keywordlist/locallang_db.xml:tt_content.menu_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'CType'
);

// Add checkbox to menu palette.
$TCA['tt_content']['palettes']['menu']['showitem'] .= ',--linebreak--,filelink_size;LLL:EXT:mw_keywordlist/locallang_db.xml:label_filelink_size';

// Add TypoScript configuration files
$extensionMamagementUtility::addStaticFile(
    $_EXTKEY,
    'static/',
    'A-Z Keywordlist'
);
