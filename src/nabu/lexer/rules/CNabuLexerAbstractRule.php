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

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\data\traits\TNabuLexerNodeChecker;

use nabu\lexer\exceptions\ENabuLexerException;

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
    /** @var string Descriptor value node literal. */
    const DESCRIPTOR_VALUE_NODE = 'value';
    /** @var string Descriptor hidden node literal. */
    const DESCRIPTOR_HIDDEN_NODE = 'hidden';

    /** @var bool If true, the Rule is an starter rule and can be placed at the begin of a sequence. */
    private $starter = false;

    /** @var string Path to store extracted value. */
    private $path = null;

    /** @var mixed|null Fixed Path value. */
    private $path_default_value = null;

    /** @var array|null $tokens Rule tokens extracted from content. */
    private $tokens = null;

    /** @var int $sourceLength Length of original string needed to detect the tokens. */
    private $sourceLength = 0;

    /** @var bool $hidden If true, methods setToken and appendTokens only considers the source length. */
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
        $this->path_default_value = $this->checkMixedNode($descriptor, self::DESCRIPTOR_VALUE_NODE);
        $this->hidden = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_HIDDEN_NODE);
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function getSourceLength(): int
    {
        return $this->sourceLength;
    }

    public function setToken($token, int $sourceLength): INabuLexerRule
    {
        if (!$this->isHidden() && !is_null($token)) {
            if (is_array($token)) {
                $this->tokens = $token;
            } else {
                $this->tokens = array($token);
            }
        }
        $this->sourceLength = $sourceLength;

        return $this;
    }

    public function appendTokens($token, int $source_length): INabuLexerRule
    {
        if (!$this->isHidden() && !is_null($token)) {
            if (is_null($this->tokens)) {
                $this->tokens = $token;
            } else {
                if (is_array($token)) {
                    $this->tokens = array_merge($this->tokens, $token);
                } elseif (!is_string($token) || mb_strlen($token) > 0) {
                    $this->tokens[] = $token;
                }
            }
        }
        $this->sourceLength += $source_length;

        return $this;
    }

    public function clearTokens(): INabuLexerRule
    {
        $this->tokens = null;
        $this->sourceLength = 0;

        return $this;
    }

    public function getPathDefaultValue()
    {
        return $this->path_default_value;
    }

    public function setPathValue($value = null): INabuLexerRule
    {
        $data = $this->getLexer()->getData();

        if ($data instanceof CNabuLexerData) {
            if (is_string($this->path)) {
                if (is_null($this->path_default_value)) {
                    if (is_array($value) && count($value) === 1) {
                        $value = array_shift($value);
                    }
                    $data->setValue($this->path, $value);
                } else {
                    $data->setValue($this->path, $this->path_default_value);
                }
            }
        } else {
            throw new ENabuLexerException(ENabuLexerException::ERROR_LEXER_DATA_INSTANCE_NOT_SET);
        }

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
