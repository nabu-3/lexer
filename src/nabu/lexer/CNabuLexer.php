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
use ReflectionClass;

use nabu\lexer\data\CNabuLexerData;

use nabu\lexer\exceptions\ENabuLexerException;

use nabu\lexer\grammar\CNabuLexerGrammarLoader;

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

    /** @var string Language name used by this Lexer. */
    protected static $grammar_name = null;
    /** @var string Language minimum version used by this lexer. */
    protected static $grammar_version_min = null;
    /** @var string Language maximum version used by this lexer. */
    protected static $grammar_version_max = null;

    /** @var CNabuLexerRuleProxy Proxy Rule instance to manage Lexer rules. */
    protected $rules_proxy = null;
    /** @var CNabuLexerData $data Data obtained after analyze a content. */
    protected $data = null;

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
            $dirname = __DIR__ . NABU_LEXER_GRAMMAR_FOLDER . DIRECTORY_SEPARATOR . self::getGrammarName() .  NABU_LEXER_RESOURCE_FOLDER;
            $class_name = (new ReflectionClass(get_called_class()))->getShortName();
            $filename = realpath($dirname . DIRECTORY_SEPARATOR . $class_name . '.json');

            $this->loadFileResources($filename);
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

    public function loadFileResources(string $filename) : bool
    {
        $loader = new CNabuLexerGrammarLoader($this);
        return $loader->loadFileResources($filename);
    }

    public function analyze(string $content): bool
    {
        $result = false;
        $this->data = new CNabuLexerData();

        foreach ($this->rules_proxy as $key => $rule) {
            if ($rule->isStarter() && $rule->applyRuleToContent($content)) {
                $this->data->setMainRuleName($key);
                $this->data->setTokens($rule->getValue());
                $this->data->setSourceLength($rule->getSourceLength());
                $result = true;
                break;
            }
        }

        return $result;
    }
}
