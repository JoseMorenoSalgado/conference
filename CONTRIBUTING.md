# Contributing to mod_conference

Thank you for helping improve the Conference activity.

## Reporting bugs

Use the public issue tracker:

https://github.com/JoseMorenoSalgado/conference/issues

For a useful bug report, include:

- Moodle version and build.
- PHP version.
- Database engine and version.
- Theme name.
- Plugin version from `version.php`.
- Steps to reproduce.
- Expected result.
- Actual result.
- Relevant debugging output with secrets, meeting URLs, and passcodes removed.

Do not post live meeting credentials, access passcodes, API keys, personal data, or private URLs in public issues.

## Feature requests

Open a feature request in GitHub Issues and describe the user problem, expected workflow, and any Moodle APIs or compatibility constraints that should be considered.

## Pull requests

1. Create a focused branch from the current development branch.
2. Follow Moodle coding style and use English for code, comments, identifiers, and the source language file.
3. Keep CSS namespaced to this plugin.
4. Do not add bundled third-party libraries unless they are GPLv3-compatible and documented using Moodle's `thirdpartylibs.xml` process.
5. Add or update tests for behavioural changes.
6. Update `CHANGES.md` for user-visible changes.
7. Confirm the GitHub Actions Moodle Plugin CI workflow passes.

## Security changes

For sensitive security issues, follow [SECURITY.md](SECURITY.md) rather than opening a public issue.
