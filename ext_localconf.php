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

// Use compatible classes for TYPO3 versions < 6.2
if (version_compare(TYPO3_branch, '6.2', '<')) {
    // Add new menu type
    t3lib_extMgm::addPItoST43(
        $_EXTKEY,
        'pi1/class.tx_mwkeywordlist_pi1.php',
        '_pi1',
        'menu_type',
        1
    );

}
else {
    // Add new menu type
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
        $_EXTKEY,
        'Classes/ListView.php',
        '_pi1',
        'menu_type',
        1
    );

}
