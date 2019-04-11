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

/**
 * MySQL Lexer Rule to parse a group of rules.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
class CNabuLexerRuleGroup extends CNabuLexerAbstractRule
{
    /** @var string Descriptor method node literal. */
    const DESCRIPTOR_METHOD_NODE = 'method';
    /** @var string Descriptor group node literal. */
    const DESCRIPTOR_GROUP_NODE = 'group';

    /** @var string Method Case literal. */
    const METHOD_CASE = 'case';
    /** @var string Method sequence literal. */
    const METHOD_SEQUENCE = 'sequence';

    /** @var string $method Method used to apply keywords. */
    private $method = null;
    /** @var array $group Rule list applicable. */
    private $group = null;

    public function initFromDescriptor(array $descriptor)
    {
        parent::initFromDescriptor($descriptor);

        $this->method = $this->checkStringLeaf($descriptor, self::DESCRIPTOR_METHOD_NODE, null, false, true);
        $group_desc = $this->checkArrayNode($descriptor, self::DESCRIPTOR_GROUP_NODE, null, false, true);

        $this->group = array();

        foreach ($group_desc as $rule_desc) {
            if (is_string($rule_desc)) {
                $this->group[] = CNabuLexerRuleKeyword::createFromDescriptor(
                    array(
                        'method' => 'direct',
                        'keyword' => $rule_desc
                    )
                );
            } else {
                $this->group[] = CNabuLexerRuleProxy::createRuleFromDescriptor($rule_desc);
            }
        }
    }

    public function applyRuleToContent(string $content): bool
    {
        return true;
    }
}
