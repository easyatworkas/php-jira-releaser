# Jira Releaser

Parses Git log for Jira issues and creates or updates releases in Jira.

## Installation

1. Clone the repository.
2. Add the variables `JIRA_API_URL` (ignore this if you're employed at EaW), `JIRA_LOGIN`, and `JIRA_TOKEN` to your environment ([how to create a token](https://support.atlassian.com/atlassian-account/docs/manage-api-tokens-for-your-atlassian-account/)).
3. Add the "bin" directory to your path.

## Usage

The command must be run in a Git repository.

```bash
jira_releaser --project=EASY --tag=2.172.1 --name-prefix="API: "
```

### Options
| Option          | Description                                                                             |
|-----------------|-----------------------------------------------------------------------------------------|
| --project or -p | Required. Jira project key.                                                             |
| --tag or -t     | Optional. Git tag to create or update release for. The latest tag is used if not given. |
| --name-prefix   | Optional. Prefix to add to release name.                                                |

## Disclaimer

This was very quickly thrown together to solve a specific problem for us. It is not intended to be a general purpose tool. It is not well tested. It is not well documented. It is not well designed. It is not well written. It is not well anything. Use at your own risk.
