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

namespace nabu\lexer\exceptions;

use ErrorException;

use nabu\min\exceptions\ENabuException;

/**
 * Exception class to handle Lexer exceptions.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package \nabu\lexer\exceptions
 */
class ENabuLexerException extends ENabuException
{
    /** @var int Lexer constructor cannot be invoqued directly. */
    public const ERROR_LEXER_CONSTRUCTOR_INVOQUED                   = 0x0001;
    /** @var int Lexer grammar does not exists. Requires the grammar name. */
    public const ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS                = 0x0002;
    /** @var int Lexer unsupported grammar version. Requires the version number. */
    public const ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION            = 0x0003;
    /** @var int Lexer minimum version great than maximum version. Requires min and max version numbers. */
    public const ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE         = 0x0004;
    /** @var int Invalid Lexer class. Requires class name. */
    public const ERROR_INVALID_LEXER_CLASS                          = 0x0005;
    /** @var int Invalid Grammar resource file. Requires the resource filename. */
    public const ERROR_INVALID_GRAMMAR_RESOURCE_FILE                = 0x0006;
    /** @var int Resource file does not contain grammar description. */
    public const ERROR_RESOURCE_GRAMMAR_DESCRIPTION_MISSING         = 0x0007;
    /** @var int Resource file language name does not match. Requires both language names. */
    public const ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH          = 0x0008;
    /** @var int Rule not found for descriptor. Requires descriptor content with var_export. */
    public const ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR                = 0x0009;
    /** @var int Rule node not found in descriptor. Requires the node name. Requires descriptor dump */
    public const ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR            = 0x000a;
    /** @var int Rule node invalid value. Requires the node name, value and type expected. */
    public const ERROR_RULE_NODE_INVALID_VALUE                      = 0x000b;
    /** @var int Invalid Rule method. Requires the method name. */
    public const ERROR_INVALID_RULE_METHOD                          = 0x000c;
    /** @var int Group Rule is empty. */
    public const ERROR_EMPTY_GROUP_RULE                             = 0x000d;
    /** @var int Rule already exists. Requires rule key. */
    public const ERROR_RULE_ALREADY_EXISTS                          = 0x000e;
    /** @var int Rule does not exists. Requires rule key. */
    public const ERROR_RULE_DOES_NOT_EXISTS                         = 0x000f;
    /** @var int Invalid Range values. Requires range as string. */
    public const ERROR_INVALID_RANGE_VALUES                         = 0x0010;
    /** @var int Lexer Data instance not set. */
    public const ERROR_LEXER_DATA_INSTANCE_NOT_SET                  = 0x0011;
    /** @var int Data Path is empty. */
    public const ERROR_LEXER_DATA_PATH_IS_EMPTY                     = 0X0012;

    /** @var array English error messages array. */
    private static $error_messages = array(
        ENabuLexerException::ERROR_LEXER_CONSTRUCTOR_INVOQUED =>
            'Lexer constructor cannot be invoqued directly.',
        ENabuLexerException::ERROR_LEXER_GRAMMAR_DOES_NOT_EXISTS =>
            'Lexer grammar does not exists [%s].',
        ENabuLexerException::ERROR_LEXER_GRAMMAR_UNSUPPORTED_VERSION =>
            'Lexer unsupported grammar version [%s].',
        ENabuLexerException::ERROR_LEXER_GRAMMAR_INVALID_VERSIONS_RANGE =>
            'Lexer invalid versions range [%s - %s].',
        ENabuLexerException::ERROR_INVALID_LEXER_CLASS =>
            'Invalid Lexer class [%s].',
        ENabuLexerException::ERROR_INVALID_GRAMMAR_RESOURCE_FILE =>
            'Invalid Grammar Resource file [%s].',
        ENabuLexerException::ERROR_RESOURCE_GRAMMAR_DESCRIPTION_MISSING =>
            'Resource file does not contain grammar description, is empty or incomplete.',
        ENabuLexerException::ERROR_RESOURCE_GRAMMAR_LANGUAGE_NOT_MATCH =>
            'Resource file Grammar Language name does not match [%s - %s]',
        ENabuLexerException::ERROR_RULE_NOT_FOUND_FOR_DESCRIPTOR =>
            "Rule not found to match descriptor.\n%s",
        ENabuLexerException::ERROR_RULE_NODE_NOT_FOUND_IN_DESCRIPTOR =>
            "Rule node [%s] not found in descriptor.\n%s",
        ENabuLexerException::ERROR_RULE_NODE_INVALID_VALUE =>
            'Rule node [%s] contains an invalid or unexpected value [%s]. Allowed are [%s].',
        ENabuLexerException::ERROR_INVALID_RULE_METHOD =>
            'Invalid Rule method [$s].',
        ENabuLexerException::ERROR_EMPTY_GROUP_RULE =>
            'Group Rule is empty.',
        ENabuLexerException::ERROR_RULE_ALREADY_EXISTS =>
            'Rule [%s] already exists.',
        ENabuLexerException::ERROR_RULE_DOES_NOT_EXISTS =>
            'Rule [%s] does not exists.',
        ENabuLexerException::ERROR_INVALID_RANGE_VALUES =>
            'Invalid Range values [%s].',
        ENabuLexerException::ERROR_LEXER_DATA_INSTANCE_NOT_SET =>
            'Lexer Data instance not set.',
        ENabuLexerException::ERROR_LEXER_DATA_PATH_IS_EMPTY =>
            'Lexer Data Path is empty.'
    );

    /**
     * Creates a Lexer Exception instance.
     * @param int $code Integer code of the exception.
     * @param array|null $values Values to be inserted in the translated message if needed.
     * @throws ErrorException Trhos an exception if $code value is not supported.
     */
    public function __construct(int $code, array $values = null)
    {
        if (array_key_exists($code, self::$error_messages)) {
            parent::__construct(self::$error_messages[$code], $code, $values);
        } else {
            parent::__construct('Invalid exception code [%s]', 0, array($code));
        }
    }
}
