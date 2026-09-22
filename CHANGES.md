# Changelog

All notable changes to `mod_conference` are documented here.

## 0.2.2-alpha - 2026-09-22

- Fixed cover image delivery by correctly separating the pluginfile item id from the stored file path.
- Removed duplicated activity description content from the conference card; Moodle/theme rendering remains the single description source.
- Preserved Moodle's forced-download argument when serving cover images.
- Clarified that provider passcodes should not be placed in the activity description because the description can be visible before the scheduled start.

## 0.2.1-alpha - 2026-09-22

- Added optional external conference access passcodes.
- Added a secure passcode handoff page available only after the conference is live.
- Added server-synchronised countdown and automatic conference state transitions.
- Added Moodle calendar action integration and restore-safe event regeneration.
- Added Moodle backup and restore support for conference settings and cover images.
- Added upgrade handling for the optional passcode field.
- Added MariaDB and PostgreSQL CI coverage for Moodle 4.5 and Moodle 5.2.
- Added PHP lint, Moodle Code Checker, PHPDoc, PHP Mess Detector, Mustache, Grunt, savepoint, validation, and PHPUnit quality gates.
- Hardened capability checks, URL validation, file serving, calendar visibility, and deletion through Moodle APIs.

## 0.1.0-alpha - 2026-09-21

- Initial scheduled external conference activity.
- Added conference name, description, HTTPS meeting URL, start/end scheduling, cover image, completion-by-view, event logging, and calendar integration.
