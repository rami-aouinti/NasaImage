<?php

declare(strict_types=1);

namespace App\Utils;

use App\Helper\AvailableDatesHelper;
use DateTime;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * Class Validator
 */
class Validator
{
    public function __construct(
        private AvailableDatesHelper $availableDatesHelper
    ) {
    }

    /**
     * @param string|null $folder
     * @return string
     */
    public function validateFolder(?string $folder): string
    {
        if (is_null($folder)) {
            throw new InvalidArgumentException('The folder name can not be empty.');
        } else
        {
            return $folder;
        }
    }

    /**
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public function validateDate(?string $date, string $format = 'Y-m-d'): string
    {
        if (is_null($date)) {
            $array = $this->availableDatesHelper->getData();
            if (is_array($array))
                return end($array);
            else
                return '2015-18-12';
        } else
        {
            $dateRef = DateTime::createFromFormat($format, $date);
            /** @phpstan-ignore-next-line */
            if (!$dateRef && $dateRef->format($format) === $date) {
                throw new RuntimeException(sprintf('The format of "%s" is invalid.', $date));
            }

            return $date;
        }
    }
}
