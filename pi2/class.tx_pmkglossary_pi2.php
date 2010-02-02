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
	*   53: class tx_pmkglossary_pi2 extends tslib_pibase
	*   66:     function main($content, $conf)
	*  111:     function init($conf)
	*  125:     function parseDOM($node)
	*  154:     function getGlossary()
	*  194:     function HTML2DOM($content)
	*  209:     function DOM2HTML(DOMDocument $domObj)
	*  224:     function xmltoxhtml($content)
	*
	* TOTAL FUNCTIONS: 7
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
		var $extKey = 'pmkglossary'; // The extension key.
		var $pi_checkCHash = true;
		var $conf;		// Plugin config options
		var $fromCS;	// Charset used when accessing DB data
		var $toCS;		// Charset used for output in browser

		/**
		* The main method of the PlugIn
		*
		* @param string		$content: The content that nshould be parsed for catchwords
		* @param array		$conf: The PlugIn configuration
		* @return string	The content that is displayed on the website
		*/
		function main($content, $conf) {
			$this->init($conf);

			// Page is excluded from parsing.
			if ($GLOBALS['TSFE']->page['tx_pmkglossary_no_parsing'] || t3lib_div::inList($this->conf['noParsePages'],$GLOBALS['TSFE']->id)) {
				return $content;
			};

/*
			debug(array(
				'TSFE->sys_language_uid' => $GLOBALS['TSFE']->sys_language_uid,
				'config.sys_language_uid' => $GLOBALS['TSFE']->config['config']['sys_language_uid'],
				'TSFE->sys_language_mode' => $GLOBALS['TSFE']->sys_language_mode,
				'config.sys_language_mode' => $GLOBALS['TSFE']->config['config']['sys_language_mode'],
				'TSFE->sys_language_content' => $GLOBALS['TSFE']->sys_language_content,
				'TSFE->sys_language_contentOL' => $GLOBALS['TSFE']->sys_language_contentOL
			),'Language Options');

			debug(array(
				'forceCharset' => $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'],
				'multiplyDBfieldSize' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['multiplyDBfieldSize'],
				'setDBinit' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit'],
				'UTF8filesystem' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem'],
				'defaultCharSet' => $GLOBALS['TSFE']->defaultCharSet,
				'renderCharset' => $GLOBALS['TSFE']->renderCharset,
				'metaCharset' => $GLOBALS['TSFE']->metaCharset
			),'Encoding');
*/

			if ($this->conf['debug']) {
				// Set start time
				$timer = time() + microtime();
			}

			$this->glossary = $this->getGlossary();

			$domObj = $this->HTML2DOM($content);
			$this->domObj = $domObj;
			$this->processDom($domObj);

			$content = $this->DOM2HTML($domObj);

			if ($this->conf['debug']) {
				// Subtract start time from current time and add it to output
				$timer = time() + microtime()-$timer;
				$content .= '<div>Glossary parsetime: '.$timer.'</div>';
			}

			return $content;
		}

		function processDom(DOMDocument $dom) {
			array_map(array($this, 'convertDOMCdataSectionToDOMText'), $this->listAllElements($dom));
			array_map(array($this, 'glossary'), $this->listAllElements($dom));
		}

		function convertDOMCdataSectionToDOMText(DOMNode $node) {
			if ($node instanceof DOMCdataSection) {
				$new = new DOMText($node->data);
				$node->parentNode->replaceChild($new, $node);
			}
		}

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

		function getParents(DOMNode $dom) {
			$parents = array();
			$parent = $dom->parentNode;
			if ($parent instanceof DOMNode) {
				$parents[] = $parent;
				$parents = array_merge($parents, $this->getParents($parent));
			}
			return $parents;
		}

		function hasTagNames(DOMNode $node, array $tag_names) {
			$tag_names = array_map('strtolower', $tag_names);
			return in_array($node->tagName, $tag_names, true);
		}

		function hasClassName($nodes) {
			$mode = false;

			foreach ($nodes as $node) {
				if ($node->nodeType == 1 && $node->hasAttribute('class')) {
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
		* @param array  $node: DOM array
		* @return void
		*/
		function glossary(DOMNode $node) {
			$parents = $this->getParents($node);

			/*
			$path = '';
			foreach (array_reverse($parents) as $parent) {
				$path.= '->'.$parent->nodeName;
			}
			debug($path,$node->nodeValue);
			*/

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
debug($this->isUTF8($string),$string);
debug($this->isUTF8($catchword),$catchword);
						//if (preg_match('%\b'.preg_quote($catchword).'\b%iu',$string,$match,PREG_OFFSET_CAPTURE)) {
						if (preg_match('%(?<=\A|\W)'.preg_quote($catchword).'(?=\z|\W)%iu', $string,$match,PREG_OFFSET_CAPTURE)) {
							$word = $match[0][0];
							$offset = $match[0][1];

							// correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
							// we fix this by recounting the text before the offset using multi-byte aware `strlen`
							//$offset = intval(mb_strlen(substr($string, 0, $offset), $this->toCS));
							//$offset = intval($GLOBALS['TSFE']->csConvObj->strlen($this->toCS,substr($string, 0, $offset)));
							//$offset = $GLOBALS['TSFE']->csConvObj->utf8_byte2char_pos($string,$offset);


//debug(array($offset,$offset2),'offsets '.$this->toCS);

							$length=strlen($word);
							//$length = $GLOBALS['TSFE']->csConvObj->strlen($this->toCS,$word);
//debug(array($length,$length2),'lengths '.$word);

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
				$new->setAttribute('title', $this->getTitle($this->glossary[$word]));
			}
		}

		function getTitle($row) {
			$title = $row['catchword_desc'];
			// Make sure that DB data matches that of rendering.
			//$title = $GLOBALS['TSFE']->csConvObj->conv($title,$this->toCS,$this->fromCS,1);
			$title = $this->pi_RTEcssText($title);
			if ($row['image'] != '') {
				$this->cObj->data = $row;
				$image = $this->cObj->cObjGetSingle($this->conf['image'],$this->conf['image.']);
				switch ($row['imageorient']) {
					case 0:
						$title = '<div style="text-align:center;">'.$image.'</div>'.$title;
						break;
					case 1:
						$title = '<div style="text-align:right;">'.$image.'</div>'.$title;
						break;
					case 2:
						$title = '<div style="text-align:left;">'.$image.'</div>'.$title;
						break;
					break;
					case 8:
						$title = $title.'<div style="text-align:center;">'.$image.'</div>';
						break;
					case 9:
						$title = $title.'<div style="text-align:right;">'.$image.'</div>';
						break;
					case 10:
						$title = $title.'<div style="text-align:left;">'.$image.'</div>';
						break;
					break;
					case 17:
						$title = '<div style="float:left;margin:0 5px 0 5px;">'.$image.'</div>'.$title;
						break;
					case 18:
						$title = '<div style="float:right;margin:0 0 5px 5px;">'.$image.'</div>'.$title;
						break;
				}
			}
			$title = $GLOBALS['TSFE']->csConvObj->conv($title,$this->fromCS,$this->toCS,1);
			return $title;
		}
		/**
		* Initialize Plugin config vars
		*
		* @param array  $conf: The PlugIn configuration
		* @return void
		*/
		function init($conf) {
			// Merge conf with that of the pi1 plugin since pi2 has no own config
			$this->conf = array_merge((array)$conf, $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pmkglossary_pi1.']);
			$this->conf['parseTags'] = preg_split('/\s*,\s*/', strtolower($this->conf['parseTags']));
			$this->conf['noParseClass'] = $this->makeRegExMatch($this->conf['noParseClass']);
			$this->conf['pid_list'] = $this->conf['pid_list'] ? implode(t3lib_div::intExplode(',', $this->conf['pid_list']), ',') : $GLOBALS['TSFE']->id;

			$this->fromCS = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->defaultCharSet;
			$this->toCS = $GLOBALS['TSFE']->metaCharset ? $GLOBALS['TSFE']->metaCharset : $GLOBALS['TSFE']->defaultCharSet;
		}

		function makeRegExMatch($string) {
			$array = array_map('preg_quote',preg_split('/\s*,\s*/', $string));
			return '/\b(?=\w)'.implode('|',$array).'\b(?!\w)/';
		}

		/**
		* Get glossary records from DB, and creates preg_replace array
		*
		* @param void
		* @return array  Complete array of glossary records (sorted by length. longest first)
		*/
		function getGlossary() {
			$glossary = array();

			$table = 'tx_pmkglossary_glossary';
			$fields = '*';
			//$where = 'pid='. intval($GLOBALS['TSFE']->id) .' OR pid='.intval($this->conf['pid_list']).' AND sys_language_uid IN (-1,0) '.$this->cObj->enableFields($table);
			$where = '(pid='. intval($GLOBALS['TSFE']->id) .' OR pid IN ('.$this->conf['pid_list'].')) AND (sys_language_uid IN (-1,0) OR (sys_language_uid='.$GLOBALS['TSFE']->sys_language_uid.' AND l10n_parent=0)) '.$this->cObj->enableFields($table);

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					// get the translated record if the content language is not the default language
					if ($GLOBALS['TSFE']->sys_language_content) {
						$OLmode = $GLOBALS['TSFE']->sys_language_contentOL ? 'hideNonTranslated' : '';
						if (!($row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $row, $GLOBALS['TSFE']->sys_language_uid, $OLmode))) {
							continue;
						}
					}
					// Convert catchword from DB charset to render charset.
					//$key = $GLOBALS['TSFE']->csConvObj->conv($key,$this->fromCS,$this->toCS,1);
					$key = $row['catchword'];
					$glossary[$key] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			// Sort array based on length of catchword (longest first)
			// (Selecting by length is useless when using getRecordOverlay)
			uksort($glossary, array($this, '_len_sort'));

			return array_reverse($glossary);
		}

		/**
		* convert HTML string data into DOM object
		*
		* @param string  $content: HTML content in text format
		* @return object  $domObj: DOM Object
		*/
		function HTML2DOM($content) {
			$domObj = new DOMDocument('1.0');
			$domObj->preserveWhiteSpace = false;
			$domObj->substituteEntities = false;
			$domObj->formatOutput = true;
			$content = preg_replace('/\r/', '', $content);
			//$content = ($this->fromCS==$this->toCS) ? $content : $GLOBALS['TSFE']->csConvObj->conv($content,$this->fromCS,$this->toCS,1);
			//$content = ($this->fromCS==$this->toCS) ? $content : $GLOBALS['TSFE']->csConvObj->conv($content,$this->toCS,$this->fromCS,1);
			$content = '<html><head><meta http-equiv="Content-Type" content="text/html; charset='.$this->fromCS.'" /></head><body>'.(preg_match('%.*?<body[^>]*>(.*)</body>%s', $content, $regs) ? $regs[1] : $content).'</body></html>';
//debug($content,'utf'.$this->isUTF8($content));

			@$domObj->loadHTML($content);
			return $domObj;
		}

		/**
		* convert DOM object into HTML string data
		*
		* @param object  $domObj: DOM Object
		* @return string  $content: HTML content in text format
		*/
		function DOM2HTML(DOMDocument $domObj) {
			//$content = $domObj->saveXML($domObj, LIBXML_NOEMPTYTAG);
			//$content = $this->xmltoxhtml($content);
			$content = $domObj->saveHTML();
			preg_match('|<body>(.*)</body>|ms', $content, $matches);
			$content = $matches[1];
			return $content;
		}

		/**
		* Converts empty HTML tags into XHTML
		*
		* @param string  $content: HTML content in text format
		* @return string  $content: XHTML content in text format
		*/
		function xmltoxhtml($content) {
			return preg_replace('%></(area|basefont|base|br|hr|img|input|link|meta)>%i', '/>', $content);
		}

		/**
		 * Custom sorting callback function
		 *
		 * @param	array		$a: Glossary record
		 * @param	array		$b: glossary record
		 * @return	mixed		-1,0 or 1
		 */
		function _len_sort($a, $b) {
			$a = $GLOBALS['TSFE']->csConvObj->strlen($this->toCS,$a);
			$b = $GLOBALS['TSFE']->csConvObj->strlen($this->toCS,$b);
			return strcmp($a,$b);
		}

		/**
		 * Check if string is in UTF-8 format
		 *
		 * @param	array	string to check
		 * @return	boolean	true if string is valid utf-8
		 */
		function isUTF8($str) {
			return preg_match('/\A(?:([\09\0A\0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*)\Z/x', $str);
		}
	}


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi2/class.tx_pmkglossary_pi2.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkglossary/pi2/class.tx_pmkglossary_pi2.php']);
	}

?>
