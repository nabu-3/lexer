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

/**
 * MySQL Lexer Rule to parse a keyword.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleKeyword extends CNabuLexerAbstractRule
{
    /** @var string Descriptor keywords node literal. */
    const DESCRIPTOR_KEYWORD_NODE = 'keyword';
    /** @var string Descriptor method node literal. */
    const DESCRIPTOR_METHOD_NODE = 'method';

    /** @var string Method Literal literal. */
    const METHOD_LITERAL = 'literal';
    /** @var string Descriptor ignore case sensitive node literal. */
    const METHOD_IGNORE_CASE = 'ignore case';
    /** @var array Methods list. */
    const METHOD_LIST = array(
        self::METHOD_LITERAL,
        self::METHOD_IGNORE_CASE
    );

    /** @var string $method Method used to apply keywords. */
    private $method = null;
    /** @var string $keyword Keyword applicable. */
    private $keyword = null;

    /**
     * Get the method attribute.
     * @return string|null Returns the value of method attribute.
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Test if Rule is Case Sensitive.
     * @return bool Return true if it is.
     */
    public function isCaseIgnored(): bool
    {
        return $this->method === self::METHOD_IGNORE_CASE;
    }

    /**
     * Test if Rule is Literal.
     * @return bool Return true if it is.
     */
    public function isLiteral(): bool
    {
        return $this->method === self::METHOD_LITERAL;
    }

    /**
     * Get the keyword attribute.
     * @return string|null Returns the value of keyword attribute.
     */
    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function initFromDescriptor(array $descriptor): void
    {
        parent::initFromDescriptor($descriptor);

        $this->method = $this->checkEnumLeaf(
            $descriptor, self::DESCRIPTOR_METHOD_NODE, self::METHOD_LIST, null, false, true
        );
        $this->keyword = $this->checkStringLeaf($descriptor, self::DESCRIPTOR_KEYWORD_NODE, null, false, true);
    }

    public function applyRuleToContent(string $content): bool
    {
        $result = false;
        $this->clearValue();

        if (is_string($this->keyword)) {
            $len = mb_strlen($this->keyword);
            $fragment = mb_substr($content, 0, $len);
            if ($this->isCaseIgnored()) {
                $fragment = mb_strtoupper($fragment);
                $case = mb_strtoupper($this->keyword);
            } else {
                $case = $this->keyword;
            }
            if (nb_strStartsWith($fragment, $case)) {
                $this->setValue(mb_substr($content, 0, $len), $len);
                $result = true;
            }
        }

        return $result;
    }
}
