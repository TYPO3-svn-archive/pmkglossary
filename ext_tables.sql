#
# Table structure for table 'tx_pmkglossary_glossary'
#
CREATE TABLE tx_pmkglossary_glossary (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l10n_parent int(11) DEFAULT '0' NOT NULL,
    l10n_diffsource mediumtext,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	wordtitle varchar(255) DEFAULT '' NOT NULL,
	alttitle varchar(255) DEFAULT '' NOT NULL,
	bodytext text NOT NULL,
	image text NOT NULL,
	imagewidth int(11) unsigned DEFAULT '0' NOT NULL,
	imageheight int(11) unsigned DEFAULT '0' NOT NULL,
	imageorient tinyint(4) unsigned DEFAULT '0' NOT NULL,
	imagecaption text,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_pmkglossary_no_parsing tinyint(4) unsigned DEFAULT '0' NOT NULL,
);
