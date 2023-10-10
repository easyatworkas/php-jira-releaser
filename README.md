# Jira Releaser

Parses Git log for Jira issues and creates or updates releases in Jira.

## Usage

Add the "bin" directory to your path, then run the command in any Git repository that uses Semver tags.

```bash
jira_releaser --project=EASY --tag=2.172.1 --name-prefix="API: "
```

## Options
| Option          | Description                                                                             |
|-----------------|-----------------------------------------------------------------------------------------|
| --project or -p | Required. Jira project key.                                                             |
| --tag or -t     | Optional. Git tag to create or update release for. The latest tag is used if not given. |
| --name-prefix   | Optional. Prefix to add to release name.                                                |

## Disclaimer

This was very quickly thrown together to solve a specific problem for us. It is not intended to be a general purpose tool. It is not well tested. It is not well documented. It is not well designed. It is not well written. It is not well anything. Use at your own risk.
