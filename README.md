# Conference activity for Moodle

mod_conference is a Moodle activity module for scheduled external video conferences.

A teacher pastes an HTTPS meeting link for Zoom, Google Meet, Microsoft Teams, Jitsi, or another provider, chooses the scheduled date and time, and can upload a cover image. Students see a responsive card. The external URL is not included in the page source before the scheduled start time; the Join button appears only when the conference is available and a protected server-side endpoint validates the schedule again before redirecting.

## Compatibility

- Moodle 4.5 LTS through Moodle 5.2.
- No third-party PHP or JavaScript dependencies.
- Uses Moodle APIs for files, capabilities, events, completion, calendar, privacy, backup, and restore.

## Current features

- Conference name and Moodle description.
- HTTPS meeting URL validation.
- Required scheduled start date and time.
- Optional scheduled end date and time.
- Optional cover image displayed as a responsive card.
- Theme-native Bootstrap styles and CSS variables.
- Camera activity icon.
- Server-side URL gate to avoid exposing the meeting URL early.
- Course calendar event.
- Completion by view.
- Moodle event logging.
- Privacy API declaration.
- Moodle backup and restore.
- PHPUnit scheduling tests.
- Moodle Plugin CI for Moodle 4.5 and 5.2 on MariaDB and PostgreSQL.

## Installation

1. Install the plugin directory as mod/conference.
2. Visit Site administration > Notifications to complete installation.
3. In a course, enable editing and add the Conference activity.
4. Enter the conference name, HTTPS meeting URL, date and time, and optional cover image.

## Security model

The configured meeting URL is stored in the activity record but is not rendered to participants before the start time. Participants use the secure join endpoint, which checks login, capability, schedule state, and HTTPS validity before redirecting.

This plugin does not bypass security controls provided by Zoom, Meet, Teams, or other conference systems. Meeting-room access controls should still be configured at the provider.

## License

GNU GPL v3 or later.
