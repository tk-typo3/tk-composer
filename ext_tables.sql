CREATE TABLE tx_tkcomposer_domain_model_account (
	username varchar(255) DEFAULT '' NOT NULL,
	password varchar(255) DEFAULT '' NOT NULL,
	all_packages smallint(5) unsigned DEFAULT '0' NOT NULL,
	package_groups int(11) unsigned DEFAULT '0' NOT NULL,
	packages int(11) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE tx_tkcomposer_domain_model_package (
	repository_url varchar(255) DEFAULT '' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	access int(11) DEFAULT '0' NOT NULL,
	relation int(11) DEFAULT '0' NOT NULL,
	hash varchar(255) DEFAULT '' NOT NULL,
	latest_tag varchar(255) DEFAULT '' NOT NULL,
	package_name varchar(255) DEFAULT '' NOT NULL,
	description text,
	tags_status text

);

CREATE TABLE tx_tkcomposer_domain_model_packagegroup (

	name varchar(255) DEFAULT '' NOT NULL,
	packages int(11) unsigned DEFAULT '0' NOT NULL

);

CREATE TABLE tx_tkcomposer_account_packagegroup_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_tkcomposer_account_package_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_tkcomposer_packagegroup_package_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

-- Limit varchar length to 190 to avoid errors on MySQL lower v5.7 with utf8mb4 encoding, where 767 bytes is the limit
-- @see https://dev.mysql.com/doc/refman/5.6/en/create-index.html#create-index-column-prefixes
CREATE TABLE tx_tkcomposer_domain_model_package (
	repository_url varchar(190) DEFAULT '' NOT NULL,

	UNIQUE INDEX repository_url (repository_url)
);

-- Add account relations on package side
CREATE TABLE tx_tkcomposer_domain_model_package (
	accounts int(11) unsigned DEFAULT '0' NOT NULL
);

-- Add package group relations on package side
CREATE TABLE tx_tkcomposer_domain_model_package (
	package_groups int(11) unsigned DEFAULT '0' NOT NULL
);

-- Add account relations on package group side
CREATE TABLE tx_tkcomposer_domain_model_packagegroup (
	accounts int(11) unsigned DEFAULT '0' NOT NULL
);
