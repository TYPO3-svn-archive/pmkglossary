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
');
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_pmkglossary_pi1.php', '_pi1', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_pmkglossary_pi2.php', '_pi2', '', 1);
?>