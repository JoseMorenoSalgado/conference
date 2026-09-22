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
 * Upgrade steps for the Conference activity.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade mod_conference.
 *
 * @param int $oldversion Installed plugin version.
 * @return bool
 */
function xmldb_conference_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026092200) {
        $table = new xmldb_table('conference');
        $field = new xmldb_field(
            'accesspassword',
            XMLDB_TYPE_CHAR,
            '255',
            null,
            XMLDB_NOTNULL,
            null,
            '',
            'meetingurl'
        );

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2026092200, 'conference');
    }

    return true;
}
