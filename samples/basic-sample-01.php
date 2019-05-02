<?php

require_once 'vendor/autoload.php';

use nabu\lexer\CNabuCustomLexer;
use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\rules\CNabuLexerRuleKeyword;

$lexer = CNabuCustomLexer::getLexer();
$lexer->setData(new CNabuLexerData());

$keyword_rule = CNabuLexerRuleKeyword::createFromDescriptor(
    $lexer,
    array(
        'keyword' => 'RULE',
        'method' => 'ignore case'
    )
);
$lexer->registerRule('keyword_rule', $keyword_rule);
$keyword_rule->applyRuleToContent('Rule is the basics');

var_export($keyword_rule->getTokens());
echo "\n";
