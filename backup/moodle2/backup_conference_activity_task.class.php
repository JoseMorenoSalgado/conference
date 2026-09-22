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
 * Backup task for the Conference activity.
 *
 * @package   mod_conference
 * @category  backup
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/conference/backup/moodle2/backup_conference_stepslib.php');

/**
 * Backup task for one Conference activity.
 */
class backup_conference_activity_task extends backup_activity_task {
    /**
     * Define activity-specific settings.
     */
    protected function define_my_settings() {
    }

    /**
     * Define backup steps.
     */
    protected function define_my_steps() {
        $this->add_step(
            new backup_conference_activity_structure_step(
                'conference_structure',
                'conference.xml'
            )
        );
    }

    /**
     * Encode links to this activity.
     *
     * @param string $content Content to process.
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        $search = '/(' . $base . '\/mod\/conference\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@CONFERENCEINDEX*$2@$', $content);

        $search = '/(' . $base . '\/mod\/conference\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@CONFERENCEVIEWBYID*$2@$', $content);

        return $content;
    }
}
