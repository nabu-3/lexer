<?php

require_once 'vendor/autoload.php';

use nabu\lexer\CNabuCustomLexer;
use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\rules\CNabuLexerRuleRegEx;

$lexer = CNabuCustomLexer::getLexer();
$lexer->setData(new CNabuLexerData());

$regex_rule = CNabuLexerRuleRegEx::createFromDescriptor(
    $lexer,
    array(
        'match' => '\\w+',
        'method' => 'ignore case'
    )
);
$lexer->registerRule('regex_rule', $regex_rule);
$regex_rule->applyRuleToContent('RUle is the basics');

var_export($regex_rule->getTokens());
echo "\n";
