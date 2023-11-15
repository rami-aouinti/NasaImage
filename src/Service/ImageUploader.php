<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * Class ImageUploader
 */
class ImageUploader
{
    public function __construct(
        private string $targetDirectory,
        private readonly string $nasaApiKey,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function uploadFromUrlToFolder(string $folder, string $date): void
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $date);
        if ($datetime) {
            $month = $datetime->format('m');
            $day = $datetime->format('d');
            $year = $datetime->format('Y');
            $uploadPath = $this->targetDirectory . '/' . $folder . '/' . $year . '/' . $month . '/' . $day;

            $filesystem = new Filesystem();

            try {
                $filesystem->mkdir(
                    Path::normalize($uploadPath),
                );
            } catch (IOExceptionInterface $exception) {
                echo 'An error occurred while creating your directory at ' . $exception->getPath();
            }
            $images = "https://api.nasa.gov/EPIC/api/natural/date/{$year}-{$month}-{$day}?api_key={$this->nasaApiKey}";
            $meta = file_get_contents($images);
            if ($meta) {
                $arr = json_decode($meta);
            } else
            {
                $arr = [];
            }

            foreach ($arr as $item) {
                $name = $item->image . '.png';
                $archive = "https://epic.gsfc.nasa.gov/archive/natural/{$year}/{$month}/{$day}/png/";

                $source = $archive . $name;
                $destination = $this->targetDirectory .
                    '/' .
                    $folder .
                    '/' .
                    $year .
                    '/' .
                    $month .
                    '/' .
                    $day .
                    '/' .
                    $name;
                copy($source, $destination);
            }
        }
    }
}
