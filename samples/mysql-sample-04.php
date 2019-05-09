<?php

/** @license
 *  Copyright 2009-2011 Rafael Gutierrez Martinez
 *  Copyright 2012-2013 Welma WEB MKT LABS, S.L.
 *  Copyright 2014-2016 Where Ideas Simply Come True, S.L.
 *  Copyright 2017 nabu-3 Group
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

use nabu\lexer\CNabuLexer;

use nabu\lexer\data\CNabuLexerData;

require_once 'vendor/autoload.php';

$lexer = CNabuLexer::getLexer(CNabuLexer::GRAMMAR_MYSQL, '5.7');
$lexer->setData(new CNabuLexerData());

$sample = <<<'SQL'
CREATE TABLE `nb_user` (
  `nb_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nb_customer_id` int(11) DEFAULT NULL,
  `nb_user_prescriber_id` int(11) DEFAULT NULL,
  `nb_user_medioteca_id` int(11) DEFAULT NULL,
  `nb_user_medioteca_item_id` int(11) DEFAULT NULL,
  `nb_user_search_visibility` enum('P','F','N') NOT NULL DEFAULT 'N',
  `nb_user_login` varchar(64) NOT NULL,
  `nb_user_passwd` varchar(32) NOT NULL,
  `nb_user_validation_status` enum('T','B','F','D','I','P') NOT NULL DEFAULT 'F' COMMENT 'T = Enabled\nB = Banned\nF = Pending to enable without invitation\nD = Disabled\nI = Invited\nP = Pending to enable with invitation',
  `nb_user_policies_accepted` enum('T','F') NOT NULL DEFAULT 'F',
  `nb_user_tester` enum('T','F') NOT NULL DEFAULT 'F'
)
SQL;
/*,
  `nb_user_creation_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nb_user_last_update_datetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `nb_user_activation_datetime` datetime DEFAULT NULL,
  `nb_user_policies_datetime` datetime DEFAULT NULL,
  `nb_user_alive` mediumtext,
  `wgeo_country_id` int(11) DEFAULT NULL,
  `nb_user_storage_id` varchar(20) DEFAULT NULL,
  `nb_user_first_name` varchar(200) DEFAULT NULL,
  `nb_user_last_name` varchar(200) DEFAULT NULL,
  `nb_user_fiscal_number` varchar(20) DEFAULT NULL,
  `nb_user_birth_date` date DEFAULT NULL,
  `nb_user_address_1` text,
  `nb_user_address_2` text,
  `nb_user_zip_code` varchar(15) DEFAULT NULL,
  `nb_user_city_name` text,
  `nb_user_province_name` text,
  `nb_user_country_name` text,
  `nb_user_telephone_prefix` varchar(5) DEFAULT NULL,
  `nb_user_telephone` varchar(20) DEFAULT NULL,
  `nb_user_cellular_prefix` varchar(5) DEFAULT NULL,
  `nb_user_cellular` varchar(20) DEFAULT NULL,
  `nb_user_fax_prefix` varchar(5) DEFAULT NULL,
  `nb_user_fax` varchar(20) DEFAULT NULL,
  `nb_user_cellular_push_key` text,
  `nb_user_email` text NOT NULL,
  `nb_user_web` text,
  `nb_user_work_centre` text,
  `nb_user_work_position` text,
  `nb_user_work_city` text,
  `nb_user_about` text,
  `nb_user_new_email` text,
  `nb_user_allow_notification` enum('T','F') NOT NULL DEFAULT 'F',
  PRIMARY KEY (`nb_user_id`),
  UNIQUE KEY `nb_user_nb_user_login_unx` (`nb_customer_id`,`nb_user_login`),
  UNIQUE KEY `nb_user_nb_user_email_unx` (`nb_customer_id`,`nb_user_email`(200)),
  KEY `nb_user_nb_user_cellular_idx` (`nb_user_cellular`),
  KEY `nb_user_nb_customer_fk` (`nb_customer_id`),
  KEY `nb_user_nb_medioteca_fk` (`nb_user_medioteca_id`),
  KEY `nb_user_nb_medioteca_item_fk` (`nb_user_medioteca_item_id`),
  KEY `wgeo_country_fk` (`wgeo_country_id`),
  FULLTEXT KEY `nb_user_fulltext` (`nb_user_first_name`,`nb_user_last_name`,`nb_user_city_name`,`nb_user_province_name`,`nb_user_email`,`nb_user_login`,`nb_user_fiscal_number`,`nb_user_address_1`,`nb_user_address_2`,`nb_user_zip_code`,`nb_user_web`,`nb_user_telephone`,`nb_user_cellular`,`nb_user_fax`,`nb_user_work_centre`,`nb_user_work_city`) KEY_BLOCK_SIZE=1024
) ENGINE=MyISAM AUTO_INCREMENT=7210 DEFAULT CHARSET=utf8
SQL; */

$rule_name = "create_table";

$rule = $lexer->getRule($rule_name);

$rule->applyRuleToContent($sample);

echo "\n------\n";
echo "Sample string: " . $sample . "\n";
echo "Parsed fragment: " . mb_substr($sample, 0, $rule->getSourceLength()) . "\n";
echo "Parsed size: " . $rule->getSourceLength() . "\n";
//echo "Tokens:\n" . var_export($rule->getTokens(), true) . "\n";
echo "Data:\n" . var_export($lexer->getData()->getValuesAsArray(), true) . "\n";
echo "\n------\n\n";
