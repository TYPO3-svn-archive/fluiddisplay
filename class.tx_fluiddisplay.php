<?php
/***************************************************************
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
***************************************************************/

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
	public function reset(){
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

		// ************************************
		// ********** INITIALISATION **********
		// ************************************

		// Initializes local cObj
		$this->localCObj = t3lib_div::makeInstance('tslib_cObj');
		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

//		$this->setPageTitle($this->conf);

		$uniqueMarkers = array();

		// Formats TypoScript configuration as array.
//		$parseObj = t3lib_div::makeInstance('t3lib_TSparser');
//		foreach ($datasource as $data) {
//			if(trim($data['configuration']) != ''){
//
//				// Clears the setup (to avoid typoscript incrementation)
//				$parseObj->setup = array();
//				$parseObj->parse($data['configuration']);
//				$data['configuration'] = $parseObj->setup;
//			}
//			else{
//				$data['configuration'] = array();
//			}
//
//			// Merges some data to create a new marker. Will look like: table.field
//			$_marker = $data['table'] . '.' . $data['field'];
//
//			// IMPORTANT NOTICE:
//			// The idea is to make the field unique and to be able to know which field of the database is associated
//			// Adds to ###FIELD.xxx### the value "table.field"
//			// Ex: [###FIELD.title###] => ###FIELD.title.pages.title###
//			$uniqueMarkers['###' . $data['marker'] . '###'] = '###' . $data['marker'] . '.' . $_marker . '###';
//
//			// Builds the datasource as an associative array.
//			// $data contains the following information: [marker], [table], [field], [type], [configuration]
//			if (preg_match('/FIELD/', $data['marker'])) {
//				$this->datasourceFields[$data['marker']] = $data;
//			}
//			else {
//				$this->datasourceObjects[$data['marker']] = $data;
//			}
//		}

		// ***************************************
		// ********** BEGINS PROCESSING **********
		// ***************************************

		// LOCAL DOCUMENTATION:
		// $templateCode -> HTML template roughly extracted from the database
		// $templateContent -> HTML that is going to be outputed

		// Loads the template file
		$templateCode = $this->consumerData['template'];

		if (preg_match('/^FILE:/isU', $templateCode)) {
			$filePath = str_replace('FILE:', '' , $templateCode);
			$filePath = t3lib_div::getFileAbsFileName($filePath);
			if (is_file($filePath)) {
				$templateCode = file_get_contents($filePath);
			}
		}
        $templateParser = Tx_Fluid_Compatibility_TemplateParserBuilder::build();
		$objectManager = t3lib_div::makeInstance('Tx_Fluid_Compatibility_ObjectManager');

		if (isset($GLOBALS['_GET']['debug']['structure']) && isset($GLOBALS['TYPO3_MISC']['microtime_BE_USER_start'])) {
			t3lib_div::debug($this->structure);
		}
        $templateContent = $templateCode;
        if ($templateContent !== false) {
			$content = $templateParser->parse($templateContent);

			$variableContainer = $objectManager->create('Tx_Fluid_Core_ViewHelper_TemplateVariableContainer', $this->structure);
			$renderingContext = $objectManager->create('Tx_Fluid_Core_Rendering_RenderingContext');
			$renderingContext->setTemplateVariableContainer($variableContainer);
			$viewHelperVariableContainer = $objectManager->create('Tx_Fluid_Core_ViewHelper_ViewHelperVariableContainer');
			$renderingContext->setViewHelperVariableContainer($viewHelperVariableContainer);
			$data = $content->render($renderingContext);


			// Hook that enables to post process the output)
			if (preg_match_all('/#{3}HOOK\.(.+)#{3}/isU', $this->result, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					$hookName = $match[1];
					if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['postProcessResult'][$hookName])) {
						foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['postProcessResult'][$hookName] as $className) {
							$postProcessor = &t3lib_div::getUserObj($className);
							$this->result = $postProcessor->postProcessResult($this->result, $hookName, $this);
						}
					}
				}
			}
            $this->result = $data;


        }

		
	}

	/**
	 * Processes markers of type ###RECORD('tt_content',1)###
	 *
	 * @param	string	$content: the content
	 * @return	string	$content:
	 */
