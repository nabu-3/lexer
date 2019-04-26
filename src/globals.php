<?php
define('NABU_LEXER_GRAMMAR_FOLDER', DIRECTORY_SEPARATOR . 'grammar');
define('NABU_LEXER_RESOURCE_FOLDER', DIRECTORY_SEPARATOR . 'resources');

/** @var string Range regular expression to extract parts. */
const LEXER_RANGE_REGEX = "/^(((0|[1-9][0-9]*)(\\.\\.|\\-|,)(n|infinity|inf|∞|[1-9][0-9]*))|(n|infinity|inf|∞|[1-9][0-9]*))$/i";
/** @var string Infinite range value represented by 'n'. */
const LEXER_RANGE_N = 'n';
/** @var string Infinite value represented by 'inf'. */
const LEXER_RANGE_INF = 'inf';
/** @var string Infinite value represented by 'infinity'. */
const LEXER_RANGE_INFINITE = 'infinity';
/** @var string Infinite value represented by '∞'. */
const LEXER_RANGE_INFINITE_SYMBOL = '∞';
/** @var array Array of allowed values to represent infinity. */
const LEXER_RANGE_INFINITE_VALUES = Array(
    LEXER_RANGE_N,
    LEXER_RANGE_INF,
    LEXER_RANGE_INFINITE,
    LEXER_RANGE_INFINITE_SYMBOL
);
