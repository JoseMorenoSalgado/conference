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
 * Internal helpers for the Conference activity.
 *
 * @package   mod_conference
 * @copyright 2026 José Moreno Salgado
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
 * Detect the external conference provider from a meeting URL.
 *
 * @param string $meetingurl Meeting URL.
 * @return string Provider key.
 */
function conference_detect_provider(string $meetingurl): string {
    $host = strtolower((string) parse_url($meetingurl, PHP_URL_HOST));

    if ($host === 'zoom.us' || str_ends_with($host, '.zoom.us')) {
        return 'zoom';
    }

    if ($host === 'meet.google.com') {
        return 'googlemeet';
    }

    if ($host === 'teams.microsoft.com' || str_ends_with($host, '.teams.microsoft.com')) {
        return 'teams';
    }

    if ($host === 'meet.jit.si' || $host === '8x8.vc' || str_ends_with($host, '.8x8.vc')) {
        return 'jitsi';
    }

    if ($host === 'webex.com' || str_ends_with($host, '.webex.com')) {
        return 'webex';
    }

    return 'other';
}

/**
 * Get the translated display name for a provider.
 *
 * @param string $provider Provider key.
 * @return string
 */
function conference_get_provider_name(string $provider): string {
    $providers = ['zoom', 'googlemeet', 'teams', 'jitsi', 'webex', 'other'];
    if (!in_array($provider, $providers, true)) {
        $provider = 'other';
    }

    return get_string('provider' . $provider, 'conference');
}

/**
 * Get the translated label for a provider meeting reference.
 *
 * @param string $provider Provider key.
 * @return string
 */
function conference_get_meeting_reference_label(string $provider): string {
    if ($provider === 'googlemeet') {
        return get_string('meetingcode', 'conference');
    }

    if ($provider === 'jitsi' || $provider === 'webex') {
        return get_string('meetingroom', 'conference');
    }

    return get_string('meetingid', 'conference');
}

/**
 * Extract a human-readable meeting reference from the provider URL.
 *
 * This is deliberately only used after the conference is live so a meeting
 * identifier is not exposed before the server-side schedule gate opens.
 *
 * @param string $meetingurl Meeting URL.
 * @param string|null $provider Provider key.
 * @return string|null
 */
function conference_get_meeting_reference(string $meetingurl, ?string $provider = null): ?string {
    $provider = $provider ?? conference_detect_provider($meetingurl);
    $path = rawurldecode((string) parse_url($meetingurl, PHP_URL_PATH));

    if ($provider === 'zoom') {
        if (preg_match('~/(?:j|wc/join)/(\d{9,12})(?:/|$)~', $path, $matches)) {
            return conference_format_zoom_meeting_id($matches[1]);
        }

        $query = (string) parse_url($meetingurl, PHP_URL_QUERY);
        parse_str($query, $params);
        if (!empty($params['confno']) && preg_match('/^\d{9,12}$/', (string) $params['confno'])) {
            return conference_format_zoom_meeting_id((string) $params['confno']);
        }

        return null;
    }

    if ($provider === 'googlemeet') {
        $code = trim($path, '/');
        if (preg_match('/^[a-z]{3}-[a-z]{4}-[a-z]{3}$/i', $code)) {
            return strtolower($code);
        }

        return null;
    }

    if ($provider === 'jitsi') {
        $room = trim($path, '/');
        return $room !== '' ? $room : null;
    }

    if ($provider === 'webex') {
        if (preg_match('~/(?:meet|join)/([^/?#]+)~i', $path, $matches)) {
            return $matches[1];
        }
    }

    return null;
}

/**
 * Format a numeric Zoom meeting id for readability.
 *
 * @param string $meetingid Raw Zoom meeting id.
 * @return string
 */
function conference_format_zoom_meeting_id(string $meetingid): string {
    $meetingid = preg_replace('/\D+/', '', $meetingid);
    $length = strlen($meetingid);

    if ($length === 11) {
        return substr($meetingid, 0, 3)
            . ' ' . substr($meetingid, 3, 4)
            . ' ' . substr($meetingid, 7, 4);
    }

    if ($length === 10) {
        return substr($meetingid, 0, 3)
            . ' ' . substr($meetingid, 3, 3)
            . ' ' . substr($meetingid, 6, 4);
    }

    return $meetingid;
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
