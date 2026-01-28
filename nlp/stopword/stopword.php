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
 * Stopword removal class.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2026 Atthoriq Adillah Wicaksana
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/nlp/cleaner/cleaner.php');

/**
 * Stopword removal class implementing cleaner interface.
 */
class stopword implements cleaner {
    /** @var array Stopwords list */
    protected $stopwords = [];

    /**
     * Constructor.
     *
     * @param string $lang Language code
     */
    public function __construct($lang) {
        $this->stopwords = require("lang/$lang.php");
    }

    /**
     * Clean tokens by removing stopwords.
     *
     * @param array $token Tokens to clean
     * @return array Cleaned tokens
     */
    public function clean(array $token): array {
        return array_udiff($token, $this->stopwords, 'strcasecmp');
    }

    /**
     * Remove stop word from token and then stem the token.
     *
     * @param array $token Tokens
     * @param stemmer $stemmer Stemmer interface
     * @return array Cleaned token
     */
    public function remove_stopword($token, $stemmer) {
        $token = array_udiff($token, $this->stopwords, 'strcasecmp');

        foreach ($token as &$tok) {
            $tok = $stemmer->stem($tok);
        }

        return $token;
    }
}
