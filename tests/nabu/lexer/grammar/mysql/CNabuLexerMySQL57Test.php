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

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\grammar\mysql\CNabuLexerMySQL57;

use nabu\lexer\interfaces\INabuLexerRule;

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
    private static $lexer = null;


    public static function setUpBeforeClass(): void
    {
        self::$lexer = CNabuLexer::getLexer('mysql', '5.7');
    }

    /**
     * Data Provider for testDbNameRule
     */
    public function dbNameRuleDataProvider()
    {
        /** @todo Review @url { https://dev.mysql.com/doc/refman/5.7/en/identifiers.html } to complete rule details. */
        return [
            ["db_name", true, "ascii_schema", array("ascii_schema"), 12],
            ["db_name", true, "dollar\$\$\$schema", array("dollar\$\$\$schema"), 15],
            ["db_name", true, "unicode_schema\u{2605}", array("unicode_schema\u{2605}"), 15],
            ["db_name", true, "unicode\u{1FFFF}", array("unicode"), 7],
            ["db_name", true, "`ascii_schema`", array("ascii_schema"), 14],
            ["db_name", true, "`dollar\$\$\$schema`", array("dollar\$\$\$schema"), 17],
            ["db_name", true, "`unicode\u{2605}_test`", array("unicode\u{2605}_test"), 15],
            ["db_name", true, "`nabu-3`", array("nabu-3"), 8],

            ["db_name", false, "-ascii-error"],
            ["db_name", false, "\u{000A}unicode_error"],

            ["comment", true, " ", array(" "), 1],
            ["comment", true, "    ", array("    "), 4],
            ["comment", true, "    /* comment */", array("    ", " comment "), 17],
            ["comment", true, "/* comment */  ", array(" comment ", "  "), 15],

            ["if_not_exists", true, "IF NOT EXISTS", array("IF", "NOT", "EXISTS"), 13],

            ["create_schema", true, 'CREATE DATABASE IF NOT EXISTS `nabu-3`',
                array('CREATE', 'DATABASE', 'IF', 'NOT', 'EXISTS', 'nabu-3'), 38],
            ["create_schema", true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3`',
                array('CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3'), 36],
            ["create_schema", true, 'CREATE DATABASE `nabu-3`',
                array('CREATE', 'DATABASE', 'nabu-3'), 24],
            ["create_schema", true, 'CREATE SCHEMA `nabu-3`',
                array('CREATE', 'SCHEMA', 'nabu-3'), 22],

            ["create_schema", true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` DEFAULT CHARACTER SET = utf8',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'DEFAULT', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                65
            ],
            ["create_schema", true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` CHARACTER SET = utf8',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                57
            ],
            ["create_schema", true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` CHARACTER SET=utf8',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                55
            ],
            ["create_schema", true, 'CREATE SCHEMA `nabu-3` CHARACTER SET=utf8',
                array(
                    'CREATE', 'SCHEMA', 'nabu-3', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                41
            ],
            ["create_schema", true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` DEFAULT COLLATE = utf8_general_ci',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'DEFAULT', 'COLLATE', '=', 'utf8_general_ci'
                ),
                70
            ],
            ["create_schema", true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE = utf8_general_ci',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3',
                    'DEFAULT', 'CHARACTER', 'SET', '=', 'utf8',
                    'DEFAULT', 'COLLATE', '=', 'utf8_general_ci'
                ),
                99
            ],

            ["create_schema", true, 'CREATE SCHEMA test; CHARACTER SET', array('CREATE', 'SCHEMA', 'test'), 19],

            ["create_schema", false, 'CREATE SCHEMA DEFAULT; CHARACTER SET', array('CREATE', 'SCHEMA', 'DEFAULT'), 22],
            ["create_schema", false, 'CREATE SCHEMA DEFAULT CHARACTER SET'],
            ["create_schema", false, 'CREATE SCHEMA test CHARACTER SET'],
            ["create_schema", false, 'CREATE SCHEMA DEFAULT']

        ];
    }

    /**
     * Testing db_name Rule.
     * @dataProvider dbNameRuleDataProvider
     * @param string $rule_name Rule name to apply.
     * @param bool $success Expected result: true => passed, false => fails.
     * @param string|null $sample Sample string to test rule.
     * @param null $result Result value after apply rule.
     * @param int $length Expected source length after apply rule.
     */
    public function testDbNameRule(
        string $rule_name, bool $success = false, string $sample = null, $result = null, int $length = 0
    ): void {
        $rule = self::$lexer->getRule($rule_name);
        $this->assertInstanceOf(INabuLexerRule::class, $rule);
        if ($success) {
            $applied = $rule->applyRuleToContent($sample);
            $this->assertTrue($applied);
            $this->assertSame($result, $rule->getTokens());
            $this->assertSame($length, $rule->getSourceLength());
        } else {
            $applied = $rule->applyRuleToContent($sample);
            $this->assertFalse($applied);
            $this->assertNull($rule->getTokens());
            $this->assertSame(0, $rule->getSourceLength());
        }
    }

    /**
     * Data provider for testAnalyze.
     */
    public function analyzeDataProvider()
    {
        return [
            [true, 'CREATE DATABASE IF NOT EXISTS `nabu-3`',
                array('CREATE', 'DATABASE', 'IF', 'NOT', 'EXISTS', 'nabu-3'), 38],
            [true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3`',
                array('CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3'), 36],
            [true, 'CREATE DATABASE `nabu-3`',
                array('CREATE', 'DATABASE', 'nabu-3'), 24],
            [true, 'CREATE SCHEMA `nabu-3`',
                array('CREATE', 'SCHEMA', 'nabu-3'), 22],

            [true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` DEFAULT CHARACTER SET = utf8',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'DEFAULT', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                65
            ],
            [true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` CHARACTER SET = utf8',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                57
            ],
            [true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` CHARACTER SET=utf8',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                55
            ],
            [true, 'CREATE SCHEMA `nabu-3` CHARACTER SET=utf8',
                array(
                    'CREATE', 'SCHEMA', 'nabu-3', 'CHARACTER', 'SET', '=', 'utf8'
                ),
                41
            ],
            [true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` DEFAULT COLLATE = utf8_general_ci',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3', 'DEFAULT', 'COLLATE', '=', 'utf8_general_ci'
                ),
                70
            ],
            [true, 'CREATE SCHEMA IF NOT EXISTS `nabu-3` DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE = utf8_general_ci',
                array(
                    'CREATE', 'SCHEMA', 'IF', 'NOT', 'EXISTS', 'nabu-3',
                    'DEFAULT', 'CHARACTER', 'SET', '=', 'utf8',
                    'DEFAULT', 'COLLATE', '=', 'utf8_general_ci'
                ),
                99
            ],

            [true, 'CREATE SCHEMA test; CHARACTER SET', array('CREATE', 'SCHEMA', 'test'), 19],

            [false, 'CREATE SCHEMA DEFAULT; CHARACTER SET', array('CREATE', 'SCHEMA', 'DEFAULT'), 22],
            [false, 'CREATE SCHEMA DEFAULT CHARACTER SET'],
            [false, 'CREATE SCHEMA test CHARACTER SET'],
            [false, 'CREATE SCHEMA DEFAULT']
        ];
    }

    /**
     * Testing CNabuLexer::analyze method.
     * @dataProvider dbNameRuleDataProvider
     * @param string $rule_name Rule name to apply.
     * @param bool $success Expected result: true => passed, false => fails.
     * @param string|null $sample Sample string to test rule.
     * @param null $result Result value after apply rule.
     * @param int $length Expected source length after apply rule.
     */
    public function testAnalyze(
        string $rule_name, bool $success = false, string $sample = null, $result = null, int $length = 0
    ): void {
        $rule = self::$lexer->getRule($rule_name);
        $this->assertInstanceOf(INabuLexerRule::class, $rule);
        if ($rule->isStarter()) {
            $success = self::$lexer->analyze($sample);
            $this->assertIsBool($success);
            $data = self::$lexer->getData();
            $this->assertInstanceOf(CNabuLexerData::class, $data);
            if ($success) {
                $this->assertSame($result, $data->getTokens());
                $this->assertSame($length, $data->getSourceLength());
            } else {
                $this->assertNull($data->getTokens());
                $this->assertSame(0, $data->getSourceLength());
            }
        }
    }
}
