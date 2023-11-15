<?php

declare(strict_types=1);

/**
 * (c) Rami Aouinti <rami.aouinti@gmail.com>
 **/

namespace App\Helper;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AvailableImagesHelper
 */
class AvailableImagesHelper
{
    public function __construct(
        private HttpClientInterface $client,
        private string $nasaApiKey
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getData($date): ?array
    {
        $url = 'https://api.nasa.gov/EPIC/api/natural/date/' . $date . '?api_key=' . $this->nasaApiKey;

        $response = $this->client->request('GET', $url);
        return $response->toArray();
    }
}
