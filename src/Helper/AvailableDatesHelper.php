<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AvailableDatesHelper
 */
class AvailableDatesHelper
{
    final public const URL = 'https://epic.gsfc.nasa.gov/api/images.php?available_dates';

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    /**
     * @return array|null
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getData(): ?array
    {
        /** @phpstan-ignore-next-line */
        $response = $this->client->request('GET',  self::URL);
        /** @phpstan-ignore-next-line */
        return $response->toArray();
    }
}
