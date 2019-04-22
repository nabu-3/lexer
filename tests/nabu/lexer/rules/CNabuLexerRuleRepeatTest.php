<?php

/** @license
 *  Copyright 2019-2011 Rafael Gutierrez Martinez
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

use PHPUnit\Framework\TestCase;

use nabu\lexer\CNabuCustomLexer;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexer;

/**
 * Test class for @see CNabuLexerRuleRepeat.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleRepeatTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstruct()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, new CNabuLexerRuleRepeat($lexer));
    }

    /**
     * @test createFromDescriptor
     * @test getTokenizer
     */
    public function testCreateFromDescriptor()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '0..1',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $repeat->getTokenizer());
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */"));
        $this->assertSame(array('    ', ' Test 1 '), $repeat->getValue());
        $this->assertSame(16, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1..3',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1..n',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1..n',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */   Test 4"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1-∞',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */   Test 4"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1,infinity',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */   Test 4"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => 'infinity',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */   Test 4"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '2..2',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 '), $repeat->getValue());
        $this->assertSame(30, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '2',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 '), $repeat->getValue());
        $this->assertSame(30, $repeat->getSourceLength());

        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '2',
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertNull($repeat->getTokenizer());
    }

    /**
     * @test initFromDescriptor
     * @test CNabuLexer::registerRule
     */
    public function testInitFromDescriptor()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $rule_1 = CNabuLexerRuleRegEx::createFromDescriptor(
            $lexer,
            array(
                'match' => '\\s*',
                'method' => 'literal'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule_1);
        $this->assertInstanceOf(INabuLexer::class, $lexer->registerRule('token', $rule_1));
        $rule_2 = CNabuLexerRuleRegEx::createFromDescriptor(
            $lexer,
            array(
                'match' => '\/\*(.*?)\*\/',
                'method' => 'literal'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule_2);
        $this->assertInstanceOf(INabuLexer::class, $lexer->registerRule('repeater', $rule_2));
        $rule_3 = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1..3',
                'tokenizer' => 'token',
                'rule' => 'repeater'
            )
        );
        $this->assertInstanceOf(INabuLexer::class, $lexer->registerRule('comment', $rule_3));
        $this->assertTrue($rule_3->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */   Test 4"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $rule_3->getValue());
        $this->assertSame(48, $rule_3->getSourceLength());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR);
        $rule_4 = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1..3',
                'tokenizer' => null,
                'rule' => 'repeater'
            )
        );
    }

    /**
     * @test applyRuleToContent
     */
    public function testApplyRuleToContent()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $repeat = CNabuLexerRuleRepeat::createFromDescriptor(
            $lexer,
            array(
                'repeat' => '1..3',
                'tokenizer' => array(
                    'match' => '\\s*',
                    'method' => 'literal'
                ),
                'rule' => array(
                    'match' => '\/\*(.*?)\*\/',
                    'method' => 'literal'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRepeat::class, $repeat);
        $this->assertTrue($repeat->applyRuleToContent("    /* Test 1 */\n\r/* Test 2 */   /* Test /* 3 */"));
        $this->assertSame(array('    ', ' Test 1 ', "\n\r", ' Test 2 ', '   ', ' Test /* 3 '), $repeat->getValue());
        $this->assertSame(48, $repeat->getSourceLength());
        $this->assertFalse($repeat->applyRuleToContent("Test without comments"));
        $this->assertFalse($repeat->applyRuleToContent("      Test without comments"));
    }
}
