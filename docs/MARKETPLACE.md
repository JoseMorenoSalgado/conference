# Moodle Marketplace submission information

This document contains the metadata and operational information needed to prepare the `mod_conference` Moodle Marketplace listing.

## Plugin identity

- Friendly name: Conference
- Frankenstyle component: `mod_conference`
- Plugin type: Activity module
- Source language: English
- Licence: GNU GPL v3 or later
- Supported Moodle versions: 4.5 through 5.2
- Source repository: https://github.com/JoseMorenoSalgado/conference
- Public issue tracker: https://github.com/JoseMorenoSalgado/conference/issues
- Documentation: https://github.com/JoseMorenoSalgado/conference/blob/main/README.md

## Suggested short description

Schedule secure access to external video conferences in Moodle with optional passcodes, cover images, calendar integration, and time-gated join links.

## Suggested full description

Conference is a Moodle activity module for scheduling access to external online meetings hosted by services such as Zoom, Google Meet, Microsoft Teams, Jitsi, or another HTTPS-based conference provider.

Teachers add the external meeting URL, choose the scheduled start time and optional end time, optionally add a provider access passcode, and can upload a cover image. Students see a responsive Moodle activity card with the conference details and a server-synchronised countdown.

The external URL and optional passcode are not sent to the participant page before the conference becomes available. At the scheduled time, the join action becomes available automatically. The server-side join endpoint checks the Moodle login, capability, schedule state, and HTTPS meeting URL before continuing.

The plugin uses Moodle APIs for capabilities, calendar events, completion, events, files, privacy, backup, and restore. It has no bundled third-party PHP or JavaScript libraries and requires no provider-specific API keys.

## Installation and setup

The production ZIP must contain a single top-level folder named `conference`, which can be installed into Moodle's `mod` plugin type.

No post-install Composer/npm commands are required.

After installation, a teacher can add the Conference activity to a course and configure:

- Name and description.
- HTTPS meeting URL.
- Optional provider access passcode.
- Scheduled start.
- Optional scheduled end.
- Optional cover image.

## Dependencies and subscriptions

The plugin has no Moodle plugin dependencies and no bundled third-party libraries.

The plugin itself does not require an external subscription or API credentials. A meeting provider may require its own account or subscription to create the meeting URL. That provider relationship is separate from this plugin.

## Privacy

The plugin does not store per-user personal data in its own tables. Moodle core can store standard logs and completion state. When a user follows an external meeting URL, the external provider processes that web request under its own privacy terms.

## Security

- Uses `required_param()` for request input.
- Requires authenticated Moodle course access.
- Requires `mod/conference:view` before displaying or taking join actions.
- Validates the configured meeting URL as HTTPS.
- Revalidates schedule state server-side on join.
- Does not expose the external URL or optional passcode to participants before the scheduled start.
- Uses Moodle file APIs for cover images.
- Uses Moodle Calendar API for event lifecycle.
- Uses Moodle Backup/Restore APIs for activity portability.

## Automated compatibility testing

The GitHub Actions matrix tests:

- Moodle 4.5 + PHP 8.1 + MariaDB
- Moodle 4.5 + PHP 8.3 + PostgreSQL
- Moodle 5.2 + PHP 8.3 + MariaDB
- Moodle 5.2 + PHP 8.3 + PostgreSQL

Checks include PHP lint, Moodle Code Checker, PHPDoc, PHP Mess Detector, plugin validation, upgrade savepoints, Mustache, Grunt, and PHPUnit.

## Screenshots required for Marketplace submission

Use screenshots from a real Moodle installation, not mockups. Recommended captures:

1. Teacher activity settings showing meeting URL, optional passcode, schedule, and cover image.
2. Student scheduled state with conference cover and countdown.
3. Student live state with the Join conference button.
4. Live passcode handoff page, using non-sensitive test credentials.
5. Ended conference state.

Store approved source screenshots outside the distributed plugin ZIP and upload them to Moodle Marketplace during listing creation.

## Repository naming

Moodle developer guidance recommends the repository naming convention `moodle-{plugintype}_{pluginname}`. For this plugin the recommended repository name is:

`moodle-mod_conference`

Renaming the GitHub repository before Marketplace submission is recommended for consistency. GitHub normally redirects the previous repository URL after a rename, but Marketplace metadata should use the final canonical URL.

## Final manual submission steps

- Confirm `mod_conference` remains available as a unique Frankenstyle component name at submission time.
- Rename the repository to `moodle-mod_conference` if adopting Moodle's recommended repository naming convention.
- Perform the manual QA procedure in [TESTING.md](TESTING.md) with developer debugging enabled.
- Capture the real screenshots listed above.
- Decide the Marketplace commercial model (free or paid).
- Download the installable `conference.zip` artifact from a successful CI run.
- Submit the plugin ZIP and listing metadata in Moodle Marketplace.
- Complete Marketplace automated archive/plugin checks.
- Address any reviewer feedback before publishing.
