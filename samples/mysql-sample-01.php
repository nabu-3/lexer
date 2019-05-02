<?php
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
