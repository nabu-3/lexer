<?php

require_once 'vendor/autoload.php';

use nabu\lexer\CNabuCustomLexer;
use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\rules\CNabuLexerRuleGroup;

$lexer = CNabuCustomLexer::getLexer();
$lexer->setData(new CNabuLexerData());

$sequence_rule = CNabuLexerRuleGroup::createFromDescriptor(
    $lexer,
    array(
        'method' => 'sequence',
        'tokenizer' => array(
            'method' => 'literal',
            'match' => '\s+',
        ),
        'group' => array(
            array(
                'keyword' => 'the',
                'method' => 'ignore case'
            ),
            array(
                'keyword' =>  'basics',
                'method' => 'literal'
            ),
            array(
                'keyword' => 'are',
                'method' => 'ignore case'
            ),
            array(
                'keyword' => 'Rules',
                'method' => 'ignore case'
            )
        )
    )
);
$lexer->registerRule('sequence_rule', $sequence_rule);
$sequence_rule->applyRuleToContent("The basics   are\tRules?");

var_export($sequence_rule->getTokens());
echo "\n";
