<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Peter Klein (pmk@io.dk)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
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
 *   52: class tx_pmkglossary_extraeval
 *   62:     function evaluateFieldValue($value, $is_in, &$set)
 *  104:     function getPid($uid)
 *  117:     function function checkForDupe($value)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * 'Custom TCA eval' for the 'pmkglossary' extension.
 *
 * @author Peter Klein <pmk@io.dk>
 * @package TYPO3
 * @subpackage tx_pmkglossary
 */
class tx_pmkglossary_extraeval {

	/**
	 * Checks if catchword is unique in pid.
	 *
	 * @param	mixed		$value: The value that has to be checked.
	 * @param	string		$is_in: Is-In String
	 * @param	integer		$set: Determines if the field can be set (value correct) or not (PASSED BY REFERENCE!)
	 * @return	string		$value: The new value of the field
	 */
	function evaluateFieldValue($value, $is_in, &$set) {

		// Data of current record
		$this->table = 'tx_pmkglossary_glossary';
		$tmp = $GLOBALS['SOBE']->editconf[$this->table];
		$this->data = array_pop($GLOBALS['SOBE']->data[$this->table]);
		$this->mode = array_pop($tmp);
		switch ((string)$this->mode) {
			case 'new':
				$this->uid = -1;
				// If creating new record , then the value is the pid of the storage folder
				$this->pid = array_pop(array_keys($GLOBALS['SOBE']->editconf[$this->table]));
			break;
			case 'edit':
				// But if editing existing record , then the value is the uid of the record!!
				$this->uid = array_pop(array_keys($GLOBALS['SOBE']->editconf[$this->table]));
				$this->pid = $this->getPid($this->uid);
			break;
			default;
		}

		// Reverse the use of the "is_in" field in order to remove possible commas from the title field
		$value = str_replace($is_in,'',$value);

		$out = array();
		$values = t3lib_div::trimExplode(',',$value);
		foreach ($values as $value) {
			if (!$this->checkForDupe($value)) {
				$out[] = $value;
			}
		}
		$value = implode(',',$out);

		return $value ? $value : ' ';
	}

	/**
	 * Get Page Id (PID) of record.
	 *
	 * @param	integer		$uid: Id of record.
	 * @return	integer		$row['pid']: Id of storage folder.
	 */
	function getPid($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid', $this->table,'uid='.$uid.' AND deleted=0');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		return $row['pid'];
	}

	/**
	 * Check if there already exists a record with the same title.
	 * Or if the title is present in the altitle of another record.
	 *
	 * @param	string		$value: The value that has to be checked.
	 * @return	boolean		true if duplicate title is found
	 */
	function checkForDupe($value) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title', $this->table,
'pid='.intval($this->pid).' AND uid!='.intval($this->uid).' AND deleted=0'.
' AND sys_language_uid IN (-1,'.intval($this->data['sys_language_uid']).
') AND (title='.$GLOBALS['TYPO3_DB']->fullQuoteStr($value, $this->table) .
' OR '.$GLOBALS['TYPO3_DB']->listQuery('alttitle', $value, $this->table).')');
		$result = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $result>0 ? true : false;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/class.tx_pmkglossary_extraeval.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/class.tx_pmkglossary_extraeval.php']);
}
?>