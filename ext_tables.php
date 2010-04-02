<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_pmkglossary_glossary');

t3lib_extMgm::addToInsertRecords('tx_pmkglossary_glossary');

	// get extension configuration
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

$TCA['tx_pmkglossary_glossary'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:pmkglossary/locallang_db.php:tx_pmkglossary_glossary',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title asc',
		'delete' => 'deleted',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dividers2tabs' => $confArr['dividers2tabs'],
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_pmkglossary_glossary.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, title, wordtitle, alttitle, bodytext, image, imagewidth, imageheight, imageorient, imagecaption',
	)
);

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';

$tempColumns = Array (
	'tx_pmkglossary_no_parsing' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:pmkglossary/locallang_db.php:pages.tx_pmkglossary_no_parsing',
		'config' => Array (
			'type' => 'check',
		)
	),
);
t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_pmkglossary_no_parsing;;;;1-1-1','','after:nav_title');
// add "context sensitive help" (csh)
t3lib_extMgm::addLLrefForTCAdescr('pages','EXT:pmkglossary/locallang_csh.xml');
t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:pmkglossary/locallang_db.xml:tt_content.list_type_pi1',
		$_EXTKEY . '_pi1',
		t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
	),
	'list_type'
);

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_pmkglossary_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_pmkglossary_pi1_wizicon.php';
	if ($confArr['customFolder']) {
		$TCA['pages']['columns']['module']['config']['items'][] = array(
			'LLL:EXT:'.$_EXTKEY.'/locallang_db.xml:pages.module.glossary',
			'glossary',
			t3lib_extMgm::extRelPath($_EXTKEY).'/folder_tx_pmkglossary_glossary.gif'
		);
		$GLOBALS['ICON_TYPES']['glossary'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'/folder_tx_pmkglossary_glossary.gif');
	}
}

t3lib_extMgm::addStaticFile($_EXTKEY,'pi2/static/parser/', 'PMK Glossary (parser)');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/ajax/', 'PMK Glossary Ajax (plugin)');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/static/', 'PMK Glossary Static (plugin)');

// add "context sensitive help" (csh)
t3lib_extMgm::addLLrefForTCAdescr('tx_pmkglossary_glossary','EXT:pmkglossary/locallang_csh.xml');
?>