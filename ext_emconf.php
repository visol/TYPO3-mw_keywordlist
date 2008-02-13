<?php

########################################################################
# Extension Manager/Repository config file for ext: "mw_keywordlist"
#
# Auto generated 13-02-2008 23:01
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'mehrwert A-Z Keyword List',
	'description' => 'Extracts all keywords from the page\'s keyword field and displays a list of keywords and links the page title to the keyword related page.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '3.0.2',
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
	'author_company' => 'mehrwert',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.0.12-5.3.0',
			'typo3' => '3.6.0-4.2.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:12:"ext_icon.gif";s:4:"b92c";s:17:"ext_localconf.php";s:4:"a264";s:14:"ext_tables.php";s:4:"3d6e";s:28:"ext_typoscript_constants.txt";s:4:"a688";s:24:"ext_typoscript_setup.txt";s:4:"45b5";s:16:"locallang_db.xml";s:4:"f097";s:14:"doc/manual.sxw";s:4:"ef3b";s:19:"doc/wizard_form.dat";s:4:"aa97";s:20:"doc/wizard_form.html";s:4:"24a4";s:34:"pi1/class.tx_mwkeywordlist_pi1.php";s:4:"9e3f";}',
);

?>