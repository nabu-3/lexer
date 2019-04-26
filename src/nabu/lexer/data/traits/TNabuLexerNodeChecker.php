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

namespace nabu\lexer\data\traits;

use Exception;

use nabu\lexer\exceptions\ENabuLexerException;

/**
 * Trait to add check operations for array elements.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\data\traits
 */
trait TNabuLexerNodeChecker
{
    /**
     * Check if a leaf have a boolean value and returns the value detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param bool $def_value Default value in case that the leaf does not exists.
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
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array(
                        $name,
                        var_export($descriptor[$name], true),
                        'bool'
                    )
                );
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR,
                array($name, var_export($descriptor, true))
            );
        }

        return $boolValue;
    }

    /**
     * Check if a leaf have a string value and returns the value detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param string|null $def_value Default value in case that the leaf does not exists.
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
                        array(
                            $name,
                            var_export($descriptor[$name], true),
                            'string, null'
                        )
                    );
                } else {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array(
                            $name,
                            var_export($descriptor[$name], true),
                            'string'
                        )
                    );
                }
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR,
                array($name, var_export($descriptor, true))
            );
        }

        return $stringValue;
    }

    /**
     * Check if a leaf have a scalar value contained in an enumeration of values and returns the value detected
     * if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param array $enum_values Array of possible enumerated values.
     * @param mixed|null $def_value Default value in case that the leaf does not exists.
     * @param bool $nullable If true, the node can contain a null value.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return mixed Returns the detected value.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkEnumLeaf(
        array $descriptor, string $name, array $enum_values, $def_value = null, bool $nullable = false,
        bool $raise_exception = false
    ) {
        $enum_val = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if (($nullable && is_null($descriptor[$name])) ||
                (is_scalar($descriptor[$name]) && in_array($descriptor[$name], $enum_values))
            ) {
                $enum_val = $descriptor[$name];
            } elseif ($raise_exception) {
                if ($nullable) {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, var_export($descriptor[$name], true), implode(', ', $enum_values) . ', null')
                    );
                } else {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, var_export($descriptor[$name], true), implode(', ', $enum_values))
                    );
                }
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR,
                array($name, var_export($descriptor, true))
            );
        }

        return $enum_val;
    }

    /**
     * Check if a node is an array and returns the array found.
     * @param array $descriptor The descriptor fragment to be analized. The node needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param array|null $def_value Default value in case that the leaf does not exists.
     * @param bool $nullable If true, allows the node to be null.
     * @param bool $raise_exception If true, throws an exception if the node des not exists.
     * @return array|null Returns the array found or null if allowed.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkArrayNode(
        array $descriptor, string $name, array $def_value = null, bool $nullable = true, bool $raise_exception = false
    ) {
        $array_value = $def_value;

        if (array_key_exists($name, $descriptor)) {
            if (is_array($descriptor[$name]) || ($nullable && is_null($descriptor[$name]))) {
                $array_value = $descriptor[$name];
            } elseif ($raise_exception) {
                if ($nullable) {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name,  var_export($descriptor[$name], true), 'array, null')
                    );
                } else {
                    throw new ENabuLexerException(
                        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                        array($name, var_export($descriptor[$name], true), 'array')
                    );
                }
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR,
                array($name, var_export($descriptor, true))
            );
        }

        return $array_value;
    }

    /**
     * Check if a node have a mixed value and returns the value found.
     * @param array $descriptor The descriptor fragment to be analized. The node needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param mixed|null $def_value Default value in case that the leaf does not exists.
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
            if ($nullable || !is_null($descriptor[$name])) {
                $mixedValue = $descriptor[$name];
            } elseif ($raise_exception) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array($name, var_export($descriptor[$name], true), 'mixed')
                );
            }
        } elseif ($raise_exception) {
            throw new ENabuLexerException(
                ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR,
                array($name, var_export($descriptor, true))
            );
        }

        return $mixedValue;
    }

    /**
     * Check if a leaf have a regular expression value and returns the value detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param bool $unicode If true then allows to use unicode values.
     * @param string|null $def_value Default value in case that the leaf does not exists.
     * @param bool $nullable If true, the node can contain a null value.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return string|null Returns the detected value.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkRegExLeaf(
        array $descriptor, string $name,
        bool $unicode = false, string $def_value = null, bool $nullable = true,
        bool $raise_exception = false
    ) {

        try {
            $regex = $this->checkStringLeaf($descriptor, $name, $def_value, $nullable, $raise_exception);
            is_string($regex) && preg_match("/$regex/" . ($unicode ? 'u' : ''), 'test pattern');
        } catch (ENabuLexerException $ex) {
            if ($ex->getCode() === ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array($name, var_export($descriptor[$name], true), 'Regular Expression')
                );
            } else {
                throw $ex;
            }
        } catch (Exception $ex) {
            if ($raise_exception) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE,
                    array($name, var_export($descriptor[$name], true), 'Regular Expression')
                );
            } else {
                $regex = $def_value;
            }
        }

        return $regex;
    }

    /**
     * Check if a leaf have a range value and returns the range detected if it is valid.
     * @param array $descriptor The descriptor fragment to be analized. The leaf needs to be in the root of the array.
     * @param string $name Name of the leaf.
     * @param string|null $def_value Default value in case that the leaf does not exists.
     * @param bool $nullable If true, the node can contain a null value.
     * @param bool $raise_exception If true, throws an exception if the leaf des not exists.
     * @return array Returns the detected value. He can be received as a list assignement.
     * @throws ENabuLexerException Throws an exception if value does not exists or is invalid.
     */
    protected function checkRangeLeaf(
        array $descriptor, string $name, string $def_value = null, bool $nullable = true, bool $raise_exception = false
    ) {
        $range = null;

        try {
            $str_range = $this->checkStringLeaf($descriptor, $name, $def_value, $nullable, $raise_exception);

            $match = null;
            if (is_string($str_range) &&
                preg_match(LEXER_RANGE_REGEX, $str_range, $match) &&
                is_array($match) &&
                (($c = count($match)) === 6 || $c === 7)
            ) {
                if ($c === 7) {
                    $range = $this->checkRangeLeafSingleValue($match[6]);
                } else {
                    $range = $this->checkRangeLeafTupla($match[3], $match[5]);
                }
            } elseif (!is_null($str_range) || !$nullable) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_INVALID_RANGE_VALUES,
                    array(var_export($str_range, true))
                );
            }
        } catch (ENabuLexerException $ex) {
            if ($ex->getCode() === ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE) {
                throw new ENabuLexerException(
                    ENabuLexerException::ERROR_INVALID_RANGE_VALUES,
                    array(var_export($descriptor[$name], true))
                );
            } else {
                throw $ex;
            }
        }

        return $range;
    }

    /**
     * Parse a range expressed as a single value. This value could be a number or a repressentation of infinity.
     * If the range is numeric, return value is (n, n) where n is the value expressed in the range.
     * If the range is invinite, return value is (1, n) where n is the literal repressenting infinity value.
     * @param string $value Value range to parse.
     * @return array Returns an array with min and max values of the range.
     */
    private function checkRangeLeafSingleValue(string $value): array
    {
        if (is_numeric($value)) {
            $range = array((int)$value, (int)$value);
        } elseif (is_string($value) && in_array(mb_strtolower($value), LEXER_RANGE_INFINITE_VALUES)) {
            $range = array(1, LEXER_RANGE_N);
        }

        return $range;
    }

    /**
     * Parse a range expressed as a tupla.
     * The first value will always be a number.
     * The second value could be a number or an infinity repressentation.
     * If the range is numeric, return value is (n, n) where n is the value expressed in the range.
     * If the range is invinite, return value is (1, n) where n is the literal repressenting infinity value.
     * @param string $min_value Minimum value fragment to parse.
     * @param string $max_value Maximum value fragment to parse.
     * @return array Returns an array with min and max values of the range.
     */
    private function checkRangeLeafTupla(string $min_value, string $max_value): array
    {
        if (in_array($max_value, LEXER_RANGE_INFINITE_VALUES)) {
            $max_value = LEXER_RANGE_N;
        } else {
            $max_value = (int)$max_value;
        }

        return array((int)$min_value, $max_value);
    }
}
