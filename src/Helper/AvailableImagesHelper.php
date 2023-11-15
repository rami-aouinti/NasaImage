<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AvailableImagesHelper
 */
class AvailableImagesHelper
{
    public function __construct(
        private HttpClientInterface $client,
        private string $nasaApiKey
    ) {
    }

    /**
     * @param string $date
     * @return array|null
     */
    public function getData(string $date): ?array
    {
        $url = 'https://api.nasa.gov/EPIC/api/natural/date/' . $date . '?api_key=' . $this->nasaApiKey;
        $response = $this->client->request('GET', $url);

        return $response->toArray();
    }
}
