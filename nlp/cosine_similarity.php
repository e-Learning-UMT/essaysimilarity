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
 * Cosine Similarity Implementaion
 * Credit to @angeloskath, copied from https://github.com/angeloskath/php-nlp-tools/blob/master/src/NlpTools/Similarity/CosineSimilarity.php
 */
class cosine_similarity {
    private function product($v1, $v2) {
        $prod = 0.0;
        foreach ($v1 as $i => $xi) {
            $prod += $xi * $v2[$i];
        }

        return $prod;
    }

    private function magintude($vect): float {
        $magnitude = 0.0;
        foreach ($vect as $v) {
            $magnitude += $v * $v;
        }

        return sqrt($magnitude);
    }

    public function get_similarity($v1, $v2) {
        $dot = $this->product($v1, $v2);
        $magnitude = $this->magintude($v1) * $this->magintude($v2);

        return $magnitude == 0 ? 0 : $dot / $magnitude;
    }
}
