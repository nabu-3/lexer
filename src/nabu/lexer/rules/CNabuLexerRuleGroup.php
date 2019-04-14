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

/**
 * MySQL Lexer Rule to parse a group of rules.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleGroup extends CNabuLexerAbstractRule
{
    /** @var string Descriptor method node literal. */
    const DESCRIPTOR_METHOD_NODE = 'method';
    /** @var string Descriptor group node literal. */
    const DESCRIPTOR_GROUP_NODE = 'group';
    /** @var string Descriptor tokenizer node literal. */
    const DESCRIPTOR_TOKENIZER_NODE = 'tokenizer';

    /** @var string Method Case literal. */
    const METHOD_CASE = 'case';
    /** @var string Method sequence literal. */
    const METHOD_SEQUENCE = 'sequence';
    /** @var array Methods list. */
    const METHOD_LIST = array(
        self::METHOD_CASE,
        self::METHOD_SEQUENCE
    );

    /** @var string $method Method used to apply keywords. */
    private $method = null;
    /** @var INabuLexerRule $tokenizer Rule that acts as separator between sequenced items. */
    private $tokenizer = null;
    /** @var array $group Rule list applicable. */
    private $group = null;

    public function initFromDescriptor(array $descriptor)
    {
        parent::initFromDescriptor($descriptor);

        $this->method = $this->checkEnumLeaf(
            $descriptor, self::DESCRIPTOR_METHOD_NODE, self::METHOD_LIST,  null, false, true
        );

        if ($this->method === self::METHOD_SEQUENCE) {
            $separator = $this->checkMixedValue($descriptor, self::DESCRIPTOR_TOKENIZER_NODE, null, false, true);
            if (is_string($separator)) {
                $this->tokenizer = CNabuLexerRuleKeyword::createFromDescriptor(
                    array(
                        'method' => 'literal',
                        'keyword' => $separator
                    )
                );
            } elseif (is_array($separator)) {
                $this->tokenizer = CNabuLexerRuleProxy::createRuleFromDescriptor($separator);
            } else {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array(
                        self::DESCRIPTOR_TOKENIZER_NODE,
                        'string, rule'
                    )
                );
            }
        }

        $group_desc = $this->checkArrayNode($descriptor, self::DESCRIPTOR_GROUP_NODE, null, false, true);

        $this->group = array();

        foreach ($group_desc as $rule_desc) {
            if (is_string($rule_desc)) {
                $this->group[] = CNabuLexerRuleKeyword::createFromDescriptor(
                    array(
                        'method' => 'literal',
                        'keyword' => $rule_desc
                    )
                );
            } else {
                $this->group[] = CNabuLexerRuleProxy::createRuleFromDescriptor($rule_desc);
            }
        }
    }

    public function applyRuleToContent(string $content): bool
    {
        $this->clearValue();

        if (!is_array($this->group) || count($this->group) === 0) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_EMPTY_GROUP_RULE);
        }

        $retval = false;

        switch ($this->method) {
            case self::METHOD_CASE:
                $retval = $this->applyRuleToContentAsCase($content);
                break;
            case self::METHOD_SEQUENCE:
                $retval = $this->applyRuleToContentAsSequence($content);
                break;
            default:
                throw new ENabuLexerException(ENabuLexerException::ERROR_INVALID_RULE_METHOD, array($this->method));
        }

        return $retval;
    }

    /**
     * Applies the group as a switch/case structure.
     * @param string $content The content to be analized.
     * @return bool Return true if some of cases are found.
     */
    private function applyRuleToContentAsCase(string $content): bool
    {
        $retval = false;

        foreach ($this->group as $rule) {
            if ($retval = $rule->applyRuleToContent($content)) {
                $this->setValue($rule->getValue(), $rule->getSourceLength());
                break;
            }
        }

        return $retval;
    }
}
