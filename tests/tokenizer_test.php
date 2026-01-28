<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the NLP tokenizer.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_essaysimilarity;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/nlp/tokenizer.php');

/**
 * Unit tests for the NLP tokenizer.
 *
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \Tokenizer
 */
final class tokenizer_test extends \advanced_testcase {
    /**
     * Set up for every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test tokenizer with English text.
     */
    public function test_tokenize_english(): void {
        $tokenizer = new \Tokenizer('en');
        $text = 'This is a simple test of the tokenizer.';

        $tokens = $tokenizer->tokenize($text);

        $this->assertIsArray($tokens);
        $this->assertNotEmpty($tokens);
        $this->assertContains('test', $tokens);
        $this->assertContains('tokenizer', $tokens);
    }

    /**
     * Test tokenizer with punctuation.
     */
    public function test_tokenize_punctuation(): void {
        $tokenizer = new \Tokenizer('en');
        $text = 'Hello, world! This is a test.';

        $tokens = $tokenizer->tokenize($text);

        $this->assertIsArray($tokens);
        // Punctuation should be removed or handled appropriately.
        $this->assertContains('hello', $tokens);
        $this->assertContains('world', $tokens);
    }

    /**
     * Test tokenizer converts to lowercase.
     */
    public function test_tokenize_lowercase(): void {
        $tokenizer = new \Tokenizer('en');
        $text = 'UPPERCASE lowercase MixedCase';

        $tokens = $tokenizer->tokenize($text);

        $this->assertIsArray($tokens);
        // Should be converted to lowercase.
        $this->assertContains('uppercase', $tokens);
        $this->assertContains('lowercase', $tokens);
        $this->assertContains('mixedcase', $tokens);
    }

    /**
     * Test tokenizer with empty string.
     */
    public function test_tokenize_empty(): void {
        $tokenizer = new \Tokenizer('en');
        $text = '';

        $tokens = $tokenizer->tokenize($text);

        $this->assertIsArray($tokens);
        $this->assertEmpty($tokens);
    }

    /**
     * Test tokenizer with no language specified.
     */
    public function test_tokenize_no_language(): void {
        $tokenizer = new \Tokenizer('none');
        $text = 'Testing without specific language.';

        $tokens = $tokenizer->tokenize($text);

        $this->assertIsArray($tokens);
        $this->assertNotEmpty($tokens);
    }
}
