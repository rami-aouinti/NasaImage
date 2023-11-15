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
 * Class AvailableDatesHelper
 */
class AvailableDatesHelper
{
    public function __construct(
        private HttpClientInterface $client,
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
    public function getData(): ?array
    {
        $url = 'https://epic.gsfc.nasa.gov/api/images.php?available_dates';

        $response = $this->client->request('GET', $url);
        return $response->toArray();
    }
}
