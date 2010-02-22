<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2010 Peter Klein <pmk@io.dk>
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
	*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*  GNU General Public License for more details.
	*
	*  This copyright notice MUST APPEAR in all copies of the script!
	***************************************************************/
	/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   52: class tx_pmkglossary_pi1 extends tslib_pibase
 *   66:     function main($content, $conf)
 *   89:     function getGlossary()
 *  128:     function displayGlossary($glossary)
 *  174:     function setMarkers(array $row,$markerArray = array()
 *  190:     function _alpha_sort($a, $b)
 *  202:     function wordcharsOnly($text)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	require_once(PATH_tslib.'class.tslib_pibase.php');


	/**
	 * Plugin 'PMK Glossary' for the 'pmkglossary' extension.
	 *
	 * @author Peter Klein <pmk@io.dk>
	 * @package TYPO3
	 * @subpackage tx_pmkglossary
	 */
	class tx_pmkglossary_pi1 extends tslib_pibase {
		var $prefixId = 'tx_pmkglossary_pi1';
		// Same as class name
		var $scriptRelPath = 'pi1/class.tx_pmkglossary_pi1.php'; // Path to this script relative to the extension dir.
		var $extKey = 'pmkglossary'; // The extension key.
		var $pi_checkCHash = true;

		/**
		 * The main method of the PlugIn
		 *
		 * @param	string		$content: The PlugIn content
		 * @param	array		$conf: The PlugIn configuration
		 * @return	string		The	content that is displayed on the website
		 */
		function main($content, $conf) {
			$this->conf = $conf;
			$this->pi_setPiVarDefaults();
			$this->pi_loadLL();

			$jsFiles = t3lib_div::trimExplode(',',$this->conf['javascriptFile']);
			$js = '';
			foreach ($jsFiles as $jsFile) {
				$js .= '<script type="text/javascript" src="'.str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($jsFile)).'"></script>'.PHP_EOL;
			}
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = $js;

			$glossary = $this->getGlossary();
			$content = $this->displayGlossary($glossary);

			return $this->pi_wrapInBaseClass($content);
		}

		/**
		 * Get glossary records from DB
		 *
		 * @return	array		Complete array of glossary records (alpha sorted)
		 */
		function getGlossary() {
			$glossary = array();

			$table = 'tx_pmkglossary_glossary';
			$fields = 'pid,uid,sys_language_uid,title,bodytext,image,imagewidth,imageorient';
			if ($this->conf['TYPO3localization']) {
				$where = '(pid='. intval($GLOBALS['TSFE']->id) .' OR pid IN ('.$this->conf['pid_list'].')) AND (sys_language_uid IN (-1,0) OR (sys_language_uid='.$GLOBALS['TSFE']->sys_language_uid.' AND l10n_parent=0)) '.$this->cObj->enableFields($table);
			}
			else {
				$where = '(pid='. intval($GLOBALS['TSFE']->id) .' OR pid IN ('.$this->conf['pid_list'].')) AND sys_language_uid IN (-1,'.$GLOBALS['TSFE']->sys_language_uid.') '.$this->cObj->enableFields($table);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

					// Language overlay:
					if (is_array($row) && $GLOBALS['TSFE']->sys_language_contentOL) {
						$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table,$row,$GLOBALS['TSFE']->sys_language_content,$GLOBALS['TSFE']->sys_language_contentOL);
					}

					// $row might be unset in the sys_page->getRecordOverlay
					if (!is_array($row)) continue;

					// Set catchword as key
					$glossary[$row['title']] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			//ksort($glossary,SORT_LOCALE_STRING);
			uksort($glossary, array($this, '_alpha_sort'));
			return $glossary;
		}

		/**
		 * Creates Glossary output
		 *
		 * @param	array		$glossary: Glossary records
		 * @return	string		The	content that is displayed on the website
		 */
		function displayGlossary($glossary) {
			$this->pi_loadLL();

			// Get the template
			$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
			$template['total'] = $this->cObj->getSubpart($this->templateCode,'###TEMPLATE_GLOSSARY###');
			$template['block'] = $this->cObj->getSubpart($template['total'],'###GLOSSARY_BLOCK###');
			$template['item'] = $this->cObj->getSubpart($template['block'],'###GLOSSARY_ITEM###');
			$markerArray = array();

			$firstBlockChar = '';
			$content = '';
			$items = '';

			// Add dummy glossery item to compensate for loop
			$glossary[] = array('title'=>'');

			foreach ($glossary as $row) {
				$firstChar = $GLOBALS['TSFE']->csConvObj->substr($GLOBALS['TSFE']->renderCharset,$this->wordcharsOnly($row['title']),0,1);
				// Convert it to uppercase
				$firstChar = $GLOBALS['TSFE']->csConvObj->conv_case($GLOBALS['TSFE']->renderCharset,$firstChar, 'toUpper');
				if ($firstChar != $firstBlockChar) {
					if ($firstBlockChar != '') {
						$markerArray['###FIRSTCHAR###'] = $firstBlockChar;
						$markerArray['###WORDS_STARTING_WITH###'] = $this->pi_getLL('words_starting_with');
						$subpartArray['###GLOSSARY_ITEM###'] = $items;
						$content .= $this->cObj->substituteMarkerArrayCached($template['block'],$markerArray,$subpartArray);
						$items = '';
					}
					$firstBlockChar = $firstChar;
				}
				$markerArray = $this->setMarkers($row);
				$items .= $this->cObj->substituteMarkerArrayCached($template['item'],$markerArray);
			}
			$markerArray['###GLOSSARY_ID###'] = 'pmkglossary'.$GLOBALS['TSFE']->id;
			$subpartArray['###GLOSSARY_BLOCK###'] .= $content;
			return $this->cObj->substituteMarkerArrayCached($template['total'],$markerArray,$subpartArray);
		}

		/**
		 * Sets markers for HTML substitution
		 *
		 * @param	array		$row: Data to add to markers
		 * @param	array		$markerArray: optional existing $markerArray
		 * @return	array		$markerArray
		 */
		function setMarkers(array $row,$markerArray = array()) {
			$this->cObj->data = $row;
			foreach ($row as $key => $value) {
				$val = $this->cObj->cObjGetSingle($this->conf[$key], $this->conf[$key.'.']);
				$markerArray['###'.strtoupper($key).'###'] = $val ? $val : $value;
			}
			return $markerArray;
		}

		/**
		 * Calback function for custom sorting
		 *
		 * @param	string		$a: First string
		 * @param	string		$b: First string
		 * @return	integer		Returns -1 if $a  is less than $b ; 1 if $a  is greater than $b , and 0 if they are equal.
		 */
		function _alpha_sort($a,$b) {
			$a = $this->wordcharsOnly($a);
			$b = $this->wordcharsOnly($b);
			return strcoll($a,$b);
		}

		/**
		 * Removes any non-word characters from string
		 *
		 * @param	string		$text: Text with possible non-word characters
		 * @return	string		Text stripped of non-word characters
		 */
		function wordcharsOnly($text) {
			return preg_replace('/(\W|_+)/', '', $text);
		}
	}


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi1/class.tx_pmkglossary_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi1/class.tx_pmkglossary_pi1.php']);
	}

?>
