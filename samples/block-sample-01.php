<?php

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
