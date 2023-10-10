<?php

require('autoload.php');

/**
 * Configuration.
 */

$options = new Options($argv, [
    '--project',
    '-p',
    '--tag',
    '-t',
    '--name-prefix',
    '-n',
]);

$jiraUrl = $_ENV['JIRA_API_URL'] ?? 'https://easyatwork.atlassian.net/rest/api/3/';
$jiraLogin = $_ENV['JIRA_LOGIN'] ?? '';
$jiraToken = $_ENV['JIRA_TOKEN'] ?? '';

$jiraProjectKey = $options->getFirstOption([ '--project', '-p' ]) ?? $_ENV['JIRA_PROJECT_KEY'] ?? '';
$jiraVersionPrefix = $options->getFirstOption([ '--name-prefix', '-n' ]) ?? $_ENV['JIRA_VERSION_PREFIX'] ?? '';

/**
 * Parse the Git log for Jira issues.
 */

$changes = new Changes();

$tag = $options->getFirstOption([ '--tag', '-t' ]) ?? $changes->getLastTag();

echo 'Comparing ', $changes->getTag($tag, -1), ' to ', $tag, '...', PHP_EOL;

$log = $changes->getLog($tag);

$issues = [];

foreach ($log as $message) {
    preg_match_all('/(' . $jiraProjectKey . '-\d+)/', $message, $matches);

    $issues = array_merge($issues, $matches[1]);
}

echo 'Found ', count($issues), ' issues.', PHP_EOL;

/**
 * Create Jira client.
 */

$jira = new JiraCloudClient($jiraUrl);
$jira->authenticate($jiraLogin, $jiraToken);

$versionName = $jiraVersionPrefix . $tag;

/**
 * Create a Jira version for the release.
 */

$jiraVersions = $jira->getProjectVersions($jiraProjectKey, $versionName);

if ($jiraVersions['total']) {
    $jiraVersion = $jiraVersions['values'][0];

    echo 'Found existing release on Jira: ', $jiraVersion['id'], PHP_EOL;
} else {
    $jiraVersion = $jira->createProjectVersion('EASY', $versionName);

    echo 'Created new release on Jira: ', $jiraVersion['id'], PHP_EOL;
}

/**
 * Mark the Jira issues as fixed in this release.
 */

echo 'Updating issues...', PHP_EOL;

foreach ($issues as $issue) {
    echo $issue, ', ';

    $jira->updateIssue($issue, [
        'update' => [
            'fixVersions' => [
                [
                    'add' => [
                        'name' => $versionName,
                    ],
                ],
            ],
        ],
    ]);
}

echo PHP_EOL;

echo 'Done :)', PHP_EOL;

exit;
