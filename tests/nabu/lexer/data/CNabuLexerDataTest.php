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

namespace nabu\lexer\data;

use PHPUnit\Framework\TestCase;

use nabu\lexer\exceptions\ENabuLexerException;

/**
 * Test class for @see { CNabuLexerData }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer
 */
class CNabuLexerDataTest extends TestCase
{
    /**
     * @test getMainRuleName
     * @test setMainRuleName
     */
    public function testMainRuleName()
    {
        $data = new CNabuLexerData();
        $this->assertInstanceOf(CNabuLexerData::class, $data->setMainRuleName('rule_name'));
        $this->assertSame('rule_name', $data->getMainRuleName());
    }

    /**
     * @test pushPath
     * @test calculateBasePath
     */
    public function testPushAndCalculatePath()
    {
        $data = new CNabuLexerData();
        $this->assertInstanceOf(CNabuLexerData::class, $data->pushPath('level1'));
        $this->assertSame('level1', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->pushPath('level2'));
        $this->assertSame('level1.level2', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->pushPath('.rewind1.rewind2'));
        $this->assertSame('rewind1.rewind2', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->pushPath('level2'));
        $this->assertSame('rewind1.rewind2.level2', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->popPath());
        $this->assertSame('rewind1.rewind2', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->popPath());
        $this->assertSame('level1.level2', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->popPath());
        $this->assertSame('level1', $data->getWithPreffix());
        $this->assertInstanceOf(CNabuLexerData::class, $data->popPath());
        $this->assertNull($data->getWithPreffix());

        $this->expectException(ENabuLexerException::class);
        $this->expectExceptionCode(ENabuLexerException::ERROR_LEXER_DATA_PATH_IS_EMPTY);
        $data->pushPath('');
    }
}
