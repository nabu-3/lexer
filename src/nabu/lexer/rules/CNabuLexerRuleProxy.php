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

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexerRule;

use nabu\min\CNabuObject;

/**
 * MySQL Lexer Rule Proxy.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleProxy extends CNabuObject
{
    /** @var string Descriptor keywords node literal. */
    private const DESCRIPTOR_KEYWORDS_NODE = 'keywords';
    /**
     * Create a Rule depending on a descriptor array structure.
     * @param array $descriptor The descriptor array.
     * @return INabuLexerRule Returns an specialized rule to dispatch grammar using the descriptor.
     * @throws ENabuLexerException Throws an exception if no rule applicable for descriptor.
     */
    public static function createRuleFromDescriptor(array $descriptor) : INabuLexerRule
    {
        if (array_key_exists(self::DESCRIPTOR_KEYWORDS_NODE, $descriptor)) {

        }
    }
}
