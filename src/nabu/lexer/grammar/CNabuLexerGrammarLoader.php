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

namespace nabu\lexer\grammar;

use Exception;

use nabu\lexer\CNabuLexer;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexer;
use nabu\lexer\interfaces\INabuLexerGrammarLoader;

use nabu\lexer\rules\CNabuLexerRuleProxy;

use nabu\min\CNabuObject;

/**
 * Lexer Grammar loader.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\grammar
 */
class CNabuLexerGrammarLoader extends CNabuObject implements INabuLexerGrammarLoader
{
    /** @var string JSON Grammar branch name */
    private const JSON_GRAMMAR_NODE = 'grammar';
    /** @var string JSON Grammar Language node name */
    private const JSON_LANGUAGE_NODE = 'language';
    /** @var string JSON Grammar Version branch name */
    private const JSON_VERSION_NODE = 'version';
    /** @var string JSON Rules branch name. */
    private const JSON_RULES_NODE = 'rules';

    /** @var INabuLexer Lexer that owns this loader instance. */
    private $lexer = null;
    /** @var CNabuLexerRuleProxy Proxy Rule instance to manage Lexer rules. */
    protected $rules_proxy = null;

    public function __construct(CNabuLexer $lexer)
    {
        parent::__construct();

        $this->lexer = $lexer;
    }

    /**
     * Gets the assigned Lexer.
     * @return INabuLexer Returns the lexer instance.
     */
    public function getLexer(): INabuLexer
    {
        return $this->lexer;
    }

    public function loadFileResources(string $filename) : bool
    {
        $retval = false;

        if (file_exists($filename)) {
            try {
                if (mime_content_type($filename) !== 'text/plain' ||
                    ($raw = file_get_contents($filename)) === false ||
                    !($json = json_decode($raw, JSON_OBJECT_AS_ARRAY))
                ) {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_INVALID_GRAMMAR_RESOURCE_FILE,
                        array(
                            $filename
                        )
                    );
                }
            } catch (ENabuLexerException $ex) {
                throw $ex;
            } catch (Exception $e) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_INVALID_GRAMMAR_RESOURCE_FILE,
                    array(
                        $filename
                    )
                );
            }

            if (is_array($json)) {
                $this->processJSONHeader($json);
                $retval = $this->processJSONRules($json);
            }
        }

        return $retval;
    }

    /**
     * Process the JSON Resource header of a resources JSON definition.
     * @param array $json JSON Array previously parsed with resources definition.
     * @throws ENabuLexerException Throws an exception if the JSON is invalid.
     */
    protected function processJSONHeader(array $json)
    {
        if (!array_key_exists(self::JSON_GRAMMAR_NODE, $json) ||
            !is_array($grammar = $json[self::JSON_GRAMMAR_NODE]) ||
            !array_key_exists(self::JSON_LANGUAGE_NODE, $grammar) ||
            !is_string($language = $grammar[self::JSON_LANGUAGE_NODE]) ||
            !array_key_exists(self::JSON_VERSION_NODE, $grammar) ||
            !is_array($version = $grammar[self::JSON_VERSION_NODE]) ||
            !array_key_exists('min', $version) ||
            !(is_string($version_min = $version['min']) || is_null($version_min)) ||
            !array_key_exists('max', $version) ||
            !(is_string($version_max = $version['max']) || is_null($version_max))
        ) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RESOURCE_GRAMMAR_DESCRIPTION_MISSING);
        }

        if (!is_null($this->lexer::getGrammarName()) && $this->lexer::getGrammarName() !== $language) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH,
                array(
                    $this->lexer::getGrammarName(),
                    $language
                )
            );
        }

        if (is_string($version_min) && is_string($version_max) && version_compare($version_min, $version_max) === 1) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE,
                array(
                    $version_min,
                    $version_max
                )
            );
        }
    }

    /**
     * Process the JSON Resource Rules list.
     * @param array $json JSON Array previously parsed with resources definition.
     * @return bool Returns true if all rules are processed.
     * @throws ENabuLexerException Throws an exception if the JSON is invalid.
     */
    protected function processJSONRules(array $json) : bool
    {
        if (array_key_exists(self::JSON_RULES_NODE, $json) &&
            is_array($json[self::JSON_RULES_NODE]) &&
            count($json[self::JSON_RULES_NODE]) > 0
        ) {
            foreach ($json[self::JSON_RULES_NODE] as $key => $rule_desc) {
                $rule = CNabuLexerRuleProxy::createRuleFromDescriptor($this->lexer, $rule_desc);
                $this->lexer->registerRule($key, $rule);
            }
        }

        return true;
    }
}
