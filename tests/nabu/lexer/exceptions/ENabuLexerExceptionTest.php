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

namespace nabu\lexer\exceptions;

use PHPUnit\Framework\TestCase;

/**
 * Test class for @see { ENabuLexerException }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer
 */
class ENabuLexerExceptionTest extends TestCase
{
    /**
     * Data provider to test exceptions list.
     * @return array Returns the list of exceptions to test and their sample data array.
     */
    public function constructProvider()
    {
        return [
            [ENabuLexerException::ERROR_LEXER_CONSTRUCTOR_INVOQUED, null],
            [ENabuLexerException::ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS, array('lexer_test')],
            [ENabuLexerException::ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION, array( '5.7.25')],
            [ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE, array( '5.7.0', '5.7.9999')],
            [ENabuLexerException::ERROR_INVALID_LEXER_CLASS, array(__CLASS__)],
            [ENabuLexerException::ERROR_INVALID_GRAMMAR_RESOURCE_FILE, array(__FILE__)],
            [ENabuLexerException::ERROR_RESOURCE_GRAMMAR_DESCRIPTION_MISSING, null],
            [ENabuLexerException::ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH, array('5.7', '5.8')],
            [ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR, null],
            [ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR, array('test_node', var_export(array('name' => 'value'), true))],
            [ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE, array('node', 'value', 'mixed')],
            [ENabuLexerException::ERROR_INVALID_RULE_METHOD, array('method_name')],
            [ENabuLexerException::ERROR_EMPTY_GROUP_RULE, null],
            [ENabuLexerException::ERROR_RULE_ALREADY_EXISTS, array('rule key')],
            [ENabuLexerException::ERROR_RULE_DOES_NOT_EXISTS, array('rule_key')],
            [ENabuLexerException::ERROR_INVALID_RANGE_VALUES, array('2..1')],
            [ENabuLexerException::ERROR_LEXER_DATA_INSTANCE_NOT_SET, null]
        ];
    }

    /**
     * @test __construct
     */
    public function testConstruct_Invalid_Exception_Code()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(0);
    }

    /**
     * @test __construct
     * @dataProvider constructProvider
     * @param int $code Exception code.
     * @param array|null $data Optional data to build message.
     */
    public function testConstruct_Valid_exceptions(int $code, array $data = null)
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException($code, $data);
    }

}
