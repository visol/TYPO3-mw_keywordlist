<?php

if (version_compare(TYPO3_version, '6.0.0', '>=') == 1) {
	$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mw_keywordlist');
	return array(
		'mehrwert\MwKeywordlist\ListView' => $extensionPath . 'Classes/ListView.php',
		'Tx_MwKeywordlist_ListView' => $extensionPath . 'Classes/ListView.php',
	);
} else {
	$extensionPath = t3lib_extMgm::extPath('mw_keywordlist');
	return array(
		'tx_mwkeywordlist_pi1' => $extensionPath . 'pi1/class.tx_mwkeywordlist_pi1.php',
	);
}

?>