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
 * Unit tests for the essaysimilarity question definition class.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_essaysimilarity;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/question.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the essaysimilarity question definition class.
 *
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qtype_essaysimilarity_question
 */
final class question_test extends \advanced_testcase {
    /**
     * Set up for every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Create a test question.
     *
     * @return \qtype_essaysimilarity_question
     */
    protected function make_essaysimilarity_question(): \qtype_essaysimilarity_question {
        \question_bank::load_question_definition_classes('essaysimilarity');
        $question = new \qtype_essaysimilarity_question();

        \test_question_maker::initialise_a_question($question);

        $question->name = 'Essay similarity question';
        $question->questiontext = 'Write an essay about software testing.';
        $question->generalfeedback = 'General feedback.';
        $question->responseformat = 'editor';
        $question->responserequired = 1;
        $question->responsefieldlines = 10;
        $question->minwordlimit = 50;
        $question->maxwordlimit = 500;
        $question->attachments = 0;
        $question->attachmentsrequired = 0;
        $question->graderinfo = '';
        $question->graderinfoformat = FORMAT_HTML;
        $question->responsetemplate = '';
        $question->responsetemplateformat = FORMAT_HTML;
        $question->answerkey = 'Software testing is a process of evaluating a software application to ensure it meets requirements and is free from defects.';
        $question->answerkeyformat = FORMAT_HTML;
        $question->showanswerkey = 0;
        $question->showfeedback = 3;
        $question->showtextstats = 2;
        $question->textstatitems = '';
        $question->questionlanguage = 'en';
        $question->upper_correctness = 0.80;
        $question->lower_correctness = 0.50;
        $question->qtype = \question_bank::get_qtype('essaysimilarity');

        return $question;
    }

    /**
     * Test validation error for empty response.
     */
    public function test_get_validation_error_empty(): void {
        $question = $this->make_essaysimilarity_question();
        $response = ['answer' => '', 'attachments' => ''];

        $error = $question->get_validation_error($response);
        $this->assertNotEmpty($error);
    }

    /**
     * Test validation error for response template.
     */
    public function test_get_validation_error_template(): void {
        $question = $this->make_essaysimilarity_question();
        $question->responsetemplate = 'This is a template';

        $response = ['answer' => 'This is a template', 'attachments' => ''];

        $error = $question->get_validation_error($response);
        $this->assertNotEmpty($error);
    }

    /**
     * Test validation passes for valid response.
     */
    public function test_get_validation_error_valid(): void {
        $question = $this->make_essaysimilarity_question();

        $response = [
            'answer' => 'Software testing involves various techniques to verify software quality.',
            'attachments' => '',
        ];

        $error = $question->get_validation_error($response);
        $this->assertEquals('', $error);
    }

    /**
     * Test is_complete_response.
     */
    public function test_is_complete_response(): void {
        $question = $this->make_essaysimilarity_question();

        // Empty response should be incomplete.
        $this->assertFalse($question->is_complete_response(['answer' => '', 'attachments' => '']));

        // Valid response should be complete.
        $this->assertTrue($question->is_complete_response([
            'answer' => 'This is a valid essay response.',
            'attachments' => '',
        ]));
    }

    /**
     * Test is_gradable_response.
     */
    public function test_is_gradable_response(): void {
        $question = $this->make_essaysimilarity_question();

        // Empty response should not be gradable.
        $this->assertFalse($question->is_gradable_response(['answer' => '', 'attachments' => '']));

        // Valid response should be gradable.
        $this->assertTrue($question->is_gradable_response([
            'answer' => 'This is a gradable response.',
            'attachments' => '',
        ]));
    }

    /**
     * Test get_stats method returns expected structure.
     */
    public function test_get_stats(): void {
        $question = $this->make_essaysimilarity_question();

        $responsetext = 'Software testing is essential for quality assurance.';
        $stats = $question->get_stats($responsetext);

        $this->assertIsObject($stats);
        $this->assertObjectHasProperty('wordcount', $stats);
        $this->assertObjectHasProperty('charcount', $stats);
        $this->assertGreaterThan(0, $stats->wordcount);
        $this->assertGreaterThan(0, $stats->charcount);
    }

    /**
     * Test summarise_response.
     */
    public function test_summarise_response(): void {
        $question = $this->make_essaysimilarity_question();

        $response = [
            'answer' => 'Software testing is a critical part of development.',
            'attachments' => '',
        ];

        $summary = $question->summarise_response($response);
        $this->assertNotEmpty($summary);
    }

    /**
     * Test grade_response method.
     */
    public function test_grade_response(): void {
        $question = $this->make_essaysimilarity_question();

        // Very similar response should get high grade.
        $similarresponse = [
            'answer' => 'Software testing is a process of evaluating a software to ensure quality and detect defects.',
        ];

        [$fraction, $state] = $question->grade_response($similarresponse);

        $this->assertIsFloat($fraction);
        $this->assertGreaterThanOrEqual(0, $fraction);
        $this->assertLessThanOrEqual(1, $fraction);
        $this->assertInstanceOf(\question_state::class, $state);
    }

    /**
     * Test plugin_name method.
     */
    public function test_plugin_name(): void {
        $question = $this->make_essaysimilarity_question();
        $this->assertEquals('qtype_essaysimilarity', $question->plugin_name());
    }
}
