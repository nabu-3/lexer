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

namespace nabu\lexer\interfaces;

use nabu\lexer\CNabuLexer;

use nabu\lexer\exceptions\ENabuLexerException;

/**
 * Interface of Lexer Language proxies.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\interfaces
 */
interface INabuLexer
{
    /**
     * Gets a valid Lexer for the grammar and versions requested.
     * @param string $grammar_name Grammar name requested.
     * @param string $grammar_version Grammar version requested.
     * @return CNabuLexer Returns a new Lexer instance.
     * @throws ENabuLexerException Throws an exception if no valid Lexer is found.
     */
    public static function getLexer(string $grammar_name, string $grammar_version) : CNabuLexer;
}
