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
 * Display a Conference activity.
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

conference_view($conference, $course, $cm, $context);

$PAGE->set_url('/mod/conference/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($conference->name));
$PAGE->set_heading(format_string($course->fullname));

$state = conference_get_state($conference);
$coverurl = conference_get_coverimage_url($context);
$elementid = 'mod-conference-card-' . $cm->id;

$statusclass = 'secondary';
if ($state === 'live') {
    $statusclass = 'success';
} else if ($state === 'ended') {
    $statusclass = 'dark';
}

$data = [
    'elementid' => $elementid,
    'name' => format_string($conference->name),
    'intro' => format_module_intro('conference', $conference, $cm->id),
    'hasintro' => !empty(trim($conference->intro)),
    'coverurl' => $coverurl ? $coverurl->out(false) : null,
    'hascover' => (bool) $coverurl,
    'haspassword' => $conference->accesspassword !== null && $conference->accesspassword !== '',
    'passwordlabel' => get_string('accesspassword', 'conference'),
    'passwordrequired' => get_string('passwordrequired', 'conference'),
    'startlabel' => get_string('timestart', 'conference'),
    'start' => userdate($conference->timestart, get_string('strftimedatetimeshort')),
    'hasend' => !empty($conference->timeend),
    'endlabel' => get_string('timeend', 'conference'),
    'end' => !empty($conference->timeend)
        ? userdate($conference->timeend, get_string('strftimedatetimeshort'))
        : '',
    'status' => get_string('status' . $state, 'conference'),
    'statusclass' => $statusclass,
    'islive' => $state === 'live',
    'isscheduled' => $state === 'scheduled',
    'isended' => $state === 'ended',
    'joinurl' => (new moodle_url('/mod/conference/join.php', ['id' => $cm->id]))->out(false),
    'joinlabel' => get_string('joinconference', 'conference'),
    'scheduledmessage' => get_string(
        'notopenyet',
        'conference',
        userdate($conference->timestart, get_string('strftimedatetimeshort'))
    ),
    'endedmessage' => get_string('conferenceended', 'conference'),
    'refreshhint' => get_string('refreshhint', 'conference'),
    'countdownprefix' => get_string('countdownprefix', 'conference'),
];

if ($state !== 'ended' && ($state === 'scheduled' || !empty($conference->timeend))) {
    $PAGE->requires->js_call_amd('mod_conference/schedule', 'init', [[
        'elementId' => $elementid,
        'serverTime' => time(),
        'startTime' => (int) $conference->timestart,
        'endTime' => (int) $conference->timeend,
        'countdownPrefix' => get_string('countdownprefix', 'conference'),
        'scheduledLabel' => get_string('statusscheduled', 'conference'),
        'liveLabel' => get_string('statuslive', 'conference'),
        'endedLabel' => get_string('statusended', 'conference'),
        'liveAnnouncement' => get_string('liveannouncement', 'conference'),
        'endedAnnouncement' => get_string('conferenceended', 'conference'),
    ]]);
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_conference/card', $data);
echo $OUTPUT->footer();
