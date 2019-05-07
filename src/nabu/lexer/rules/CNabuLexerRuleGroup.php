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

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexerRule;

/**
 * MySQL Lexer Rule to parse a group of rules.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleGroup extends CNabuLexerAbstractBlockRule
{
    /** @var string Descriptor group node literal. */
    const DESCRIPTOR_GROUP_NODE = 'group';
    /** @var string Descriptor method node literal. */
    const DESCRIPTOR_METHOD_NODE = 'method';
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

    /**
     * Get the method attribute.
     * @return string|null Returns the value of method attribute.
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Get the tokenizer attribute.
     * @return INabuLexerRule|null Returns the value of tokenizer attribute.
     */
    public function getTokenizer(): ?INabuLexerRule
    {
        return $this->tokenizer;
    }

    public function initFromDescriptor(array $descriptor): void
    {
        parent::initFromDescriptor($descriptor);

        $this->method = $this->checkEnumNode(
            $descriptor, self::DESCRIPTOR_METHOD_NODE, self::METHOD_LIST,  null, false, true
        );

        if ($this->method === self::METHOD_SEQUENCE) {
            $separator = $this->checkMixedNode($descriptor, self::DESCRIPTOR_TOKENIZER_NODE);
            if (is_string($separator)) {
                $this->tokenizer = $this->getLexer()->getRule($separator);
            } elseif (is_array($separator)) {
                $this->tokenizer = CNabuLexerRuleProxy::createRuleFromDescriptor($this->getLexer(), $separator);
            } elseif (!is_null($separator) && !($separator instanceof INabuLexerRule)) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array(
                        self::DESCRIPTOR_TOKENIZER_NODE,
                        var_export($separator, true),
                        'rule, descriptor'
                    )
                );
            }
        }

        $group_desc = $this->checkArrayNode($descriptor, self::DESCRIPTOR_GROUP_NODE, null, false, true);

        $this->group = array();

        foreach ($group_desc as $rule_desc) {
            if (is_string($rule_desc)) {
                $this->group[] = CNabuLexerRuleKeyword::createFromDescriptor(
                    $this->getLexer(),
                    array(
                        CNabuLexerRuleKeyword::DESCRIPTOR_METHOD_NODE => CNabuLexerRuleKeyword::METHOD_LITERAL,
                        CNabuLexerRuleKeyword::DESCRIPTOR_KEYWORD_NODE => $rule_desc
                    )
                );
            } else {
                $this->group[] = CNabuLexerRuleProxy::createRuleFromDescriptor($this->getLexer(), $rule_desc);
            }
        }
    }

    public function applyRuleToContent(string $content): bool
    {
        $pushed = $this->pushPath();
        $this->clearTokens();

        if (!is_array($this->group) || count($this->group) === 0) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_EMPTY_GROUP_RULE);
        }

        $retval = false;

        switch ($this->method) {
            case self::METHOD_SEQUENCE:
                $retval = $this->applyRuleToContentAsSequence($content);
                break;
            case self::METHOD_CASE:
            default:
                $retval = $this->applyRuleToContentAsCase($content);
                break;
        }

        if (!$retval && $this->isOptional()) {
            $retval = true;
        }

        $pushed && $this->popPath();

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
        $this->clearTokens();

        if (mb_strlen($content) > 0) {
            foreach ($this->group as $rule) {
                if ($retval = $rule->applyRuleToContent($content)) {
                    $this->setToken($rule->getTokens(), $rule->getSourceLength());
                    $this->setPathValue($rule->getTokens());
                    break;
                }
            }
        }

        return $retval;
    }

    /**
     * Applies the group as a sequence structure.
     * @param string $content The content to be analized.
     * @return bool Return true if some of cases are found.
     */
    private function applyRuleToContentAsSequence(string $content): bool
    {
        $retval = false;
        $this->clearTokens();

        if (is_array($this->group) && count($this->group) > 0) {
            $first = true;
            foreach ($this->group as $rule) {
                if (!($retval = $this->applyRuleToContentAsSequenceInternal($rule, $content, $first))) {
                    break;
                }
            }
        }

        if ($retval) {
            $this->setPathValue($this->getTokens());
        }

        return $retval;
    }

    /**
     * Subprocess to aply Rule to content as Sequence.
     * @param INabuLexerRule $rule Rule to be applied.
     * @param string &$content Current content buffer to be analized.
     * @param bool &$first If true then this call is the first and does not apply the tokenizer.
     * @return bool Returns true if Rule was applied.
     */
    private function applyRuleToContentAsSequenceInternal(INabuLexerRule $rule, string &$content, bool &$first): bool
    {
        $retval = false;
        $tkl = 0;
        $tkv = null;
        $cursor = $content;

        if (!$first &&
            $this->tokenizer instanceof INabuLexerRule &&
            $this->tokenizer->applyRuleToContent($cursor)
        ) {
            $tkv = $this->tokenizer->getTokens();
            $tkl = $this->tokenizer->getSourceLength();
            $cursor = mb_substr($cursor, $tkl);
        }

        if ($rule->applyRuleToContent($cursor)) {
            $l = $rule->getSourceLength();
            if ($l > 0) {
                if ($tkl > 0) {
                    $this->appendTokens($tkv, $tkl);
                }
                $this->appendTokens($rule->getTokens(), $l);
                $content = mb_substr($content, $tkl + $l);
                $first = false;
            }
            $retval = true;
        } else {
            $this->clearTokens();
            $retval = false;
        }

        return $retval;
    }
}
