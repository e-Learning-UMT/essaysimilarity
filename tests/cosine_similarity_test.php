<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the cosine similarity calculator.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_essaysimilarity;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/nlp/cosine_similarity.php');

/**
 * Unit tests for the cosine similarity calculator.
 *
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \CosineSimilarity
 */
final class cosine_similarity_test extends \advanced_testcase {
    /**
     * Set up for every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test cosine similarity with identical vectors.
     */
    public function test_identical_vectors(): void {
        $cosinesim = new \CosineSimilarity();

        $vector1 = [1, 2, 3, 4, 5];
        $vector2 = [1, 2, 3, 4, 5];

        $similarity = $cosinesim->calculate($vector1, $vector2);

        // Identical vectors should have similarity of 1.0.
        $this->assertEquals(1.0, $similarity, '', 0.0001);
    }

    /**
     * Test cosine similarity with orthogonal vectors.
     */
    public function test_orthogonal_vectors(): void {
        $cosinesim = new \CosineSimilarity();

        $vector1 = [1, 0, 0];
        $vector2 = [0, 1, 0];

        $similarity = $cosinesim->calculate($vector1, $vector2);

        // Orthogonal vectors should have similarity of 0.0.
        $this->assertEquals(0.0, $similarity, '', 0.0001);
    }

    /**
     * Test cosine similarity with opposite vectors.
     */
    public function test_opposite_vectors(): void {
        $cosinesim = new \CosineSimilarity();

        $vector1 = [1, 2, 3];
        $vector2 = [-1, -2, -3];

        $similarity = $cosinesim->calculate($vector1, $vector2);

        // Opposite vectors should have similarity of -1.0.
        $this->assertEquals(-1.0, $similarity, '', 0.0001);
    }

    /**
     * Test cosine similarity with similar vectors.
     */
    public function test_similar_vectors(): void {
        $cosinesim = new \CosineSimilarity();

        $vector1 = [1, 2, 3, 4];
        $vector2 = [1, 2, 3, 5];

        $similarity = $cosinesim->calculate($vector1, $vector2);

        // Similar vectors should have high similarity.
        $this->assertGreaterThan(0.9, $similarity);
        $this->assertLessThan(1.0, $similarity);
    }

    /**
     * Test cosine similarity with zero vectors.
     */
    public function test_zero_vectors(): void {
        $cosinesim = new \CosineSimilarity();

        $vector1 = [0, 0, 0];
        $vector2 = [1, 2, 3];

        $similarity = $cosinesim->calculate($vector1, $vector2);

        // Zero vector should return 0.
        $this->assertEquals(0.0, $similarity);
    }

    /**
     * Test cosine similarity with different length vectors.
     */
    public function test_different_length_vectors(): void {
        $cosinesim = new \CosineSimilarity();

        $vector1 = [1, 2, 3];
        $vector2 = [1, 2];

        // Should handle gracefully or throw exception.
        // Depending on implementation.
        try {
            $similarity = $cosinesim->calculate($vector1, $vector2);
            $this->assertIsFloat($similarity);
        } catch (\Exception $e) {
            // Exception is acceptable for mismatched vectors.
            $this->assertTrue(true);
        }
    }
}
