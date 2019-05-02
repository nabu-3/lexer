<?php

require_once 'vendor/autoload.php';

use nabu\lexer\CNabuCustomLexer;
use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\rules\CNabuLexerRuleRepeat;

$lexer = CNabuCustomLexer::getLexer();
$lexer->setData(new CNabuLexerData());

$repeat_rule = CNabuLexerRuleRepeat::createFromDescriptor(
    $lexer,
    array(
        'repeat' => '1..4',
        'tokenizer' => array(
            'method' => 'literal',
            'match' => '\s+'
        ),
        'rule' => array(
            'method' => 'ignore case',
            'match' => '[a-zA-Z]+'
        )
    )
);
$lexer->registerRule('repeat_rule', $repeat_rule);
$repeat_rule->applyRuleToContent("The basics   are\tRules?");

var_export($repeat_rule->getTokens());
echo "\n";
