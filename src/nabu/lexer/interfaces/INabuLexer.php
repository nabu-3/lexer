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
     * Gets a valid Lexer for the grammar and versions requested. If no grammar nor version provided, then
     * check if the Lexer instance is a final Lexer or the base class and returns a new instance in the first case
     * or throws an exception in the second case.
     * @param string|null $grammar_name Grammar name requested.
     * @param string|null $grammar_version Grammar version requested.
     * @return INabuLexer Returns a new Lexer instance.
     * @throws ENabuLexerException Throws an exception if no valid Lexer is found.
     */
    public static function getLexer(string $grammar_name = null, string $grammar_version = null) : INabuLexer;
    /**
     * Get the Grammar Name as defined for each Language supported.
     * @return string|null Returns the Grammar Name if defined or null otherwise.
     */
    public static function getGrammarName();
    /**
     * Get the Grammar minimum version as defined for each Language supported.
     * @return string|null Returns the Grammar minimum version if defined or null otherwise.
     */
    public static function getMinimumVersion();
    /**
     * Get the Grammar maximum version as defined for each Language supported.
     * @return string|null Returns the Grammar maximum version if defined or null otherwise.
     */
    public static function getMaximumVersion();
    /**
     * Check if an specific version is supported by this lexer.
     * @param string $version Version string to check.
     * @return bool Returns true if version is supported.
     */
    public static function isValidVersion(string $version) : bool;
    /**
     * Load and setup the Lexer from a File Resources descriptor containing all grammar rules to work.
     * @param string $filename Name of file to load.
     * @return bool Returns true if file is valid and resources are loaded, or false if file is empty or have no rules.
     * @throws ENabuLexerException Throws an exception if the file does not exists or have errors.
     */
    public function loadFileResources(string $filename) : bool;
}
