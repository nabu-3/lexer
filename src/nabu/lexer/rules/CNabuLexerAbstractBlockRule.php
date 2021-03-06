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

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\interfaces\INabuLexer;

/**
 * Abstract base class to implement a Lexer BLock Rule.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\rules
 */
abstract class CNabuLexerAbstractBlockRule extends CNabuLexerAbstractRule
{
    /** @var string Descriptor push node literal. */
    const DESCRIPTOR_PUSH_NODE = 'push';

    /** @var string Push named path to store extracted value. */
    private $push_path = null;

    public function initFromDescriptor(array $descriptor): void
    {
        parent::initFromDescriptor($descriptor);

        $this->push_path = $this->checkStringNode($descriptor, self::DESCRIPTOR_PUSH_NODE);
    }

    /**
     * Get the push path value (slug).
     * @return string|null Returns current Push Path value (slug).
     */
    public function getPushPath(): ?string
    {
        return $this->push_path;
    }

    /**
     * Push the path if exists.
     * @return bool Returns true if the path was pushed.
     */
    public function pushPath(): bool
    {
        $retval = false;

        if (($lexer = $this->getLexer()) instanceof INabuLexer) {
            $data = $lexer->getData();
            if ($data instanceof CNabuLexerData && is_string($this->push_path)) {
                $data->pushPath($this->push_path);
                $retval = true;
            }
        }

        return $retval;
    }

    /**
     * Pop the path if exists.
     * @return bool Returns true if the path was popped.
     */
    public function popPath(): bool
    {
        $retval = false;

        if (($lexer = $this->getLexer()) instanceof INabuLexer) {
            $data = $lexer->getData();
            if ($data instanceof CNabuLexerData && is_string($this->push_path)) {
                $data->popPath();
                $retval = true;
            }
        }

        return $retval;
    }
}
