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

use nabu\lexer\CNabuLexer;

use nabu\lexer\data\traits\TNabuLexerNodeChecker;

use nabu\lexer\interfaces\INabuLexer;
use nabu\lexer\interfaces\INabuLexerRule;

/**
 * Main class to implement a Lexer.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
abstract class CNabuLexerAbstractRule implements INabuLexerRule
{
    use TNabuLexerNodeChecker;

    /** @var string Descriptor starter node literal. */
    const DESCRIPTOR_STARTER_NODE = 'starter';
    /** @var string Descriptor path node literal. */
    const DESCRIPTOR_PATH_NODE = 'path';
    /** @var string Descriptor hidden node literal. */
    const DESCRIPTOR_HIDDEN_NODE = 'hidden';

    /** @var bool If true, the Rule is an starter rule and can be placed at the begin of a sequence. */
    private $starter = false;

    /** @var string Path to store extracted value. */
    private $path = null;

    /** @var mixed $value Rule value extrated from content. */
    private $value = null;

    /** @var int $sourceLength Length of original string needed to detect the value. */
    private $sourceLength = 0;

    /** @var bool $hidden If true, methods setValue and appendValue only considers the source length. */
    private $hidden = false;

    /** @var CNabuLexer $lexer Lexer that manages this rule. */
    private $lexer = null;

    /**
     * Creates the instance and sets initial attributes.
     * @param INabuLexer $lexer Lexer that governs this Rule,
     */
    public function __construct(INabuLexer $lexer)
    {
        $this->lexer = $lexer;
    }

    public static function createFromDescriptor(INabuLexer $lexer, array $descriptor): INabuLexerRule
    {
        $caller = get_called_class();
        $rule = new $caller($lexer);
        $rule->initFromDescriptor($descriptor);

        return $rule;
    }

    public function initFromDescriptor(array $descriptor): void
    {
        $this->starter = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_STARTER_NODE);
        $this->path = $this->checkStringNode($descriptor, self::DESCRIPTOR_PATH_NODE);
        $this->hidden = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_HIDDEN_NODE);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getSourceLength(): int
    {
        return $this->sourceLength;
    }

    public function setValue($value, int $sourceLength): INabuLexerRule
    {
        if (!$this->isHidden() && !is_null($value)) {
            $this->value = $value;
        }
        $this->sourceLength = $sourceLength;

        return $this;
    }

    public function appendValue($value, int $source_length): INabuLexerRule
    {
        if (!$this->isHidden() && !is_null($value)) {
            if (is_null($this->value)) {
                $this->value = $value;
            } elseif (is_array($this->value)) {
                if (is_array($value)) {
                    $this->value = array_merge($this->value, $value);
                } elseif (!is_string($value) || mb_strlen($value) > 0) {
                    $this->value[] = $value;
                }
            } else {
                $this->value = array($this->value, $value);
            }
        }
        $this->sourceLength += $source_length;

        return $this;
    }

    public function clearValue(): INabuLexerRule
    {
        $this->value = null;
        $this->sourceLength = 0;

        return $this;
    }

    public function isStarter(): bool
    {
        return $this->starter;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getLexer(): INabuLexer
    {
        return $this->lexer;
    }
}
