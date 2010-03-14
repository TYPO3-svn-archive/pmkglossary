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
	*   48: class ext_update
	*   55:     function main()
	*  100:     function convertData($options)
	*  135:     function convertOverlay($parentRow,$newParentUid,$options)
	*  166:     function createRecord($row)
	*  192:     wordcharsOnly($text)
	*  204:     function access()
	*
	* TOTAL FUNCTIONS: 4
	* (This index is automatically created/updated by the extension "extdeveval")
	*
	*/

require_once(PATH_t3lib.'class.t3lib_page.php');

/**
 * Class for updating/converting mr_glossary data into pmkglossary format
 *
 * @author	 Peter Klein <peter@umloud.dk>
 */
 class ext_update {

	var $dupeCount = 0;
	var $convertCount = 0;
	var $totalCount = 0;
	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{
		global $BACK_PATH,$LANG;
		$LANG->includeLLFile("EXT:pmkglossary/locallang.xml");
		$this->sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
		if (t3lib_extMgm::isLoaded('mr_glossary')) {

			if (t3lib_div::_GP('update')) {
				$this->convertData(intval(t3lib_div::_GP('options')));
				$content = sprintf($LANG->getLL('upd.result'),$this->totalCount,$this->convertCount);
				if ($this->dupeCount>0) {
					$content .= '<br />'.sprintf($LANG->getLL('upd.duperesult'),$this->dupeCount);
				}
			}
			else {
				$content =$test.'<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">
					<fieldset>
						<legend>'.$LANG->getLL('upd.legend').'</legend>
 						<p>&nbsp;</p>
						<div>
							<label for="options">'.$LANG->getLL('upd.option').'
							<select name="options">
								<option value="1">'.$LANG->getLL('upd.option.nothing').'</option>
								<option value="2">'.$LANG->getLL('upd.option.hide').'</option>
								<option value="3">'.$LANG->getLL('upd.option.delete').'</option>
							</select>
							</label>
						</div>
 						<p>&nbsp;</p>
						<div>
							<input name="update" value="'.$LANG->getLL('upd.convert').'" type="submit" />
						</div>
					</fieldset>
				</form>';
			}
		}
		else {
			$content = $LANG->getLL('upd.error');
		}
		return $content;
	}

	/**
	 * Convert mrglossary data to PMK Glossary format
	 *
	 * @param integer		$options: What to do with record after converting. (0=Nothing, 1=Hide, 2=Delete)
	 * @return	void
	 */
	function convertData($options) {
		$this->convertCount = 0;
		$this->totalCount = 0;
		$this->dupeCount = 0;
		$table = 'tx_mrglossary_glossary';
		$fields = '*';
		$where = '1=1 '.$this->sys_page->enableFields($table);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->totalCount++;
				if ($this->checkForDupe($row['catchword'],'tx_pmkglossary_glossary',$row['pid'],0)) {
					$this->dupeCount++;
				}
				else {
					$this->createRecord($row);
					$this->convertCount++;
					switch ($options) {
						case 2:
							// Hide tx_mrglossary_glossary record
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('hidden' => 1));
						break;
						case 3:
							// Delete tx_mrglossary_glossary record
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('deleted' => 1));
						break;
						default:
						break;
					}
				}
				$this->convertOverlay($row,$GLOBALS['TYPO3_DB']->sql_insert_id(),$options);
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	/**
	 * Check if there already exists a record with the same title.
	 * Or if the title is present in the altitle of another record.
	 *
	 * @param	string		$value: The value that has to be checked.
	 * @param	string		$table: table of record
	 * @param	string		$pid: pid of record
	 * @param	string		$sys_language_uid: language id of record
	 * @return	boolean		true if duplicate title is found
	 */
	function checkForDupe($value,$table,$pid,$sys_language_uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title', $table,
'pid='.intval($pid).' AND deleted=0'.
' AND sys_language_uid IN (-1,'.intval($sys_language_uid).
') AND (title='.$GLOBALS['TYPO3_DB']->fullQuoteStr($value, $table) .
' OR '.$GLOBALS['TYPO3_DB']->listQuery('alttitle', $value, $table).')');
		$result = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $result>0 ? true : false;
	}

	/**
	 * Convert mrglossary overlay data to PMK Glossary format
	 *
	 * @param array		$parentRow: Data from main record
	 * @param integer	$newParentUid: Id for parent record
	 * @param integer	$options: What to do with record after converting. (0=Nothing, 1=Hide, 2=Delete)
	 * @return	void
	 */
	function convertOverlay($parentRow,$newParentUid,$options) {
		$table = 'tx_mrglossary_glossary_language_overlay';
		$fields = '*';
		$where = 'page_uid='.intval($parentRow['uid']).$this->sys_page->enableFields($table);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->totalCount++;
				if ($this->checkForDupe($row['catchword'],'tx_pmkglossary_glossary',$row['pid'],$row['sys_language_uid'])) {
					$this->dupeCount++;
				}
				else {
					$row['l10n_parent'] = $newParentUid;
					$this->createRecord($row);
					$this->convertCount++;
					switch ($options) {
						case 2:
							// Hide tx_mrglossary_glossary record
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('hidden' => 1));
						break;
						case 3:
							// Delete tx_mrglossary_glossary record
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('deleted' => 1));
						break;
						default:
						break;
					}
				}
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	/**
	 * Save converted data in PMK Glossary format
	 *
	 * @param array		$row: Data to save in DB
	 * @return	void
	 */
	function createRecord($row) {
		$table = 'tx_pmkglossary_glossary';
		$fields = array(
			'pid' => $row['pid'],
			'tstamp' => $row['tstamp'],
			'crdate' => $row['crdate'],
			'title' => $row['catchword'],
			'wordtitle' => $this->wordcharsOnly($row['catchword']),
			'bodytext' => $row['catchword_desc'],
			'sys_language_uid' => intval($row['sys_language_uid']),
			'l10n_parent' => intval($row['l10n_parent'])
		);
		if (isset($row['image'])) {
			$fields['image'] = $row['image'];
			$fields['imagewidth'] = $row['imagewidth'];
		}
		$GLOBALS['TYPO3_DB']->exec_INSERTquery($table,$fields);
	}

	/**
	 * Removes any non-word characters from string
	 * If result is empty string then original string is returned
	 *
	 * @param	string		$text: Text with possible non-word characters
	 * @return	string		Text stripped of non-word characters
	 */
	function wordcharsOnly($text) {
		$stext = preg_replace('/(\W|_+)/u', '', $text);
		return $stext ? $stext : $text;
	}

	/**
	 * access is always allowed
	 *
	 * @return	boolean		Always returns true
	 */
	function access() {
		return true;
	}

}

// Include extension?
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/class.ext_update.php']);
}

?>
