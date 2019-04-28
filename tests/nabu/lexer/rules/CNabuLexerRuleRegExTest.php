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

namespace nabu\lexer\rules;

use Exception;

use PHPUnit\Framework\TestCase;

use nabu\lexer\CNabuCustomLexer;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexerRule;

/**
 * Test class for @see CNabuLexerRuleRegEx.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleRegExTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstruct()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, new CNabuLexerRuleRegEx($lexer));
    }

    /**
     * Provides data to test method testCreateInitFromDescriptor.
     * @return array Returns an array with all test case combinatios.
     */
    public function dataProviderCreateInitFromDescriptor()
    {
        return [
            [false, false, false, false, 'literal', "^''|'(.*?[^\\\\])'", null,
                "'test \'with single quotes\' inside #0' and more test", array("test \'with single quotes\' inside #0"), 39, true],
            [false, false, false, false, 'literal', "^'()'|'(.*?[^\\\\])'", null,
                "'' and more test", array(""), 2, true],
            [false, false, false, false, 'literal', "('\\s*'|'.*?[^\\\\]')", null,
                "'test \'with single quotes\' inside #1' and more test", array("'test \'with single quotes\' inside #1'"), 39, true],
            [false, false, false, false, 'literal', '([a-z]+) ([A-Z]+)', null,
                "test CASE with multiple params", array('test', 'CASE'), 9, true],

            [false, false, false, false, 'literal', '([a-z]+)', null,
                "123456", null, 0, false],
            [true, false, false, false, 'literal', '([a-z+', null,
                "test CASE", array('test'), 4, true],

            [false, false, false, true, 'literal', "([\x{0080}-\x{FFFF}]+)", null,
                "\u{0080}\u{0100}\u{1FF00}", array("\u{0080}\u{0100}"), 2, true]
        ];
    }

    /**
     * Private method to create a Descriptor structure by parameters.
     * @param bool|null $starter If null acts as not declared.
     * @param bool|null $case_ignored If null acts as not declared.
     * @param bool|null $unicode If different of null, applies Unicode boolean attribute.
     * @param string|null $method If null acts as not declared.
     * @param string|null $match If null acts as not declared.
     * @param mixed|null $exclude Exclude rule to apply.
     * @return array|null If, at least, one parameter is different than null, then returns a well formed descriptor.
     * Otherwise, returns null.
     */
    private function createDescriptor(
        bool $starter = null, bool $case_ignored = null, bool $unicode = null, string $method = null,
        string $match = null, $exclude = null
    ) {
        $params = array();
        if (is_bool($starter)) {
            $params['starter'] = $starter;
        }
        if (is_bool($case_ignored)) {
            $params['case_ignored'] = $case_ignored;
        }
        if (is_bool($unicode)) {
            $params['unicode'] = $unicode;
        }
        if (is_string($method)) {
            $params['method'] = $method;
        }
        if (is_string($match)) {
            $params['match'] = $match;
        }
        if (!is_null($exclude)) {
            $params['exclude'] = $exclude;
        }
        if (count($params) === 0) {
            $params = null;
        }

        return $params;
    }

    /**
     * @test createFromDescriptor
     * @test initFromDescriptor
     * @test CnabuLexerAbstractRule::isStarter
     * @test isCaseIgnored
     * @dataProvider dataProviderCreateInitFromDescriptor
     * @param bool $throwable If true expects that this test throws an exception in any moment.
     * @param bool|null $starter If null acts as not declared.
     * @param bool|null $case_ignored If null acts as not declared.
     * @param bool|null $unicode If different of null, applies Unicode boolean attribute.
     * @param string|null $method If null acts as not declared.
     * @param string|null $match If null acts as not declared.
     * @param mixed|null $exclude Exclude rule to apply.
     * @param string|null $content Content to test case.
     * @param mixed|null $result Expected result.
     * @param int $length Source length expected result.
     * @param bool $passed Apply Rule is passed.
     */
    public function testCreateInitFromDescriptor(
        bool $throwable, bool $starter = null, bool $case_ignored = null, bool $unicode = null,
        string $method = null, string $match = null, $exclude = null, string $content = null,
        $result = null, int $length = 0, bool $passed = false
    ) {
        $lexer = CNabuCustomLexer::getLexer();
        $params = $this->createDescriptor($starter, $case_ignored, $unicode, $method, $match, $exclude);

        if ($throwable) {
            $this->expectException(ENabuLexerException::class);
        }

        $rule = CNabuLexerRuleRegEx::createFromDescriptor($lexer, $params);
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule);

        if (is_bool($starter)) {
            $this->assertSame($starter, $rule->isStarter());
        } else {
            $this->assertFalse($rule->isStarter());
        }

        if (is_bool($unicode)) {
            $this->assertSame($unicode, $rule->isUnicodeAllowed());
        } else {
            $this->assertFalse($rule->isUnicodeAllowed());
        }

        if ($method === CNabuLexerRuleKeyword::METHOD_IGNORE_CASE) {
            $this->assertTrue($rule->isCaseIgnored());
        } else {
            $this->assertFalse($rule->isCaseIgnored());
        }

        if ($method === CNabuLexerRuleKeyword::METHOD_LITERAL) {
            $this->assertTrue($rule->isLiteral());
        } else {
            $this->assertFalse($rule->isLiteral());
        }

        $this->assertSame($method, $rule->getMethod());
        $this->assertSame($match, $rule->getMatchRegularExpression());

        if (is_null($exclude)) {
            $this->assertNull($rule->getExclusionRule());
        } else {
            $this->assertInstanceOf(INabuLexerRule::class, $rule->getExclusionRule());
        }
    }

    /**
     * @test applyRuleToContent
     * @test initFromDescriptor
     * @test CnabuLexerAbstractRule::isStarter
     * @test isCaseIgnored
     * @test getExclusionRule
     * @dataProvider dataProviderCreateInitFromDescriptor
     * @param bool $throwable If true expects that this test throws an exception in any moment.
     * @param bool|null $starter If null acts as not declared.
     * @param bool|null $case_ignored If null acts as not declared.
     * @param bool|null $unicode If different of null, applies Unicode boolean attribute.
     * @param string|null $method If null acts as not declared.
     * @param string|null $match If null acts as not declared.
     * @param mixed|null $exclude Exclude rule to apply.
     * @param string|null $content Content to test case.
     * @param mixed|null $result Expected result.
     * @param int $length Source length expected result.
     * @param bool $passed Apply Rule is passed.
     */
    public function testApplyRuleToContent(
        bool $throwable, bool $starter = null, bool $case_ignored = null, bool $unicode = null,
        string $method = null, string $match = null, $exclude = null, string $content = null,
        $result = null, int $length = 0, bool $passed = false
    ) {
        if ($throwable) {
            $this->assertTrue($throwable);
        } else {
            $lexer = CNabuCustomLexer::getLexer();
            try {
                $rule = CNabuLexerRuleRegEx::createFromDescriptor(
                    $lexer,
                    $this->createDescriptor($starter, $case_ignored, $unicode, $method, $match, $exclude)
                );
            } catch (Exception $ex) {
                $this->assertInstanceOf(ENabuLexerException::class, $ex);
                $rule = null;
            }

            if (!is_null($rule)) {
                $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule);
                if (is_null($exclude)) {
                    $this->assertNull($rule->getExclusionRule());
                } else {
                    $this->assertInstanceOf(INabuLexerRule::class, $rule->getExclusionRule());
                }
                if ($passed) {
                    $this->assertTrue($rule->applyRuleToContent($content));
                    $this->assertSame($result, $rule->getValue());
                    $this->assertSame($length, $rule->getSourceLength());
                } else {
                    $this->assertFalse($rule->applyRuleToContent($content));
                    $this->assertNull($rule->getValue());
                    $this->assertSame(0, $rule->getSourceLength());
                }
            }
        }
    }
}
