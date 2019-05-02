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
 * MySQL Lexer Rule to repeat another rule between m and n times.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleRepeat extends CNabuLexerAbstractBlockRule
{
    /** @var string Descriptor repeat node literal. */
    const DESCRIPTOR_REPEAT_NODE = 'repeat';
    /** @var string Descriptor tokenizer node literal. */
    const DESCRIPTOR_TOKENIZER_NODE = 'tokenizer';

    /** @var INabuLexerRule $tokenizer Rule that acts as separator between sequenced items. */
    private $tokenizer = null;
    /** @var int $min_repeat Minimum value to count iterations. */
    private $min_repeat = 1;
    /** @var string|int $max_repeat Maximum value to count iterations. 'n' to infinite iterations. */
    private $max_repeat = 'n';
    /** @var INabuLexerRule $repeater Rule to repeat in each iteration. */
    private $repeater = null;

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

        list($this->min_repeat, $this->max_repeat) =
            $this->checkRangeNode($descriptor, self::DESCRIPTOR_REPEAT_NODE, null, false, true);
        $this->tokenizer = $this->checkRuleNode($descriptor, self::DESCRIPTOR_TOKENIZER_NODE);
        $this->repeater = $this->checkRuleNode($descriptor, CNabuLexerRuleProxy::DESCRIPTOR_RULE_NODE, null, false, true);
    }

    public function applyRuleToContent(string $content): bool
    {
        $pushed = $this->pushPath();
        $iteration = 0;

        $this->clearTokens();
        $first = true;

        do {
            if ($this->applyRuleToContentIteration($content, $first)) {
                $iteration++;
            } else {
                break;
            }
        } while (
            ($this->max_repeat === NABU_LEXER_RANGE_N || $iteration < $this->max_repeat) &&
            mb_strlen($content) > 0
        );

        if (!($success = ($iteration >= $this->min_repeat &&
                          ($this->max_repeat === NABU_LEXER_RANGE_N ||
                           $iteration <= $this->max_repeat
             )))
        ) {
            $this->clearTokens();
        } else {
            $this->setPathValue($this->getTokens());
        }

        $pushed && $this->popPath();

        return $success;
    }

    /**
     * Calculates an iteration of @see { CNabuLexerRuleRepeat::applyRuleToContent }.
     * @param string &$content Current string to be parsed.
     * @param bool &$first If true the call is the first of iterator and does not apply tokenizer rule.
     * @return bool Returns true if rule was applied or false otherwise.
     */
    private function applyRuleToContentIteration(string &$content, bool &$first): bool
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

        if ($this->repeater->applyRuleToContent($cursor)) {
            if ($this->repeater->getSourceLength() > 0) {
                if ($tkl > 0) {
                    $this->appendTokens($tkv, $tkl);
                }
                $v = $this->repeater->getTokens();
                $l = $this->repeater->getSourceLength();
                $this->appendTokens($v, $l);
                $content = mb_substr($content, $l + $tkl);
                $first = false;
            }
            $retval = true;
        }

        return $retval;
    }
}
