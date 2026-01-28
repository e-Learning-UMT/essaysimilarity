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
 * Stemmer factory for creating language-specific stemmers.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2024 Thoriq Adillah
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('stemmer.php');

/**
 * Factory class for creating stemmers.
 */
class stemmer_factory {
    /**
     * Create a language-specific stemmer.
     *
     * @param string $lang Language code (en, id, none)
     * @return stemmer Language-specific stemmer instance
     */
    public static function create(string $lang): stemmer {
        require_once($lang."/".$lang.".php");

        $stemmer = $lang."_stemmer";
        return new $stemmer();
    }
}
