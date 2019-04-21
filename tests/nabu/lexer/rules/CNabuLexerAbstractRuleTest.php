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

/**
 * Test class for @see { CNabuLexerAbstractRule }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerAbstractRuleTest extends TestCase
{
    private $lexer;

    public function setUp(): void
    {
        $this->lexer = CNabuCustomLexer::getLexer();
    }

    /**
     * @test getPath
     */
    public function testGetPath()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => true,
                'string_required' => 'required',
                'string_null' => 'nullable',
                'path' => 'a.b.c'
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame('a.b.c', $rule->getPath());

        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => true,
                'string_required' => 'required',
                'string_null' => 'nullable',
                'other' => 'a.b.c'
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertNull($rule->getPath());
    }

    /**
     * @test checkBooleanLeaf
     */
    public function testCheckBooleanLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => true,
                'string_required' => 'required',
                'string_null' => 'nullable'
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertTrue($rule->getBoolRequired());

        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => 'nullable'
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertFalse($rule->getBoolRequired());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'string_required' => 'required',
                'string_null' => 'nullable',
                'other' => null
            )
        );
    }

    /**
     * @test checkBooleanLeaf
     */
    public function testCheckBooleanLeafFails()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => 'test string',
                'string_required' => 'required',
                'string_null' => 'nullable'
            )
        );
    }

    /**
     * @test checkStringLeaf
     */
    public function testCheckStringLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame('required', $rule->getStringRequired());
        $this->assertNull($rule->getStringNull());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[string]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => null,
                'string_null' => 'nullable'
            )
        );
    }

    /**
     * @test checkStringLeaf
     */
    public function testCheckStringLeafFails1()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[string, null]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => array()
            )
        );
    }

    /**
     * @test checkStringLeaf
     */
    public function testCheckStringLeafFails2()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_null' => null
            )
        );
    }
}

class CNabuLexerAbstractRuleTestingLeafs extends CNabuLexerAbstractRule
{
    /** @var bool $bool_required Required bool parameter. */
    private $bool_required = false;
    /** @var string|null $string_required Required string parameter. */
    private $string_required = null;
    /** @var string|null $string_null Null string parameter. */
    private $string_null = null;

    /**
     * @return bool
     */
    public function getBoolRequired(): bool
    {
        return $this->bool_required;
    }

    /**
     * @return string|null
     */
    public function getStringRequired(): ?string
    {
        return $this->string_required;
    }

    /**
     * @return string|null
     */
    public function getStringNull(): ?string
    {
        return $this->string_null;
    }

    public function initFromDescriptor(array $descriptor): void
    {
        parent::initFromDescriptor($descriptor);

        $this->bool_required = $this->checkBooleanLeaf($descriptor, 'bool_required', false, true);
        $this->string_required = $this->checkStringLeaf($descriptor, 'string_required', null, false, true);
        $this->string_null = $this->checkStringLeaf($descriptor, 'string_null', null, true, true);
    }

    public function applyRuleToContent(string $content): bool
    {
        return true;
    }
}
