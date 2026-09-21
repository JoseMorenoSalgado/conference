# Conference activity for Moodle

A Moodle activity module for scheduled external video conferences. Teachers can configure a meeting URL (for example Zoom, Google Meet, Microsoft Teams, Jitsi, or another HTTPS provider), a scheduled start time, an optional end time, and a cover image. Students see a clean conference card and the join button is revealed server-side only when the scheduled time is reached.

Development targets Moodle 4.5 LTS through Moodle 5.2.

> Status: initial development repository. The implementation is being developed on a feature branch before the first release.

## Planned v1.0 scope

- Activity module: `mod_conference`.
- Conference name and description.
- HTTPS meeting URL.
- Required scheduled start date/time and optional end date/time.
- Optional uploaded cover image.
- Theme-native Bootstrap styling; no hard-coded brand palette.
- Server-side join gating so the URL is not sent to the browser before the start time.
- Countdown that refreshes the page when the conference opens.
- Moodle event logging and completion-by-view support.
- Course calendar event.
- Backup and restore.
- Privacy API declaration.
- English and Spanish language packs.
- Moodle Plugin CI workflow.

## License

GPL-3.0-or-later.
