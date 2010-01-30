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
 * Class for updating/converting mr_glossary data to pmkglossary format
 *
 * @author	 Peter Klein <peter@umloud.dk>
 */
if (@is_dir(PATH_site.'typo3/sysext/cms/tslib/')) {
        define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
} elseif (@is_dir(PATH_site.'tslib/')) {
        define('PATH_tslib', PATH_site.'tslib/');
}
else {
	die('PATH_tslib mot defined!');
}

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_page.php');

class ext_update extends tslib_pibase {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{
		global $BACK_PATH;
		$this->sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
		if (t3lib_extMgm::isLoaded('mr_glossary')) {

			if (t3lib_div::_GP('update')) {
				$this->convertData(intval(t3lib_div::_GP('options')));
				$content = 'Records converted!';
			}
			else {
				$content ='<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">
					<fieldset>
						<legend>Convert mr_glossary data to pmkglossary format</legend>
						<div>
							<label for="options">What to do with mr_glossary records?
							<select name="options">
								<option value="0">Nothing</option>
								<option value="1">Hide</option>
								<option value="2">Delete</option>
							</select>
							</label>
						</div>
						<div>
							<input name="update" value="Convert" type="submit" />
						</div>
					</fieldset>
				</form>';
			}
		}
		else {
			$content = 'mr_glossary not installed!';
		}
		return $content;
	}

	function convertData($options) {
		$table = 'tx_mrglossary_glossary';
		$fields = '*';
		$where = '1=1 '.$this->sys_page->enableFields($table);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->createRecord($row);
				$this->convertOverlay($row,$GLOBALS['TYPO3_DB']->sql_insert_id(),$options);
				switch ($options) {
					case 1:
						// Hide tx_mrglossary_glossary record
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('hidden' => 1));
					break;
					case 2:
						// Delete tx_mrglossary_glossary record
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('deleted' => 1));
					break;
				}
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	function convertOverlay($parentRow,$newParentUid,$options) {
		$table = 'tx_mrglossary_glossary_language_overlay';
		$fields = '*';
		$where = 'page_uid='.intval($parentRow['uid']).$this->sys_page->enableFields($table);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row['l10n_parent'] = $newParentUid;
				$this->createRecord($row);
				switch ($options) {
					case 1:
						// Hide tx_mrglossary_glossary record
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('hidden' => 1));
					break;
					case 2:
						// Delete tx_mrglossary_glossary record
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid='.intval($row['uid']),array('deleted' => 1));
					break;
				}
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	function createRecord($row) {
		$table = 'tx_pmkglossary_glossary';
		$fields = array(
			'pid' => $row['pid'],
			'tstamp' => $row['tstamp'],
			'crdate' => $row['crdate'],
			'starttime' => $row['starttime'],
			'endtime' => $row['endtime'],
			'fe_group' => $row['fe_group'],
			'catchword' => $row['catchword'],
			'catchword_desc' => $row['catchword_desc'],
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
