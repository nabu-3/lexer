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

namespace nabu\lexer\grammar\mysql;

use PHPUnit\Framework\TestCase;

use nabu\lexer\CNabuLexer;

use nabu\lexer\grammar\mysql\CNabuLexerMySQL57;

/**
 * Test class for @see { CNabuLexerMySQL57 }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\grammar|mysql
 */
class CNabuLexerMySQL57Test extends TestCase
{
    /** @var CNabuLexerMySQL57 $lexer Lexer to perform tests. */
    private $lexer = null;


    public function setUp(): void
    {
        $this->lexer = CNabuLexer::getLexer('mysql', '5.7');
    }

    /**
     * Data Provider for testDbNameRule
     */
    public function dbNameRuleDataProvider()
    {
        /** @todo Review @url { https://dev.mysql.com/doc/refman/5.7/en/identifiers.html } to complete rule details. */
        return [
            [true, "ascii_schema", "ascii_schema", 12],
            [true, "dollar\$\$\$schema", "dollar\$\$\$schema", 15],
            [true, "unicode_schema\u{2605}", "unicode_schema\u{2605}", 15],
            [true, "unicode\u{1FFFF}", "unicode", 7],
            [true, "`ascii_schema`", "ascii_schema", 14],
            [true, "`dollar\$\$\$schema`", "dollar\$\$\$schema", 17],
            [true, "`unicode\u{2605}_test`", "unicode\u{2605}_test", 15],

            [false, "-ascii-error"],
            [false, "\u{000A}unicode_error"],
        ];
    }
    /**
     * Testing db_name Rule.
     * @dataProvider dbNameRuleDataProvider
     * @param bool $success Expected result: true => passed, false => fails.
     * @param string|null $sample Sample string to test rule.
     * @param string|null $result Result string after apply rule.
     * @param int $length Expected source length after apply rule.
     */
    public function testDbNameRule(bool $success = false, string $sample = null, string $result = null, int $length = 0): void
    {
        $rule = $this->lexer->getRule('db_name');
        if ($success) {
            $this->assertTrue($rule->applyRuleToContent($sample));
            $this->assertSame($result, $rule->getValue());
            $this->assertSame($length, $rule->getSourceLength());
        } else {
            $this->assertFalse($rule->applyRuleToContent($sample));
            $this->assertNull($rule->getValue());
            $this->assertSame(0, $rule->getSourceLength());
        }
    }

    /**
     * Testing CREATE SCHEMA Syntax.
     */
    public function testCreateSchema()
    {
        $rule = $this->lexer->getRule('create_schema');

        $this->assertTrue($rule->applyRuleToContent('CREATE DATABASE IF NOT EXISTS `nabu-3`'));
        $this->assertTrue($rule->applyRuleToContent('CREATE SCHEMA IF NOT EXISTS `nabu-3`'));
        $this->assertTrue($rule->applyRuleToContent('CREATE DATABASE `nabu-3`'));
        $this->assertTrue($rule->applyRuleToContent('CREATE SCHEMA `nabu-3`'));
    }
}
