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

use nabu\lexer\interfaces\INabuLexerRule;

/**
 * MySQL Lexer Rule to parse a Regular Expression.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleRegEx extends CNabuLexerAbstractRule
{
    /** @var string Descriptor match node literal. */
    const DESCRIPTOR_MATCH_NODE = 'match';
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

    /** @var string $method Method used to apply both Regular Expressions (match and extract). */
    private $method = null;
    /** @var string $match Match Regular Expression to apply. */
    private $match = null;
    /** @var bool $use_unicode If true, Unicode is used in Regular expressions and /u is added to all preg_x functions. */
    private $use_unicode = false;
    /** @var INabuLexerRule|null $exclusion_rule Rule to evaluate if extracted string from Regular Expression is not valid. */
    private $exclusion_rule = null;

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
     * Test if Rule allows Unicode Regular Expressions.
     * @return bool Return true if Unicode is allowed.
     */
    public function isUnicodeAllowed(): bool
    {
        return is_bool($this->use_unicode) && $this->use_unicode;
    }

    /**
     * Get the Match Regular Expression of this Rule.
     * @return string|null Returns the Match Regular Expression if assigned or null otherwise.
     */
    public function getMatchRegularExpression(): ?string
    {
        return $this->match;
    }

    /**
     * Get the Rule to detect exclusion values.
     * @return INabuLexerRule|null Returns the assigned rule if exists or null otherwise.
     */
    public function getExclusionRule(): ?INabuLexerRule
    {
        return $this->exclusion_rule;
    }

    public function initFromDescriptor(array $descriptor): void
    {
        parent::initFromDescriptor($descriptor);

        $this->method = $this->checkEnumNode(
            $descriptor, self::DESCRIPTOR_METHOD_NODE, self::METHOD_LIST, null, false, true
        );
        $this->use_unicode = $this->checkBooleanNode($descriptor, 'unicode');
        $this->match = $this->checkRegExNode(
            $descriptor, self::DESCRIPTOR_MATCH_NODE, $this->use_unicode, null, false, true
        );
        $this->exclusion_rule = $this->checkRuleNode($descriptor, 'exclude');
    }

    public function applyRuleToContent(string $content): bool
    {
        $result = false;
        $this->clearTokens();

        $matches = null;
        $regex_modif = ($this->isCaseIgnored() ? 'i' : '') . ($this->isUnicodeAllowed() ? 'u' : '');

        if (is_string($this->match) && preg_match("/^$this->match/$regex_modif", $content, $matches)) {
            $len = mb_strlen($matches[0]);
            count($matches) > 1 && array_shift($matches);
            $tokens = $this->applyExclusionRuleToMatches($matches);

            if (count($matches) === count($tokens)) {
                if (count($tokens) < 2) {
                    $this->setToken($tokens[0], $len);
                    $this->setPathValue($tokens[0]);
                } else {
                    $this->setToken($tokens, $len);
                    $this->setPathValue($tokens);
                }
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Apply Exclusion Rule to parsed content.
     * @param array $matches Array of match strings after apply a Regular expression.
     * @return array Returns $matches filtered.
     */
    private function applyExclusionRuleToMatches(array $matches): array
    {
        if ($this->exclusion_rule instanceof INabuLexerRule) {
            $cnt = count($matches);
            $tokens = array();
            for ($i = 0; $i < $cnt; $i++) {
                if (!$this->exclusion_rule->applyRuleToContent($matches[$i]) ||
                    mb_strlen($matches[$i]) !== $this->exclusion_rule->getSourceLength()
                ) {
                    $tokens[$i] = $matches[$i];
                }
            }
        } else {
            $tokens = $matches;
        }

        return $tokens;
    }
}
