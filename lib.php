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
 * Library functions for the Conference activity.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');
require_once($CFG->dirroot . '/mod/conference/locallib.php');

define('CONFERENCE_EVENT_TYPE_START', 'start');

/**
 * Declare supported Moodle features.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function conference_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COMMUNICATION;
        default:
            return null;
    }
}

/**
 * Add a conference instance.
 *
 * @param stdClass $data Form data.
 * @param mod_conference_mod_form|null $mform Form instance.
 * @return int New instance id.
 */
function conference_add_instance($data, $mform = null) {
    global $DB;

    $now = time();
    $data->timecreated = $now;
    $data->timemodified = $now;
    $data->timeend = !empty($data->enabletimeend) ? (int) $data->timeend : 0;
    $data->meetingurl = clean_param($data->meetingurl, PARAM_URL);
    $data->accesspassword = clean_param($data->accesspassword ?? '', PARAM_RAW_TRIMMED);

    $id = $DB->insert_record('conference', $data);

    $context = context_module::instance($data->coursemodule);
    conference_save_coverimage($data, $context);
    conference_update_calendar_event($id, $data->coursemodule);

    $completionexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event(
        $data->coursemodule,
        'conference',
        $id,
        $completionexpected
    );

    return $id;
}

/**
 * Update a conference instance.
 *
 * @param stdClass $data Form data.
 * @param mod_conference_mod_form|null $mform Form instance.
 * @return bool
 */
function conference_update_instance($data, $mform = null) {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();
    $data->timeend = !empty($data->enabletimeend) ? (int) $data->timeend : 0;
    $data->meetingurl = clean_param($data->meetingurl, PARAM_URL);
    $data->accesspassword = clean_param($data->accesspassword ?? '', PARAM_RAW_TRIMMED);

    $DB->update_record('conference', $data);

    $context = context_module::instance($data->coursemodule);
    conference_save_coverimage($data, $context);
    conference_update_calendar_event($data->id, $data->coursemodule);

    $completionexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event(
        $data->coursemodule,
        'conference',
        $data->id,
        $completionexpected
    );

    return true;
}

/**
 * Delete a conference instance.
 *
 * @param int $id Instance id.
 * @return bool
 */
function conference_delete_instance($id) {
    global $DB;

    $conference = $DB->get_record('conference', ['id' => $id]);
    if (!$conference) {
        return false;
    }

    $cm = get_coursemodule_from_instance('conference', $id);
    if ($cm) {
        \core_completion\api::update_completion_date_event($cm->id, 'conference', $id, null);
    }

    $eventid = $DB->get_field('event', 'id', [
        'modulename' => 'conference',
        'instance' => $id,
        'eventtype' => CONFERENCE_EVENT_TYPE_START,
    ]);
    if ($eventid) {
        $event = calendar_event::load($eventid);
        $event->delete();
    }

    $DB->delete_records('conference', ['id' => $id]);

    return true;
}

/**
 * Save the cover image from the draft area.
 *
 * @param stdClass $data Form data.
 * @param context_module $context Module context.
 */
function conference_save_coverimage($data, context_module $context) {
    if (!isset($data->coverimage)) {
        return;
    }

    file_save_draft_area_files(
        $data->coverimage,
        $context->id,
        'mod_conference',
        'coverimage',
        0,
        [
            'subdirs' => false,
            'maxfiles' => 1,
            'accepted_types' => ['image'],
        ]
    );
}

/**
 * Create or update the calendar event.
 *
 * @param int $conferenceid Conference id.
 * @param int|null $cmid Course module id, when already known.
 */
function conference_update_calendar_event($conferenceid, $cmid = null) {
    global $DB;

    $conference = $DB->get_record('conference', ['id' => $conferenceid], '*', MUST_EXIST);

    if ($cmid === null) {
        $cm = get_coursemodule_from_instance(
            'conference',
            $conferenceid,
            $conference->course,
            false,
            MUST_EXIST
        );
        $cmid = $cm->id;
    } else {
        $cm = $DB->get_record('course_modules', ['id' => $cmid], '*', MUST_EXIST);
    }

    $eventdata = (object) [
        'type' => CALENDAR_EVENT_TYPE_ACTION,
        'name' => get_string('calendarstart', 'conference', format_string($conference->name)),
        'description' => format_module_intro('conference', $conference, $cmid, false),
        'format' => FORMAT_HTML,
        'courseid' => $conference->course,
        'groupid' => 0,
        'userid' => 0,
        'modulename' => 'conference',
        'instance' => $conferenceid,
        'eventtype' => CONFERENCE_EVENT_TYPE_START,
        'timestart' => $conference->timestart,
        'timesort' => $conference->timestart,
        'timeduration' => $conference->timeend > $conference->timestart
            ? $conference->timeend - $conference->timestart
            : 0,
        'visible' => (int) $cm->visible,
        'priority' => null,
    ];

    $existingid = $DB->get_field('event', 'id', [
        'modulename' => 'conference',
        'instance' => $conferenceid,
        'eventtype' => CONFERENCE_EVENT_TYPE_START,
    ]);

    if ($existingid) {
        $eventdata->id = $existingid;
        $event = calendar_event::load($existingid);
        $event->update($eventdata, false);
        return;
    }

    calendar_event::create($eventdata, false);
}

