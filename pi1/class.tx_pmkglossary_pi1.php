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
 *   51: class tx_pmkglossary_pi1 extends tslib_pibase
 *   65:     function main($content, $conf)
 *   89:     function getGlossary()
 *  121:     function displayGlossary($glossary)
 *  163:     function _len_sort($a, $b)
 *  177:     function _alpha_sort($a, $b)
 *
 * TOTAL FUNCTIONS: 5
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
			$GLOBALS['TSFE']->additionalHeaderData['msAccordion'] = '<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/jquery.msAccordion.min.js"></script>';

			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<script language="javascript" type="text/javascript">
				$(document).ready(function() {
				$("#pmkglossary'.$GLOBALS['TSFE']->id.'").msAccordion({defaultid:0, vertical:true});
				})
				</script>';

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
			$table = 'tx_pmkglossary_glossary';
			$fields = '*';
			$where = '(pid='. intval($GLOBALS['TSFE']->id) .' OR pid IN ('.$this->conf['pid_list'].')) AND (sys_language_uid IN (-1,0) OR (sys_language_uid='.$GLOBALS['TSFE']->sys_language_uid.' AND l10n_parent=0)) '.$this->cObj->enableFields($table);

			$charset = $GLOBALS['TSFE']->csConvObj->get_locale_charset($locale);
			$glossary = array();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if ($GLOBALS['TSFE']->sys_language_content) {
						$OLmode = $GLOBALS['TSFE']->sys_language_contentOL ? 'hideNonTranslated' :
						 '';
						if (!($row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $row, $GLOBALS['TSFE']->sys_language_uid, $OLmode))) {
							continue;
						}
					}
					$key = $GLOBALS['TSFE']->csConvObj->conv_case($GLOBALS['TSFE']->renderCharset,$row['catchword'], 'toUpper');
					$key = $GLOBALS['TSFE']->csConvObj->conv($key,$GLOBALS['TSFE']->renderCharset,$charset);
					$glossary[$key] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
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
			$content = '';
			$firstBlockChar = '';
			foreach ($glossary as $row) {
				$image = '';
				if ($row['image'] !== null && $this->conf['showImage']) {
					$iConf = array(
					'file' => 'uploads/tx_pmkglossary/' . $row['image'],
						'file.' => array(
					'width' => $this->conf['imageWidth'],
						'height' => $this->conf['imageHeight'] ),
						'altText' => $row['catchword'],
						'params' => 'style="float: left;margin: 0 5px 2px 0;"' );
					$image = $this->cObj->IMAGE($iConf);
				}
				// Get rid of any non-word characters in front of word
				$firstChar = preg_replace('/\A\W*/i', '', $row['catchword']);
				// Get the 1st character
				$firstChar = $GLOBALS['TSFE']->csConvObj->substr($GLOBALS['TSFE']->renderCharset,$firstChar, 0, 1);
				// Convert it to uppercase
				$firstChar = $GLOBALS['TSFE']->csConvObj->conv_case($GLOBALS['TSFE']->renderCharset,$firstChar, 'toUpper');

				if ($firstChar != $firstBlockChar) {
					if ($firstBlockChar != '') {
						$content .= '</dl></div></div>';
					}
					$firstBlockChar = $firstChar;
					$content .= '<div class="set">
						<div class="title">
						<h3 title="'.$this->pi_getLL('words_starting_with').' '.$firstBlockChar.'">'.$firstBlockChar.'</h3>
						</div>
						<div class="content">
						<dl>';
				}
				$content .= '<dt>'. ($row['catchword']).'</dt>';
				$content .= '<dd>'. $image . $this->pi_RTEcssText($row['catchword_desc']).'</dd>';
			}
			$content .= '</dl></div></div>';
			return '<div class="no-glossary" id="pmkglossary'.$GLOBALS['TSFE']->id.'">'.$content.'</div>';
		}

		/**
 * Custom sorting callback function
 *
 * @param	array		$a: Glossary record
 * @param	array		$b: glossary record
 * @return	mixed		-1,0 or 1
 */
		function _alpha_sort($a, $b) {
			$a = preg_replace('/\A\W*/i', '', $a);
			$b = preg_replace('/\A\W*/i', '', $b);
			// No matter what I try here, the sorting does not come up as expected,
			// due to the match is done by unicode, not letters. WHY????
			//return strcoll($a, $b);
			return strcmp($a, $b);
		}
	}


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi1/class.tx_pmkglossary_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi1/class.tx_pmkglossary_pi1.php']);
	}

?>
