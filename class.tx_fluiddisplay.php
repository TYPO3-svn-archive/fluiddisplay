<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2007 Francois Suter (Cobweb) <typo3@cobweb.ch>
 *  (c) 2008 Fabien Udriot <fabien.udriot@ecodev.ch>
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

require_once(t3lib_extMgm::extPath('tesseract', 'services/class.tx_tesseract_feconsumerbase.php'));

/**
 * Plugin 'Data Displayer' for the 'fluiddisplay' extension.
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
		$this->datasourceFields = array();
		$this->LLkey = 'default';
		$this->fieldMarkers = array();
	}

	/**
	 * Return the controller data.
	 *
	 * @return	array
	 */
	public function getController() {
		return $this->pObj;
	}

	/**
	 * Return the filter data.
	 *
	 * @return	array
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 *
	 * @var tslib_cObj
	 */
	protected $localCObj;

	/**
	 * This method is used to pass a TypoScript configuration (in array form) to the Data Consumer
	 *
	 * @param	array	$conf: TypoScript configuration for the extension
	 */
	public function setTypoScript($conf) {
		$this->conf = $conf;
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
	 * This method is used to pass a filter to the Data Consumer
	 *
	 * @param 	array	$filter: Data Filter structure
	 * @return	void
	 */
	public function setDataFilter($filter) {
		$this->filter = $filter;
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
	 * @return	void
	 */
	public function setResult($result) {

		$this->result = $result;
	}

	/**
	 * This method starts whatever rendering process the Data Consumer is programmed to do
	 *
	 * @return	void
	 */
	public function startProcess() {

		// Loads the template file
		$filePath = t3lib_div::getFileAbsFileName($this->consumerData['template']);

		if (is_file($filePath)) {

			$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
			$view->setTemplatePathAndFilename($filePath);
			$view->assign('datastructure', $this->structure);
			$this->result = $view->render();

			// Hook that enables to post process the output
			if (preg_match_all('/#{3}HOOK\.(.+)#{3}/isU', $this->result, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					$hookName = $match[1];
					if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['postProcessResult'][$hookName])) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['postProcessResult'][$hookName] as $className) {
							$postProcessor = &t3lib_div::getUserObj($className);
							$this->result = $postProcessor->postProcessResult($this->result, $hookName, $this);
						}
					}
				}
			}
		}
		else {
			throw new tx_tesseract_exception('No template file has been found in Fluid Display', 1295025186);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fluiddisplay/class.tx_fluiddisplay.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fluiddisplay/class.tx_fluiddisplay.php']);
}
?>
