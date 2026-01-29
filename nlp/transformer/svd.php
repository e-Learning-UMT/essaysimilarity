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
 * Singular Value Decomposition (SVD) implementation.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2024 Thoriq Adillah
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('matrix.php');

/**
 * SVD class for matrix decomposition.
 */
class svd {
    /**
     * Left singular vector
     * @var array
     */
    private $u  = [];

    /**
     * One-dimensional array of singular vector
     * @var array
     */
    private $sv  = [];

    /**
     * Right singular vector
     * @var array
     */
    private $v  = [];

    /**
     * Matrix that being passed
     * @var matrix
     */
    private $matrix = [];

    /**
     * @var int Number of rows.
     */
    private $m;

    /**
     * @var int Number of columns.
     */
    private $n;

    /**
     * Perform SVD
     *
     * @param matrix $matrix Matrix class to perform calculation
     */
    public function __construct($matrix) {
        $this->matrix = $matrix;
        $this->m = count($this->matrix->get());
        $this->n = count($this->matrix->get()[0]);

        $this->decompose();
    }

    /**
     * Perform Singular Value Decomposition
     * Translation of JAMA SVD implementation to PHP https://github.com/fiji/Jama/blob/master/src/main/java/Jama/SingularValueDecomposition.java
     */
    public function decompose() {
        // Convert array key from string to numeric.
        $m = $this->m;
        $n = $this->n;
        $nu = min($m, $n);

        // Copy matrix to A
        $a = $this->matrix->get();

        $s = array_fill(0, min($m + 1, $n), 0);
        $u = array_fill(0, $m, array_fill(0, $nu, 0));
        $v = array_fill(0, $n, array_fill(0, $n, 0));
        $e = array_fill(0, $n, 0);
        $work = array_fill(0, $m, 0);

        // TODO: delete want u and want v later
        $wantu = true;
        $wantv = true;

        // Reduce A to bidiagonal form,
        // storing the diagonal elements in S and the super-diagonal elements in e.
        $nct = min($m - 1, $n);
        $nrt = max(0, min($n - 2, $m));
        for ($k = 0; $k < max($nct, $nrt); $k++) {
            if ($k < $nct) {
                // Compute the transformation for the k-th column and
                // place the k-th diagonal in s[k].
                // Compute 2-norm of k-th column without under/overflow.
                $s[$k] = 0;
                for ($i = $k; $i < $m; $i++) {
                    $s[$k] = hypot($s[$k], $a[$i][$k]);
                }

                if ($s[$k] != 0.0) {
                    if ($a[$k][$k] < 0.0) {
                        $s[$k] = -$s[$k];
                    }
                    for ($i = $k; $i < $m; $i++) {
                        $a[$i][$k] /= $s[$k];
                    }
                    $a[$k][$k] += 1.0;
                }

                $s[$k] = -$s[$k];
            }

            for ($j = $k + 1; $j < $n; $j++) {
                if (($k < $nct) && ($s[$k] != 0.0)) {
                    // Apply the transformation.
                    $t = 0;
                    for ($i = $k; $i < $m; $i++) {
                        $t += $a[$i][$k] * $a[$i][$j];
                    }

                    $t = -$t / $a[$k][$k];
                    for ($i = $k; $i < $m; $i++) {
                        $a[$i][$j] += $t * $a[$i][$k];
                    }
                }

                // Place the k-th row of A into e for the
                // subsequent calculation of the row transformation.
                $e[$j] = $a[$k][$j];
            }

            // TODO: delete want u later
            if ($wantu && ($k < $nct)) {
                // Place the transformation in U for subsequent back
                // multiplication.
                for ($i = $k; $i < $m; $i++) {
                    $u[$i][$k] = $a[$i][$k];
                }
            }

            if ($k < $nct) {
                // Compute the k-th row transformation and place the
                // k-th super-diagonal in e[k].
                // Compute 2-norm without under/overflow.
                $e[$k] = 0;
                for ($i = $k + 1; $i < $n; $i++) {
                    $e[$k] = hypot($e[$k], $e[$i]);
                }

                if ($e[$k] != 0.0) {
                    if ($e[$k + 1] < 0.0) {
                        $e[$k] = -$e[$k];
                    }

                    for ($i = $k + 1; $i < $n; $i++) {
                        $e[$i] /= $e[$k];
                    }

                    $e[$k + 1] += 1.0;
                }

                $e[$k] = -$e[$k];
                if (($k + 1 < $m) && ($e[$k] != 0.0)) {
                    // Apply the transformation.
                    for ($i = $k + 1; $i < $m; $i++) {
                        $work[$i] = 0.0;
                    }

                    for ($j = $k + 1; $j < $n; $j++) {
                        for ($i = $k + 1; $i < $m; $i++) {
                            $work[$i] += $e[$j] * $a[$i][$j];
                        }
                    }

                    for ($j = $k + 1; $j < $n; $j++) {
                        $t = -$e[$j] / $e[$k + 1];
                        for ($i = $k + 1; $i < $m; $i++) {
                            $a[$i][$j] += $t * $work[$i];
                        }
                    }
                }

                // TODO: delete want v later
                if ($wantv) {
                    // Place the transformation in V for subsequent
                    // back multiplication.
                    for ($i = $k + 1; $i < $n; $i++) {
                        $v[$i][$k] = $e[$i];
                    }
                }
            }
        }

        // Set up the final bidiagonal matrix or order p.
        $p = min($n, $m + 1);
        if ($nct < $n) {
            $s[$nct] = $a[$nct][$nct];
        }

        if ($m < $p) {
            $s[$p - 1] = 0.0;
        }

        if ($nrt + 1 < $p) {
            $e[$nrt] = $a[$nrt][$p - 1];
        }

        $e[$p - 1] = 0.0;

        // TODO: delete want u later
        if ($wantu) {
            for ($j = $nct; $j < $nu; $j++) {
                for ($i = 0; $i < $m; $i++) {
                    $u[$i][$j] = 0.0;
                }

                $u[$j][$j] = 1.0;
            }

            for ($k = $nct - 1; $k >= 0; $k--) {
                if ($s[$k] != 0.0) {
                    for ($j = $k + 1; $j < $nu; $j++) {
                        $t = 0;
                        for ($i = $k; $i < $m; $i++) {
                            $t += $u[$i][$k] * $u[$i][$j];
                        }

                        $t = -$t / $u[$k][$k];
                        for ($i = $k; $i < $m; $i++) {
                            $u[$i][$j] += $t * $u[$i][$k];
                        }
                    }

                    for ($i = $k; $i < $m; $i++) {
                        $u[$i][$k] = -$u[$i][$k];
                    }

                    $u[$k][$k] = 1.0 + $u[$k][$k];
                    for ($i = 0; $i < $k - 1; $i++) {
                        $u[$i][$k] = 0.0;
                    }
                } else {
                    for ($i = 0; $i < $m; $i++) {
                        $u[$i][$k] = 0.0;
                    }

                    $u[$k][$k] = 1.0;
                }
            }
        }

        // TODO: delete want v later
        if ($wantv) {
            for ($k = $n - 1; $k >= 0; $k--) {
                if (($k < $nrt) && ($e[$k] != 0.0)) {
                    for ($j = $k + 1; $j < $nu; $j++) {
                        $t = 0;
                        for ($i = $k + 1; $i < $n; $i++) {
                              $t += $v[$i][$k] * $v[$i][$j];
                        }

                        $t = -$t / $v[$k + 1][$k];
                        for ($i = $k + 1; $i < $n; $i++) {
                            $v[$i][$j] += $t * $v[$i][$k];
                        }
                    }
                }

                for ($i = 0; $i < $n; $i++) {
                    $v[$i][$k] = 0.0;
                }

                $v[$k][$k] = 1.0;
            }
        }

        // Main iteration loop for the singular values.
        $pp = $p - 1;
        $iter = 0;
        $eps = pow(2.0, -52.0);
        $tiny = pow(2.0, -966.0);
        while ($p > 0) {
            $k = $kase = 0;

            // Here is where a test for too many iterations would go.

            // This section of the program inspects for
            // Negligible elements in the s and e arrays.  On
            // completion the variables kase and k are set as follows.

            // Kase = 1     if s(p) and e[k-1] are negligible and k<p.
            // Kase = 2     if s(k) is negligible and k<p.
            // Kase = 3     if e[k-1] is negligible, k<p, and
            // s(k), ..., s(p) are not negligible (qr step).
            // kase = 4     if e(p-1) is negligible (convergence).
            for ($k = $p - 2; $k >= -1; $k--) {
                if ($k == -1) {
                    break;
                }

                if (abs($e[$k]) <= $tiny + $eps * (abs($s[$k]) + abs($s[$k + 1]))) {
                    $e[$k] = 0.0;
                    break;
                }
            }

            if ($k == $p - 2) {
                $kase = 4;
            } else {
                $ks = 0;
                for ($ks = $p - 1; $ks >= $k; $ks--) {
                    if ($ks == $k) {
                        break;
                    }

                    $t = ($ks != $p ? abs($e[$ks]) : 0.) + ($ks != $k + 1 ? abs($e[$ks - 1]) : 0.);
                    if (abs($s[$ks]) <= $tiny + $eps * $t) {
                        $s[$ks] = 0.0;
                        break;
                    }
                }

                if ($ks == $k) {
                    $kase = 3;
                } else if ($ks == $p - 1) {
                    $kase = 1;
                } else {
                    $kase = 2;
                    $k = $ks;
                }
            }
            $k++;

            // Perform the task indicated by kase.
            switch ($kase) {
                // Deflate negligible s(p).
                case 1:
                    $f = $e[$p - 2];
                    $e[$p - 2] = 0.0;

                    for ($j = $p - 2; $j >= $k; $j--) {
                        $t = hypot($s[$j], $f);
                        $cs = $s[$j] / $t;
                        $sn = $f / $t;
                        $s[$j] = $t;
                        if ($j != $k) {
                            $f = -$sn * $e[$j - 1];
                            $e[$j - 1] = $cs * $e[$j - 1];
                        }

                        // TODO: delete want v later
                        if ($wantv) {
                            for ($i = 0; $i < $n; $i++) {
                                $t = $cs * $v[$i][$j] + $sn * $v[$i][$p - 1];
                                $v[$i][$p - 1] = -$sn * $v[$i][$j] + $cs * $v[$i][$p - 1];
                                $v[$i][$j] = $t;
                            }
                        }
                    }
                    break;

                // Split at negligible s(k).
                case 2:
                    $f = $e[$k - 1];
                    $e[$k - 1] = 0.0;

                    for ($j = $k; $j < $p; $j++) {
                        $t = hypot($s[$j], $f);
                        $cs = $s[$j] / $t;
                        $sn = $f / $t;
                        $s[$j] = $t;
                        $f = -$sn * $e[$j];
                        $e[$j] = $cs * $e[$j];

                        // TODO: delete want u later
                        if ($wantu) {
                            for ($i = 0; $i < $m; $i++) {
                                $t = $cs * $u[$i][$j] + $sn * $u[$i][$k - 1];
                                $u[$i][$k - 1] = -$sn * $u[$i][$j] + $cs * $u[$i][$k - 1];
                                $u[$i][$j] = $t;
                            }
                        }
                    }
                    break;

                // Perform one qr step.
                case 3:
                    // Calculate the shift.
                    $scale = max(max(
                        max(max(
                            abs($s[$p - 1]),
                            abs($s[$p - 2])
                        ), abs($e[$p - 2])),
                        abs($s[$k])
                    ), abs($e[$k]));
                    $sp = $s[$p - 1] / $scale;
                    $spm1 = $s[$p - 2] / $scale;
                    $epm1 = $e[$p - 2] / $scale;
                    $sk = $s[$k] / $scale;
                    $ek = $e[$k] / $scale;
                    $b = (($spm1 + $sp) * ($spm1 - $sp) + $epm1 * $epm1) / 2.0;
                    $c = ($sp * $epm1) * ($sp * $epm1);
                    $shift = 0.0;

                    if (($b != 0.0) || ($c != 0.0)) {
                          $shift = sqrt($b * $b + $c);
                        if ($b < 0.0) {
                            $shift = -$shift;
                        }
                          $shift = $c / ($b + $shift);
                    }

                    $f = ($sk + $sp) * ($sk - $sp) + $shift;
                    $g = $sk * $ek;

                    // Chase zeros.
                    for ($j = $k; $j < $p - 1; $j++) {
                        $t = hypot($f, $g);
                        $cs = $f / $t;
                        $sn = $g / $t;
                        if ($j != $k) {
                            $e[$j - 1] = $t;
                        }

                        $f = $cs * $s[$j] + $sn * $e[$j];
                        $e[$j] = $cs * $e[$j] - $sn * $s[$j];
                        $g = $sn * $s[$j + 1];
                        $s[$j + 1] = $cs * $s[$j + 1];

                        // TODO: delete want v later
                        if ($wantv) {
                            for ($i = 0; $i < $n; $i++) {
                                  $t = $cs * $v[$i][$j] + $sn * $v[$i][$j + 1];
                                  $v[$i][$j + 1] = -$sn * $v[$i][$j] + $cs * $v[$i][$j + 1];
                                  $v[$i][$j] = $t;
                            }
                        }

                        $t = hypot($f, $g);
                        $cs = $f / $t;
                        $sn = $g / $t;
                        $s[$j] = $t;
                        $f = $cs * $e[$j] + $sn * $s[$j + 1];
                        $s[$j + 1] = -$sn * $e[$j] + $cs * $s[$j + 1];
                        $g = $sn * $e[$j + 1];
                        $e[$j + 1] = $cs * $e[$j + 1];

                        // TODO: delete want u later
                        if ($wantu && ($j < $m - 1)) {
                            for ($i = 0; $i < $m; $i++) {
                                $t = $cs * $u[$i][$j] + $sn * $u[$i][$j + 1];
                                $u[$i][$j + 1] = -$sn * $u[$i][$j] + $cs * $u[$i][$j + 1];
                                $u[$i][$j] = $t;
                            }
                        }
                    }

                    $e[$p - 2] = $f;
                    $iter = $iter + 1;
                    break;

                case 4:
                    // Make the singular values positive.
                    if ($s[$k] <= 0.0) {
                        $s[$k] = ($s[$k] < 0.0 ? -$s[$k] : 0.0);

                        // TODO: delete want v later
                        if ($wantv) {
                            for ($i = 0; $i <= $pp; $i++) {
                                $v[$i][$k] = -$v[$i][$k];
                            }
                        }
                    }

                    // Order the singular values.
                    while ($k < $pp) {
                        if ($s[$k] >= $s[$k + 1]) {
                            break;
                        }
                        $t = $s[$k];
                        $s[$k] = $s[$k + 1];
                        $s[$k + 1] = $t;

                        // TODO: delete want v later
                        if ($wantv && ($k < $n - 1)) {
                            for ($i = 0; $i < $n; $i++) {
                                $t = $v[$i][$k + 1];
                                $v[$i][$k + 1] = $v[$i][$k];
                                $v[$i][$k] = $t;
                            }
                        }

                        // TODO: delete want u later
                        if ($wantu && ($k < $m - 1)) {
                            for ($i = 0; $i < $m; $i++) {
                                $t = $u[$i][$k + 1];
                                $u[$i][$k + 1] = $u[$i][$k];
                                $u[$i][$k] = $t;
                            }
                        }

                        $k++;
                    }

                    $iter = 0;
                    $p--;
                    break;
            }
        }

        $this->U = $u;
        $this->Sv = $s;
        $this->V = $v;
    }

