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
 * Test class for @see CNabuLexerRuleProxy.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleProxyTest extends TestCase
{
    /**
     * @test createRuleFromDescriptor
     * @return CNabuLexerRuleGroup Returns created rule for tests.
     */
    public function testCreateRuleFromDescriptorRuleGroup() : CNabuLexerRuleGroup
    {
        $rule = CNabuLexerRuleProxy::createRuleFromDescriptor(
            array(
                "starter" => true,
                "case_sensitive" => false,
                "method" => "case",
                "group" => array(
                    "CREATE", "DROP"
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);

        return $rule;
    }

    /**
     * @test applyRuleToContent
     * @test getValue
     * @test setValue
     * @test clearValue
     * @depends testCreateRuleFromDescriptorRuleGroup
     * @param CNabuLexerRuleGroup $rule Rule created in previous test.
     */
    public function testApplyRuleToContentRuleGroup(CNabuLexerRuleGroup $rule)
    {
        $this->assertTrue($rule->applyRuleToContent('CREATE TABLE'));
        $this->assertSame('CREATE', $rule->getValue());
        $this->assertTrue($rule->applyRuleToContent('DROP TABLE'));
        $this->assertSame('DROP', $rule->getValue());
        $this->assertFalse($rule->applyRuleToContent('ALTER TABLE'));
        $this->assertNull($rule->getValue());
    }
}
