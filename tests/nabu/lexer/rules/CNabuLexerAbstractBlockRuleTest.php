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

/**
 * Test class for @see { CNabuLexerAbstractBlockRule }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerAbstractBlockRuleTest extends TestCase
{
    /** @var CNabuCustomLexer $lexer Lexer instance to be used across all tests. */
    private $lexer;

    public function setUp(): void
    {
        $this->lexer = CNabuCustomLexer::getLexer();
    }

    /**
     * @test initFromDescriptor
     * @test getPushPath
     */
    public function testGetPushPath()
    {
        $rule = CNabuLexerAbstractBlockRuleTesting::createFromDescriptor(
            $this->lexer,
            array(
                'push' => 'push_node'
            )
        );
        $this->assertInstanceOf(CNabuLexerAbstractBlockRuleTesting::class, $rule);
        $this->assertSame('push_node', $rule->getPushPath());
    }
}

class CNabuLexerAbstractBlockRuleTesting extends CNabuLexerAbstractBlockRule
{
    public function applyRuleToContent(string $content): bool
    {
        throw new \LogicException('Not implemented'); // TODO
    }
}
