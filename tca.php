<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_pmkglossary_glossary'] = Array (
	'ctrl' => $TCA['tx_pmkglossary_glossary']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,title,wordtitle,alttitle,bodytext,image,imagewidth,imageheight,imageorient'
	),
	'feInterface' => $TCA['tx_pmkglossary_glossary']['feInterface'],
	'columns' => Array (
		'sys_language_uid' => array (
            'exclude' => 1,
            'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array (
                'type'                => 'select',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l10n_parent' => array (
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config'      => array (
                'type'  => 'select',
                'items' => array (
                    array('', 0),
                ),
                'foreign_table'       => 'tx_pmkglossary_glossary',
                'foreign_table_where' => 'AND tx_pmkglossary_glossary.pid=###CURRENT_PID### AND tx_pmkglossary_glossary.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array (
            'config' => array (
                'type' => 'passthrough'
            )
        ),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:pmkglossary/locallang_db.xml:tx_pmkglossary_glossary.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'tx_pmkglossary_extraeval,required,trim',
				'is_in' => ',',
			)
		),
		'wordtitle' => array (
			'config' => array (
				'type' => 'passthrough',
				//'type' => 'input',
				'size' => '30',
				'readOnly' => '1',
			)
		),
		'alttitle' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:pmkglossary/locallang_db.xml:tx_pmkglossary_glossary.alttitle',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'tx_pmkglossary_extraeval,trim',
			)
		),
		'bodytext' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:pmkglossary/locallang_db.xml:tx_pmkglossary_glossary.bodytext',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '4'
			)
		),
        'image' => array (
            'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.image',
            'config' => array (
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'uploads/tx_pmkglossary',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
		'imagewidth' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:cms/locallang_ttc.xml:imagewidth',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'range' => Array (
					'upper' => '999',
					'lower' => '25'
				),
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'imageheight' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:cms/locallang_ttc.xml:imageheight',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'range' => Array (
					'upper' => '999',
					'lower' => '25'
				),
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'imageorient' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:cms/locallang_ttc.xml:imageorient',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.0', 0, 'selicons/above_center.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.1', 1, 'selicons/above_right.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.2', 2, 'selicons/above_left.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.3', 8, 'selicons/below_center.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.4', 9, 'selicons/below_right.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.5', 10, 'selicons/below_left.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.6', 17, 'selicons/intext_right.gif'),
					Array('LLL:EXT:cms/locallang_ttc.xml:imageorient.I.7', 18, 'selicons/intext_left.gif'),
				),
				'selicon_cols' => 6,
				'default' => '0',
				'iconsInOptionTags' => 1,
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => '--div--;LLL:EXT:pmkglossary/locallang_db.xml:tx_pmkglossary_glossary.tab1,sys_language_uid;;1;;1-1-1, hidden, title, wordtitle, alttitle, bodytext;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_pmkglossary/rte/], --div--;LLL:EXT:pmkglossary/locallang_db.xml:tx_pmkglossary_glossary.tab2,image;;;;2-2-2, imageorient, imagewidth;;2;;')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'l10n_parent, l10n_diffsource'),
		'2' => Array('showitem' => 'imageheight')
	)
);

?>