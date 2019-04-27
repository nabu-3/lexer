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

namespace nabu\lexer;

use PHPUnit\Framework\TestCase;

use nabu\lexer\CNabuLexer;

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\grammar\mysql\CNabuLexerMySQL57;
use nabu\lexer\grammar\mysql\CNabuLexerMySQL81;

use nabu\lexer\grammar\unittests2\CNabuLexerGrammarTestSubclass1;
use nabu\lexer\grammar\unittests2\CNabuLexerGrammarTestSubclass3;

use nabu\lexer\interfaces\INabuLexer;

/**
 * Test class for @see { CNabuLexer }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer
 */
class CNabuLexerTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstruct_1()
    {
        $this->expectException(\Error::class);
        new CNabuLexer();
    }

    /**
     * @test __construct
     * @test getLexer
     */
    public function testConstruct_2()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE);
        CNabuLexerGrammarTestSubclass3::getLexer();
    }

    /**
     * @test getLexer
     * @test preloadFileResources
     * @test loadFileResources
     * @test processJSONHeader
     * @test processJSONRules
     */
    public function testGetLexerSuccess()
    {
        $this->assertInstanceOf(
            CNabuLexerMySQL57::class,
            CNabuLexer::getLexer(CNabuLexer::GRAMMAR_MYSQL, '5.7'),
            'Test getting Lexer for MySQL v.5.7'
        );
        $this->assertInstanceOf(
            CNabuLexerMySQL57::class,
            CNabuLexer::getLexer(CNabuLexer::GRAMMAR_MYSQL, '5.7.24'),
            'Test getting Lexer for MySQL v.5.7.24'
        );
        $this->assertInstanceOf(
            CNabuLexerMySQL81::class,
            CNabuLexer::getLexer(CNabuLexer::GRAMMAR_MYSQL, '8.1'),
            'Test getting Lexer for MySQL v.8.1'
        );
        $this->assertInstanceOf(
            CNabuLexerMySQL81::class,
            CNabuLexer::getLexer(CNabuLexer::GRAMMAR_MYSQL, '9.0'),
            'Test getting Lexer for MySQL v.9.0'
        );
    }

    /**
     * @test getLexer
     */
    public function testGetLexerFails1()
    {
        $this->expectException(ENabuLexerException::class, 'Test getting Lexer for MySQL v.5.8');
        $this->expectExceptionCode(ENabuLexerException::ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION);
        CNabuLexer::getLexer(CNabuLexer::GRAMMAR_MYSQL, '5.8');
    }

    /**
     * @test getLexer
     */
    public function testGetLexerFails2()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS);
        CNabuLexer::getLexer('unittests', '5.6');
    }

    /**
     * @test getLexer
     */
    public function testGetLexerLoadWithoutResourcesFile()
    {
        $this->assertInstanceOf(CNabuLexerGrammarTestSubclass1::class, CNabuLexer::getLexer('unittests2', '1.8'));
    }

    /**
     * @test getLexer
     */
    public function testGetLexerFails4()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION);
        CNabuLexer::getLexer('unittests2', '3.2');
    }

    /**
     * @test getLexer
     */
    public function testGetLexerFails5()
    {
        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_INVALID_LEXER_CLASS);
        CNabuLexer::getLexer('unittests3', '3.2');
    }

    /**
     * @test getGrammarName
     */
    public function testGetGrammarName()
    {
        $this->assertNull(CNabuLexer::getGrammarName());
    }

    /**
     * @test getMinimumVersion
     */
    public function testGetMinimumVersion()
    {
        $this->assertNull(CNabuLexer::getMinimumVersion());
    }

    /**
     * @test getMaximumVersion
     */
    public function testGetMaximumVersion()
    {
        $this->assertNull(CNabuLexer::getMaximumVersion());
    }

    /**
     * @test getData
     * @test setData
     */
    public function testGetSetData()
    {
        $lexer = CNabuCustomLexer::getLexer();
        $this->assertInstanceOf(CNabuCustomLexer::class, $lexer);

        $data = new CNabuLexerData();
        $this->assertInstanceOf(INabuLexer::class, $lexer->setData($data));
        $this->assertSame($data, $lexer->getData());
    }
}
