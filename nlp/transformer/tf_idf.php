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
 * TF-IDF implementation with some modification
 * Credit to @jorgecasas from his PHP-ML library. Copied from https://github.com/jorgecasas/php-ml/blob/develop/src/FeatureExtraction/TfIdfTransformer.php
 */
class tf_idf {
    private $documents = [];
    private $idf = [];

    public function __construct($documents) {
        $this->documents = $documents;
        if (count($this->documents) > 0) {
            $this->fit();
        }
    }

    private function count_idf() {
        $this->idf = array_fill_keys(array_keys($this->documents[0]), 0);

        foreach ($this->documents as $sample) {
            foreach ($sample as $index => $count) {
                if ($count > 0) {
                    ++$this->idf[$index];
                }
            }
        }
    }

    public function fit() {
        $this->count_idf();

        $n_docs = count($this->documents);
        foreach ($this->idf as &$value) {
            $value = 1 + log((float) (($n_docs + 1) / ($value + 1)), 10.0); // idf with smoothing to avoid division by zero
        }
    }

    public function transform() {
        foreach ($this->documents as &$document) {
            foreach ($document as $index => &$feature) {
                $feature *= $this->idf[$index];
            }
        }

        return $this->documents;
    }
}