    /**
     * Get left singular vectors.
     *
     * @return array U matrix
     */
    public function u() {
        return $this->U;
    }

    /**
     * Calculate the multi-diagonal S
     */
    public function s() {
        $s = array_fill(0, $this->m, array_fill(0, $this->n, 0));
        for ($i = 0; $i < $this->m; $i++) {
            $s[$i][$i] = $this->Sv[$i];
        }

        return $s;
    }

    /**
     * Get right singular vectors.
     *
     * @return array V matrix
     */
    public function v() {
        return $this->V;
    }

    /**
     * Get transposed right singular vectors.
     *
     * @return array V^T matrix
     */
    public function vt() {
        return $this->matrix->transpose($this->V);
    }

    /**
     * Calculate the rank of the matrix.
     *
     * @return int Matrix rank
     */
    public function rank() {
        $eps = pow(2, -52);
        $tol = max($this->m, $this->n) * $this->Sv[0] * $eps;
        $rank = 0;
        for ($i = 0; $i < count($this->Sv); $i++) {
            if ($this->Sv[$i] > $tol) {
                ++$rank;
            }
        }

        return $rank;
    }

    /**
     * Low rank approximation
     */
    public function k() {
        $q = 0.9;
        $k = 0;
        $froba = 0;
        $frobak = 0;
        for ($i = 0; $i < $this->rank(); $i++) {
            $froba += $this->Sv[$i];
        }
        do {
            for ($i = 0; $i <= $k; $i++) {
                $frobak += $this->Sv[$i];
            }
            $clt = $frobak / $froba;
            $k++;
        } while ($clt < $q);

        return $k;
    }
}
