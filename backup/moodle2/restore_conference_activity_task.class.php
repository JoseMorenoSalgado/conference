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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Restore task for the Conference activity.
 *
 * @package   mod_conference
 * @category  backup
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/conference/backup/moodle2/restore_conference_stepslib.php');

/**
 * Restore task for one Conference activity.
 */
class restore_conference_activity_task extends restore_activity_task {
    /**
     * Define activity-specific settings.
     */
    protected function define_my_settings() {
    }

    /**
     * Define restore steps.
     */
    protected function define_my_steps() {
        $this->add_step(
            new restore_conference_activity_structure_step(
                'conference_structure',
                'conference.xml'
            )
        );
    }

    /**
     * Define text content requiring link decoding.
     *
     * @return array
     */
    public static function define_decode_contents() {
        return [
            new restore_decode_content('conference', ['intro'], 'conference'),
        ];
    }

    /**
     * Define link decoding rules.
     *
     * @return array
     */
    public static function define_decode_rules() {
        return [
            new restore_decode_rule(
                'CONFERENCEVIEWBYID',
                '/mod/conference/view.php?id=$1',
                'course_module'
            ),
            new restore_decode_rule(
                'CONFERENCEINDEX',
                '/mod/conference/index.php?id=$1',
                'course'
            ),
        ];
    }

    /**
     * Define activity restore log rules.
     *
     * @return array
     */
    public static function define_restore_log_rules() {
        return [
            new restore_log_rule(
                'conference',
                'view',
                'view.php?id={course_module}',
                '{conference}'
            ),
        ];
    }

    /**
     * Define course-level restore log rules.
     *
     * @return array
     */
    public static function define_restore_log_rules_for_course() {
        return [
            new restore_log_rule(
                'conference',
                'view all',
                'index.php?id={course}',
                null
            ),
        ];
    }
}
