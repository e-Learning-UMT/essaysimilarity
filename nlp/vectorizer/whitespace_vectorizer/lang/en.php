<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * English whitespace vectorizer implementation.
 *
 * Credit to @angeloskath, adapted from
 * https://github.com/angeloskath/php-nlp-tools/blob/master/src/NlpTools/Tokenizers/WhitespaceTokenizer.php
 *
 * @package    qtype_essaysimilarity
 * @copyright  2024 Thoriq Adillah
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/nlp/vectorizer/vectorizer.php');

/**
 * English whitespace vectorizer class.
 */
class en_whitespace_vectorizer implements vectorizer {
    /**
     * Whitespace vectorizer.
     *
     * @param string $str Input text
     * @return array Array of tokens
     */
    public function vectorize(string $str): array {
        $str = $this->normalize($str);
        $token = preg_split('/[\pZ\pC]+/u', $str, -1, PREG_SPLIT_NO_EMPTY);

        return $token;
    }

    /**
     * Normalize the string from special characters and symbols.
     *
     * @param string $str Input text
     * @return string Normalized text
     */
    protected function normalize(string $str): string {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^a-z -]/im', ' ', $str);
        $str = preg_replace('/( +)/im', ' ', $str);
        $str = str_replace('- ', '', $str);

        return trim($str);
    }
}
