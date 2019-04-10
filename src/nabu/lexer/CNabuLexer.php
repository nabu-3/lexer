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

use Error;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexer;

use nabu\min\CNabuObject;

/**
 * Main class to implement a Lexer.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer
 */
class CNabuLexer extends CNabuObject implements INabuLexer
{
    /** @var string Language MySQL */
    public const GRAMMAR_MYSQL = 'mysql';

    /** @var string Language name used by this Lexer. */
    private $grammar_name = null;
    /** @var string Language version used by this lexer. */
    private $grammar_version = null;

    /**
     * Protected constructor invoqued from the getFactory instance.
     * @param string $grammar_name Language name used in this Lexer.
     * @param string $grammar_version Language version used by this Lexer.
     */
    protected function __construct(string $grammar_name, string $grammar_version)
    {
        $this->grammar_name = $grammar_name;
        $this->grammar_version = $grammar_version;
    }

    public static function getLexer(string $grammar_name, string $grammar_version) : CNabuLexer
    {
        try {
            $class_name = "nabu\\lexer\\grammar\\$grammar_name\\CNabuLexerLanguageProxy";
            $proxy = new $class_name();
        } catch (Error $e) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS, array($grammar_name));
        }

        return $proxy->getLexer($grammar_version);
    }
}
