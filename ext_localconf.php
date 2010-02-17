<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_pmkglossary_glossary=1');

t3lib_extMgm::addPageTSConfig('

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

# Special TinyMCE RTE config.
RTE.tx_pmkglossary_glossary < RTE.default
RTE.tx_pmkglossary_glossary.init {
	theme_advanced_buttons1 = undo,redo,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,fullscreen,|,cleanup,nonbreaking
	theme_advanced_buttons2 = link,typo3link,unlink,|,tablecontrols
	theme_advanced_buttons3 = code,|,anchor,charmap,attribs,styleprops,|,forecolor,backcolor,strikethrough,sub,sup,|,bullist,numlist,|,outdent,indent,|,blockquote
	theme_advanced_buttons4 = styleselect,|,formatselect,|,fontselect,|,fontsizeselect,|,bold,italic,underline
	height = 350
}


');
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_pmkglossary_pi1.php', '_pi1', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_pmkglossary_pi2.php', '_pi2', '', 1);
?>