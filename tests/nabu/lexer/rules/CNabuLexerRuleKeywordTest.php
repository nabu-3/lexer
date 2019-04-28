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

/**
 * Test class for @see CNabuLexerRuleKeyword.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleKeywordTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstruct()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->assertInstanceOf(CNabuLexerRuleKeyword::class, new CNabuLexerRuleKeyword($lexer));
    }

    /**
     * Provides data to test method testCreateInitFromDescriptor.
     * @return array Returns an array with all test case combinatios.
     */
    public function dataProviderCreateInitFromDescriptor()
    {
        return [
            [false, false, false, 'literal', 'test', 'test data', array('test'), true],
            [false, false, false, 'literal', 'TEST', 'TEST data', array('TEST'), true],
            [false, false, false, 'literal', 'TEST', 'test data', array('test'), false],
            [false, false, false, 'literal', 'test', 'TEST data', array('TEST'), false],
            [false, false, false, 'literal', 'Test', 'TEst data', array('TEst'), false],
            [false, false, false, 'literal', 'Test', 'TEST data', array('TEST'), false],
            [false, true, false, 'literal', 'test', 'test data', array('test'), true],
            [false, null, false, 'literal', 'test', 'test data', array('test'), true],
            [false, false, null, 'literal', 'test', 'test data', array('test'), true],
            [false, false, null, 'literal', 'test', 'Other data', array('Other'), false],
            [false, false, true, 'ignore case', 'test', 'test data', array('test'), true],
            [false, false, true, 'ignore case', 'TEST', 'test data', array('test'), true],
            [false, false, true, 'ignore case', 'test', 'TEST data', array('TEST'), true],
            [false, false, true, 'ignore case', 'Test', 'TEST data', array('TEST'), true],
            [false, false, true, 'ignore case', 'Test', 'TeST data', array('TeST'), true],
            [false, false, true, 'ignore case', 'Test', 'Other data', array('Other'), false],
            [false, true, true, 'ignore case', 'test', 'test data', array('test'), true],
            [false, null, null, 'literal', 'test', 'test data', array('test'), true],

            [true, false, false, 'literal', null, 'test', array('test'), false],
            [true, false, false, null, 'test', 'test', array('test'), false],
            [true, false, false, null, null, 'test', array('test'), false],
            [true, false, false, 'anything', 'test', 'test', array('test'), false]
        ];
    }

    /**
     * Private method to create a Descriptor structure by parameters.
     * @param bool|null $starter If null acts as not declared.
     * @param bool|null $case_ignored If null acts as not declared.
     * @param string|null $method If null acts as not declared.
     * @param string|null $keyword If null acts as not declared.
     * @return array|null If, at least, one parameter is different than null, then returns a well formed descriptor.
     * Otherwise, returns null.
     */
    private function createDescriptor(
        bool $starter = null, bool $case_ignored = null, string $method = null, string $keyword = null
    ) {
        $params = array();
        if (is_bool($starter)) {
            $params['starter'] = $starter;
        }
        if (is_bool($case_ignored)) {
            $params['case_ignored'] = $case_ignored;
        }
        if (is_string($method)) {
            $params['method'] = $method;
        }
        if (is_string($keyword)) {
            $params['keyword'] = $keyword;
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
     * @param string|null $method If null acts as not declared.
     * @param string|null $keyword If null acts as not declared.
     * @param string|null $content Content to test case.
     * @param array|null $result Expected result.
     * @param bool $passed Apply Rule is passed.
     */
    public function testCreateInitFromDescriptor(
        bool $throwable, bool $starter = null, bool $case_ignored = null,
        string $method = null, string $keyword = null, string $content = null, array $result = null,
        bool $passed = false
    ) {
        $lexer = CNabuCustomLexer::getLexer();
        $params = $this->createDescriptor($starter, $case_ignored, $method, $keyword);

        if ($throwable) {
            $this->expectException(ENabuLexerException::class);
        }

        $rule = CNabuLexerRuleKeyword::createFromDescriptor($lexer, $params);
        $this->assertInstanceOf(CNabuLexerRuleKeyword::class, $rule);

        if (is_bool($starter)) {
            $this->assertSame($starter, $rule->isStarter());
        } else {
            $this->assertFalse($rule->isStarter());
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
        $this->assertSame($keyword, $rule->getKeyword());

        if ($throwable) {
            $this->expectException(null);
        }
    }

    /**
     * @test applyRuleToContent
     * @depends testCreateInitFromDescriptor
     * @dataProvider dataProviderCreateInitFromDescriptor
     * @param bool $throwable If true expects that this test throws an exception in any moment.
     * @param bool|null $starter If null acts as not declared.
     * @param bool|null $case_ignored If null acts as not declared.
     * @param string|null $method If null acts as not declared.
     * @param string|null $keyword If null acts as not declared.
     * @param string|null $content Content to test case.
     * @param array|null $result Expected result.
     * @param bool $passed Apply Rule is passed.
     */
    public function testApplyRuleToContent(
        bool $throwable, bool $starter = null, bool $case_ignored = null,
        string $method = null, string $keyword = null, string $content = null, array $result = null,
        bool $passed = false
    ) {
        if ($throwable) {
            $this->assertTrue($throwable);
        } else {
            $lexer = CNabuCustomLexer::getLexer();
            try {
                $rule = CNabuLexerRuleKeyword::createFromDescriptor(
                    $lexer,
                    $this->createDescriptor($starter, $case_ignored, $method, $keyword)
                );
            } catch (Exception $ex) {
                $this->assertInstanceOf(ENabuLexerException::class, $ex);
                $rule = null;
            }

            if (!is_null($rule)) {
                $this->assertInstanceOf(CNabuLexerRuleKeyword::class, $rule);
                if ($passed) {
                    $this->assertTrue($rule->applyRuleToContent($content));
                    $this->assertSame($result, $rule->getTokens());
                } else {
                    $this->assertFalse($rule->applyRuleToContent($content));
                    $this->assertNull($rule->getTokens());
                }
            }
        }
    }
}
