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

namespace nabu\lexer\exceptions;

use PHPUnit\Framework\TestCase;

/**
 * Test class for @see ENabuLexerException.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer
 */
class ENabuLexerExceptionTest extends TestCase
{
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
     */
    public function testConstruct_ERROR_LEXER_CONSTRUCTOR_INVOQUED()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(ENabuLexerException::ERROR_LEXER_CONSTRUCTOR_INVOQUED);
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(
            ENabuLexerException::ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS,
            array(
                'lexer_test'
            )
        );
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(
            ENabuLexerException::ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION,
            array(
                '5.7.25'
            )
        );
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(
            ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE,
            array(
                '5.7.0',
                '5.7.9999'
            )
        );
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_INVALID_LEXER_CLASS()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(
            ENabuLexerException::ERROR_INVALID_LEXER_CLASS,
            array(
                __CLASS__
            )
        );
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_ERROR_INVALID_GRAMMAR_RESOURCE_FILE()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(
            ENabuLexerException::ERROR_INVALID_GRAMMAR_RESOURCE_FILE,
            array(
                __FILE__
            )
        );
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_RESOURCE_GRAMMAR_DESCRIPTION_MISSING()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(ENabuLexerException::ERROR_RESOURCE_GRAMMAR_DESCRIPTION_MISSING);
    }

    /**
     * @test __construct
     */
    public function testConstruct_ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH()
    {
        $this->expectException(ENabuLexerException::class);
        throw new ENabuLexerException(
            ENabuLexerException::ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH,
            array(
                '5.7',
                '5.8'
            )
        );
    }
}
