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

/**
 * Test class for @see CNabuLexerRuleGroup.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleGroupTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, new CNabuLexerRuleGroup());
    }

    /**
     * @test createFromDescriptor
     * @test initFromDescriptor
     * @return CNabuLexerRuleGroup Returns created rule to pass to next step
     */
    public function testCreateInitFromDescriptor(): CNabuLexerRuleGroup
    {
        $rule = CNabuLexerRuleGroup::createFromDescriptor(
            array(
                'starter' => false,
                'method' => CNabuLexerRuleGroup::METHOD_CASE,
                'tokenizer' => array(
                    'starter' => false,
                    'method' => CNabuLexerRuleRegEx::METHOD_LITERAL,
                    'match' => '\s+'
                ),
                'group' => array(
                    'CREATE', 'ALTER', 'DELETE', 'DROP'
                )
            )
        );

        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);

        return $rule;
    }

    /**
     * @test applyRuleToContent
     * @test applyRuleToContentAsCase
     * @depends testCreateInitFromDescriptor
     * @param CNabuLexerRuleGroup $rule Rule passed from previous test.
     */
    public function testApplyRuleToContent(CNabuLexerRuleGroup $rule)
    {
        $this->assertTrue($rule->applyRuleToContent('CREATE TABLE'));
        $this->assertSame('CREATE', $rule->getValue());
        $this->assertSame(6, $rule->getSourceLength());
        $this->assertTrue($rule->applyRuleToContent('ALTER TABLE'));
        $this->assertSame('ALTER', $rule->getValue());
        $this->assertSame(5, $rule->getSourceLength());
        $this->assertTrue($rule->applyRuleToContent('DELETE FROM TABLE'));
        $this->assertSame('DELETE', $rule->getValue());
        $this->assertSame(6, $rule->getSourceLength());
        $this->assertTrue($rule->applyRuleToContent('DROP TABLE'));
        $this->assertSame('DROP', $rule->getValue());
        $this->assertSame(4, $rule->getSourceLength());
    }
}
