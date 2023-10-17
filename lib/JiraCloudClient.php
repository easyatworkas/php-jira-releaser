<?php

/**
 * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/
 */
class JiraCloudClient extends JsonApiClient
{
    /**
     * @param string $method
     * @param string $endpoint
     * @param array|null $params
     * @param array|null $data
     * @param array $headers
     * @return array
     * @throws Exception
     */
    protected function request(string $method, string $endpoint, array $params = null, array $data = null, array $headers = []): array
    {
        $response = parent::request($method, $endpoint, $params, $data, $headers);

        if ($response['errorMessages'] ?? null) {
            throw new Exception(implode(', ', $response['errorMessages']));
        }

        return $response;
    }

    /**
     * @param string $email
     * @param string $token
     * @return void
     */
    public function authenticate(string $email, string $token)
    {
        $this->headers['Authorization'] = 'Basic ' . base64_encode($email . ':' . $token);
    }

    /**
     * @param string $project
     * @param string|null $query
     * @return array
     * @throws Exception
     */
    public function getProjectVersions(string $project, string $query = null)
    {
        return $this->request('GET', 'project/' . $project . '/version', array_filter([
            'query' => $query,
        ]));
    }

    /**
     * @param string $project
     * @param string $version
     * @return array
     * @throws Exception
     */
    public function createProjectVersion(string $project, string $version)
    {
        return $this->request('POST', 'version', null, [
            'name' => $version,
            'project' => $project, // Deprecated by Jira, but easier for us :)
            'released' => true,
            'releaseDate' => date('Y-m-d'),
        ]);
    }

    /**
     * @param string $issue
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function updateIssue(string $issue, array $data)
    {
        return $this->request('PUT', 'issue/' . $issue, null, $data);
    }
}
