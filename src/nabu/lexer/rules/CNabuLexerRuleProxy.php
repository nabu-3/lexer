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

use Iterator;

use nabu\lexer\CNabuLexer;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexer;
use nabu\lexer\interfaces\INabuLexerRule;

use nabu\min\CNabuObject;

/**
 * MySQL Lexer Rule Proxy.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleProxy extends CNabuObject implements Iterator
{
    /** @var string Descriptor rule node literal. */
    const DESCRIPTOR_RULE_NODE = 'rule';
    /** @var string Descriptor clone node literal. */
    const DESCRIPTOR_CLONE_NODE = 'clone';

    /** @var CNabuLexer $lexer Lexer associated to this Proxy. */
    private $lexer = null;
    /** @var array $inventory Rules inventory associated to this Proxy instance. */
    private $inventory = null;
    /** @var int $inventory_position Position for $inventory iterator. */
    private $inventory_position = 0;

    public function __construct(CNabuLexer $lexer)
    {
        $this->lexer = $lexer;
        $this->inventory = null;
        $this->inventory_position = 0;
    }

    public function current()
    {
        $current = null;

        if (is_array($this->inventory)) {
            $keys = array_keys($this->inventory);
            $current = $this->inventory[$keys[$this->inventory_position]];
        }

        return $current;
    }

    public function next()
    {
        $this->inventory_position++;
    }

    public function key()
    {
        $key = null;

        if (is_array($this->inventory)) {
            $keys = array_keys($this->inventory);
            $key = $keys[$this->inventory_position];
        }

        return $key;
    }

    public function valid()
    {
        $size = is_array($this->inventory) ? count($this->inventory) : 0;

        return $this->inventory_position < $size;
    }

    public function rewind()
    {
        $this->inventory_position = 0;
    }

    /**
     * Create a Rule depending on a descriptor array structure.
     * @param INabuLexer $lexer Lexeer that will govern described rule.
     * @param array $descriptor The descriptor array.
     * @return INabuLexerRule Returns an specialized rule to dispatch grammar using the descriptor.
     * @throws ENabuLexerException Throws an exception if no rule applicable for descriptor.
     */
    public static function createRuleFromDescriptor(INabuLexer $lexer, array $descriptor) : INabuLexerRule
    {
        $rule = null;

        if (array_key_exists(CNabuLexerRuleGroup::DESCRIPTOR_GROUP_NODE, $descriptor)) {
            $rule = CNabuLexerRuleGroup::createFromDescriptor($lexer, $descriptor);
        } elseif (array_key_exists(CNabuLexerRuleKeyword::DESCRIPTOR_KEYWORD_NODE, $descriptor)) {
            $rule = CNabuLexerRuleKeyword::createFromDescriptor($lexer, $descriptor);
        } elseif (array_key_exists(CNabuLexerRuleRegEx::DESCRIPTOR_MATCH_NODE, $descriptor)) {
            $rule = CNabuLexerRuleRegEx::createFromDescriptor($lexer, $descriptor);
        } elseif (array_key_exists(CNabuLexerRuleRepeat::DESCRIPTOR_REPEAT_NODE, $descriptor)) {
            $rule = CNabuLexerRuleRepeat::createFromDescriptor($lexer, $descriptor);
        } elseif (array_key_exists(self::DESCRIPTOR_RULE_NODE, $descriptor)) {
            if (is_string($descriptor[self::DESCRIPTOR_RULE_NODE])) {
                $rule = $lexer->getRule($descriptor[self::DESCRIPTOR_RULE_NODE]);
            } else {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR,
                    array(var_export($descriptor[self::DESCRIPTOR_RULE_NODE], true))
                );
            }
        } elseif (array_key_exists(self::DESCRIPTOR_CLONE_NODE, $descriptor)) {
            if (is_string($descriptor[self::DESCRIPTOR_CLONE_NODE]) &&
                ($source = $lexer->getRule($descriptor[self::DESCRIPTOR_CLONE_NODE])) instanceof INabuLexerRule
            ) {
                $rule = clone $source;
                $rule->overrideFromDescriptor($descriptor);
            }
        }

        if (is_null($rule)) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR,
                array(var_export($descriptor, true))
            );
        }

        return $rule;
    }

    /**
     * Register a Rule associated to a key.
     * @param string $key Key to identify the Rule.
     * @param INabuLexerRule $rule Rule to register.
     * @throws ENabuLexerException Throws an exception if the rule already exists.
     */
    public function registerRule(string $key, INabuLexerRule $rule)
    {
        if (is_null($this->inventory)) {
            $this->inventory = array( $key => $rule);
        } elseif (!array_key_exists($key, $this->inventory)) {
            $this->inventory[$key] = $rule;
        } else {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_ALREADY_EXISTS, array($key));
        }
    }

    /**
     * Get a Rule by his key.
     * @param string $key Key to identify the Rule.
     * @return INabuLexerRule Returns the Rule identified by the key.
     * @throws ENabuLexerException Throws an exception if the Rule does not exists.
     */
    public function getRule(string $key): INabuLexerRule
    {
        if (!is_array($this->inventory) || !array_key_exists($key, $this->inventory)) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_DOES_NOT_EXISTS, array($key));
        }

        return $this->inventory[$key];
    }

}
