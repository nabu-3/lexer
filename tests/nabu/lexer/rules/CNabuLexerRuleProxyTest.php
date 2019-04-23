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

use PHPUnit\Framework\TestCase;

use nabu\lexer\CNabuCustomLexer;

use nabu\lexer\exceptions\ENabuLexerException;

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
    public function testCreateRuleFromDescriptor()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $rule = CNabuLexerRuleProxy::createRuleFromDescriptor(
            $lexer,
            array(
                "starter" => true,
                "method" => "literal",
                "keyword" => "test"
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleKeyword::class, $rule);

        $rule = CNabuLexerRuleProxy::createRuleFromDescriptor(
            $lexer,
            array(
                "starter" => true,
                "method" => "literal",
                "match" => "(.*)"
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule);

        $rule = CNabuLexerRuleProxy::createRuleFromDescriptor(
            $lexer,
            array(
                "starter" => true,
                "method" => "case",
                "group" => array(
                    "CREATE", "DROP"
                )
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleGroup::class, $rule);

        $this->expectException(ENabuLexerException::class);
        CNabuLexerRuleProxy::createRuleFromDescriptor(
            $lexer,
            array(
                'starter' => true
            )
        );
    }

    /**
     * @test createRuleFromDescriptor
     */
    public function testCreateRuleFromDescriptorWithInvalidRule()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR);
        $rule = CNabuLexerRuleProxy::createRuleFromDescriptor(
            $lexer,
            array(
                'rule' => array(
                    'starter' => true,
                    'method' => 'literal',
                    'keyword' => 'Test'
                )
            )
        );
    }

    /**
     * @test registerRule
     */
    public function testRegisterRule()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $rule = CNabuLexerRuleKeyword::createFromDescriptor(
            $lexer,
            array(
                'keyword' => 'Test',
                'method' => 'literal'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleKeyword::class, $rule);
        $lexer->registerRule('rule_reg', $rule);
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_ALREADY_EXISTS);
        $lexer->registerRule('rule_reg', $rule);
    }

    /**
     * @test getRule
     */
    public function testGetRuleWithEmptyInventory()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_DOES_NOT_EXISTS);
        $lexer->getRule('rule_reg');
    }

    /**
     * @test getRule
     */
    public function testGetRuleThatDoesNotExists()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $rule = CNabuLexerRuleKeyword::createFromDescriptor(
            $lexer,
            array(
                'keyword' => 'Test',
                'method' => 'literal'
            )
        );
        $this->assertInstanceOf(CNabuLexerRuleKeyword::class, $rule);
        $lexer->registerRule('rule_reg', $rule);
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_DOES_NOT_EXISTS);
        $lexer->getRule('another_rule');
    }

}