//	protected function processRECORDS($content) {
//
//		if (preg_match_all("/#{3}RECORD\((.+),(.+)\)#{3}/isU", $content, $matches, PREG_SET_ORDER)) {
//
//			// Stores the filter. Fluiddisplay is a singleton and the filter property will be override by a child call.
//			$GLOBALS['tesseract']['filter']['parent'] = $this->filter;
//
//			foreach ($matches as $match) {
//				$marker = $match[0];
//				$table = trim($match[1]);
//				$uid = trim($match[2]);
//
//				// Avoids recursive call
//				if ($this->pObj->cObj->data['uid'] != $uid) {
//					$conf = array();
//					$conf['source'] = $table.'_'.$uid;
//					$conf['tables'] = $table;
//					$_content = $this->localCObj->RECORDS($conf);
//					$content = str_replace($marker, $_content, $content);
//				}
//			}
//		}
//		return $content;
//	}

	/**
	 * Changes the page title if fluiddisplay encounters typoScript configuration.
	 * Typoscript configuration have the insertData syntax e.g. {table.field}
	 * This is done by changing the page title in the tslib_fe object.
	 *
	 * @param	array	$configuration: Local TypoScript configuration
	 * @return	void
	 */
//	protected function setPageTitle($configuration) {
//		// Checks wheter the title of the template need to be changed
//		if ($configuration['substitutePageTitle']) {
//			$pageTitle = $configuration['substitutePageTitle'];
//
//			// extracts the {table.field}
//			if (preg_match_all('/\{(.+)\}/isU', $pageTitle, $matches, PREG_SET_ORDER)) {
//				foreach ($matches as $match) {
//					$expression = $match[0];
//					$expressionInner = $match[1];
//					$values = explode('.', $expressionInner);
//
//					// Checks if table name is given or not.
//					if (count($values) == 1) {
//						$table = $this->structure['name'];
//						$field = $values[0];
//					} elseif (count($values) == 2) {
//						$table = $values[0];
//						$field = $values[1];
//					}
//					$expressionResult = $this->getValueFromStructure($this->structure, 0, $table, $field);
//					$pageTitle = str_replace($expression, $expressionResult, $pageTitle);
//				}
//			}
//			$GLOBALS['TSFE']->page['title'] = $pageTitle;
//		}
//	}

	/**
	 * If found, returns markers of type SORT
	 *
	 * Example of marker: ###SORT###
	 *
	 * @param	string	$content HTML code
	 * @return	string	$content transformed HTML code
	 */
//	protected function getSortMarkers($content) {
//		$markers = array();
//		if (preg_match_all('/#{3}SORT\.(.+)#{3}/isU', $content, $matches, PREG_SET_ORDER)) {
//			foreach($matches as $match){
//				$marker = $match[0];
//				$markerContent = $match[1];
//				// Get the position of the sort
//				if (preg_match('/([0-9])$/is', $markerContent, $positions)) {
//					$position = $positions[0];
//				}
//				else {
//					$position = 1;
//				}
//
//				// Gets whether it is a sort or an order
//				if (strpos($markerContent, 'sort') !== FALSE) {
//					$sortTable = '';
//					if ($this->filter['orderby'][$position * 2 - 1]['table'] != '') {
//						$sortTable = $this->filter['orderby'][$position * 2 - 1]['table'] . '.';
//					}
//					$markers[$marker] = $sortTable . $this->filter['orderby'][$position * 2 - 1]['field'];
//				}
//				else if (strpos($markerContent, 'order') !== FALSE) {
//					$markers[$marker] = $this->filter['orderby'][$position * 2 - 1]['order'];
//				}
//			}
//		}
//		return $markers;
//	}

	/**
	 * If found, returns markers of type SORT
	 *
	 * Example of marker: ###SORT###
	 *
	 * @param	string	$content HTML code
	 * @return	string	$content transformed HTML code
	 */
