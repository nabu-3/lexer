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

/**
 * MySQL Lexer Rule to parse a Regular Expression.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleRegEx extends CNabuLexerAbstractRule
{
    /** @var string Descriptor method node literal. */
    const DESCRIPTOR_METHOD_NODE = 'method';
    /** @var string Descriptor match node literal. */
    const DESCRIPTOR_MATCH_NODE = 'match';
    /** @var string Descriptor extract node literal. */
    const DESCRIPTOR_EXTRACT_NODE = 'extract';

    /** @var string Method Literal literal. */
    const METHOD_LITERAL = 'literal';
    /** @var string Descriptor ignore case sensitive node literal. */
    const METHOD_IGNORE_CASE = 'ignore case';
    /** @var array Methods list. */
    const METHOD_LIST = array(
        self::METHOD_LITERAL,
        self::METHOD_IGNORE_CASE
    );

    /** @var string $method Method used to apply both Regular Expressions (match and extract). */
    private $method = null;
    /** @var string $match Match Regular Expression to apply. */
    private $match = null;
    /** @var string $extract Extractor Regular Expression to apply. */
    private $extract = null;

    /**
     * Get the method attribute.
     * @return string|null Returns the value of method attribute.
     */
    public function getMethod()
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
     * Get the Match Regular Expression of this Rule.
     * @return string|null Returns the Match Regular Expression if assigned or null otherwise.
     */
    public function getMatchRegularExpression()
    {
        return $this->match;
    }

    /**
     * Get the Extract Regular Expression of this Rule.
     * @return string|null Returns the Extract Regular Expression if assigned or null otherwise.
     */
    public function getExtractRegularExpression()
    {
        return $this->extract;
    }

    public function initFromDescriptor(array $descriptor)
    {
        parent::initFromDescriptor($descriptor);

        $this->method = $this->checkEnumLeaf(
            $descriptor, self::DESCRIPTOR_METHOD_NODE, self::METHOD_LIST, null, false, true
        );
        $this->match = $this->checkRegExLeaf($descriptor, self::DESCRIPTOR_MATCH_NODE, null, false, true);
        $this->extract = $this->checkRegExLeaf($descriptor, self::DESCRIPTOR_EXTRACT_NODE);
    }

    private function sintesizeRegExResult(array &$result)
    {
        if (count($result) > 2) {
            array_shift($result);
            $final_value = $result;
        } elseif (count($result) === 2) {
            $final_value = $result[1];
        } else {
            $final_value = $result[0];
        }

        return $final_value;
    }

    public function applyRuleToContent(string $content): bool
    {
        $result = false;
        $this->clearValue();

        $matches = null;
        $regex_modif = ($this->isCaseIgnored() ? 'i' : '');

        if (is_string($this->match) &&
            preg_match("/^$this->match/$regex_modif", $content, $matches)
        ) {
            $len = mb_strlen($matches[0]);
            $cnt = count($matches);
            if ($cnt < 3) {
                $this->setValue($matches[-1 + $cnt], $len);
            } else {
                array_shift($matches);
                $this->setValue($matches, $len);
            }
            $result = true;
        } else {
            $this->setValue(null, 0);
        }

        return $result;
    }

    /**
     * Check if a leaf have a string value and returns the value detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param string|null $def_value Boolean default value in case that the leaf does not exists.
     * @param bool $nullable If true, the node can contain a null value.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return string|null Returns the detected value.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkRegExLeaf(
        array $descriptor, string $name, string $def_value = null, bool $nullable = true, bool $raise_exception = false
    ) {
        $regex = $this->checkStringLeaf($descriptor, $name, $def_value, $nullable, $raise_exception);

        if (is_string($regex) && preg_match("/$regex/", 'test pattern') === false) {
            if ($raise_exception) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array($name, 'Regular Expression')
                );
            } else {
                $regex = $def_value;
            }
        }

        return $regex;
    }
}
