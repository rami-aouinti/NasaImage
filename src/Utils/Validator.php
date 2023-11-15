<?php

declare(strict_types=1);

/**
 * (c) Rami Aouinti <rami.aouinti@gmail.com>
 **/

namespace App\Utils;

use App\Helper\AvailableDatesHelper;
use DateTime;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function Symfony\Component\String\u;


/**
 * Class Validator
 */
final class Validator
{


    public function __construct(
        private AvailableDatesHelper $availableDatesHelper
    )
    {
    }

    public function validateFolder(?string $folder): string
    {
        if (empty($folder)) {
            throw new InvalidArgumentException('The folder name can not be empty.');
        }

        if (1 !== preg_match('/^[a-z_]+$/', $folder)) {
            throw new InvalidArgumentException(
                'The folder name must contain only lowercase latin characters and underscores.'
            );
        }

        return $folder;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function validateDate(?string $date, string $format = 'Y-m-d'): string
    {
        if(empty($date))
        {
            $array1 = $this->availableDatesHelper->getData();
            $date = end($array1);
        }
        $d = DateTime::createFromFormat($format, $date);
        if (empty($date)) {
            $array = $this->availableDatesHelper->getData();
            return end($array);
        }

         if (!$d && $d->format($format) === $date)
            throw new RuntimeException(sprintf('The format of "%s" is invalid.', $date));

        return $date;
    }
}
