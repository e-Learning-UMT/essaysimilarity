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
 * Defines the editing form for the essaysimilarity question type.
 *
 * @package    qtype
 * @subpackage essaysimilarity
 * @copyright  2022 Atthoriq Adillah Wicaksana (thoriqadillah59@gmail.com)
 * @copyright  based on work by 2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Get parent class.
require_once($CFG->dirroot . '/question/type/essay/edit_essay_form.php');

/**
 * Essay question type editing form.
 *
 * @copyright  2022 Atthoriq Adillah Wicaksana (thoriqadillah59@gmail.com)
 * @copyright  based on work by 2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essaysimilarity_edit_form extends qtype_essay_edit_form {
    /** Number of rows in TEXTAREA elements */
    const TEXTAREA_ROWS = 5;

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        parent::definition_inner($mform);

        $plugin = $this->plugin_name();

        // Add plugin main form elements.
        $headername = 'autograding';
        $headerlabel = get_string($headername, $plugin);
        $mform->addElement('header', $headername, $headerlabel);
        $mform->setExpanded($headername, true);
        $name = 'upper_correctness';
        $label = get_string($name, $plugin);
        $mform->addElement('text', $name, $label);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_FLOAT);

        $name = 'lower_correctness';
        $label = get_string($name, $plugin);
        $mform->addElement('text', $name, $label);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_FLOAT);

        $name = 'questionlanguage';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $this->language_options());
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_ALPHA);
        $mform->setDefault($name, $this->get_default($name, $this->get_constant('NO_LANG')));

        $name = 'answerkey';
        $label = get_string($name, $plugin);
        $mform->addElement('editor', $name, $label, [], $this->editoroptions);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->addRule($name, get_string('requiredanswerkey', $plugin), 'required', '', 'client');

        $name = 'showanswerkey';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $this->dropdown_options());
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_default($name, $this->get_constant('SHOW_NONE')));

        $name = 'showfeedback';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $this->dropdown_options());
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_default($name, $this->get_constant('SHOW_TEACHERS_AND_STUDENTS')));

        $name = 'showtextstats';
        $label = get_string($name, $plugin);
        $mform->addElement('select', $name, $label, $this->dropdown_options());
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setType($name, PARAM_INT);
        $mform->setDefault($name, $this->get_default($name, $this->get_constant('SHOW_TEACHERS_ONLY')));

        $name = 'textstatitems';
        $label = get_string($name, $plugin);
        $options = $this->textstatitems_options();
        $elements = [];
        foreach ($options as $value => $text) {
            $elements[] = $mform->createElement('checkbox', $name . "[$value]", '', $text);
        }
        $mform->addGroup($elements, $name, $label, html_writer::empty_tag('br'), false);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->disabledIf($name, 'showtextstats', 'eq', $this->get_constant('SHOW_NONE'));

        foreach ($options as $value => $text) {
            $mform->setType($name . "[$value]", PARAM_INT);
        }

        // Set this options to be collapsed.
        $sections = ['responseoptions', 'responsetemplateheader', 'graderinfoheader'];
        foreach ($sections as $section) {
            if ($mform->elementExists($section)) {
                $mform->setExpanded($section, false);
            }
        }

        // Set these section textarea height.
        $sections = [
            'questiontext',
            'answerkey',
            'generalfeedback',
            'responsetemplate',
            'responsesample',
            'graderinfo',
        ];
        foreach ($sections as $section) {
            if ($mform->elementExists($section)) {
                $element = $mform->getElement($section);
                $attributes = $element->getAttributes();
                $attributes['rows'] = self::TEXTAREA_ROWS;
                $element->setAttributes($attributes);
            }
        }

        // Insert plugin main form before response options.
        $prevsection = 'responseoptions';
        $names = [
            'autograding',
            'upper_correctness',
            'lower_correctness',
            'questionlanguage',
            'answerkey',
            'showanswerkey',
            'showfeedback',
            'showtextstats',
            'textstatitems',
        ];
        foreach ($names as $name) {
            if ($mform->elementExists($name)) {
                $mform->insertElementBefore($mform->removeElement($name, false), $prevsection);
            }
        }
    }

    /**
     * Perform any preprocessing needed on the data passed to set_data() when editing a question.
     *
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        if (empty($question->options)) {
            return $question;
        }

        // Initialize fields that has numeric value.
        $question->showanswerkey = $question->options->showanswerkey;
        $question->showfeedback = $question->options->showfeedback;
        $question->showtextstats = $question->options->showtextstats;
        $question->upper_correctness = $question->options->upper_correctness;
        $question->lower_correctness = $question->options->lower_correctness;

        // Initialize fields that has text value in question language.
        $question->questionlanguage = $question->options->questionlanguage;

        // Initialize fields that has HTML editor value.
        $question->answerkey = [
            'text' => $question->options->answerkey,
            'format' => $question->options->answerkeyformat,
        ];

        $question->responsetemplate = [
            'text' => $question->options->responsetemplate,
            'format' => $question->options->responsetemplateformat,
        ];

        // Initialize textstatitems (a comma delimited list).
        if (!isset($question->options->textstatitems)) {
            $question->options->textstatitems = '';
        }
        $question->textstatitems = $question->options->textstatitems;
        $question->textstatitems = explode(',', $question->textstatitems);
        $question->textstatitems = array_flip($question->textstatitems);
        foreach ($this->textstatitems_options(false) as $value) {
            $question->textstatitems[$value] = array_key_exists($value, $question->textstatitems);
        }

        return $question;
    }

    /**
     * Get language options from available stopword files.
     *
     * @return array Language options for the select element.
     */
    private function language_options() {
        global $CFG;

        $dir = $CFG->dirroot . '/question/type/essaysimilarity/nlp/stopword/lang';
        $plugin = $this->plugin_name();

        // Get all file from preprocessing/stopword/lang and make them as options.
        $options = ['none' => get_string('language_none', $plugin)];

        $files = scandir($dir);
        $files = array_splice($files, 2);
        foreach ($files as $file) {
            $lang = substr($file, 0, 2);
            $options[$lang] = get_string('language_' . $lang, $plugin);
        }

        return $options;
    }

    /**
     * Dropdown option for select form type for show/hide.
     *
     * @return array Options for showing to students/teachers.
     */
    private function dropdown_options() {
        $plugin = $this->plugin_name();

        return [
            $this->get_constant('SHOW_NONE') => get_string('no', $plugin),
            $this->get_constant('SHOW_STUDENTS_ONLY') => get_string('showtostudentsonly', $plugin),
            $this->get_constant('SHOW_TEACHERS_ONLY') => get_string('showtoteachersonly', $plugin),
            $this->get_constant('SHOW_TEACHERS_AND_STUDENTS') => get_string('showtoteachersandstudents', $plugin),
        ];
    }

    /**
     * Return default value of an item.
     *
     * @param string $name Item name
     * @param string|mixed|null $value Default value
     * @return mixed
     */
    private function get_default($name, $value) {
        if (method_exists($this, 'get_default_value')) {
            return $this->get_default_value($name, $value); // For Moodle >= v3.10.
        }

        $itemname = $this->plugin_name() . '' . $name;
        return get_user_preferences($itemname, $value); // For Moodle <= v3.9.
    }

    /**
     * Get array of countable statistical item.
     *
     * @param bool $returntext Whether to return text labels or just values
     * @return array [type => description]
     */
    private function textstatitems_options($returntext = true) {
        $plugin = $this->plugin_name();

        $options = [
            'chars',
            'words',
            'sentences',
            'paragraphs',
            'uniquewords',
            'longwords',
            'charspersentence',
            'wordspersentence',
            'longwordspersentence',
            'sentencesperparagraph',
            'lexicaldensity',
            'fogindex',
        ];

        if ($returntext) {
            $options = array_flip($options);
            foreach (array_keys($options) as $option) {
                $options[$option] = get_string($option, $plugin);
            }
        }

        return $options;
    }

    /**
     * Fetch a constant attribute of qtype_essaysimilarity class inside "questiontype.php" file.
     *
     * @param string $name constant name
     * @return int constant value
     */
    private function get_constant($name) {
        return constant("qtype_essaysimilarity::$name");
    }

    /**
     * Get the plugin name.
     *
     * @return string
     */
    public function plugin_name() {
        return 'qtype_essaysimilarity';
    }

    /**
     * Get the question type.
     *
     * @return string
     */
    public function qtype() {
        return 'essaysimilarity';
    }
}
