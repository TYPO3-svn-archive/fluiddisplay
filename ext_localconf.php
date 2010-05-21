<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_fluiddisplay_displays=1
');

// Register method with generic BE ajax calls handler
// (as from TYPO3 4.2)

$TYPO3_CONF_VARS['BE']['AJAX']['fluiddisplay::saveConfiguration'] = 'typo3conf/ext/fluiddisplay/class.tx_temlatedisplay_ajax.php:tx_fluiddisplay_ajax->saveConfiguration';
$TYPO3_CONF_VARS['BE']['AJAX']['fluiddisplay::saveTemplate'] = 'typo3conf/ext/fluiddisplay/class.tx_temlatedisplay_ajax.php:tx_fluiddisplay_ajax->saveTemplate';

// Register as Data Consumer service
// Note that the subtype corresponds to the name of the database table
t3lib_extMgm::addService($_EXTKEY,  'dataconsumer' /* sv type */,  'tx_fluiddisplay_dataconsumer' /* sv key */,
	array(

		'title' => 'Data Display Engine',
		'description' => 'Generic Data Consumer for recordset-type data structures',

		'subtype' => 'tx_fluiddisplay_displays',

		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,

		'os' => '',
		'exec' => '',

		'classFile' => t3lib_extMgm::extPath($_EXTKEY, 'class.tx_fluiddisplay.php'),
		'className' => 'tx_fluiddisplay',
	)
);
?>
