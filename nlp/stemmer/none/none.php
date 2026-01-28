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
 * None stemmer (no stemming).
 *
 * @package    qtype_essaysimilarity
 * @copyright  2026 Atthoriq Adillah Wicaksana
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/nlp/stemmer/stemmer.php');

/**
 * None stemmer class - returns words unchanged.
 */
class none_stemmer implements stemmer {
    /**
     * Clean tokens (no stemming).
     *
     * @param array $token Tokens to clean
     * @return array Unchanged tokens
     */
    public function clean(array $token): array {
        return $token;
    }

    /**
     * Stem word (returns word unchanged).
     *
     * @param string $word Word to stem
     * @return string Unchanged word
     */
    public function stem(string $word): string {
        return $word;
    }
}
