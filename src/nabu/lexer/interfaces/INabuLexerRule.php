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
     * @param array $descriptor Descriptor information.
     * @return INabuLexerRule Returns a rule instance parametrized matching the descriptor.
     * @throws ENabuLexerException Throws an exception if the descriptor is invalid.
     */
    public static function createFromDescriptor(array $descriptor) : INabuLexerRule;
    /**
     * Init the instance using a descriptor array info.
     * @param array $descriptor The descriptor array to init the instance.
     * @throws ENabuLexerException Throws an exception if the descriptor is not valod.
     */
    public function initFromDescriptor(array $descriptor);
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
     * Set the value of the rule.
     * @param mixed $value The value to be stored. It can be of any type.
     */
    public function setValue($value);
    /**
     * Clear previous stored value of the rule.
     */
    public function clearValue();
    /**
     * Check if the rule is a starter rule or could be placed in any moment.
     * @return bool Returns true if it is a starter rule.
     */
    public function isStarter() : bool;
    /**
     * Check it hte rule is case sensitive.
     * @return bool Returns true if it is case sensitive.
     */
    public function isCaseSensitive() : bool;
}