<?php

########################################################################
# Extension Manager/Repository config file for ext: "mw_keywordlist"
#
# Auto generated 03-05-2009 19:09
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'A-Z Keyword List',
	'description' => 'Extracts all keywords from the page\'s keyword field and displays a list of keywords and links the page title to the keyword related page.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '3.0.5',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'mehrwert',
	'author_email' => 'typo3@mehrwert.de',
	'author_company' => 'mehrwert intermediale kommunikation GmbH',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.0.12-5.2.99',
			'typo3' => '3.6.0-4.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:12:"ext_icon.gif";s:4:"b92c";s:17:"ext_localconf.php";s:4:"b932";s:15:"ext_php_api.dat";s:4:"6513";s:14:"ext_tables.php";s:4:"e356";s:28:"ext_typoscript_constants.txt";s:4:"c710";s:24:"ext_typoscript_setup.txt";s:4:"fd28";s:16:"locallang_db.php";s:4:"abb1";s:14:"doc/manual.sxw";s:4:"18e5";s:19:"doc/wizard_form.dat";s:4:"aa97";s:20:"doc/wizard_form.html";s:4:"24a4";s:34:"pi1/class.tx_mwkeywordlist_pi1.php";s:4:"ebd3";}',
	'suggests' => array(
	),
);

?>