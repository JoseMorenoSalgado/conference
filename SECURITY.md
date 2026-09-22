# Security policy

## Supported versions

Security fixes are applied to the current development line of `mod_conference`.

## Reporting a vulnerability

Do not disclose exploitable vulnerabilities, private meeting URLs, access passcodes, credentials, or personal data in a public GitHub issue.

For non-sensitive hardening suggestions, use the public issue tracker:

https://github.com/JoseMorenoSalgado/conference/issues

For a sensitive vulnerability, contact the maintainer privately through the GitHub account associated with this repository and provide:

- A concise description of the issue.
- Affected Moodle/plugin versions.
- Reproduction steps or proof of concept.
- Security impact.
- Suggested remediation, if known.

Please remove real user data and use test meeting credentials when reproducing the issue.

## Security model

The plugin does not expose the configured external meeting URL or optional provider passcode to participants before the scheduled start. The server-side join endpoint revalidates authentication, capability, schedule state, and HTTPS URL validity.

Security controls enforced by the external conference provider remain the responsibility of that provider and the Moodle administrator.
