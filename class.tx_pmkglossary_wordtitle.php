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
 *   48: class tx_pmkglossary_wordtitle
 *   60:     function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$reference) {
 *   75:     function wordcharsOnly($text)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

 require_once(PATH_typo3.'sysext/lang/lang.php');

/**
 * 'TCA preprocessing' for the 'pmkglossary' extension.
 *
 * @author Peter Klein <pmk@io.dk>
 * @package TYPO3
 * @subpackage tx_pmkglossary
 */
	class tx_pmkglossary_wordtitle {

	/**
	 * Main function. Hook from t3lib/class.t3lib_tcemain.php
	 *
	 * @param	string		$status: Status of the current operation, 'new' or 'update
	 * @param	string		$table: The table currently processing data for
	 * @param	string		$id: The record uid currently processing data for, [integer] or [string] (like 'NEW...')
	 * @param	array		$fieldArray: The field array of a record
	 * @param	object		$reference: reference to parent object
	 * @return	void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$reference) {
		if ($table=='tx_pmkglossary_glossary' && isset($fieldArray['title'])) {
			$uid = ($status == 'new') ? $reference->substNEWwithIDs[$id] : $id;
			// $row = $reference->datamap['tx_pmkglossary_glossary'][$id];
			// Store a clean (non-word chars removed) version of the title
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid='.$uid, array('wordtitle' => $this->wordcharsOnly($fieldArray['title'])));
		}
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
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/class.tx_pmkglossary_wordtitle.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/class.tx_pmkglossary_wordtitle.php']);
}
?>