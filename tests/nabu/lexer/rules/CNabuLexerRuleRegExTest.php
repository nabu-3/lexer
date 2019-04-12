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

use Exception;

use PHPUnit\Framework\TestCase;

use nabu\lexer\exceptions\ENabuLexerException;

/**
 * Test class for @see CNabuLexerRuleRegEx.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 0.0.2
 * @version 0.0.2
 * @package nabu\lexer\rules
 */
class CNabuLexerRuleRegExTest extends TestCase
{
    /**
     * @test __construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(CNabuLexerRuleRegEx::class, new CNabuLexerRuleRegEx());
    }

    /**
     * Provides data to test method testCreateInitFromDescriptor.
     * @return array Returns an array with all test case combinatios.
     */
     public function dataProviderCreateInitFromDescriptor()
     {
         return [
             [false, false, false, 'literal', "('\\\\s*'|'(.*?[^\\\\])')", "'(.*)'",
                "'test \'with single quotes\' inside' and more test", "test \'with single quotes\' inside", 36, true],
             [false, false, false, 'literal', "('\\\\s*'|'(.*?[^\\\\])')", null,
                "'test \'with single quotes\' inside' and more test", "'test \'with single quotes\' inside'", 36, true]
         ];
     }

     /**
      * Private method to create a Descriptor structure by parameters.
      * @param bool|null $starter If null acts as not declared.
      * @param bool|null $case_sensitive If null acts as not declared.
      * @param string|null $method If null acts as not declared.
      * @param string|null $match If null acts as not declared.
      * @param string|null $extract If null acts as not declared.
      * @return array|null If, at least, one parameter is different than null, then returns a well formed descriptor.
      * Otherwise, returns null.
      */
     private function createDescriptor(
         bool $starter = null, bool $case_sensitive = null, string $method = null, string $match = null, string $extract = null
     ) {
         $params = array();
         if (is_bool($starter)) {
             $params['starter'] = $starter;
         }
         if (is_bool($case_sensitive)) {
             $params['case_sensitive'] = $case_sensitive;
         }
         if (is_string($method)) {
             $params['method'] = $method;
         }
         if (is_string($match)) {
             $params['match'] = $match;
         }
         if (is_string($extract)) {
             $params['extract'] = $extract;
         }
         if (count($params) === 0) {
             $params = null;
         }

         return $params;
     }

     /**
      * @test createFromDescriptor
      * @test initFromDescriptor
      * @test CnabuLexerAbstractRule::isStarter
      * @test isCaseIgnored
      * @dataProvider dataProviderCreateInitFromDescriptor
      * @param bool $throwable If true expects that this test throws an exception in any moment.
      * @param bool|null $starter If null acts as not declared.
      * @param bool|null $case_sensitive If null acts as not declared.
      * @param string|null $method If null acts as not declared.
      * @param string|null $match If null acts as not declared.
      * @param string|null $extract If null acts as not declared.
      * @param string|null $content Content to test case.
      * @param string|null $result Expected result.
      * @param int $length Source length expected result.
      * @param bool $passed Apply Rule is passed.
      */
     public function testCreateInitFromDescriptor(
         bool $throwable, bool $starter = null, bool $case_sensitive = null,
         string $method = null, string $match = null, string $extract = null, string $content = null,
         string $result = null, int $length = 0, bool $passed = false
     ) {
         $params = $this->createDescriptor($starter, $case_sensitive, $method, $match, $extract);

         if ($throwable) {
             $this->expectException(ENabuLexerException::class);
         }

         $rule = CNabuLexerRuleRegEx::createFromDescriptor($params);
         $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule);

         if (is_bool($starter)) {
             $this->assertSame($starter, $rule->isStarter());
         } else {
             $this->assertFalse($rule->isStarter());
         }

         if (is_bool($case_sensitive)) {
             $this->assertSame($case_sensitive, $rule->isCaseIgnored());
         } else {
             $this->assertFalse($rule->isCaseIgnored());
         }

         $this->assertSame($method, $rule->getMethod());
         $this->assertSame($match, $rule->getMatchRegularExpression());
         $this->assertSame($extract, $rule->getExtractRegularExpression());

         if ($throwable) {
             $this->expectException(null);
         }
     }

     /**
      * @test applyRuleToContent
      * @test initFromDescriptor
      * @test CnabuLexerAbstractRule::isStarter
      * @test isCaseIgnored
      * @dataProvider dataProviderCreateInitFromDescriptor
      * @param bool $throwable If true expects that this test throws an exception in any moment.
      * @param bool|null $starter If null acts as not declared.
      * @param bool|null $case_sensitive If null acts as not declared.
      * @param string|null $method If null acts as not declared.
      * @param string|null $match If null acts as not declared.
      * @param string|null $extract If null acts as not declared.
      * @param string|null $content Content to test case.
      * @param string|null $result Expected result.
      * @param int $length Source length expected result.
      * @param bool $passed Apply Rule is passed.
      */
     public function testApplyRuleToContent(
         bool $throwable, bool $starter = null, bool $case_sensitive = null,
         string $method = null, string $match = null, string $extract = null, string $content = null,
         string $result = null, int $length = 0, bool $passed = false
     ) {
         if ($throwable) {
             $this->assertTrue($throwable);
         } else {
             try {
                 $rule = CNabuLexerRuleRegEx::createFromDescriptor(
                     $this->createDescriptor($starter, $case_sensitive, $method, $match, $extract)
                 );
             } catch (Exception $ex) {
                 $this->assertInstanceOf(ENabuLexerException::class, $ex);
                 $rule = null;
             }

             if (!is_null($rule)) {
                 $this->assertInstanceOf(CNabuLexerRuleRegEx::class, $rule);
                 if ($passed) {
                     $this->assertTrue($rule->applyRuleToContent($content));
                     $this->assertSame($result, $rule->getValue());
                     $this->assertSame($length, $rule->getSourceLength());
                 } else {
                     $this->assertFalse($rule->applyRuleToContent($content));
                     $this->assertNull($rule->getValue());
                     $this->assertSame(0, $rule->getSourceLength());
                 }
             }
         }
     }
}
