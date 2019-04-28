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

    /**
     * @return array|null
     */
    public function getTokens(): ?array
    {
        return $this->tokens;
    }

    /**
     * @param array|null $tokens
     *
     * @return static
     */
    public function setTokens(?array $tokens)
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * @return int
     */
    public function getSourceLength(): int
    {
        return $this->source_length;
    }

    /**
     * @param int $source_length
     *
     * @return static
     */
    public function setSourceLength(int $source_length)
    {
        $this->source_length = $source_length;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMainRuleName(): ?string
    {
        return $this->main_rule_name;
    }

    /**
     * @param string|null $main_rule_name
     *
     * @return static
     */
    public function setMainRuleName(?string $main_rule_name)
    {
        $this->main_rule_name = $main_rule_name;
        return $this;
    }
}
