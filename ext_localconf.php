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

t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_mwkeywordlist_pi1.php","_pi1","menu_type",1);

?>