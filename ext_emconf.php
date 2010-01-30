<?php

########################################################################
# Extension Manager/Repository config file for ext "pmkglossary".
#
# Auto generated 30-01-2010 21:09
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'PMK Glossary',
	'description' => 'PMK Glossary',
	'category' => 'plugin',
	'author' => 'Peter Klein',
	'author_email' => 'pmk@io.dk',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => 'mr_parseglossary',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.13',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
			'mr_parseglossary' => '0.0.0-',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:32:{s:9:"ChangeLog";s:4:"b164";s:10:"README.txt";s:4:"ee2d";s:20:"class.ext_update.php";s:4:"4317";s:12:"ext_icon.gif";s:4:"0e18";s:17:"ext_localconf.php";s:4:"058b";s:15:"ext_php_api.dat";s:4:"ee21";s:14:"ext_tables.php";s:4:"407d";s:14:"ext_tables.sql";s:4:"88bc";s:32:"icon_tx_pmkglossary_glossary.gif";s:4:"a05a";s:13:"locallang.xml";s:4:"3759";s:17:"locallang_csh.xml";s:4:"bf82";s:16:"locallang_db.xml";s:4:"c71e";s:7:"tca.php";s:4:"8eda";s:19:"doc/wizard_form.dat";s:4:"b677";s:20:"doc/wizard_form.html";s:4:"698b";s:14:"pi1/ce_wiz.gif";s:4:"0bc8";s:32:"pi1/class.tx_pmkglossary_pi1.php";s:4:"7ab7";s:40:"pi1/class.tx_pmkglossary_pi1_wizicon.php";s:4:"d1dc";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"efef";s:32:"pi2/class.tx_pmkglossary_pi2.php";s:4:"dd2f";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"13a1";s:18:"res/img-loader.gif";s:4:"4889";s:23:"res/jquery-1.3.2.min.js";s:4:"bb38";s:29:"res/jquery.msAccordion.min.js";s:4:"dd71";s:18:"res/noglossary.png";s:4:"cc5a";s:23:"res/vTip_v2/vtip-min.js";s:4:"7ed5";s:19:"res/vTip_v2/vtip.js";s:4:"676c";s:33:"res/vTip_v2/images/vtip_arrow.png";s:4:"528d";s:33:"static/pmk_glossary/constants.txt";s:4:"ccc4";s:29:"static/pmk_glossary/setup.txt";s:4:"3950";}',
	'suggests' => array(
	),
);

?>