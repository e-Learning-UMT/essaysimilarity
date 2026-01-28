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

class matrix {
    private $matrix = [];
    private $original = [];

    /**
     * @param array $matrix a multi-dimensional array
     */
    public function __construct($matrix) {
        $this->original = $matrix;
    }

    public function truncate(&$matrix, $rows, $columns) {
        for ($i = 0; $i < count($matrix); $i++) {
            if ($i > $rows) {
                array_splice($matrix, $rows);
                break;
            }

            array_splice($matrix, $columns);
        }
    }

    /**
     * Get the original matrix of documents
     */
    public function original() {
        return $this->original;
    }

    /**
     * Get matrix from the original documents vector
     */
    public function get() {
        $matrix = [];

        // Convert string key to numerical key for operational
        foreach ($this->original as $mtx) {
            $matrix[] = array_values($mtx);
        }

        return $matrix;
    }

    /**
     * Matrix multiplication
     *
     * @param array $matrix_a A multi-dimensional matrix
     * @param array $matrix_a A matrix that at least one dimensional
     * @return array
     */
    public function multiply($matrix_a, $matrix_b) {
        $product = [];

        $cols_a = count($matrix_a[0]);
        $rows_b = count($matrix_b);

        // multiplication cannot be done
        if ($cols_a !== $rows_b) {
            throw new InvalidArgumentException("Column A ($cols_a) and Row B ($rows_b) is not equal");
        }

        foreach ($matrix_a as $i => $_) {
            foreach ($matrix_b[0] as $j => $_) {
                foreach ($matrix_a[0] as $p => $_) {
                    $product[$i][$j] += $matrix_a[$i][$p] * $matrix_b[$p][$j];
                }
            }
        }

        return $product;
    }

    /**
     * Matrix transposition
     *
     * @param array $matrix
     * @return array
     */
    public function transpose($matrix) {
        $result = [];

        foreach ($matrix as $i => $_) {
            foreach ($matrix[0] as $j => $_) {
                $result[$i][$j] = $matrix[$j][$i];
            }
        }

        return $result;
    }

    /**
     * Matrix rounding
     *
     * @param array $matrix
     * @return array
     */
    public function round($matrix) {
        $result = [];

        foreach ($matrix as $i => $_) {
            foreach ($matrix[0] as $j => $_) {
                $result[$i][$j] = round($matrix[$j][$i], 2);
            }
        }

        return $result;
    }
}