//	protected function getFilterMarkers($content) {
//		$markers = array();
//		if (preg_match_all('/#{3}FILTER\.(.+)#{3}/isU', $content, $matches, PREG_SET_ORDER)) {
//
//			// Defines the filters array.
//			// It can be the property of the object
//			// But the filter can be given by the caller. @see method processRECORDS();
//			$uid = $this->pObj->cObj->data['uid'];
//			if (isset($GLOBALS['tesseract']['filter']['parent'])) {
//				$filters = $GLOBALS['tesseract']['filter']['parent'];
//			}
//			else {
//				$filters = $this->filter;
//			}
//
//			// Traverse the FILTER markers
//			foreach($matches as $match){
//				$marker = $match[0];
//				$markerInner = $match[1];
//
//				// Traverses the array and finds the value
//				if (isset($filters['parsed']['filters'][$markerInner])) {
//					$_filter = $filters['parsed']['filters'][$markerInner];
//					$_filter = reset($_filter); //retrieve the cell indepantly from the key
//					$markers[$marker] = $_filter['value'];
//				}
//			}
//		}
//		return $markers;
//	}

	/**
	 * If found, returns all markers that correspond to subexpressions
	 * and can be parsed using tx_expressions_parser
	 *
	 * Example of GP marker: ###EXPRESSION.gp|parameter###
	 *
	 * @param	string	$content HTML code
	 * @return	string	$content transformed HTML code
	 */
//	protected function getAllExpressionMarkers($content) {
//		$markers = array();
//		if (preg_match_all('/#{3}EXPRESSION\.(.+)#{3}/isU', $content, $matches, PREG_SET_ORDER)) {
//			$numberOfMatches = count($matches);
//			if ($numberOfMatches > 0) {
//				for ($index = 0; $index < $numberOfMatches; $index ++) {
//					try {
//						$markers[$matches[$index][0]] = tx_expressions_parser::evaluateExpression($matches[$index][1]);
//					}
//					catch (Exception $e) {
//						continue;
//					}
//				}
//			}
//		}
//		return $markers;
//	}

	/**
	 * If found, returns markers, of type global template variable
	 * Global template variable can be ###TOTAL_RECORDS### ###SUBTOTAL_RECORDS###
	 *
	 * @param	string	$content: HTML content
	 * @return	 string	$content: transformed HTML content
	 */
//	protected function getGlobalVariablesMarkers($content) {
//		$markers = array();
//		if (preg_match('/#{3}TOTAL_RECORDS#{3}/isU', $content)) {
//			$markers['###TOTAL_RECORDS###']	= $this->structure['totalCount'];
//		}
//		if (preg_match('/#{3}SUBTOTAL_RECORDS#{3}/isU', $content)) {
//			$markers['###SUBTOTAL_RECORDS###']  = $this->structure['count'];
//		}
//
//		if (preg_match('/#{3}RECORD_OFFSET#{3}/isU', $content)) {
//			if (!$this->pObj->piVars['page']) {
//				$this->pObj->piVars['page'] = 0;
//			}
//
//			// Computes the record offset
//			$recordOffset = ($this->pObj->piVars['page'] + 1) * $this->filter['limit']['max'];
//			if ($recordOffset > $this->structure['totalCount']) {
//				$recordOffset = $this->structure['totalCount'];
//			}
//			$markers['###RECORD_OFFSET###']	= $recordOffset;
//		}
//		return $markers;
//	}

	/**
	 * Processe the function PAGE_STATUS
	 *
	 * @param	string	$content HTML code
	 * @return	string	$content transformed HTML code if the datastructure is *not* empty.
	 */
