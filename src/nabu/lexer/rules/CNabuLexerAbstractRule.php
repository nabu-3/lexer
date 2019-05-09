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

use nabu\lexer\base\CNabuAbstractLexerChild;

use nabu\lexer\data\traits\TNabuLexerNodeChecker;

use nabu\lexer\interfaces\INabuLexer;
use nabu\lexer\interfaces\INabuLexerRule;

/**
 * Abstract base class to implement a Lexer Rule.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
abstract class CNabuLexerAbstractRule extends CNabuAbstractLexerChild implements INabuLexerRule
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
    /** @var string Descriptor default node literal. */
    const DESCRIPTOR_DEFAULT_NODE = 'default';
    /** @var string Descriptor optional node literal. */
    const DESCRIPTOR_OPTIONAL_NODE = 'optional';
    /** @var string Descriptor root node literal. */
    const DESCRIPTOR_LEAF_NODE = 'leaf';

    /** @var bool If true, the Rule is an starter rule and can be placed at the begin of a sequence. */
    private $starter = false;
    /** @var bool $hidden If true, methods setToken and appendTokens only considers the source length. */
    private $hidden = false;
    /** @var bool $optional If true, the rule is evaluated as valid even if not complies. */
    private $optional = false;
    /** @var bool $leaf If true, the rule puts evaluated value as leaf. */
    private $leaf = false;
    /** @var string Path to store extracted value. */
    private $path = null;
    /** @var mixed|null Fixed Path value. */
    private $path_value = null;
    /** @var bool If trhe the Value Path is setted. */
    private $path_value_exists = false;
    /** @var array|null $tokens Rule tokens extracted from content. */
    private $tokens = null;
    /** @var int $sourceLength Length of original string needed to detect the tokens. */
    private $sourceLength = 0;
    /** @var mixed|null Default Path value in case that path is not setted by other rules previously evaluated. */
    private $path_default = null;
    /** @var bool If true, the Default Path is setted. */
    private $path_default_exists = false;

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
        $this->hidden = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_HIDDEN_NODE);
        $this->optional = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_OPTIONAL_NODE);
        $this->leaf = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_LEAF_NODE);
        $this->path = $this->checkStringNode($descriptor, self::DESCRIPTOR_PATH_NODE);

        if (array_key_exists(self::DESCRIPTOR_VALUE_NODE, $descriptor)) {
            $this->path_value = $this->checkMixedNode($descriptor, self::DESCRIPTOR_VALUE_NODE);
            $this->path_value_exists = true;
        } else {
            $this->path_value = null;
            $this->path_value_exists = false;
        }

        if (array_key_exists(self::DESCRIPTOR_DEFAULT_NODE, $descriptor)) {
            $this->path_default = $this->checkMixedNode($descriptor, self::DESCRIPTOR_DEFAULT_NODE);
            $this->path_default_exists = true;
        } else {
            $this->path_default = null;
            $this->path_default_exists = false;
        }
    }

    public function overrideFromDescriptor(array $descriptor): void
    {
        $this->starter = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_STARTER_NODE, $this->starter);
        $this->hidden = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_HIDDEN_NODE, $this->hidden);
        $this->optional = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_OPTIONAL_NODE, $this->optional);
        $this->leaf = $this->checkBooleanNode($descriptor, self::DESCRIPTOR_LEAF_NODE, $this->leaf);
        $this->path = $this->checkStringNode($descriptor, self::DESCRIPTOR_PATH_NODE, $this->path);

        if (array_key_exists(self::DESCRIPTOR_VALUE_NODE, $descriptor)) {
            $this->path_value = $this->checkMixedNode($descriptor, self::DESCRIPTOR_VALUE_NODE, $this->path_value);
            $this->path_value_exists = true;
        } else {
            $this->path_value = null;
            $this->path_value_exists = false;
        }

        if (array_key_exists(self::DESCRIPTOR_DEFAULT_NODE, $descriptor)) {
            $this->path_default = $this->checkMixedNode($descriptor, self::DESCRIPTOR_DEFAULT_NODE, $this->path_default);
            $this->path_default_exists = true;
        } else {
            $this->path_default = null;
            $this->path_default_exists = false;
        }
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

    public function appendTokens($tokens, int $source_length): INabuLexerRule
    {
        if (!$this->isHidden() && !is_null($tokens)) {
            if (is_null($this->tokens)) {
                $this->tokens = $tokens;
            } else {
                if (is_array($tokens)) {
                    $this->tokens = array_merge($this->tokens, $tokens);
                } elseif (!is_string($tokens) || mb_strlen($tokens) > 0) {
                    if (is_array($this->tokens)) {
                        $this->tokens[] = $tokens;
                    } else {
                        $this->tokens = array($this->tokens, $tokens);
                    }
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

    public function setPathValue($value = null): INabuLexerRule
    {
        $data = $this->getLexerData();

        if ($this->leaf) {
            $current_path = $data->getWithPreffix();
            error_log('====> ' . $current_path . ' ' . var_export($value, true));
            $data->with()->setValue($current_path, $value);
            $data->with($current_path);
        } elseif (is_string($this->path)) {
            if ($this->path_value_exists && $this->getSourceLength() > 0) {
                $data->setValue($this->path, $this->path_value);
            } elseif ($this->path_default_exists && !$data->hasValue($this->path)) {
                 $data->setValue($this->path, $this->path_default);
            } elseif (!$this->path_default_exists && !$this->path_value_exists) {
                if (is_array($value) && count($value) === 1) {
                    $value = array_shift($value);
                }
                $data->setValue($this->path, $value);
            }
        }

        return $this;
    }

    public function isStarter(): bool
    {
        return $this->starter;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function isLeaf(): bool
    {
        return $this->leaf;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function hasPathValue(): bool
    {
        return $this->path_value_exists;
    }

    public function getPathValue()
    {
        return $this->path_value;
    }

    public function hasPathDefaultValue(): bool
    {
        return $this->path_default_exists;
    }

    public function getPathDefaultValue()
    {
        return $this->path_default;
    }
}
