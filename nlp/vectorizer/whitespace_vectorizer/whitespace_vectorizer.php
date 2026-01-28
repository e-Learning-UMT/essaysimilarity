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
 * Whitespace vectorizer factory for creating language-specific vectorizers.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2024 Thoriq Adillah
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/nlp/vectorizer/vectorizer.php');

/**
 * Factory class for creating whitespace vectorizers.
 */
class whitespace_vectorizer {
    /**
     * Create a language-specific vectorizer.
     *
     * @param string $lang Language code (en, id, none)
     * @return vectorizer Language-specific vectorizer instance
     */
    public static function create(string $lang): vectorizer {
        require_once("lang/" . $lang . ".php");

        $vectorizer = $lang . "_whitespace_vectorizer";
        return new $vectorizer();
    }
}
