<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2012 Francois Suter (Cobweb) <typo3@cobweb.ch>
 *                Fabien Udriot <fabien.udriot@ecodev.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 *
 * $Id$
 * ************************************************************* */

/**
 * This class is the actual data consumer for extension fluiddisplay
 * It performs a rendering of the data structure using a Fluid template
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @author		Fabien Udriot <fabien.udriot@ecodev.ch>
 * @package		TYPO3
 * @subpackage	tx_fluiddisplay
 */
class tx_fluiddisplay extends tx_tesseract_feconsumerbase {

	public $tsKey = 'tx_fluiddisplay';
	public $extKey = 'fluiddisplay';
	protected $conf;
	protected $table; // Name of the table where the details about the data display are stored
	protected $uid; // Primary key of the record to fetch for the details
	protected $structure = array(); // Input standardised data structure
	protected $result = ''; // The result of the processing by the Data Consumer
	protected $counter = array();

	/**
	 * This method resets values for a number of properties
	 * This is necessary because services are managed as singletons
	 *
	 * @return	void
	 */
	public function reset() {
		$this->structure = array();
		$this->result = '';
		$this->uid = '';
		$this->table = '';
		$this->conf = array();
	}

	/**
	 * Return the filter data.
	 *
	 * @return	array
	 */
	public function getFilter() {
		return $this->filter;
	}

	// Data Consumer interface methods

	/**
	 * This method returns the type of data structure that the Data Consumer can use
	 *
	 * @return	string	type of used data structures
	 */
	public function getAcceptedDataStructure() {
		return tx_tesseract::RECORDSET_STRUCTURE_TYPE;
	}

	/**
	 * This method indicates whether the Data Consumer can use the type of data structure requested or not
	 *
	 * @param	string		$type: type of data structure
	 * @return	boolean		true if it can use the requested type, false otherwise
	 */
	public function acceptsDataStructure($type) {
		return $type == tx_tesseract::RECORDSET_STRUCTURE_TYPE;
	}

	/**
	 * This method is used to pass a data structure to the Data Consumer
	 *
	 * @param 	array	$structure: standardised data structure
	 * @return	void
	 */
	public function setDataStructure($structure) {
		$this->structure[$structure['name']] = $structure;
	}

	/**
	 * This method is used to get a data structure
	 *
	 * @return 	array	$structure: standardised data structure
	 */
	public function getDataStructure() {
		return $this->structure;
	}

	/**
	 * This method returns the result of the work done by the Data Consumer (FE output or whatever else)
	 *
	 * @return	mixed	the result of the Data Consumer's work
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * This method sets the result. Useful for hooks.
	 *
	 * @param mixed $result Predefined result
	 * @return void
	 */
	public function setResult($result) {
		$this->result = $result;
	}

	/**
	 * This method starts the rendering using the Fluid engine
	 *
	 * @throws tx_tesseract_exception
	 * @return void
	 */
	public function startProcess() {

			// Get the full path to the template file
		try {
			$filePath = tx_tesseract_utilities::getTemplateFilePath($this->consumerData['template']);

				// Instantiate a Fluid stand-alone view and load the template file
				/** @var $view Tx_Fluid_View_StandaloneView */
			$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
			$view->setTemplatePathAndFilename($filePath);
				// Assign the Tesseract Data Structure
			$view->assign('datastructure', $this->structure);
				// Define a hook allowing pre-processing of the view before rendering
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['preProcessView'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['preProcessView'] as $className) {
					$preProcessor = &t3lib_div::getUserObj($className);
					$preProcessor->preProcessView($view, $this);
				}
			}
				// Render the result
			$this->result = $view->render();

		}
		catch (Exception $e) {
			$this->controller->addMessage(
				$this->extKey,
				$e->getMessage() . ' (' . $e->getCode() . ')',
				'Error processing the view',
				t3lib_FlashMessage::ERROR
			);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fluiddisplay/class.tx_fluiddisplay.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fluiddisplay/class.tx_fluiddisplay.php']);
}
?>
