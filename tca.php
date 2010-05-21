<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_fluiddisplay_displays'] = array(
	'ctrl' => $TCA['tx_fluiddisplay_displays']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,description,mappings'
	),
	'feInterface' => $TCA['tx_fluiddisplay_displays']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluiddisplay/locallang_db.xml:tx_fluiddisplay_displays.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluiddisplay/locallang_db.xml:tx_fluiddisplay_displays.description',
			'config' => array(
				'type' => 'text',
				'cols' => '40',
				'rows' => '4',
			)
		),
		'template' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluiddisplay/locallang_db.xml:tx_fluiddisplay_displays.template',
			'config' => array(
				'type' => 'text',
				'cols' => '40',
				'rows' => '4',
			)
		),
		'mappings' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluiddisplay/locallang_db.xml:tx_fluiddisplay_displays.mappings',
			'config' => array(
				#'type' => 'user',
				#'userFunc' => 'tx_fluiddisplay_tceforms->mappingField',
				'type' => 'text',
				'cols' => '40',
				'rows' => '4',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, template, mappings, description')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);

t3lib_extMgm::addToAllTCAtypes("tx_fluiddisplay_displays","--palette--;LLL:EXT:fluiddisplay/locallang_db.xml:tx_fluiddisplay_displays.debug;10","","after:description");
t3lib_extMgm::addToAllTCAtypes("tx_fluiddisplay_displays","--palette--;LLL:EXT:fluiddisplay/locallang_db.xml:tx_fluiddisplay_displays.pagebrowser;20","","after:description");

#$TCA['tx_fluiddisplay_displays']['palettes']['10'] = array(
#	"showitem" => "debug_markers, debug_template_structure, debug_data_structure",
#	"canNotCollapse" => 1
#);

?>