<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
	// get extension configuration
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

if ($confArr['unique']) {
	// Enable custom TCA field evaluation
	$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_pmkglossary_extraeval'] = 'EXT:'.$_EXTKEY.'/class.tx_pmkglossary_extraeval.php';
}

// Enable hook after saving glossary element
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:'.$_EXTKEY.'/class.tx_pmkglossary_wordtitle.php:&tx_pmkglossary_wordtitle';


t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_pmkglossary_glossary=1');

if (t3lib_extMgm::isLoaded('tinymce_rte')) {
	t3lib_extMgm::addPageTSConfig('
    # ***************************************************************************************
    # CONFIGURATION of TinyMCE_RTE in table "tx_pmkglossary_glossary"
    # ***************************************************************************************
RTE.tx_pmkglossary_glossary < RTE.default
RTE.tx_pmkglossary_glossary.init {
	theme_advanced_buttons1 = undo,redo,|,justifyleft,justifycenter,justifyright,justifyfull,|cut,copy,paste,pastetext,pasteword,|,search,replace,|,fullscreen,|,cleanup,nonbreaking,charmap,|,code
	theme_advanced_buttons2 = link,typo3link,unlink,anchor,|,strikethrough,bold,italic,underline,sub,sup,|,bullist,numlist,|,outdent,indent,|,blockquote
	theme_advanced_buttons3 =
	theme_advanced_buttons4 =
	height = 300
}
RTE.default.init.theme_advanced_styles := addToList(No Glossary=no-glossary)
RTE.default.proc.allowedClasses := addToList(no-glossary)
');
}
else {
	t3lib_extMgm::addPageTSConfig('
RTE.classes.no-glossary {
	name = No Glossary
	value = background-color: red;
}
RTE.default.classesCharacter := addToList(no-glossary)
RTE.default.classesParagraph := addToList(no-glossary)
RTE.default.proc.allowedClasses := addToList(no-glossary)
    # ***************************************************************************************
    # CONFIGURATION of RTE in table "tx_pmkglossary_glossary", field "bodytext"
    # ***************************************************************************************
RTE.config.tx_pmkglossary_glossary.bodytext {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');
}
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_pmkglossary_pi1.php', '_pi1', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_pmkglossary_pi2.php', '_pi2', '', 1);
?>