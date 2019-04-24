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
 * Test class for @see { CNabuLexerAbstractRule }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerAbstractRuleTest extends TestCase
{
    /** @var CNabuCustomLexer $lexer Lexer instance to be used across all tests. */
    private $lexer;

    public function setUp(): void
    {
        $this->lexer = CNabuCustomLexer::getLexer();
    }

    /**
     * @test getPath
     * @test isHidden
     */
    public function testGetPath()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => true,
                'string_required' => 'required',
                'string_null' => 'nullable',
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null,
                'path' => 'a.b.c',
                'hidden' => false
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame('a.b.c', $rule->getPath());
        $this->assertFalse($rule->isHidden());

        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => true,
                'string_required' => 'required',
                'string_null' => 'nullable',
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null,
                'other' => 'a.b.c',
                'hidden' => true
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertNull($rule->getPath());
        $this->assertTrue($rule->isHidden());
    }

    /**
     * @test checkBooleanLeaf
     * @test isHidden
     */
    public function testCheckBooleanLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => true,
                'string_required' => 'required',
                'string_null' => 'nullable',
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertTrue($rule->getBoolRequired());
        $this->assertFalse($rule->isHidden());

        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => 'nullable',
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
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
                'other' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
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
                'string_null' => 'nullable',
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
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
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
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
                'string_null' => 'nullable',
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'range_required' => '0..1',
                'range_null' => null
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
                'string_null' => array(),
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
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
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkEnumLeaf
     */
    public function testCheckEnumLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame('literal', $rule->getEnumRequired());
        $this->assertNull($rule->getEnumNull());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[literal, ignore case]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => null,
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkEnumLeaf
     */
    public function testCheckEnumLeafFails1()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[literal, ignore case, null]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => array(),
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkEnumLeaf
     */
    public function testCheckEnumLeafFails2()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_null' => array(),
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkArrayLeaf
     */
    public function testCheckArrayLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame(array('value'), $rule->getArrayRequired());
        $this->assertNull($rule->getArrayNull());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[array]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => 'value',
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkArrayLeaf
     */
    public function testCheckArrayLeafFails1()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[array, null]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => 'value',
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkArrayLeaf
     */
    public function testCheckArrayLeafFails2()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkMixedLeaf
     */
    public function testCheckMixedLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame('mixed', $rule->getMixedRequired());
        $this->assertNull($rule->getMixedNull());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[mixed]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => null,
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkMixedLeaf
     */
    public function testCheckMixedLeafFails1()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkRegExLeaf
     */
    public function testCheckRegExLeaf()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '\s+',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $this->assertSame('\s+', $rule->getRegExRequired());
        $this->assertNull($rule->getRegExNull());
        $this->assertSame('.*', $rule->getRegExDefault());

        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '[.*]',
                'regex_null' => null,
                'regex_default' => '[.*',
                'range_required' => '0..1',
                'range_null' => null
            )
        );
        $this->assertSame('.*', $rule->getRegExDefault());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[Regular Expression]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => null,
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkRegExLeaf
     */
    public function testCheckRegExLeafFails1()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE);
        $this->expectExceptionMessage('[Regular Expression]');
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '[.*',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * @test checkRegExLeaf
     */
    public function testCheckRegExLeafFails2()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => null
            )
        );
    }

    /**
     * Data Provider to perform normal tests of checkRangeLeaf 1
     */
    public function checkRangeLeaf1DataProvider()
    {
        return [
            [ '1', array(1, 1) ],
            [ '3', array(3, 3) ],
            [ '12', array(12, 12) ],
            [ 'n', array(1, 'n') ],
            [ 'inf', array(1, 'n') ],
            [ 'infinity', array(1, 'n') ],
            [ '∞', array(1, 'n') ],

            [ '0..1', array(0, 1) ],
            [ '0..45', array(0, 45) ],
            [ '1..1', array(1, 1) ],
            [ '1..30', array(1, 30) ],
            [ '0..n', array(0, 'n') ],
            [ '1..n', array(1, 'n') ],
            [ '3..n', array(3, 'n') ],
            [ '0..inf', array(0, 'n') ],
            [ '1..inf', array(1, 'n') ],
            [ '3..inf', array(3, 'n') ],
            [ '0..infinity', array(0, 'n') ],
            [ '1..infinity', array(1, 'n') ],
            [ '3..infinity', array(3, 'n') ],
            [ '0..∞', array(0, 'n') ],
            [ '1..∞', array(1, 'n') ],
            [ '3..∞', array(3, 'n') ],

            [ '0,1', array(0, 1) ],
            [ '0,45', array(0, 45) ],
            [ '1,1', array(1, 1) ],
            [ '1,30', array(1, 30) ],
            [ '0,n', array(0, 'n') ],
            [ '1,n', array(1, 'n') ],
            [ '3,n', array(3, 'n') ],
            [ '0,inf', array(0, 'n') ],
            [ '1,inf', array(1, 'n') ],
            [ '3,inf', array(3, 'n') ],
            [ '0,infinity', array(0, 'n') ],
            [ '1,infinity', array(1, 'n') ],
            [ '3,infinity', array(3, 'n') ],
            [ '0,∞', array(0, 'n') ],
            [ '1,∞', array(1, 'n') ],
            [ '3,∞', array(3, 'n') ],

            [ '0-1', array(0, 1) ],
            [ '0-45', array(0, 45) ],
            [ '1-1', array(1, 1) ],
            [ '1-30', array(1, 30) ],
            [ '0-n', array(0, 'n') ],
            [ '1-n', array(1, 'n') ],
            [ '3-n', array(3, 'n') ],
            [ '0-inf', array(0, 'n') ],
            [ '1-inf', array(1, 'n') ],
            [ '3-inf', array(3, 'n') ],
            [ '0-infinity', array(0, 'n') ],
            [ '1-infinity', array(1, 'n') ],
            [ '3-infinity', array(3, 'n') ],
            [ '0-∞', array(0, 'n') ],
            [ '1-∞', array(1, 'n') ],
            [ '3-∞', array(3, 'n') ]
        ];
    }

    /**
     * @test checkRangeLeaf
     * @test checkRangeLeafSingleValue
     * @test checkRangeLeafTupla
     * @dataProvider checkRangeLeaf1DataProvider
     * @param string $value Value to insert in the test.
     * @param array $range Range to obtain after call.
     */
    public function testCheckRangeLeaf1(string $value, array $range)
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '.*',
                'regex_null' => null,
                'range_required' => $value,
                'range_null' => null
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $parsed_range = $rule->getRangeRequired();
        if (is_array($range)) {
            $this->assertIsArray($parsed_range);
            $this->assertSame($range, $parsed_range);
        } else {
            $this->assertIsNull($parsed_range);
        }
    }

    /**
     * Data Provider to perform normal tests of checkRangeLeaf 1
     */
    public function checkRangeLeaf2DataProvider()
    {
        return array_merge($this->checkRangeLeaf1DataPrivder(), [
            [ null, null ]
        ]);
    }

    /**
     * @test checkRangeLeaf
     * @test checkRangeLeafSingleValue
     * @test checkRangeLeafTupla
     * @dataProvider checkRangeLeaf1DataProvider
     * @param string|null $value Value to insert in the test.
     * @param array|null $range Range to obtain after call.
     */
    public function testCheckRangeLeaf2(string $value = null, array $range = null)
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '.*',
                'regex_null' => null,
                'range_required' => '0..1',
                'range_null' => $value
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $parsed_range = $rule->getRangeNull();
        if (is_array($range)) {
            $this->assertIsArray($parsed_range);
            $this->assertSame($range, $parsed_range);
        } else {
            $this->assertIsNull($parsed_range);
        }
    }

    /**
     * @test checkRangeLeaf
     * @test checkRangeLeafSingleValue
     * @test checkRangeLeafTupla
     */
    public function testCheckRangeLeaf3()
    {
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '.*',
                'regex_null' => null,
                'range_required' => '1..n'
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractRuleTestingLeafs::class, $rule);
        $parsed_range = $rule->getRangeNull();
        $this->assertSame(array(1, 'n'), $rule->getRangeRequired());
        $this->assertIsArray($parsed_range);
        $this->assertSame(array(0, 1), $parsed_range);
    }

    /**
     * Data Provier for checkRangeLeaf fails
     */
    public function checkRangeLeafFailsDataProvider()
    {
        return [
            [ '0' ],
            [ 0 ],
            [ 1 ],
            [ 1.24 ],
            [ 0x009A ],
            [ array() ],
            [ '01' ],
            [ '024' ],
            [ '0n' ],
            [ '0inf' ],
            [ '0infinity' ],
            [ '0∞' ],
            [ 'something' ]
        ];
    }

    /**
     * @test checkRangeLeaf
     * @test checkRangeLeafSingleValue
     * @test checkRangeLeafTupla
     * @dataProvider checkRangeLeafFailsDataProvider
     * @param mixed $value Value to insert in the test.
     */
    public function testCheckRangeLeafFails($value)
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_INVALID_RANGE_VALUES);
        $rule = CNabuLexerAbstractRuleTestingLeafs::createFromDescriptor(
            $this->lexer,
            array(
                'bool_required' => false,
                'string_required' => 'required',
                'string_null' => null,
                'enum_required' => 'literal',
                'enum_null' => null,
                'array_required' => array('value'),
                'array_null' => null,
                'mixed_required' => 'mixed',
                'mixed_null' => null,
                'regex_required' => '.*',
                'regex_null' => null,
                'range_required' => $value,
                'range_null' => null
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
    /** @var string|null $enum_required Required enum parameter. */
    private $enum_required = null;
    /** @var string|null $enum_null Null enum parameter. */
    private $enum_null = null;
    /** @var array|null $array_required Required array parameter. */
    private $array_required = null;
    /** @var array|null $array_null Null array parameter. */
    private $array_null = null;
    /** @var mixed|null $mixed_required Required mixed parameter. */
    private $mixed_required = null;
    /** @var mixed|null $mixed_null Null mixed parameter. */
    private $mixed_null = null;
    /** @var string|null $regex_required Required regex parameter. */
    private $regex_required = null;
    /** @var string|null $regex_null Null regex parameter. */
    private $regex_null = null;
    /** @var string|null $regex_default Default regex parameter. */
    private $regex_default = null;
    /** @var array|null $range_required Required range value parameter. */
    private $range_required = null;
    /** @var array|null $range_null Null range parameter. */
    private $range_null = null;

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

    /**
     * @return string|null
     */
    public function getEnumRequired(): ?string
    {
        return $this->enum_required;
    }

    /**
     * @return string|null
     */
    public function getEnumNull(): ?string
    {
        return $this->enum_null;
    }

    /**
     * @return array|null
     */
    public function getArrayRequired(): ?array
    {
        return $this->array_required;
    }

    /**
     * @return array|null
     */
    public function getArrayNull(): ?array
    {
        return $this->array_null;
    }

    /**
     * @return mixed|null
     */
    public function getMixedRequired()
    {
        return $this->mixed_required;
    }

    /**
     * @return mixed|null
     */
    public function getMixedNull()
    {
        return $this->mixed_null;
    }

    /**
     * @return string|null
     */
    public function getRegExRequired(): ?string
    {
        return $this->regex_required;
    }

    /**
     * @return string|null
     */
    public function getRegExNull(): ?string
    {
        return $this->regex_null;
    }

    /**
     * @return string|null
     */
    public function getRegExDefault(): ?string
    {
        return $this->regex_default;
    }

    /**
     * @return array|null
     */
    public function getRangeRequired(): ?array
    {
        return $this->range_required;
    }

    /**
     * @return array|null
     */
    public function getRangeNull(): ?array
    {
        return $this->range_null;
    }

    public function initFromDescriptor(array $descriptor): void
    {
        parent::initFromDescriptor($descriptor);

        $this->bool_required = $this->checkBooleanLeaf($descriptor, 'bool_required', false, true);
        $this->string_required = $this->checkStringLeaf($descriptor, 'string_required', null, false, true);
        $this->string_null = $this->checkStringLeaf($descriptor, 'string_null', null, true, true);
        $this->enum_required = $this->checkEnumLeaf(
            $descriptor,
            'enum_required',
            array('literal', 'ignore case'),
            null,
            false,
            true
        );
        $this->enum_null = $this->checkEnumLeaf(
            $descriptor,
            'enum_null',
            array('literal', 'ignore case'),
            null,
            true,
            true
        );
        $this->array_required = $this->checkArrayNode($descriptor, 'array_required', null, false, true);
        $this->array_null = $this->checkArrayNode($descriptor, 'array_null', null, true, true);
        $this->mixed_required = $this->checkMixedNode($descriptor, 'mixed_required', null, false, true);
        $this->mixed_null = $this->checkMixedNode($descriptor, 'mixed_null', null, true, true);
        $this->regex_required = $this->checkRegExLeaf($descriptor, 'regex_required', false, null, false, true);
        $this->regex_null = $this->checkRegExLeaf($descriptor, 'regex_null', false, null, true, true);
        $this->regex_default = $this->checkRegExLeaf($descriptor, 'regex_default', false, '.*', true, false);
        $this->range_required = $this->checkRangeLeaf($descriptor, 'range_required', null, false, true);
        $this->range_null = $this->checkRangeLeaf($descriptor, 'range_null', '0..1', true, false);
    }

    public function applyRuleToContent(string $content): bool
    {
        return true;
    }
}
