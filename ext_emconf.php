<?php

########################################################################
# Extension Manager/Repository config file for ext "pmkglossary".
#
# Auto generated 14-03-2010 14:18
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
	'version' => '0.5.0',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
			'mr_parseglossary' => '0.0.0-',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:48:{s:9:"ChangeLog";s:4:"b164";s:10:"README.txt";s:4:"ee2d";s:20:"class.ext_update.php";s:4:"4adb";s:34:"class.tx_pmkglossary_extraeval.php";s:4:"d75e";s:34:"class.tx_pmkglossary_wordtitle.php";s:4:"6777";s:21:"ext_conf_template.txt";s:4:"cd05";s:12:"ext_icon.gif";s:4:"0e18";s:17:"ext_localconf.php";s:4:"12ef";s:15:"ext_php_api.dat";s:4:"095f";s:14:"ext_tables.php";s:4:"601e";s:14:"ext_tables.sql";s:4:"d81a";s:34:"folder_tx_pmkglossary_glossary.gif";s:4:"f696";s:32:"icon_tx_pmkglossary_glossary.gif";s:4:"a05a";s:13:"locallang.xml";s:4:"c090";s:17:"locallang_csh.xml";s:4:"4568";s:16:"locallang_db.xml";s:4:"7662";s:7:"tca.php";s:4:"b084";s:14:"doc/manual.sxw";s:4:"273c";s:19:"doc/wizard_form.dat";s:4:"b677";s:20:"doc/wizard_form.html";s:4:"698b";s:14:"pi1/ce_wiz.gif";s:4:"0bc8";s:32:"pi1/class.tx_pmkglossary_pi1.php";s:4:"f3a3";s:40:"pi1/class.tx_pmkglossary_pi1_wizicon.php";s:4:"d1dc";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"3532";s:29:"pi1/static/ajax/constants.txt";s:4:"0a77";s:25:"pi1/static/ajax/setup.txt";s:4:"7fc0";s:31:"pi1/static/static/constants.txt";s:4:"c102";s:27:"pi1/static/static/setup.txt";s:4:"b9f1";s:32:"pi2/class.tx_pmkglossary_pi2.php";s:4:"70b2";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"13a1";s:31:"pi2/static/parser/constants.txt";s:4:"e3aa";s:27:"pi2/static/parser/setup.txt";s:4:"f7fe";s:14:"res/folder.png";s:4:"1799";s:20:"res/glossary_ajax.js";s:4:"73e6";s:23:"res/jquery-1.4.1.min.js";s:4:"9eb3";s:29:"res/jquery.simpletip-1.3.1.js";s:4:"b20b";s:33:"res/jquery.simpletip-1.3.1.min.js";s:4:"2da2";s:34:"res/pmkglossary_template_ajax.html";s:4:"88c9";s:36:"res/pmkglossary_template_static.html";s:4:"047b";s:21:"res/simpletip_ajax.js";s:4:"f3ef";s:23:"res/simpletip_static.js";s:4:"d9b7";s:31:"res/images/accordion_tab_bg.png";s:4:"7692";s:27:"res/images/ajax-loader1.gif";s:4:"6ca2";s:27:"res/images/ajax-loader2.gif";s:4:"95d6";s:25:"res/images/fancypants.gif";s:4:"b6d6";s:25:"res/images/noglossary.png";s:4:"cc5a";}',
	'suggests' => array(
	),
);

?>