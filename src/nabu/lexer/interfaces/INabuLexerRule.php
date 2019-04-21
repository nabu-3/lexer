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
 * Interface of Lexer Rule.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\interfaces
 */
interface INabuLexerRule
{
    /**
     * Creates a Rule using a descriptor array info.
     * @param INabuLexer $lexer Lexer that governs this Rule.
     * @param array $descriptor Descriptor information.
     * @return INabuLexerRule Returns a rule instance parametrized matching the descriptor.
     * @throws ENabuLexerException Throws an exception if the descriptor is invalid.
     */
    public static function createFromDescriptor(INabuLexer $lexer, array $descriptor) : INabuLexerRule;
    /**
     * Init the instance using a descriptor array info.
     * @param array $descriptor The descriptor array to init the instance.
     * @throws ENabuLexerException Throws an exception if the descriptor is not valod.
     */
    public function initFromDescriptor(array $descriptor): void;
    /**
     * Applies the rule to a content string.
     * @param string $content The content to be analized.
     * @return bool Returns true if the Rule is successful applied.
     */
    public function applyRuleToContent(string $content) : bool;
    /**
     * Returns the value stored in the last execution of the rule.
     * @return mixed Returns the value according to rule specifications. If case of no value setted, then returns null.
     */
    public function getValue();
    /**
     * Returns the amount of characters needed in the source string to detect and extract the value.
     * @return int Returns the amount.
     */
    public function getSourceLength() : int;
    /**
     * Set the value of the rule.
     * @param mixed $value The value to be stored. It can be of any type.
     * @param int $source_length Amount of characters used to detect and extract the value in the source string.
     * @return INabuLexerRule Returns self pointer to grant fluent interface.
     */
    public function setValue($value, int $source_length): INabuLexerRule;
    /**
     * Append a value of the rule.
     * @param mixed $value The value to be stored. It can be of any type.
     * @param int $source_length Amount of characters used to detect and extract the value in the source string.
     * @return INabuLexerRule Returns self pointer to grant fluent interface.
     */
    public function appendValue($value, int $source_length): INabuLexerRule;
    /**
     * Clear previous stored value of the rule.
     * @return INabuLexerRule Returns self pointer to grant fluent interface.
     */
    public function clearValue(): INabuLexerRule;
    /**
     * Check if the rule is a starter rule or could be placed in any moment.
     * @return bool Returns true if it is a starter rule.
     */
    public function isStarter() : bool;
    /**
     * Get the path of the value repressenting this rule.
     * @return string|null Returns the path if setted or null otherwise.
     */
    public function getPath(): ?string;
    /**
     * Get the Lexer that governs this Rule.
     * @return INabuLexer Returns assigned Lexer.
     */
    public function getLexer(): INabuLexer;
}
