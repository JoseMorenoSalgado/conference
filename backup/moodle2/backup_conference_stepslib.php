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
 * Backup structure for the Conference activity.
 *
 * @package   mod_conference
 * @category  backup
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the Conference backup structure.
 */
class backup_conference_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the structure.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        $conference = new backup_nested_element(
            'conference',
            ['id'],
            [
                'name',
                'intro',
                'introformat',
                'meetingurl',
                'accesspassword',
                'timestart',
                'timeend',
                'timecreated',
                'timemodified',
            ]
        );

        $conference->set_source_table(
            'conference',
            ['id' => backup::VAR_ACTIVITYID]
        );

        $conference->annotate_files('mod_conference', 'intro', null);
        $conference->annotate_files('mod_conference', 'coverimage', null);

        return $this->prepare_activity_structure($conference);
    }
}
