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
 * Internal helpers for the Conference activity.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get conference state.
 *
 * @param stdClass $conference Conference record.
 * @param int|null $now Timestamp override.
 * @return string
 */
function conference_get_state($conference, $now = null) {
    $now = $now ?? time();

    if ($now < (int) $conference->timestart) {
        return 'scheduled';
    }

    if (!empty($conference->timeend) && $now >= (int) $conference->timeend) {
        return 'ended';
    }

    return 'live';
}

/**
 * Get the first cover image URL.
 *
 * @param context_module $context Module context.
 * @return moodle_url|null
 */
function conference_get_coverimage_url(context_module $context) {
    $fs = get_file_storage();
    $files = $fs->get_area_files(
        $context->id,
        'mod_conference',
        'coverimage',
        0,
        'sortorder, id',
        false
    );

    if (!$files) {
        return null;
    }

    $file = reset($files);

    return moodle_url::make_pluginfile_url(
        $context->id,
        'mod_conference',
        'coverimage',
        0,
        $file->get_filepath(),
        $file->get_filename()
    );
}
