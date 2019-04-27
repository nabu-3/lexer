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

namespace nabu\lexer;

use Error;
use Exception;
use ReflectionClass;

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\interfaces\INabuLexer;
use nabu\lexer\interfaces\INabuLexerRule;

use nabu\lexer\rules\CNabuLexerRuleProxy;

use nabu\min\CNabuObject;

/**
 * Main class to implement a Lexer.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer
 */
class CNabuLexer extends CNabuObject implements INabuLexer
{
    /** @var string Language MySQL */
    public const GRAMMAR_MYSQL = 'mysql';

    /** @var string JSON Grammar branch name */
    private const JSON_GRAMMAR_NODE = 'grammar';
    /** @var string JSON Grammar Language node name */
    private const JSON_LANGUAGE_NODE = 'language';
    /** @var string JSON Grammar Version branch name */
    private const JSON_VERSION_NODE = 'version';
    /** @var string JSON Rules branch name. */
    private const JSON_RULES_NODE = 'rules';

    /** @var string Language name used by this Lexer. */
    protected static $grammar_name = null;
    /** @var string Language minimum version used by this lexer. */
    protected static $grammar_version_min = null;
    /** @var string Language maximum version used by this lexer. */
    protected static $grammar_version_max = null;

    /** @var CNabuLexerData Data storage for analysis. */
    protected $data = null;

    /** @var CNabuLexerRuleProxy Proxy Rule instance to manage Lexer rules. */
    protected $rules_proxy = null;

    /**
     * Protects the constructor to force to be invoqued from the getLexer method.
     */
    protected function __construct()
    {
        $caller = get_called_class();

        if (is_string($caller::$grammar_version_min) &&
            is_string($caller::$grammar_version_max) &&
            version_compare($caller::$grammar_version_min, $caller::$grammar_version_max) === 1
        ) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE,
                array(
                    $caller::$grammar_version_min,
                    $caller::$grammar_version_max
                )
            );
        }

        $this->rules_proxy = new CNabuLexerRuleProxy($this);

        if (is_string(self::getGrammarName())) {
            $this->preloadFileResources();
        }
    }

    public static function getLexer(string $grammar_name = null, string $grammar_version = null) : INabuLexer
    {
        $lexer = null;

        if ($grammar_name === null && $grammar_version === null && is_string(self::getGrammarName())) {
            $caller = get_called_class();
            $lexer = new $caller();
        } else {
            try {
                $class_name = "nabu\\lexer\\grammar\\$grammar_name\\CNabuLexerGrammarProxy";
                $proxy = new $class_name();
            } catch (Error $e) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS,
                    array(
                        $grammar_name
                    )
                );
            }
            $lexer = $proxy->getLexer($grammar_version);
        }

        return $lexer;
    }

    public static function getGrammarName()
    {
        return get_called_class()::$grammar_name;
    }

    public static function getMinimumVersion()
    {
        return get_called_class()::$grammar_version_min;
    }

    public static function getMaximumVersion()
    {
        return get_called_class()::$grammar_version_max;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(CNabuLexerData $data): INabuLexer
    {
        $this->data = $data;

        return $this;
    }

    public static function isValidVersion(string $version): bool
    {
        $retval = true;
        $caller = get_called_class();

        if ((is_string($caller::$grammar_version_min) && version_compare($caller::$grammar_version_min, $version) === 1) ||
            (is_string($caller::$grammar_version_max) && version_compare($caller::$grammar_version_max, $version) === -1)
        ) {
            $retval = false;
        }

        return $retval;
    }

    public function getRule(string $key): INabuLexerRule
    {
        return $this->rules_proxy->getRule($key);
    }

    public function registerRule(string $key, INabuLexerRule $rule): INabuLexer
    {
        $this->rules_proxy->registerRule($key, $rule);

        return $this;
    }

    /**
     * Load the default File Resource descriptor to prepare the Lexer.
     * @throws ENabuLexerException Throws an exception if the file exists but their content is not valid.
     */
    protected function preloadFileResources()
    {
        $dirname = __DIR__ . NABU_LEXER_GRAMMAR_FOLDER . DIRECTORY_SEPARATOR . self::getGrammarName() .  NABU_LEXER_RESOURCE_FOLDER;
        $class_name = (new ReflectionClass(get_called_class()))->getShortName();
        $filename = realpath($dirname . DIRECTORY_SEPARATOR . $class_name . '.json');

        $this->loadFileResources($filename);
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

        if (!is_null(self::getGrammarName()) && self::getGrammarName() !== $language) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH,
                array(
                    self::$grammar_name,
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
                $rule = CNabuLexerRuleProxy::createRuleFromDescriptor($this, $rule_desc);
                $this->rules_proxy->registerRule($key, $rule);
            }
        }

        return true;
    }
}
