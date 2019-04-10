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

namespace nabu\lexer\grammar;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexerGrammarProxy;

use nabu\min\CNabuObject;

/**
 * Abstract Lexer Language proxy.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\grammar\mysql
 */
abstract class CNabuAbstractLexerGrammarProxy extends CNabuObject implements INabuLexerGrammarProxy
{
    /** @var array|null List of Lexer version classes. */
    private $versions = null;

    /**
     * Register all Lexer Version Classes of know available Lexer versions.
     * @param array $versions Associative array with minimum version as key and instance class as value.
     * @throws ENabuLexerException Throws an exception if each version number and lexer class are not compatibles.
     */
    protected function registerLexerVersionClasses(array $versions)
    {
        foreach ($versions as $version => $class) {
            if ($class::isValidVersion($version)) {
                if (is_array($this->versions)) {
                    $this->versions[$version] = $class;
                } else {
                    $this->versions = array($version => $class);
                }
            } else {
                throw new ENabuLexerException(ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_MIN_VERSION);
            }
        }
    }
}
