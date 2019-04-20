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
    }
}
