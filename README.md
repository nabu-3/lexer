# nabu-3 Lexer
[![GitHub](https://img.shields.io/github/license/nabu-3/lexer.svg)](https://opensource.org/licenses/Apache-2.0)
[![Build Status](https://travis-ci.org/nabu-3/lexer.svg?branch=master)](https://travis-ci.org/nabu-3/lexer)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_lexer&metric=alert_status)](https://sonarcloud.io/dashboard?id=nabu-3_lexer)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_lexer&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=nabu-3_lexer&metric=Maintainability)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_lexer&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=nabu-3_lexer&metric=Reliability)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=nabu-3_lexer&metric=security_rating)](https://sonarcloud.io/dashboard?id=nabu-3_lexer&metric=Security)

This is a Lexer library written in __PHP__ to analyze lexical expressions and obtain a tokenized representation and a data structure as a descriptor of interpreted content.

The Lexer supports Unicode strings and Regular Expressions.
## Installation
Lexer library requires __PHP 7.2__ or higher and __mb_string__ native module.

The library is deployed as part of [__composer__](https://getcomposer.org) and [__Packagist__](https://packagist.org/packages/nabu-3/lexer) standard __PHP__ packages distribution. To use this library you need only to require it via composer:
```sh
composer require nabu-3/lexer
```
## Basic usage
To start using this library you need to include the standard _autoload.php_ file that is maintained by __composer__:
```php
<?php

require_once 'vendor/autoload.php';
```
To start using this library, you can create a CNabuCustomLexer object and provide a Lexer Data storage as is:
```php
<?php

use nabu\lexer\CNabuCustomLexer;
use nabu\lexer\data\CNabuLexerData;

$lexer = CNabuCustomLexer::getLexer();
$lexer->setData(new CNabuLexerData());
```
This action provides a custom lexer that you can customize to add rules and to perform analysis over your sample strings.
### The Keyword Rule
The most basic rule, is the Keyword Rule. With it, you can parse a keyword and obtain the tokenized result.

Below, a basic sample using the __Keyword Rule__:
```php
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
$keyword_rule->applyRuleToContent('RULE is the basics');

var_export($keyword_rule->getTokens());
echo "\n";
```
Allowed _methods_ are 'ignore case' and 'literal'. Then:
- 'ignore case' allows to match the keyword ignoring case letters. Internally, both strings (sample and keyword) are converted to lowercase and compare it. If both matches then interprets that the rule is covered and returns true.
- 'literal' forces that all characters matches exactly as expected by the keyword, and rule only is covered if all characters matches _literally_.

You can run this sample from the terminal typing:
```sh
php samples/basic_sample_01.php
```
After execute this sample, you can see in your terminal the list of parsed tokens:
```php
array (
  0 => 'Rule',
)
```
Note that the list contains only an item because the _Keyword Rule_ affects only to one occurrence of keyword. As the rule method is defined as 'ignore case', the token included matches with the sample source string and not like the keyword attribute.
### The Regular Expression Rule
This rule offers a wide application for polymorphic strings or dynamic structures that requires a use of a regular expression to interpret his content. Like the Keyword Rule, you can apply the match as 'literal' or 'ignore case', and, with ignore case, the '/i' modifier is applied when parse regular expressions using preg_match.

Below, a basic example using the __Regular Expression Rule__:
```php
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
```
Allowed _methods_ are the same than Keyword Rules attribute.

You can run this sample from the terminal typing:
```sh
php samples/basic_sample_02.php
```
After execute this sample, you can see in your terminal the list of parsed tokens:
```php
array (
  0 => 'RUle',
)
```
Note that the list contains only an item because the _Regular Expression Rule_ affects only to one occurrence of the expression. As the rule method is defined as 'ignore case', the token included matches with the sample source string and not like the keyword attribute.
## Block rules
Block rules have the capability of group any kind or rule to apply a _case_, _sequence_ or _repetition_ of a list of rules.
### The Case Rule
This rule allows to treat a list of rules as a switch/case sentence. Then, you can define this list and apply the rule. If the sample string matches, at least one of the listed rules, the first matched is applied and the evaluation of the rule stops here.

Below, a basic example using the __Case Rule__:
```php
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
                'keyword' => 'Rule',
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
```
You can run this sample from the terminal typing:
```sh
php samples/block_sample_01.php
```
After execute this sample, you can see in your terminal the list of parsed tokens:
```php
array (
  0 => 'The',
)
```
Note that the list contains only an item because the _Case Rule_ affects only to the first occurrence in the list or rules.
### The Sequence Rule
Sequence rules are similar to Case Rules, but it's necessary to look at the method, that it will be 'sequence', and also, that you can define a _tokenizer_ expression to allow a _separator_ between rules involved in the sequence.

Below, a basic example using the __Sequence Rule__:
```php
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
```
Note that the variation respecting to __Case Rule__ are two factors:
1. The __method__ is 'sequence'.
2. We add a __tokenizer__ attribute that contains an explicit rule declaration (in this case a Regular Expression Rule). This rule is applied before each iteration in the list of rules.

You can run this sample from the terminal typing:
```sh
php samples/block_sample_02.php
```
After execute this sample, you can see in your terminal the list of parsed tokens:
```php
array (
  0 => 'The',
  1 => ' ',
  2 => 'basics',
  3 => '   ',
  4 => 'are',
  5 => '	',
  6 => 'Rules',
)
```
Note that the list contains all words in the sample string because the _Sequence Rule_ try to match the full list in the order the it is declared. If one rule fails, then the sequence stops and rewinds the list to NULL to ensure that no tokens are parsed.
### The Repeat Rule
Repeat rules have the capability of define a cardinality for a rule. This cardinality can be defined as a minimum value and a maximum value or as a fixed value. Allowed formats can be:
- Fixed cardinality: any natural number starting at 0. This will be applied as '_repeat exactly n times_', where __n__ is the selected number.
- Range: a range it's a tuple of values in the form '_m..n_', where __m__ and __n__ are a natural number starting at 0 for __m__ and at __m__ for __n__. This means '_repeat between __m__ and __n__ times_'. If the repeat number is less than __m__ then the rule evaluation fails. If the repeat evaluation rule fails between __m__ and __n__ iterations, the evaluation rule success. If the repeat iteration reach __n__ the evaluation stops and finish successful.
- Infinite: in this case, you choose 'n' as value. Internally, this is translated as __1..n__ and applies Range cardinality as explained above, and then, will be applied as '_at least one time, but until infinite times or rule fails_'.
Like __Sequence Rules__, this kind of rules supports the use of a _tokenizer_ acting as a separator between each iteration of the rule.

Below, a basic example using the __Repeat Rule__:
```php
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
```
You can run this sample from the terminal typing:
```sh
php samples/block_sample_03.php
```
This sample have a similar result than the above of __Sequence Rule__, but in this case, implied rules are less restrictives as the rule matches with any kind of repetition between 1 and 4 times, matching a sequence of letters in lowercase or uppercase. As is, another phrase containing at least one word will match this rule until a limit of four words.
```php
array (
  0 => 'The',
  1 => ' ',
  2 => 'basics',
  3 => '   ',
  4 => 'are',
  5 => '	',
  6 => 'Rules',
)
```
