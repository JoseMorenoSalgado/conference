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
 * Tests for Conference helpers.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_conference;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/conference/locallib.php');

/**
 * Tests conference scheduling states.
 *
 * @coversNothing
 */
final class locallib_test extends \advanced_testcase {
    /**
     * Test scheduled, live, and ended states.
     */
    public function test_conference_states(): void {
        $conference = (object) [
            'timestart' => 1000,
            'timeend' => 2000,
        ];

        $this->assertSame('scheduled', conference_get_state($conference, 999));
        $this->assertSame('live', conference_get_state($conference, 1000));
        $this->assertSame('live', conference_get_state($conference, 1999));
        $this->assertSame('ended', conference_get_state($conference, 2000));
    }

    /**
     * Test a conference without an end time remains available.
     */
    public function test_conference_without_end_time(): void {
        $conference = (object) [
            'timestart' => 1000,
            'timeend' => 0,
        ];

        $this->assertSame('live', conference_get_state($conference, 999999));
    }
}
