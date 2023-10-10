<?php

abstract class JsonApiClient
{
    /** @var string */
    protected $apiUrl;

    /** @var string[] */
    protected $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * @param string $apiUrl
     */
    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

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
        $ch = curl_init();

        $headers = array_merge($this->headers, $headers);

        if ($data !== null) {
            $data = json_encode($data);

            $headers['Content-Length'] = strlen($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_URL => $this->apiUrl . $endpoint . ($params === null ? '' : '?' . http_build_query($params)),
            CURLOPT_HTTPHEADER => array_map(function (string $key, string $value) {
                return $key . ': ' . $value;
            }, array_keys($headers), $headers),
            CURLOPT_POSTFIELDS => $data,
        ]);

        list($headers, $body) = explode("\r\n\r\n", curl_exec($ch), 2);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        $decoded = json_decode($body === '' ? '[]' : $body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON error: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
