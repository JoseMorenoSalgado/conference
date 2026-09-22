# Manual QA and release testing

Run these tests on a disposable Moodle site with developer debugging enabled and debugging messages displayed.

Use a non-default database prefix where practical.

## Installation

- Install the packaged `conference.zip` through Moodle's web plugin installer.
- Confirm there are no notices, warnings, debugging messages, or schema errors.
- Confirm the activity appears in the activity chooser.
- Confirm upgrade from the previous development build completes without errors.

## Teacher workflow

- Create a course and add a Conference activity.
- Save a valid HTTPS meeting URL.
- Verify an HTTP URL is rejected.
- Configure a start time in the future.
- Configure an optional end time after the start.
- Verify an end time before/equal to the start is rejected.
- Add an optional passcode containing letters, numbers, punctuation, and spaces.
- Upload a cover image and confirm it displays correctly.
- Edit the activity and confirm all settings persist.

## Student workflow before start

- Log in as an enrolled student.
- Confirm the activity is visible only when Moodle availability/visibility allows it.
- Confirm the external meeting URL is not present in the rendered page source.
- Confirm the configured passcode is not present in the rendered page source.
- Confirm the scheduled status and countdown are visible.
- Attempt the internal join endpoint directly and confirm access is denied until the start time.

## Student workflow when live

- Reach the scheduled start time without manually refreshing.
- Confirm the state changes automatically to Available now.
- Confirm the Join conference button becomes available.
- If no passcode is configured, confirm join redirects to the external HTTPS meeting.
- If a passcode is configured, confirm the passcode handoff page appears only after the start time.
- Confirm the passcode handoff page continues to the configured external meeting.

## Student workflow after end

- Reach the configured end time.
- Confirm the state changes automatically to Ended.
- Confirm the join button is removed.
- Attempt the internal join endpoint directly and confirm the conference is no longer joinable through the plugin.

## Permissions and visibility

- Verify a user without `mod/conference:view` cannot view or join.
- Verify hidden activities are not exposed to students.
- Verify course/calendar links respect Moodle visibility.

## Calendar

- Confirm a calendar event is created at the conference start time.
- Edit the schedule and confirm the calendar event updates.
- Hide/show the activity and confirm event visibility follows the activity.
- Delete the activity and confirm its calendar event is removed.

## Backup and restore

- Back up a course containing a Conference activity.
- Restore it to a different course/date context.
- Confirm name, description, meeting URL, optional passcode, cover image, start/end schedule, and completion settings are restored.
- Confirm Moodle's date offset is applied to restored conference dates.
- Confirm the calendar event is regenerated.
- Treat the backup as sensitive because it can contain the meeting URL and passcode.

## Database coverage

Run the functional smoke test at least once on MariaDB/MySQL-compatible infrastructure and once on PostgreSQL if available.

## Release decision

Do not submit the Marketplace release until:

- Automated CI is green.
- Manual QA has no unexpected debugging messages.
- Actual Marketplace screenshots have been captured.
- Documentation matches the released behaviour.
- The installable ZIP has been tested through Moodle's web installer.
