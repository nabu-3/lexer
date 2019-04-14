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

use nabu\lexer\exceptions\ENabuLexerException;

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
    /** @var string Descriptor starter node literal. */
    const DESCRIPTOR_STARTER_NODE = 'starter';

    /** @var bool If true, the Rule is an starter rule and can be placed at the begin of a sequence. */
    private $starter = false;

    /** @var mixed $value Rule value extrated from content. */
    private $value = null;

    /** @var int $sourceLength Length of original string needed to detect the value. */
    private $sourceLength = 0;

    /**
     * Creates the instance and sets initial attributes.
     */
    public function __construct()
    {

    }

    public static function createFromDescriptor(array $descriptor): INabuLexerRule
    {
        $caller = get_called_class();
        $rule = new $caller();
        $rule->initFromDescriptor($descriptor);

        return $rule;
    }

    public function initFromDescriptor(array $descriptor)
    {
        $this->starter = $this->checkBooleanLeaf($descriptor, self::DESCRIPTOR_STARTER_NODE);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getSourceLength(): int
    {
        return $this->sourceLength;
    }

    public function setValue($value, int $sourceLength)
    {
        $this->value = $value;
        $this->sourceLength = $sourceLength;
    }

    public function clearValue()
    {
        $this->value = null;
        $this->sourceLength = 0;
    }

    public function isStarter(): bool
    {
        return $this->starter;
    }

    /**
     * Check if a leaf have a boolean value and returns the value detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param bool $def_value Boolean default value in case that the leaf does not exists.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return bool Returns the detected value.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkBooleanLeaf(
        array $descriptor, string $name, bool $def_value = false, bool $raise_exception = false
    ): bool {
        $boolValue = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if (is_bool($descriptor[$name])) {
                $boolValue = $descriptor[$name];
            } else {
                throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE, array($name, 'bool'));
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR, array($name));
        }

        return $boolValue;
    }

    /**
     * Check if a leaf have a string value and returns the value detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param string|null $def_value Boolean default value in case that the leaf does not exists.
     * @param bool $nullable If true, the node can contain a null value.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return string|null Returns the detected value.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkStringLeaf(
        array $descriptor, string $name, string $def_value = null, bool $nullable = true, bool $raise_exception = false
    ) {
        $stringValue = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if (is_string($descriptor[$name]) || ($nullable && is_null($descriptor[$name]))) {
                $stringValue = $descriptor[$name];
            } elseif ($raise_exception) {
                if ($nullable) {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, 'string, null')
                    );
                } else {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, 'string')
                    );
                }
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR, array($name));
        }

        return $stringValue;
    }

    /**
     * Check if a leaf have a scalar value contained in an enumeration of values and returns the value detected
     * if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param array $enum_values Array of possible enumerated values.
     * @param mixed|null $def_value Boolean default value in case that the leaf does not exists.
     * @param bool $nullable If true, the node can contain a null value.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return mixed Returns the detected value.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkEnumLeaf(
        array $descriptor, string $name, array $enum_values, $def_value = null, bool $nullable = false,
        bool $raise_exception = false
    ) {
        $enumValue = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if (($nullable && is_null($descriptor[$name])) ||
                (is_scalar($descriptor[$name]) && in_array($descriptor[$name], $enum_values))
            ) {
                $enumValue = $descriptor[$name];
            } elseif ($raise_exception) {
                if ($nullable) {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, implode(', ', $enum_values) . ', null')
                    );
                } else {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, implode(', ', $enum_values))
                    );
                }
            }
        } elseif ($raise_exception) {
            throw new  ENabuLexerException(ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR, array($name));
        }

        return $enumValue;
    }

    /**
     * Check if a node is an array and returns the array found.
     * @param array $descriptor The descriptor fragment to be analized. The node needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param array|null $def_value Boolean default value in case that the leaf does not exists.
     * @param bool $nullable If true, allows the node to be null.
     * @param bool $raise_exception If true, throws an exception if the node des not exists.
     * @return array|null Returns the array found or null if allowed.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkArrayNode(
        array $descriptor, string $name, array $def_value = null, bool $nullable = true, bool $raise_exception = false
    ) {
        $arrayValue = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if (is_array($descriptor[$name]) || ($nullable && is_null($descriptor[$name]))) {
                $arrayValue = $descriptor[$name];
            } elseif ($raise_exception) {
                if ($nullable) {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, 'array, null')
                    );
                } else {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, 'array')
                    );
                }
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR, array($name));
        }

        return $arrayValue;
    }

    /**
     * Check if a node have a mixed value and returns the value found.
     * @param array $descriptor The descriptor fragment to be analized. The node needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param mixed|null $def_value Boolean default value in case that the leaf does not exists.
     * @param bool $nullable If true, allows the node to be null.
     * @param bool $raise_exception If true, throws an exception if the node des not exists.
     * @return array|null Returns the array found or null if allowed.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkMixedNode(
        array $descriptor, string $name, $def_value = null, bool $nullable = false, bool $raise_exception = false
    ) {
        $mixedValue = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if ((is_null($descriptor[$name]) && $nullable) || !is_null($descriptor[$name])) {
                $mixedValue = $descriptor[$name];
            } elseif ($raise_exception) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE, array($name, 'mixed')
                );
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR, array($name));
        }

        return $mixedValue;
    }
}
