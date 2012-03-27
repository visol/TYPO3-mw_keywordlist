<?php

########################################################################
# Extension Manager/Repository config file for ext "mw_keywordlist".
#
# Auto generated 14-05-2010 10:34
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'A-Z Keyword List',
	'description' => 'Extracts all keywords from the page\'s keyword field and displays a list of keywords and links the page title to the keyword related page.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '3.2.1',
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
			'php' => '5.0.0-5.3.99',
			'typo3' => '4.5.0-4.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3jquery' => '2.0.0-0.0.0'
		),
	),
	'suggests' => array(
	),
);

?>