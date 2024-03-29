<?php

########################################################################
# Extension Manager/Repository config file for ext "pmkglossary".
#
# Auto generated 11-04-2010 21:59
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'PMK Glossary',
	'description' => 'Parses the output HTML and automatically inserts glossary definitions. Glossary links opens up a AJAX-based tooltipbox showing the definition. Supports multiple languages. Also includes FE plugin for displaying the entire glossary.',
	'category' => 'plugin',
	'author' => 'Peter Klein',
	'author_email' => 'pmk@io.dk',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => 'mr_parseglossary',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.5.7',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.2.0-4.99.99',
			'php' => '5.2.0-10.0.0',
		),
		'conflicts' => array(
			'mr_parseglossary' => '0.0.0-',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:46:{s:9:"ChangeLog";s:4:"b164";s:10:"README.txt";s:4:"ee2d";s:20:"class.ext_update.php";s:4:"8271";s:34:"class.tx_pmkglossary_extraeval.php";s:4:"59d1";s:34:"class.tx_pmkglossary_wordtitle.php";s:4:"a763";s:21:"ext_conf_template.txt";s:4:"ba68";s:12:"ext_icon.gif";s:4:"0e18";s:17:"ext_localconf.php";s:4:"54fc";s:15:"ext_php_api.dat";s:4:"a61e";s:14:"ext_tables.php";s:4:"0e89";s:14:"ext_tables.sql";s:4:"70f6";s:34:"folder_tx_pmkglossary_glossary.gif";s:4:"f696";s:32:"icon_tx_pmkglossary_glossary.gif";s:4:"a05a";s:13:"locallang.xml";s:4:"145c";s:17:"locallang_csh.xml";s:4:"3f31";s:16:"locallang_db.xml";s:4:"8191";s:7:"tca.php";s:4:"bdb0";s:14:"doc/manual.sxw";s:4:"fb15";s:14:"pi1/ce_wiz.gif";s:4:"0bc8";s:32:"pi1/class.tx_pmkglossary_pi1.php";s:4:"1ece";s:40:"pi1/class.tx_pmkglossary_pi1_wizicon.php";s:4:"d1dc";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"3532";s:29:"pi1/static/ajax/constants.txt";s:4:"e98e";s:25:"pi1/static/ajax/setup.txt";s:4:"10f2";s:31:"pi1/static/static/constants.txt";s:4:"4b85";s:27:"pi1/static/static/setup.txt";s:4:"5b7a";s:32:"pi2/class.tx_pmkglossary_pi2.php";s:4:"8f5b";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"13a1";s:31:"pi2/static/parser/constants.txt";s:4:"b8bb";s:27:"pi2/static/parser/setup.txt";s:4:"8448";s:20:"res/glossary_ajax.js";s:4:"57f6";s:23:"res/jquery-1.4.2.min.js";s:4:"1009";s:29:"res/jquery.simpletip-1.3.1.js";s:4:"b20b";s:33:"res/jquery.simpletip-1.3.1.min.js";s:4:"2da2";s:34:"res/pmkglossary_template_ajax.html";s:4:"88c9";s:36:"res/pmkglossary_template_static.html";s:4:"047b";s:23:"res/realurl_example.txt";s:4:"ca4c";s:21:"res/simpletip_ajax.js";s:4:"50a7";s:23:"res/simpletip_static.js";s:4:"98ae";s:31:"res/images/accordion_tab_bg.png";s:4:"7692";s:27:"res/images/ajax-loader1.gif";s:4:"6ca2";s:27:"res/images/ajax-loader2.gif";s:4:"95d6";s:25:"res/images/fancypants.gif";s:4:"b6d6";s:25:"res/images/noglossary.png";s:4:"cc5a";}',
	'suggests' => array(
	),
);

?>