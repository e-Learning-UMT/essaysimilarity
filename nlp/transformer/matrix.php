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
 * Matrix class for handling multi-dimensional arrays.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2024 Thoriq Adillah
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix {
    /**
     * @var array Current matrix data.
     */
    private $matrix = [];

    /**
     * @var array Original matrix data.
     */
    private $original = [];

    /**
     * Constructor.
     *
     * @param array $matrix A multi-dimensional array
     */
    public function __construct($matrix) {
        $this->original = $matrix;
    }

    /**
     * Truncate matrix to specified dimensions.
     *
     * @param array $matrix Matrix to truncate
     * @param int $rows Maximum number of rows
     * @param int $columns Maximum number of columns
     */
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
     * Get matrix from the original documents vector.
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
     * Matrix Multiplication.
     *
     * @param array $matrix_a A multi-dimensional matrix
     * @param array $matrix_a A matrix that at least one dimensional
     * @return array
     */
    public function multiply($matrixa, $matrixb) {
        $product = [];

        $colsa = count($matrixa[0]);
        $rowsb = count($matrixb);

        // multiplication cannot be done
        if ($colsa !== $rowsb) {
            throw new InvalidArgumentException("Column A ($colsa) and Row B ($rowsb) is not equal");
        }

        foreach ($matrixa as $i => $row) {
            foreach ($matrixb[0] as $j => $col) {
                foreach ($matrixa[0] as $p => $key) {
                    $product[$i][$j] += $matrixa[$i][$p] * $matrixb[$p][$j];
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

        foreach ($matrix as $i => $row) {
            foreach ($matrix[0] as $j => $col) {
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

        foreach ($matrix as $i => $row) {
            foreach ($matrix[0] as $j => $col) {
                $result[$i][$j] = round($matrix[$j][$i], 2);
            }
        }

        return $result;
    }
}
