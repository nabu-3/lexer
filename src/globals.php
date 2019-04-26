<?php
/** @var string Grammar folder name. */
const NABU_LEXER_GRAMMAR_FOLDER = DIRECTORY_SEPARATOR . 'grammar';
/** @var string Resources folder name. */
const NABU_LEXER_RESOURCE_FOLDER = DIRECTORY_SEPARATOR . 'resources';
/** @var string Range regular expression to extract parts. */
const NABU_LEXER_RANGE_REGEX =
    "/^(((0|[1-9][0-9]*)(\\.\\.|\\-|,)(n|infinity|inf|∞|[1-9][0-9]*))|(n|infinity|inf|∞|[1-9][0-9]*))$/i";
/** @var string Infinite range value represented by 'n'. */
const NABU_LEXER_RANGE_N = 'n';
/** @var string Infinite value represented by 'inf'. */
const NABU_LEXER_RANGE_INF = 'inf';
/** @var string Infinite value represented by 'infinity'. */
const NABU_LEXER_RANGE_INFINITE = 'infinity';
/** @var string Infinite value represented by '∞'. */
const NABU_LEXER_RANGE_INFINITE_SYMBOL = '∞';
/** @var array Array of allowed values to represent infinity. */
const NABU_LEXER_RANGE_INFINITE_VALUES = Array(
    NABU_LEXER_RANGE_N,
    NABU_LEXER_RANGE_INF,
    NABU_LEXER_RANGE_INFINITE,
    NABU_LEXER_RANGE_INFINITE_SYMBOL
);
