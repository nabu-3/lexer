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

require_once 'vendor/autoload.php';

use nabu\lexer\CNabuCustomLexer;
use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\rules\CNabuLexerRuleGroup;

$lexer = CNabuCustomLexer::getLexer();
$lexer->setData(new CNabuLexerData());

$case_rule = CNabuLexerRuleGroup::createFromDescriptor(
    $lexer,
    array(
        'method' => 'case',
        'group' => array(
            array(
                'keyword' => 'Rules',
                'method' => 'ignore case'
            ),
            array(
                'keyword' => 'are',
                'method' => 'ignore case'
            ),
            array(
                'keyword' => 'the',
                'method' => 'ignore case'
            ),
            array(
                'keyword' =>  'basics',
                'method' => 'literal'
            )
        )
    )
);
$lexer->registerRule('case_rule', $case_rule);
$case_rule->applyRuleToContent('The basics are Rules?');

var_export($case_rule->getTokens());
echo "\n";
