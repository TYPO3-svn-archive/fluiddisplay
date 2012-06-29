<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_fluiddisplay_displays');

	// TCA ctrl for new table
$TCA['tx_fluiddisplay_displays'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:fluiddisplay/Resources/Private/Language/locallang_db.xml:tx_fluiddisplay_displays',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'searchFields' => 'title,description,template',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'Configuration/TCA/tx_fluiddisplay_displays.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'Resources/Public/Icons/tx_fluiddisplay_displays.png',
	),
);

	// Add context sensitive help (csh) for this table
t3lib_extMgm::addLLrefForTCAdescr('tx_fluiddisplay_displays', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_txfluiddisplaydisplays.xml');

	// Add a wizard for adding a fluiddisplay
$addTemplateDisplayWizard = array(
						'type' => 'script',
						'title' => 'LLL:EXT:fluiddisplay/Resources/Private/Language/locallang_db.xml:wizards.add_fluiddisplay',
						'script' => 'wizard_add.php',
						'icon' => 'EXT:fluiddisplay/Resources/Public/Icons/add_fluiddisplay_wizard.png',
						'params' => array(
								'table' => 'tx_fluiddisplay_displays',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'set'
							)
						);
$TCA['tt_content']['columns']['tx_displaycontroller_consumer']['config']['wizards']['add_fluiddisplay'] = $addTemplateDisplayWizard;

	// Register fluiddisplay with the Display Controller as a Data Consumer
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['columns']['tx_displaycontroller_consumer']['config']['allowed'] .= ',tx_fluiddisplay_displays';

?>