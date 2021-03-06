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

use stdClass;

use PHPUnit\Framework\TestCase;

use nabu\lexer\CNabuCustomLexer;

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\exceptions\ENabuLexerException;

/**
 * Test class for @see CNabuLexerRuleGroup.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleGroupTest extends TestCase
{
    /** @var CNabuCustomLexer Lexer used for tests. */
    private $lexer = null;

    public function setUp(): void
    {
        $this->lexer = CNabuCustomLexer::getLexer();
        $this->lexer->setData(new CNabuLexerData());
    }

    /**
     * @test __construct
     */
    public function testConstructor()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->assertInstanceOf(CNabuCustomLexer::class, $lexer);
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, new CNabuLexerRuleGroup($lexer));
    }

    /**
     * @test createFromDescriptor
     * @test initFromDescriptor
     * @test getMethod
     * @return CNabuLexerRuleGroup Returns created rule to pass to next step
     */
    public function testCreateInitFromDescriptorCase(): CNabuLexerRuleGroup
    {
        $rule = CNabuLexerRuleRegEx::createFromDescriptor(
            $this->lexer,
            array(
                'starter' => false,
                'method' => CNabuLexerRuleRegEx::METHOD_LITERAL,
                'match' => '\s+'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule);
        $this->lexer->registerRule('token', $rule);

        $rule = CNabuLexerRuleGroup::createFromDescriptor(
            $this->lexer,
            array(
                'starter' => false,
                'method' => CNabuLexerRuleGroup::METHOD_SEQUENCE,
                'tokenizer' => 'token',
                'group' => array(
                    'CREATE',
                    'ALTER',
                    'DELETE',
                    array(
                        'starter' => false,
                        'method' => 'literal',
                        'keyword' => 'DROP'
                    )
                ),
                'path' => 'method'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);
        $this->assertSame(CNabuLexerRuleGroup::METHOD_SEQUENCE, $rule->getMethod());
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule->getTokenizer());

        $rule = CNabuLexerRuleGroup::createFromDescriptor(
            $this->lexer,
            array(
                'starter' => false,
                'method' => CNabuLexerRuleGroup::METHOD_CASE,
                'tokenizer' => array(
                    'starter' => false,
                    'method' => CNabuLexerRuleRegEx::METHOD_LITERAL,
                    'match' => '\s+'
                ),
                'group' => array(
                    'CREATE',
                    'ALTER',
                    'DELETE',
                    array(
                        'starter' => false,
                        'method' => 'literal',
                        'keyword' => 'DROP'
                    )
                ),
                'path' => 'method'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);
        $this->assertSame(CNabuLexerRuleGroup::METHOD_CASE, $rule->getMethod());
        $this->assertNull($tokenizer = $rule->getTokenizer());

        return $rule;
    }

    /**
     * @test createFromDescriptor
     */
    public function testCreateFromDescriptorFails()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[rule, descriptor]');
        $ule = CNabuLexerRuleGroup::createFromDescriptor(
            $this->lexer,
            array(
                'starter' => false,
                'method' => CNabuLexerRuleGroup::METHOD_SEQUENCE,
                'tokenizer' => new stdClass(),
                'group' => array('CREATE', 'TABLE'),
                'path' => 'a.b.c'
            )
        );
    }

    /**
     * @test applyRuleToContent
     * @test applyRuleToContentAsCase
     * @depends testCreateInitFromDescriptorCase
     * @param CNabuLexerRuleGroup $rule Rule passed from previous test.
     */
    public function testApplyRuleToContentCase(CNabuLexerRuleGroup $rule)
    {
        $this->assertTrue($rule->applyRuleToContent('CREATE TABLE'));
        $this->assertSame(array('CREATE'), $rule->getTokens());
        $this->assertSame(6, $rule->getSourceLength());
        $this->assertTrue($rule->applyRuleToContent('ALTER TABLE'));
        $this->assertSame(array('ALTER'), $rule->getTokens());
        $this->assertSame(5, $rule->getSourceLength());
        $this->assertTrue($rule->applyRuleToContent('DELETE FROM TABLE'));
        $this->assertSame(array('DELETE'), $rule->getTokens());
        $this->assertSame(6, $rule->getSourceLength());
        $this->assertTrue($rule->applyRuleToContent('DROP TABLE'));
        $this->assertSame(array('DROP'), $rule->getTokens());
        $this->assertSame(4, $rule->getSourceLength());
    }

    /**
     * @test createFromDescriptor
     * @test initFromDescriptor
     * @return CNabuLexerRuleGroup Returns created rule to pass to next step
     */
    public function testCreateInitFromDescriptorSequence(): CNabuLexerRuleGroup
    {
        $rule = CNabuLexerRuleGroup::createFromDescriptor(
            $this->lexer,
            array(
                'starter' => false,
                'method' => CNabuLexerRuleGroup::METHOD_SEQUENCE,
                'tokenizer' => array(
                    'starter' => false,
                    'method' => CNabuLexerRuleRegEx::METHOD_LITERAL,
                    'match' => '\s+'
                ),
                'group' => array(
                    'CREATE',
                    'TABLE',
                    'IF',
                    array(
                        'starter' => false,
                        'method' => 'literal',
                        'keyword' => 'NOT'
                    ),
                    array(
                        'starter' => false,
                        'method' => 'literal',
                        'keyword' => 'EXISTS'
                    )
                )
            )
        );

        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);

        return $rule;
    }

    /**
     * @test applyRuleToContent
     * @test applyRuleToContentAsSequence
     * @depends testCreateInitFromDescriptorSequence
     * @param CNabuLexerRuleGroup $rule Rule passed from previous test.
     */
    public function testApplyRuleToContentSequence(CNabuLexerRuleGroup $rule)
    {
        $this->assertTrue($rule->applyRuleToContent('CREATE TABLE IF NOT EXISTS'));
        $this->assertSame(array('CREATE', ' ', 'TABLE', ' ', 'IF', ' ', 'NOT', ' ', 'EXISTS'), $rule->getTokens());
    }

    /**
     * @test applyRuleToContent
     */
    public function testApplyRuleToContentFails()
    {
        $rule = new CNabuLexerRuleGroup($this->lexer);

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_EMPTY_GROUP_RULE);
        $rule->applyRuleToContent('Empty test');
    }

    /**
     * @test applyRuleToContent
     * @test applyRuleToContentAsSequence
     */
    public function testApplyRuleToContentAsSequence()
    {
        $rule = CNabuLexerRuleGroup::createFromDescriptor(
            $this->lexer,
            array(
                'starter' => false,
                'method' => CNabuLexerRuleGroup::METHOD_SEQUENCE,
                'tokenizer' => array(
                    'starter' => false,
                    'method' => CNabuLexerRuleRegEx::METHOD_LITERAL,
                    'match' => '\s+'
                ),
                'group' => array(
                    'IF',
                    'NOT',
                    'EXISTS'
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);
        $this->assertFalse($rule->applyRuleToContent('IF EXISTS'));
        $this->assertSame(null, $rule->getTokens());
        $this->assertSame(0, $rule->getSourceLength());

        return $rule;
    }
}
