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

	// Add new menu type
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_mwkeywordlist_pi1.php', '_pi1', 'menu_type', 1);

	// Add eID script
$TYPO3_CONF_VARS['FE']['eID_include']['tx_mwkeywordlist_fe_eidtools'] = 
		'EXT:mw_keywordlist/res/classes/class.sc_tx_mwkeywordlist_fe_eidtools.php';

?>