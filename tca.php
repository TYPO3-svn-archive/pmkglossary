<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_pmkglossary_glossary'] = Array (
	'ctrl' => $TCA['tx_pmkglossary_glossary']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,catchword,catchword_desc,image,imagewidth,imageorient'
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
		'starttime' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'catchword' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:pmkglossary/locallang_db.php:tx_pmkglossary_glossary.catchword',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'catchword_desc' => Array (
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:pmkglossary/locallang_db.php:tx_pmkglossary_glossary.catchword_desc',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '4',
                'wizards' => array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'type'          => 'script',
                        'title'         => 'LLL:EXT:pmkglossary/locallang_db.php:tx_pmkglossary_glossary.catchword_desc',
                        'icon'          => 'wizard_rte2.gif',
                        'script'        => 'wizard_rte.php',
                    ),
                ),
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
		'imageorient' => Array (
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
		'0' => Array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden, catchword, catchword_desc;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_pmkglossary/rte/], image, imageorient, imagewidth')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'starttime, endtime, fe_group'),
		'2' => Array('showitem' => 'imagewidth, imageorient')
	)
);

?>