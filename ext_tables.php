<?php

/**
 * TYPO3 Extension configuration for the mw_keywordlist Extension
 *
 * @version		$Rev$
 * @id			$Id$
 * @author		mehrwert <typo3@mehrwert.de>
 * @license		GPL
 */
 
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPlugin(Array("LLL:EXT:mw_keywordlist/locallang_db.php:tt_content.menu_type_pi1", $_EXTKEY."_pi1"),"menu_type");

?>