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

namespace nabu\lexer;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\rules\interfaces\INabuLexerRule;

/**
 * Main class to implement a Lexer.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer
 */
class CNabuLexer
{
    /**
     * Creates the instance and sets initial attributes.
     */
    public function __construct()
    {

    }

    /**
     * Add a Rule to Lexer.
     * @param INabuLexerRule $rule Rule instance to be added.
     * @return bool Returns true if the rule is added.
     * @throws ENabuLexerException Throws an exception if rule is wrong.
     */
    public function addRule(INabuLexerRule $rule) : bool
    {
        throw new ENabuLexerException();
    }
}
