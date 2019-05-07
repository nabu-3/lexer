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

namespace nabu\lexer\base;

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexer;

use nabu\min\CNabuObject;

/**
 * Lexer child base instance.
 * This class can be inherited by all Lexer dependent classes.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\base
 */
abstract class CNabuAbstractLexerChild extends CNabuObject
{
    /** @var INabuLexer $lexer Lexer that manages this rule. */
    private $lexer = null;

    /**
     * Creates the instance and sets initial attributes.
     * @param INabuLexer $lexer Lexer that governs this Rule,
     */
    public function __construct(INabuLexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Get the Lexer that governs this Rule.
     * @return INabuLexer Returns assigned Lexer.
     */
    public function getLexer(): INabuLexer
    {
        return $this->lexer;
    }

    /**
     * Get the Lexer Data instance. If she does not exists throws an exception.
     * @return CNabuLexerData Returns the data instance.
     */
    public function getLexerData(): CNabuLexerData
    {
        if (is_null($data = $this->lexer->getData())) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_LEXER_DATA_INSTANCE_NOT_SET);
        }

        return $data;
    }
}
