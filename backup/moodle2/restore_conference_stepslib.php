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
 * Restore structure for the Conference activity.
 *
 * @package   mod_conference
 * @category  backup
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restores one Conference activity.
 */
class restore_conference_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define restore paths.
     *
     * @return restore_path_element[]
     */
    protected function define_structure() {
        $paths = [
            new restore_path_element(
                'conference',
                '/activity/conference'
            ),
        ];

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore the Conference record.
     *
     * @param array $data Backup data.
     */
    protected function process_conference($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record('conference', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restore related files.
     */
    protected function after_execute() {
        $this->add_related_files('mod_conference', 'intro', null);
        $this->add_related_files('mod_conference', 'coverimage', null);
    }
}
