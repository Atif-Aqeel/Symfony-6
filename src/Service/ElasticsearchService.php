<?php

namespace App\Service;

// use Elastic\Elasticsearch\Client;
use Elasticsearch\Client;

class ElasticsearchService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $indexName
     */
    public function search(string $index, array $query) #: array
    {
        // Implement your Elasticsearch search logic here
        $params = [
            'index' => $index,
            'body' => $query,
        ];

        return $this->client->search($params);
    }
}
