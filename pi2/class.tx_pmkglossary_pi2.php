<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Peter Klein <pmk@io.dk>
 *  All rights reserved
 *
 *  Part of code taken from the 'mr_parseGlossary' extension.
 *  @ author alex widschwendter <a.widschwendter@mediares.at>
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
 *   62: class tx_pmkglossary_pi2 extends tslib_pibase
 *   80:     function main($content, $conf)
 *  132:     function processDom(DOMDocument $dom)
 *  142:     function listAllElements(DOMNode $dom)
 *  162:     function getParents(DOMNode $dom)
 *  179:     function hasTagNames(DOMNode $node, array $tag_names)
 *  190:     function hasClassName($nodes)
 *  212:     function glossary(DOMNode $node)
 *  281:     function init($conf)
 *  300:     function makeRegExMatch($string)
 *  311:     function getGlossary()
 *  371:     function HTML2DOM($content)
 *  389:     function DOM2HTML(DOMDocument $domObj)
 *  403:     function _len_sort($a, $b)
 *
 * TOTAL FUNCTIONS: 13
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
	class tx_pmkglossary_pi2 extends tslib_pibase {
		var $prefixId = 'tx_pmkglossary_pi2'; // Same as class name
		var $scriptRelPath = 'pi2/class.tx_pmkglossary_pi2.php'; // Path to this script relative to the extension dir.
		var $extKey = 'pmkglossary';	// The extension key.
		var $pi_checkCHash = true;
		var $conf;						// Plugin config options
		var $fromCS;					// Charset used when accessing DB data
		var $toCS;						// Charset used for output in browser
		var $glossary = array();		// Glossary array
		var $cObj;

		/**
		 * The main method of the PlugIn
		 *
		 * @param	string		$content: The content that should be parsed for catchwords
		 * @param	array		$conf: The PlugIn configuration
		 * @return	string		The content that is displayed on the website
		 */
		function main($content, $conf) {
			// Get config options from TS
			$this->init($conf);

			// Page is excluded from parsing.
			if ($GLOBALS['TSFE']->page['tx_pmkglossary_no_parsing'] || t3lib_div::inList($this->conf['noParsePages'],$GLOBALS['TSFE']->id)) {
				return $content;
			};

			// If config value "debug" is set, then start time
			if ($this->conf['debug']) {
				$timer = time() + microtime();
			}

			$this->glossary = $this->getGlossary();

			$domObj = $this->HTML2DOM($content);
			$this->processDom($domObj);

			$content = $this->DOM2HTML($domObj);

			// If config value "debug" is set, display the glossary parsetime
			if ($this->conf['debug']) {
				if (intval($this->conf['debug'])>1) {
					$content.='$GLOBALS["TYPO3_CONF_VARS"]["BE"]["forceCharset"] = '.$GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'].'<br />';
					$content.='$GLOBALS["TYPO3_CONF_VARS"]["SYS"]["UTF8filesystem"] = '.$GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem'].'<br />';
					$content.='$GLOBALS["TYPO3_CONF_VARS"]["SYS"]["t3lib_cs_convMethod"] = '.$GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'].'<br />';
					$content.='$GLOBALS["TSFE"]->defaultCharSet = '.$GLOBALS['TSFE']->defaultCharSet.'<br />';
					$content.='$GLOBALS["TSFE"]->metaCharset = '.$GLOBALS['TSFE']->metaCharset.'<br />';
					$content.='$GLOBALS["TSFE"]->renderCharset = '.$GLOBALS['TSFE']->renderCharset.'<br />';
					$content.='$this->fromCS = '.$this->fromCS.'<br />';
					$content.='$this->toCS = '.$this->toCS.'<br />';
					$content.='$_SERVER["SERVER_SOFTWARE"] = '. $_SERVER['SERVER_SOFTWARE'] .'<br />';
					$content.='TYPO3_OS = '.TYPO3_OS.'<br />';
					$content.='TYPO3_version = '.TYPO3_version.'<br />';
					$content.='PHP_VERSION = '.PHP_VERSION.'<br />';
					$content.='mysql_get_server_info() = '.mysql_get_server_info().'<br />';
				}
				// Subtract start time from current time and add it to output
				$timer = time() + microtime()-$timer;
				$content .= '<div id="tx-pmkglossary-debug"><span>Glossary parsetime: '.$timer.'</span></div>';
			}

			return $content;
		}

		/**
		 * Process DOMDocument object and insert glossary tags.
		 *
		 * @param	object		$domObj: DOMDocument Object
		 * @return	void
		 */
		function processDom(DOMDocument $dom) {
			array_map(array($this, 'glossary'), $this->listAllElements($dom));
		}

		/**
		 * Process Convert DOMDocument into array of nodes.
		 *
		 * @param	object		$domObj: DOMnode Object
		 * @return	array		$total_nodes: Array of DOM nodes
		 */
		function listAllElements(DOMNode $dom) {
			$children = $dom->childNodes;
			$length = $children->length;
			$total_nodes = array();
			for ($i = 0; $i < $length; $i++) {
				$node = $children->item($i);
				$total_nodes[] = $node;
				if ($node->hasChildNodes()) {
					$total_nodes = array_merge($total_nodes, $this->listAllElements($node));
				}
			}
			return $total_nodes;
		}

		/**
		 * Get parent nodes for element.
		 *
		 * @param	object		$domObj: DOMnode Object
		 * @return	array		$parents: parent nodes
		 */
		function getParents(DOMNode $dom) {
			$parents = array();
			$parent = $dom->parentNode;
			if ($parent instanceof DOMNode) {
				$parents[] = $parent;
				$parents = array_merge($parents, $this->getParents($parent));
			}
			return $parents;
		}

		/**
		 * Test if node tag is in list of tagnames.
		 *
		 * @param	object		$node: DOMnode Object
		 * @param	array		$tag_names: tag names
		 * @return	boolean
		 */
		function hasTagNames(DOMNode $node, array $tag_names) {
			$tag_names = array_map('strtolower', $tag_names);
			return in_array($node->tagName, $tag_names, true);
		}

		/**
		 * Test if class name for "no parse" has been set on parent nodes
		 *
		 * @param	object		$nodes: DOMnodes Object
		 * @return	boolean
		 */
		function hasClassName($nodes) {
			$mode = false;

			foreach ($nodes as $node) {
				if ($node->nodeType === 1 && $node->hasAttribute('class')) {
					$class = $node->getAttribute('class');
					if (preg_match($this->conf['noParseClass'], $class)) {
						$mode = true;
						break;
					}
				}
			}
			return $mode;
		}


		/**
		 * Parse DOMDocument and replace catchwords with glossary info
		 *
		 * @param	array		$node: DOM array
		 * @return	void
		 */
		function glossary(DOMNode $node) {
			$parents = $this->getParents($node);

			if (!($node instanceof DOMText && $this->hasTagNames($parents[0], $this->conf['parseTags'] ))) {
				// If the node is NOT a textNode or the textNode is NOT inside a tag
				// defined in $this->conf['parseTags'], then it should not be parsed
				return;
			}
			if ($this->hasClassName($parents)) {
				// If the node is inside a nodechain with className = 'no-glossary', then it should not be parsed
				return;
			}

			$unmatched_nodes = array($node);
			$matched_nodes = array();

			do {
				$match = false;
				foreach ($unmatched_nodes as $node) {
					foreach ($this->glossary as $catchword => $data) {
						$string = $node->data;
						if (preg_match('%(?<=\A|\W)'.preg_quote($catchword).'(?=\z|\W)%iu', $string,$wordMatch,PREG_OFFSET_CAPTURE)) {
							$word = $wordMatch[0][0];
							$length = strlen($word);
							$offset = $wordMatch[0][1];
							if ($this->conf['offsetAdjust']) {
								// correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
								$offset = $GLOBALS['TSFE']->csConvObj->utf8_byte2char_pos($string,$offset);
								$length = $GLOBALS['TSFE']->csConvObj->strlen($this->toCS,$word);
							}

							$matched_node = $node->splitText($offset);
							$unmatched_nodes[] = $matched_node->splitText($length);
							$matched_nodes[] = array($matched_node, $catchword);
							$match = true;
							break 2;
						}
					}
				}
			} while ($match);
			//debug($matched_nodes);
			foreach ($matched_nodes as $matched_node) {
				list($node, $word) = $matched_node;
				$parent = $node->parentNode;
				$new = new DOMElement($this->conf['catchwordWrapTag']);
				$parent->replaceChild($new, $node);
				$new->appendChild($node);
				$new->setAttribute('class', $this->conf['catchwordWrapClass']);

				if ($this->conf['tooltipMode'] === 'ajax') {
					$new->setAttribute('title', $this->cObj->typoLink_URL(array(
						'parameter' => $GLOBALS['TSFE']->id,
						'useCacheHash' => 1,
						'additionalParams' => '&type=52&tx_pmkglossary_pi2[uid]='.$this->glossary[$word]['uid']
					)));
				}
				else {
					$this->cObj->data = $this->glossary[$word];
					$new->setAttribute('title', $this->cObj->cObjGetSingle($this->conf['tooltip'],$this->conf['tooltip.']));
				}
			}
		}

		/**
		 * Initialize Plugin config vars
		 *
		 * @param	array		$conf: The PlugIn configuration
		 * @return	void
		 */
		function init($conf) {
			// Merge local config with config of the pi2 object
			$this->conf = array_merge((array)$conf, $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pmkglossary_pi2.']);
			$this->conf['parseTags'] = preg_split('/\s*,\s*/', strtolower($this->conf['parseTags']));
			$this->conf['noParseClass'] = $this->makeRegExMatch($this->conf['noParseClass']);
			$this->conf['pid_list'] = $this->conf['pid_list'] ? implode(t3lib_div::intExplode(',', $this->conf['pid_list']), ',') : $GLOBALS['TSFE']->id;

			// Charset used for DB records
			$this->fromCS = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->defaultCharSet;
			// Charset used for output
			$this->toCS = $GLOBALS['TSFE']->metaCharset ? $GLOBALS['TSFE']->metaCharset : $GLOBALS['TSFE']->defaultCharSet;
		}

		/**
		 * Creates RexEx ready comparison string
		 *
		 * @param	string		String/word to look for
		 * @return	string		RexEx ready comparison string
		 */
		function makeRegExMatch($string) {
			$array = array_map('preg_quote',preg_split('/\s*,\s*/', $string));
			return '/\b(?=\w)'.implode('|',$array).'\b(?!\w)/';
		}

		/**
		 * Get glossary records from DB, and creates preg_replace array
		 *
		 * @param	void
		 * @return	array		Complete array of glossary records (sorted by length. longest first)
		 */
		function getGlossary() {
			$glossary = array();

			$table = 'tx_pmkglossary_glossary';
			$fields = 'pid,uid,sys_language_uid,title,alttitle,bodytext,image,imagewidth,imageorient';
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
					// $row might be unset in sys_page->getRecordOverlay
					if (!is_array($row)) continue;
					if (!$row['title']) continue;

					// Make sure that data is in UTF-8 format
					if ($this->fromCS != 'utf-8') {
						$row['title'] = utf8_encode($row['title']);
						$row['alttitle'] = utf8_encode($row['alttitle']);
						$row['bodytext'] = utf8_encode($row['bodytext']);
					}
					// Set catchword as key
					$glossary[$row['title']] = $row;

					// Is there any alternate catchwords set in this record?
					if ($row['alttitle']) {
						$alt = t3lib_div::trimExplode(',',$row['alttitle']);
						foreach ($alt as $aTitle) {
							$row['title'] = $aTitle;
							$glossary[$row['title']] = $row;
						}
					}

				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			// Sort glossary array based on length (longest first) of key (title)
			// (Selecting by length is useless when using getRecordOverlay)
			uksort($glossary, array($this, '_len_sort'));

			return $glossary;
		}

		/**
		 * Convert HTML string data into DOM object
		 *
		 * NOTE: Internal DOM format is ALWAYS UTF-8, regardless of the value of
		 *       $this->toCS. Value of $this->toCS is ONLY used when saving data.
		 *
		 * @param	string		$content: HTML content in text format
		 * @return	object		$domObj: DOM Object
		 */
		function HTML2DOM($content) {
			$domObj = new DOMDocument('1.0');
			$domObj->encoding = $this->toCS;
			$domObj->preserveWhiteSpace = false;
			$domObj->substituteEntities = false;
			$domObj->formatOutput = true;
			$content = preg_replace('/\r/', '', $content);
			$content = '<html><head><meta http-equiv="Content-Type" content="text/html; charset='.$this->fromCS.'" /></head><body>'.(preg_match('%.*?<body[^>]*>(.*)</body>%s', $content, $regs) ? $regs[1] : $content).'</body></html>';
			@$domObj->loadHTML($content);
			return $domObj;
		}

		/**
		 * Convert DOM object into HTML string data
		 *
		 * @param	object		$domObj: DOMDocument Object
		 * @return	string		$content: HTML content in text format
		 */
		function DOM2HTML(DOMDocument $domObj) {
			$content = $domObj->saveHTML();
			preg_match('|<body>(.*)</body>|ms', $content, $matches);
			$content = $matches[1];
			return $content;
		}

		/**
		 * Custom sorting callback function
		 *
		 * @param	array		$a: Glossary record
		 * @param	array		$b: glossary record
		 * @return	mixed		-1,0 or 1
		 */
		function _len_sort($a, $b) {
			//$a = $GLOBALS['TSFE']->csConvObj->strlen($this->toCS,$a);
			//$b = $GLOBALS['TSFE']->csConvObj->strlen($this->toCS,$b);
			$a = strlen($a);
			$b = strlen($b);
			return strcmp($b,$a);
		}

	}

	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi2/class.tx_pmkglossary_pi2.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi2/class.tx_pmkglossary_pi2.php']);
	}

?>