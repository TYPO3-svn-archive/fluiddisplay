<?php

########################################################################
# Extension Manager/Repository config file for ext "fluiddisplay".
#
# Auto generated 21-03-2011 10:03
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Fluid-based Data Consumer - Tesseract project',
	'description' => 'Use Fluid-based templates to display any kind of data returned by a Data Provider. More info on http://www.typo3-tesseract.com.',
	'category' => 'fe',
	'author' => 'Francois Suter (Cobweb) / Fabien Udriot',
	'author_email' => 'typo3@cobweb.ch',
	'shy' => '',
	'dependencies' => 'tesseract',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-0.0.0',
			'tesseract' => '1.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:18:{s:9:"ChangeLog";s:4:"8d21";s:10:"README.txt";s:4:"5078";s:25:"class.tx_fluiddisplay.php";s:4:"b9b9";s:12:"ext_icon.gif";s:4:"a406";s:17:"ext_localconf.php";s:4:"909d";s:14:"ext_tables.php";s:4:"da88";s:14:"ext_tables.sql";s:4:"9c07";s:46:"Configuration/TCA/tx_fluiddisplay_displays.php";s:4:"23d1";s:67:"Resources/Private/Language/locallang_csh_txfluiddisplaydisplays.xml";s:4:"a27f";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"afb1";s:50:"Resources/Public/Icons/add_fluiddisplay_wizard.png";s:4:"9cf0";s:51:"Resources/Public/Icons/tx_fluiddisplay_displays.png";s:4:"cd04";s:38:"Samples/class.tx_fluiddisplay_hook.php";s:4:"9c66";s:21:"Samples/locallang.xml";s:4:"cdf6";s:27:"Samples/sampleTemplate.html";s:4:"ca31";s:35:"Samples/Partials/samplePartial.html";s:4:"3bef";s:14:"doc/manual.pdf";s:4:"c1a8";s:14:"doc/manual.sxw";s:4:"02f6";}',
	'suggests' => array(
	),
);

?>