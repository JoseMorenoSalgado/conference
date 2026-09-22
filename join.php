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
 * Secure join endpoint for Conference.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/mod/conference/locallib.php');

$id = required_param('id', PARAM_INT);

[$course, $cm] = get_course_and_cm_from_cmid($id, 'conference');
$conference = $DB->get_record('conference', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/conference:view', $context);

$state = conference_get_state($conference);
$viewurl = new moodle_url('/mod/conference/view.php', ['id' => $cm->id]);

if ($state === 'scheduled') {
    redirect(
        $viewurl,
        get_string(
            'notopenyet',
            'conference',
            userdate($conference->timestart, get_string('strftimedatetimeshort'))
        ),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

if ($state === 'ended') {
    redirect(
        $viewurl,
        get_string('conferenceended', 'conference'),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

$meetingurl = clean_param($conference->meetingurl, PARAM_URL);
$parts = parse_url($meetingurl);
if (!$parts || empty($parts['scheme']) || strtolower($parts['scheme']) !== 'https' || empty($parts['host'])) {
    throw new moodle_exception('invalidmeetingurl', 'conference');
}

$providerkey = conference_detect_provider($meetingurl);
$provider = conference_get_provider_name($providerkey);
$meetingreference = conference_get_meeting_reference($meetingurl, $providerkey);

$accesspassword = clean_param($conference->accesspassword ?? '', PARAM_RAW_TRIMMED);
if ($accesspassword === '') {
    redirect(new moodle_url($meetingurl));
}

$PAGE->set_url('/mod/conference/join.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($conference->name));
$PAGE->set_heading(format_string($course->fullname));

$data = [
    'name' => format_string($conference->name),
    'status' => get_string('statuslive', 'conference'),
    'passwordlabel' => get_string('accesspassword', 'conference'),
    'accesspassword' => $accesspassword,
    'passwordhint' => get_string('passwordhint', 'conference'),
    'continueconference' => get_string('continueconference', 'conference'),
    'providerlabel' => get_string('provider', 'conference'),
    'provider' => $provider,
    'hasmeetingreference' => $meetingreference !== null,
    'meetingreferencelabel' => conference_get_meeting_reference_label($providerkey),
    'meetingreference' => $meetingreference,
    'meetingurl' => (new moodle_url($meetingurl))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_conference/join', $data);
echo $OUTPUT->footer();
