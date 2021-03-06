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

$samples = array(
    // "DEFAULT CHARACTER SET=utf8",
    // "DEFAULT COLLATE=utf8_general_ci",
    "DEFAULT CHARACTER SET=utf8 DEFAULT COLLATE=utf8_general_ci"
);
$rule_name = "create_specification";

$rule = $lexer->getRule($rule_name);

foreach ($samples as $sample) {
    $rule->applyRuleToContent($sample);

    echo "\n------\n";
    echo "Sample string: " . $sample . "\n";
    echo "Parsed fragment: " . mb_substr($sample, 0, $rule->getSourceLength()) . "\n";
    echo "Parsed size: " . $rule->getSourceLength() . "\n";
    echo "Tokens: " . var_export($rule->getTokens(), true) . "\n";
    echo "Data: " . var_export($lexer->getData()->getValuesAsArray(), true) . "\n";
    echo "\n------\n\n";
}
