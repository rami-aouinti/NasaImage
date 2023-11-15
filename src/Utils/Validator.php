<?php

declare(strict_types=1);

namespace App\Utils;

use DateTime;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Validator
{
    final public const URL = 'https://epic.gsfc.nasa.gov/api/images.php?available_dates';

    public function __construct(
        private HttpClientInterface $client
    ) {
    }

    public function validateFolder(?string $folder): string
    {
        if ($folder === null) {
            throw new InvalidArgumentException('The folder name can not be empty.');
        }

        return $folder;
    }

    public function validateDate(?string $date, string $format = 'Y-m-d'): string
    {
        if ($date === null) {
            $array = $this->client->request('GET', self::URL)->toArray();

            return end($array);
        }
        $dateRef = DateTime::createFromFormat($format, $date);
        if ($dateRef) {
            if ($dateRef->format($format) === $date) {
                throw new InvalidArgumentException(sprintf('The format of "%s" is invalid.', $date));
            }
        }

        return $date;
    }
}