/**
 * Refresh Conference calendar events.
 *
 * Moodle calls this after operations such as course restore so action events
 * can be recreated from the activity records.
 *
 * @param int $courseid Optional course id.
 * @param stdClass|int|null $instance Optional Conference instance or id.
 * @param stdClass|int|null $cm Optional course module object or id.
 * @return bool
 */
function conference_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $DB;

    if ($instance !== null) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('conference', ['id' => $instance], '*', MUST_EXIST);
        }

        $cmid = null;
        if ($cm !== null) {
            $cmid = is_object($cm) ? $cm->id : (int) $cm;
        }

        conference_update_calendar_event($instance->id, $cmid);
        return true;
    }

    $params = $courseid ? ['course' => $courseid] : [];
    $conferences = $DB->get_records('conference', $params);

    foreach ($conferences as $conference) {
        conference_update_calendar_event($conference->id);
    }

    return true;
}

/**
 * Provide the calendar action for a Conference event.
 *
 * @param calendar_event $event Calendar event.
 * @param \core_calendar\action_factory $factory Action factory.
 * @param int $userid Optional user id.
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_conference_core_calendar_provide_event_action(
    calendar_event $event,
    \core_calendar\action_factory $factory,
    int $userid = 0
) {
    global $DB, $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    $modinfo = get_fast_modinfo($event->courseid, $userid);
    if (empty($modinfo->instances['conference'][$event->instance])) {
        return null;
    }

    $cm = $modinfo->instances['conference'][$event->instance];
    if (!$cm->uservisible) {
        return null;
    }

    $conference = $DB->get_record('conference', ['id' => $event->instance]);
    if (!$conference) {
        return null;
    }

    $state = conference_get_state($conference);
    if ($state === 'ended') {
        return null;
    }

    if ($state === 'live') {
        $actionname = get_string('joinconference', 'conference');
        $url = new moodle_url('/mod/conference/join.php', ['id' => $cm->id]);
    } else {
        $actionname = get_string('viewconference', 'conference');
        $url = new moodle_url('/mod/conference/view.php', ['id' => $cm->id]);
    }

    return $factory->create_instance($actionname, $url, 1, true);
}

/**
 * Register file areas.
 *
 * @param stdClass $course Course.
 * @param stdClass $cm Course module.
 * @param context $context Context.
 * @return array
 */
function conference_get_file_areas($course, $cm, $context) {
    return ['coverimage' => get_string('coverimage', 'conference')];
}

/**
 * Serve conference cover images.
 *
 * @param stdClass $course Course.
 * @param stdClass $cm Course module.
 * @param context $context Context.
 * @param string $filearea File area.
 * @param array $args Path arguments.
 * @param bool $forcedownload Force download.
 * @param array $options Options.
 * @return bool
 */
function conference_pluginfile(
    $course,
    $cm,
    $context,
    $filearea,
    $args,
    $forcedownload,
    array $options = []
) {
    if ($context->contextlevel !== CONTEXT_MODULE || $filearea !== 'coverimage') {
        return false;
    }

    require_login($course, true, $cm);
    require_capability('mod/conference:view', $context);

    $filename = array_pop($args);
    $filepath = '/' . implode('/', $args) . '/';

    $fs = get_file_storage();
    $file = $fs->get_file(
        $context->id,
        'mod_conference',
        'coverimage',
        0,
        $filepath,
        $filename
    );

    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, DAYSECS, 0, false, $options);
}

/**
 * Record a view and update completion.
 *
 * @param stdClass $conference Conference.
 * @param stdClass $course Course.
 * @param cm_info|stdClass $cm Course module.
 * @param context_module $context Module context.
 */
function conference_view($conference, $course, $cm, $context) {
    $event = \mod_conference\event\course_module_viewed::create([
        'context' => $context,
        'objectid' => $conference->id,
    ]);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('conference', $conference);
    $event->trigger();

    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Return page type descriptions.
 *
 * @param string $pagetype Page type.
 * @param context $parentcontext Parent context.
 * @param context $currentcontext Current context.
 * @return array
 */
function conference_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return ['mod-conference-*' => get_string('page-mod-conference-x', 'conference')];
}
