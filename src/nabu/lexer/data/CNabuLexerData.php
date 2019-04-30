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

namespace nabu\lexer\data;

use nabu\data\CNabuDataObject;

use nabu\data\traits\TNabuNestedData;

/**
 * Lexer data instance to store Lexer analysis.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\data
 */
class CNabuLexerData extends CNabuDataObject
{
    use TNabuNestedData;

    /** @var array|null $tokens Token list after analyze a content */
    protected $tokens = null;
    /** @var int $source_length Content length parsed to obtain tokens. */
    protected $source_length = 0;
    /** @var string|null $main_rule_name Name of main rule applied to obtain data and tokens. */
    protected $main_rule_name = null;
    /** @var array|null $push_path Array of pushed path fragments. */
    protected $push_path = null;
    /** @var string|null $push_path_str Compound string of current pushed path fragments. */
    protected $push_path_str = null;

    /**
     * Returns the list of stored tokens.
     * @return array|null If at least one token is set, then returns an array of tokens. otherwise returns null.
     */
    public function getTokens(): ?array
    {
        return $this->tokens;
    }

    /**
     * Set a list of tokens.
     * @param array $tokens Array of tokens to set.
     * @return CNabuLexerData Returns self pointer to grant fluent interfaces.
     */
    public function setTokens(array $tokens): CNabuLexerData
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * Get the total Source Length scanned to obtain the current list of tokens and values.
     * @return int Returns the length of the source taken.
     */
    public function getSourceLength(): int
    {
        return $this->source_length;
    }

    /**
     * Set the total Source Length scanned for current list of tokens and values.
     * @param int $source_length New Source Length.
     * @return CNabuLexerData Returns the self pointer to grant fluent interfaces.
     */
    public function setSourceLength(int $source_length): CNabuLexerData
    {
        $this->source_length = $source_length;

        return $this;
    }

    /**
     * Get the name of the main rule applied to get the current list of tokens and values.
     * @return string|null If setted returns the name. Otherwise returns null,
     */
    public function getMainRuleName(): ?string
    {
        return $this->main_rule_name;
    }

    /**
     * Set the name of the main rule applied to get the current list of tokens and values.
     * @param string|null $main_rule_name The rule name to set.
     * @return CNabuLexerData Returns the self pointer to grant fluent interfaces.
     */
    public function setMainRuleName(?string $main_rule_name): CNabuLexerData
    {
        $this->main_rule_name = $main_rule_name;
        return $this;
    }

    /**
     * Push the slug passed as parameter to preffix next acquisition of values. If slug starts by a dot, then rewind
     * current stored path to this slug.
     * @param string $slug Slug to set.
     * @return CNabuLexerData Returns the self pointer to grant fluent interfaces.
     */
    public function pushPath(string $slug): CNabuLexerData
    {
        return $this;
    }
}
