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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Activity configuration form for Conference.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Conference module form.
 */
class mod_conference_mod_form extends moodleform_mod {
    /**
     * Build the form.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        $mform->addElement('header', 'meetingdetails', get_string('meetingdetails', 'conference'));

        $mform->addElement(
            'url',
            'meetingurl',
            get_string('meetingurl', 'conference'),
            ['size' => 64],
            ['usefilepicker' => false]
        );
        $mform->setType('meetingurl', PARAM_URL);
        $mform->addRule('meetingurl', null, 'required', null, 'client');
        $mform->addHelpButton('meetingurl', 'meetingurl', 'conference');

        $mform->addElement(
            'passwordunmask',
            'accesspassword',
            get_string('accesspassword', 'conference'),
            ['size' => 32, 'maxlength' => 255, 'autocomplete' => 'off']
        );
        $mform->setType('accesspassword', PARAM_RAW_TRIMMED);
        $mform->addRule(
            'accesspassword',
            get_string('maximumchars', '', 255),
            'maxlength',
            255,
            'client'
        );
        $mform->addHelpButton('accesspassword', 'accesspassword', 'conference');

        $mform->addElement('date_time_selector', 'timestart', get_string('timestart', 'conference'));
        $mform->addHelpButton('timestart', 'timestart', 'conference');
        $mform->setDefault('timestart', time() + HOURSECS);

        $mform->addElement('advcheckbox', 'enabletimeend', get_string('enabletimeend', 'conference'));
        $mform->setDefault('enabletimeend', 0);

        $mform->addElement('date_time_selector', 'timeend', get_string('timeend', 'conference'));
        $mform->addHelpButton('timeend', 'timeend', 'conference');
        $mform->setDefault('timeend', time() + (2 * HOURSECS));
        $mform->hideIf('timeend', 'enabletimeend', 'notchecked');

        $fileoptions = [
            'accepted_types' => ['image'],
            'maxbytes' => 0,
            'maxfiles' => 1,
            'subdirs' => false,
        ];
        $mform->addElement(
            'filemanager',
            'coverimage',
            get_string('coverimage', 'conference'),
            null,
            $fileoptions
        );
        $mform->addHelpButton('coverimage', 'coverimage', 'conference');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Prepare existing cover image.
     *
     * @param array $defaultvalues Existing values.
     */
    public function data_preprocessing(&$defaultvalues) {
        if (empty($this->current->instance)) {
            return;
        }

        $draftitemid = file_get_submitted_draft_itemid('coverimage');
        file_prepare_draft_area(
            $draftitemid,
            $this->context->id,
            'mod_conference',
            'coverimage',
            0,
            [
                'subdirs' => false,
                'maxfiles' => 1,
                'accepted_types' => ['image'],
            ]
        );
        $defaultvalues['coverimage'] = $draftitemid;
        $defaultvalues['enabletimeend'] = !empty($defaultvalues['timeend']);
    }

    /**
     * Validate form data.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $url = clean_param($data['meetingurl'] ?? '', PARAM_URL);
        $parts = $url ? parse_url($url) : false;
        if (!$parts || empty($parts['scheme']) || strtolower($parts['scheme']) !== 'https' || empty($parts['host'])) {
            $errors['meetingurl'] = get_string('invalidmeetingurl', 'conference');
        }

        if (core_text::strlen((string) ($data['accesspassword'] ?? '')) > 255) {
            $errors['accesspassword'] = get_string('maximumchars', '', 255);
        }

        if (!empty($data['enabletimeend']) && (int) $data['timeend'] <= (int) $data['timestart']) {
            $errors['timeend'] = get_string('invalidtimeend', 'conference');
        }

        return $errors;
    }
}
