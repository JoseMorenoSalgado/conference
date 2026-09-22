# Conference activity for Moodle

`mod_conference` is a Moodle activity module for scheduling access to external video conferences.

A teacher adds an HTTPS meeting link for Zoom, Google Meet, Microsoft Teams, Jitsi, or another provider, optionally adds a meeting passcode, chooses the scheduled start and optional end time, and can upload a cover image. Students see a responsive conference card with a server-synchronised countdown. The external meeting URL and optional passcode are not exposed before the conference becomes available.

## Compatibility

- Moodle 4.5 LTS through Moodle 5.2.
- Tested with MariaDB and PostgreSQL in CI.
- No bundled third-party PHP or JavaScript libraries.
- No provider-specific API keys are required by this plugin.
- Uses Moodle APIs for capabilities, calendar, completion, events, files, privacy, backup, and restore.

## Features

- Conference name and Moodle description.
- HTTPS meeting URL validation on save and join.
- Optional conference access password/passcode.
- Passcodes are withheld until the server confirms the conference is live.
- Required scheduled start date and time.
- Optional scheduled end date and time.
- Optional 16:9 cover image.
- Theme-native Bootstrap presentation and namespaced CSS.
- Camera activity icon.
- Server-side join gate that does not expose the external URL early.
- Server-synchronised countdown with automatic Scheduled → Available now → Ended transitions.
- Course calendar action event with restore-safe regeneration.
- Completion by view.
- Moodle event logging.
- Privacy API declaration.
- Moodle backup and restore.
- PHPUnit scheduling tests.
- Moodle Plugin CI across Moodle 4.5 and 5.2 on MariaDB and PostgreSQL.

## Installation

### ZIP installation

1. Download the installable `conference.zip` package from a successful GitHub Actions run or create a ZIP whose single top-level directory is named `conference`.
2. In Moodle, go to **Site administration → Plugins → Install plugins**.
3. Upload the ZIP and complete Moodle's validation and installation steps.
4. Visit **Site administration → Notifications** if Moodle requests a database upgrade.

### Manual installation

1. Place this repository in `mod/conference`.
2. Visit **Site administration → Notifications**.
3. Complete the Moodle installation/upgrade process.

No Composer, npm, API keys, or shell commands are required on the production Moodle server.

## Usage

1. Enable editing in a course.
2. Add the **Conference** activity.
3. Enter the conference name and HTTPS meeting URL.
4. Optionally enter a provider passcode.
5. Select the start time and, if required, an end time.
6. Optionally upload a cover image.
7. Save the activity.

Before the scheduled start, students see the conference details and countdown but not the external meeting URL or passcode. When the conference becomes live, Moodle activates the join action. The protected join endpoint checks login, capability, schedule state, and the meeting URL again before continuing.

## External services

The plugin itself does not require a subscription or API credentials. It opens a meeting URL supplied by the teacher. The meeting provider may require its own account, licence, subscription, or meeting passcode. Those provider requirements are outside this plugin and should be disclosed to site users according to the provider's terms and privacy policy.

## Security

The configured meeting URL and optional access passcode are stored in the activity record but are not rendered to participants before the scheduled start. The protected join endpoint checks the authenticated Moodle session, `mod/conference:view`, schedule state, and HTTPS URL validity.

This plugin does not bypass waiting rooms, provider authentication, host controls, or any other security feature supplied by Zoom, Meet, Teams, Jitsi, or another provider.

Moodle backup files can contain the configured meeting URL and optional access passcode. Protect backup files according to the site's security policy.

See [SECURITY.md](SECURITY.md) for vulnerability reporting guidance.

## Privacy

The activity does not store per-user personal data in its own database tables. Moodle core may store normal access logs and completion information. When a participant follows the external conference link, the external provider processes the connection under its own privacy policy.

## Documentation and support

- Documentation: [docs/MARKETPLACE.md](docs/MARKETPLACE.md)
- Testing guide: [docs/TESTING.md](docs/TESTING.md)
- Issue tracker: https://github.com/JoseMorenoSalgado/conference/issues
- Source code: https://github.com/JoseMorenoSalgado/conference
- Contributing: [CONTRIBUTING.md](CONTRIBUTING.md)
- Changelog: [CHANGES.md](CHANGES.md)

## Development

The repository root is the Moodle plugin root, so it can be checked out directly as `mod/conference`.

Every pull request is validated with Moodle Plugin CI for:

- Moodle 4.5 + PHP 8.1 + MariaDB
- Moodle 4.5 + PHP 8.3 + PostgreSQL
- Moodle 5.2 + PHP 8.3 + MariaDB
- Moodle 5.2 + PHP 8.3 + PostgreSQL

The workflow runs PHP lint, Moodle Code Checker, PHPDoc checks, PHP Mess Detector, plugin validation, upgrade savepoint checks, Mustache lint, Grunt, and PHPUnit.

## License

GNU GPL v3 or later. See [LICENSE](LICENSE).
