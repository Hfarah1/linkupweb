<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeepAiService
{
    private $client;
    private $apiKey = 'a0bbe45f-77a3-4ad7-a08a-961704fa2d56';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function generateImage(string $description): ?string
    {
        $response = $this->client->request('POST', 'https://api.deepai.org/api/text2img', [
            'headers' => [
                'api-key' => $this->apiKey,
            ],
            'body' => [
                'text' => $description,
            ],
        ]);

        $data = $response->toArray(false);
        return $data['output_url'] ?? null;
    }
} 