//	protected function checkPageStatus($content) {
//
//		// Preprocesses the <!--IF(###MARKER### == '')-->, puts a '' around the marker
//		$pattern = '/PAGE_STATUS\((.+)\)/isU';
//		if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
//			foreach($matches as $match) {
//				$expression = $match[0];
//
//				// explode values
//				$_match = explode(',',$match[1]);
//
//				// avoid possible problem
//				$_match = array_map('trim', $_match);
//				$errorCode = $_match[0];
//				$redirect = $replace = '';
//				if (isset($_match[1])) {
//					$redirect = $_match[1];
//				}
//
//				if (empty($this->structure['records'])) {
//					switch ($errorCode) {
//						case '301' : // 301 Moved Permanently
//							header("Location: " . $redirect,TRUE,301);
//							break;
//						case '302' : // 302 Found
//							header("Location: /" . $redirect,TRUE,302);
//							break;
//						case '303' : // 303 See Other
//							header("Location: " . $redirect,TRUE,303);
//							break;
//						case '307' : // 307 Temporary Redirect
//							header("Location: " . $redirect,TRUE,307);
//							break;
//						case '404' : // 404
//							header("HTTP/1.1 404 Not Found");
//							if ($redirect != '') {
//								header("Location: " . $redirect,TRUE,302);
//							}
//							break;
//						case '500' :
//							header("HTTP/1.1 500 Internal Server Error");
//							if ($redirect != '') {
//								header("Location: " . $redirect,TRUE,302);
//							}
//							break;
//						default :
//							$replace = 'Sorry the status ' . $errorCode . ' is not handled yet.';
//						}
//					}
//					$content = str_replace($expression, $replace, $content);
//				}
//			}
//			return $content;
//		}

	/**
	 * Extracts the filename of a path
	 *
	 * @param	string	$filename
	 * @return	string	the filename
	 */
	protected function getFileName($filepath) {
		$filename = '';
		$fileInfo = t3lib_div::split_fileref($filepath);
		if (isset($fileInfo['filebody'])) {
			$filename = $fileInfo['filebody'];
		}
		return $filename;
	}


	/**
	 * Displays in the frontend or in the devlog some debug output
	 *
	 * @param array $markers
	 * @param array $templateStructure
	 */
//	protected function debug($markers, $templateStructure) {
//		if (isset($GLOBALS['_GET']['debug']['markers']) && isset($GLOBALS['TYPO3_MISC']['microtime_BE_USER_start'])) {
//			t3lib_div::debug($markers);
//		}
//
//		if (isset($GLOBALS['_GET']['debug']['template']) && isset($GLOBALS['TYPO3_MISC']['microtime_BE_USER_start'])) {
//			t3lib_div::debug($templateStructure);
//		}
//
//
//		if (isset($GLOBALS['_GET']['debug']['filter']) && isset($GLOBALS['TYPO3_MISC']['microtime_BE_USER_start'])) {
//			t3lib_div::debug($this->filter);
//		}
//
//		if ($this->configuration['debug'] || TYPO3_DLOG) {
//			t3lib_div::devLog('Markers: "' . $this->consumerData['title'] . '"', $this->extKey, -1, $markers);
//			t3lib_div::devLog('Template structure: "' . $this->consumerData['title'] . '"', $this->extKey, -1, $templateStructure);
//			t3lib_div::devLog('Data structure: "' . $this->pObj->cObj->data['header'] . '"', $this->extKey, -1, $this->structure);
//		}
//
//		if ($this->consumerData['debug_markers'] && !$this->configuration['debug']) {
//			t3lib_div::devLog('Markers: "' . $this->consumerData['title'] . '"', $this->extKey, -1, $markers);
//		}
//
//		if ($this->consumerData['debug_template_structure'] && !$this->configuration['debug']) {
//			t3lib_div::devLog('Template structure: "' . $this->consumerData['title'] . '"', $this->extKey, -1, $templateStructure);
//		}
//
//		if ($this->consumerData['debug_data_structure'] && !$this->configuration['debug']) {
//			t3lib_div::devLog('Data structure: "' . $this->pObj->cObj->data['header'] . '"', $this->extKey, -1, $this->structure);
//		}
//	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fluiddisplay/class.tx_fluiddisplay.php']){
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fluiddisplay/class.tx_fluiddisplay.php']);
}

?>
