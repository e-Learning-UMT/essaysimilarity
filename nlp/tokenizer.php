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

require_once('stopword/stopword.php');
require_once('stemmer/stemmer.php');

class tokenizer {
    private $lang = 'none';

    public function __construct($lang) {
        $this->lang = clean_param($lang, PARAM_ALPHA);
    }

    /**
     * Whitespace tokenizer
     * Credit to @angeloskath, copied from https://github.com/angeloskath/php-nlp-tools/blob/master/src/NlpTools/Tokenizers/WhitespaceTokenizer.php
     * @param string $str string to be tokenized
     * @return array $str tokenized string
     */
    public function tokenize($str) {
        $str = $this->normalize($str);
        $token = preg_split('/[\pZ\pC]+/u', $str, -1, PREG_SPLIT_NO_EMPTY);

        // we assume that stemmer implementation and stopword dictionary for certain language is present, otherwise errors will be thrown
        if ($this->lang !== 'none') {
            require_once("stemmer/$this->lang/$this->lang.php");

            $classname = $this->lang . '_stemmer';
            $stemmer = new $classname();

            $stopword = new stopword($this->lang);
            $token = $stopword->remove_stopword($token, $stemmer);
        }

        $raw = array_flip($token);
        $raw = array_map(function () {
            return 0;
        }, $raw);

        return [
        'counted' => array_count_values($token),
        'raw' => $raw,
        ];
    }

    /**
     * Normalize the string from special characters and symbols
     */
    protected function normalize($str) {
        $str = preg_replace('/[^a-z -]/im', ' ', $str);
        $str = preg_replace('/( +)/im', ' ', $str);
        $str = str_replace('- ', '', $str);

        return trim($str);
    }
}
