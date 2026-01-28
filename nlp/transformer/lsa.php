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

require_once('matrix.php');
require_once('svd.php');

class lsa {
    /**
     * @var matrix
     */
    private $matrix;

    /**
     * @param array $documents
     */
    public function __construct($documents) {
        $this->matrix = new matrix($documents);
    }

    /**
     * Perform latent semantic analysis to get the most important topic of the word with dimensional reduction
     */
    public function transform() {
        $svd = new svd($this->matrix);
        $S = $svd->S();

        // Truncate the matrix with low-rank approximation
        for ($i = $svd->K(); $i < count($S); $i++) {
            $S[$i][$i] = 0;
        }

        // Perform LSA
        $lsa = $this->matrix->multiply(
            $this->matrix->multiply($svd->U(), $S),
            $svd->VT()
        );

        $transformed = [];
        foreach ($this->matrix->original() as $i => $_) {
            $transformed[$i] = array_combine(
                array_keys($this->matrix->original()[0]),
                array_values($lsa[$i])
            );
        }

        return $transformed;
    }
}
