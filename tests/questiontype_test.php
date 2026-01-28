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
 * Unit tests for the essaysimilarity question type class.
 *
 * @package    qtype_essaysimilarity
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_essaysimilarity;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/essaysimilarity/questiontype.php');

/**
 * Unit tests for the essaysimilarity question type class.
 *
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qtype_essaysimilarity
 */
final class questiontype_test extends \advanced_testcase {
    /**
     * @var \qtype_essaysimilarity
     */
    protected $qtype;

    /**
     * Set up for every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->qtype = new \qtype_essaysimilarity();
    }

    /**
     * Test that the question type is manual graded.
     */
    public function test_is_manual_graded(): void {
        $this->assertTrue($this->qtype->is_manual_graded());
    }

    /**
     * Test extra question fields.
     */
    public function test_extra_question_fields(): void {
        $fields = $this->qtype->extra_question_fields();
        $this->assertIsArray($fields);
        $this->assertEquals('qtype_essaysimilarity_option', $fields[0]);
        $this->assertContains('responseformat', $fields);
        $this->assertContains('answerkey', $fields);
        $this->assertContains('upper_correctness', $fields);
        $this->assertContains('lower_correctness', $fields);
        $this->assertContains('questionlanguage', $fields);
    }

    /**
     * Test response file areas.
     */
    public function test_response_file_areas(): void {
        $areas = $this->qtype->response_file_areas();
        $this->assertIsArray($areas);
        $this->assertContains('attachments', $areas);
        $this->assertContains('answer', $areas);
    }

    /**
     * Test plugin name.
     */
    public function test_plugin_name(): void {
        $this->assertEquals('qtype_essaysimilarity', $this->qtype->plugin_name());
    }

    /**
     * Test get_defaults method.
     */
    public function test_get_defaults(): void {
        $defaults = \qtype_essaysimilarity::get_defaults();
        $this->assertIsArray($defaults);
        $this->assertEquals('editor', $defaults['responseformat']);
        $this->assertEquals(1, $defaults['responserequired']);
        $this->assertEquals(10, $defaults['responsefieldlines']);
        $this->assertEquals(0.99, $defaults['upper_correctness']);
        $this->assertEquals(0.01, $defaults['lower_correctness']);
        $this->assertEquals('none', $defaults['questionlanguage']);
    }

    /**
     * Test save and delete question options.
     */
    public function test_save_and_delete_question(): void {
        global $DB;

        $this->setAdminUser();

        // Create a test category.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();

        // Create question data.
        $questiondata = new \stdClass();
        $questiondata->category = $category->id;
        $questiondata->contextid = $category->contextid;
        $questiondata->qtype = 'essaysimilarity';
        $questiondata->name = 'Test essaysimilarity question';
        $questiondata->questiontext = 'Write an essay about testing.';
        $questiondata->generalfeedback = 'General feedback.';
        $questiondata->defaultmark = 10;
        $questiondata->penalty = 0.3;
        $questiondata->length = 1;

        // Set essaysimilarity specific options.
        $questiondata->responseformat = 'editor';
        $questiondata->responserequired = 1;
        $questiondata->responsefieldlines = 10;
        $questiondata->minwordlimit = 50;
        $questiondata->maxwordlimit = 500;
        $questiondata->attachments = 0;
        $questiondata->attachmentsrequired = 0;
        $questiondata->maxbytes = 0;
        $questiondata->filetypeslist = '';
        $questiondata->showfeedback = 3;
        $questiondata->showanswerkey = 0;
        $questiondata->showtextstats = 2;
        $questiondata->textstatitems = '';
        $questiondata->responsetemplate = ['text' => '', 'format' => 1];
        $questiondata->questionlanguage = 'none';
        $questiondata->upper_correctness = 0.99;
        $questiondata->lower_correctness = 0.01;

        $questiondata->graderinfo = [
            'text' => 'Grader info',
            'format' => FORMAT_HTML,
        ];

        $questiondata->answerkey = [
            'text' => 'This is the answer key for testing purposes.',
            'format' => FORMAT_HTML,
        ];

        // Save the question.
        $question = $generator->create_question('essaysimilarity', null, ['category' => $category->id]);

        // Check that the question was created.
        $this->assertNotEmpty($question->id);

        // Check that options were saved.
        $options = $DB->get_record('qtype_essaysimilarity_option', ['questionid' => $question->id]);
        $this->assertNotEmpty($options);

        // Test delete.
        $this->qtype->delete_question($question->id, $category->contextid);

        // Check that options were deleted.
        $this->assertFalse($DB->record_exists('qtype_essaysimilarity_option', ['questionid' => $question->id]));
    }
}
